<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\UserBranch;
use App\Transformer\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(){
        return view('admin.user.index');
    }

    public function datatable(Request $request){
        $data = DB::table('users')
            ->join('statuses', 'users.status_id', '=', 'statuses.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->join('hierarchies', 'users.hierarchy_id', '=', 'hierarchies.id')
            ->select('users.id as id',
                'users.username as username',
                'users.name as name',
                'users.email as email',
                'users.branch_id as branch_id',
                'users.status_id as status_id',
                'users.created_at as created_at',
                'hierarchies.name as level',
                'statuses.display as status',
                'branches.name as branch')
            ->where('users.is_superadmin', '=', 0);

        return DataTables::of($data)
            ->setTransformer(new UserTransformer)
            ->make(true);
    }

    public function create(){
        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();

        return view('admin.user.create', compact('branches'));
    }

    public function store(Request $request){
        $this->validate($request, [
            'hierarchy_id' => 'required|exists:hierarchies,id',
            'username' => 'required|max:255|unique:users,username',
            'password' => 'required|max:255|min:6|same:confirm_password',
            'confirm_password' => 'required|max:255|min:6',
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users,email',
        ]);

        try {
            $mainBranchId = $request->input('main_branch');
            $hierarchyId = $request->input('hierarchy_id');

            $newUser = User::create([
                'hierarchy_id' => $hierarchyId,
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
                'branch_id' => $mainBranchId,
                'status_id' => 1,
                'is_superadmin' => $hierarchyId == 2 ? 1 : 0,
            ]);

            if($request->filled('other_branches')){
                $otherBranchIds = $request->input('other_branches');
                foreach($otherBranchIds as $otherBranchId){
                    if($mainBranchId == $otherBranchId) continue;
                    $newLink = new UserBranch();
                    $newLink->user_id = $newUser->id;
                    $newLink->branch_id = $otherBranchId;
                    $newLink->save();
                }
            }

            return \redirect()->route('user')->with([
                'success' => 'Successfully created new User!'
            ]);
        } catch (\Exception $ex){
            Log::channel('user')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }

    public function edit($id){
        $user = User::find($id);

        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();
        $otherBranches = [];
        if($user->is_superadmin == 0){
            $otherBranchIds = DB::table('user_branches')
            ->where('user_id', '=', $id)
            ->pluck('branch_id')
            ->toArray();

            foreach($branches as $branch){
                if($branch->id == $user->branch_id) continue;
                $otherBranches[] = [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'selected' => in_array($branch->id, $otherBranchIds)
                ];
            }
        }


        $data = [
            'user' => $user,
            'branches' => $branches,
            'otherBranches' => $otherBranches
        ];

        return view('admin.user.edit')->with($data);
    }

    public function update(Request $request){
        $userId = $request->input('id');

        $this->validate($request, [
            'id' => 'required',
            'username' => 'required|max:255|unique:users,username,'. $userId,
            'password' => 'nullable|required_with:confirm_password|max:255|min:6|same:confirm_password',
            'confirm_password' => 'nullable|required_with:password|max:255|min:6',
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users,email,'. $userId,
        ]);

        try {
            $user = User::find($userId);
            $mainBranchId = $request->input('main_branch');
            $hierarchyId = $request->input('hierarchy_id');

            $user->update([
                'username' => $request->input('username'),
                'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password,
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'status_id' => $request->input('status'),
                'hierarchy_id' => $request->input('hierarchy_id'),
                'branch_id' => $user->is_superadmin == 0 ? $mainBranchId : null,
                'is_superadmin' => $hierarchyId == 2 ? 1 : 0
            ]);

            if($user->is_superadmin == 0){
                if($request->filled('other_branches')){
                    $otherBranchIds = $request->input('other_branches');

                    // Check deleted links
                    $existingLinks = UserBranch::where('user_id', '=', $user->id)
                        ->get();

                    foreach($existingLinks as $link){
                        if(!in_array($link->id, $otherBranchIds)){
                            $link->delete();
                        }

                        if($link->branch_id == $mainBranchId){
                            $link->delete();
                        }
                    }

                    // Check new link
                    foreach($otherBranchIds as $otherBranchId){
                        if($otherBranchId == $mainBranchId) continue;
                        if(DB::table('user_branches')
                            ->where('user_id', '=', $user->id)
                            ->where('branch_id', '=', $otherBranchId)
                            ->exists()){
                            continue;
                        }

                        $newLink = new UserBranch();
                        $newLink->user_id = $user->id;
                        $newLink->branch_id = $otherBranchId;
                        $newLink->save();
                    }
                }
                else{
                    // Delete all links if exists
                    DB::table('user_branches')
                        ->where('user_id', '=', $user->id)
                        ->delete();
                }
            }

            Session::flash('success', 'User updated successfully!');
            return \redirect()->route('user.edit', ['id' => $user->id])->with([
                'success' => 'User updated successfully!'
            ]);
        } catch (\Exception $ex){
            dd($ex);
            Log::channel('user')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Internal Server Error!'
            ]);
        }
    }
}
