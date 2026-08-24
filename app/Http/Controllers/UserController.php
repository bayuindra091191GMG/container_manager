<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBranch;
use App\Transformer\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.user.index');
    }

    public function datatable(Request $request)
    {
        $data = DB::table('users')
            ->leftJoin('statuses', 'users.status_id', '=', 'statuses.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select(
                'users.id as id',
                'users.username as username',
                'users.name as name',
                'users.email as email',
                'users.branch_id as branch_id',
                'users.status_id as status_id',
                'users.created_at as created_at',
                'roles.name as level',
                'statuses.display as status',
                'branches.name as branch'
            );

        return DataTables::of($data)
            ->setTransformer(new UserTransformer)
            ->make(true);
    }

    public function create()
    {
        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.user.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|exists:roles,id',
            'username' => 'required|max:255|unique:users,username',
            'password' => 'required|max:255|min:6|same:confirm_password',
            'confirm_password' => 'required|max:255|min:6',
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users,email',
        ]);

        try {
            $mainBranchId = $request->input('main_branch');
            $role = Role::where('guard_name', 'web')->findOrFail($request->integer('role_id'));
            $isSuperAdmin = $role->name === 'super-admin';

            $newUser = User::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
                'branch_id' => $isSuperAdmin ? null : $mainBranchId,
                'status_id' => 1,
            ]);
            $newUser->assignRole($role);

            if (! $isSuperAdmin && $request->filled('other_branches')) {
                $otherBranchIds = $request->input('other_branches');
                foreach ($otherBranchIds as $otherBranchId) {
                    if ($mainBranchId == $otherBranchId) {
                        continue;
                    }

                    $newLink = new UserBranch;
                    $newLink->user_id = $newUser->id;
                    $newLink->branch_id = $otherBranchId;
                    $newLink->save();
                }
            }

            return redirect()->route('user')->with([
                'success' => 'Successfully created new User!',
            ]);
        } catch (\Exception $ex) {
            Log::channel('user')->error($ex);

            return redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!',
            ]);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();
        $selectedRoleId = $user->roles()
            ->where('guard_name', 'web')
            ->value('roles.id');
        $otherBranches = [];

        if (! $user->isSuperAdmin()) {
            $otherBranchIds = DB::table('user_branches')
                ->where('user_id', $id)
                ->pluck('branch_id')
                ->toArray();

            foreach ($branches as $branch) {
                if ($branch->id == $user->branch_id) {
                    continue;
                }

                $otherBranches[] = [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'selected' => in_array($branch->id, $otherBranchIds),
                ];
            }
        }

        return view('admin.user.edit', [
            'user' => $user,
            'branches' => $branches,
            'roles' => $roles,
            'selectedRoleId' => $selectedRoleId,
            'otherBranches' => $otherBranches,
        ]);
    }

    public function update(Request $request)
    {
        $userId = $request->input('id');

        $this->validate($request, [
            'id' => 'required',
            'role_id' => 'required|exists:roles,id',
            'username' => 'required|max:255|unique:users,username,'.$userId,
            'password' => 'nullable|required_with:confirm_password|max:255|min:6|same:confirm_password',
            'confirm_password' => 'nullable|required_with:password|max:255|min:6',
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users,email,'.$userId,
        ]);

        try {
            $user = User::findOrFail($userId);
            $mainBranchId = $request->input('main_branch');
            $role = Role::where('guard_name', 'web')->findOrFail($request->integer('role_id'));
            $isSuperAdmin = $role->name === 'super-admin';

            $user->update([
                'username' => $request->input('username'),
                'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password,
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'status_id' => $request->input('status'),
                'branch_id' => $isSuperAdmin ? null : $mainBranchId,
            ]);
            $user->syncRoles([$role]);

            if (! $isSuperAdmin) {
                if ($request->filled('other_branches')) {
                    $otherBranchIds = $request->input('other_branches');

                    $existingLinks = UserBranch::where('user_id', $user->id)->get();
                    foreach ($existingLinks as $link) {
                        if (! in_array($link->branch_id, $otherBranchIds) || $link->branch_id == $mainBranchId) {
                            $link->delete();
                        }
                    }

                    foreach ($otherBranchIds as $otherBranchId) {
                        if ($otherBranchId == $mainBranchId) {
                            continue;
                        }
                        if (DB::table('user_branches')
                            ->where('user_id', $user->id)
                            ->where('branch_id', $otherBranchId)
                            ->exists()) {
                            continue;
                        }

                        $newLink = new UserBranch;
                        $newLink->user_id = $user->id;
                        $newLink->branch_id = $otherBranchId;
                        $newLink->save();
                    }
                } else {
                    DB::table('user_branches')
                        ->where('user_id', $user->id)
                        ->delete();
                }
            } else {
                DB::table('user_branches')
                    ->where('user_id', $user->id)
                    ->delete();
            }

            Session::flash('success', 'User updated successfully!');

            return redirect()->route('user.edit', ['id' => $user->id])->with([
                'success' => 'User updated successfully!',
            ]);
        } catch (\Exception $ex) {
            Log::channel('user')->error($ex);

            return redirect()->back()->withInput()->withErrors([
                'Internal Server Error!',
            ]);
        }
    }
}
