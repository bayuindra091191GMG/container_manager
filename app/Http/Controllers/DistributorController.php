<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchRecipient;
use App\Transformer\DistributorTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DistributorController extends Controller
{
    public function index(){
        return view('masters.distributor.index');
    }

    public function datatable(){
        $recipients = DB::table('branch_recipients')
            ->join('branches', 'branch_recipients.branch_id', '=', 'branches.id')
            ->select('branch_recipients.id as id',
                'branch_recipients.code as code',
                'branch_recipients.name as name',
                'branches.name as branch_name')
            ->where('branch_recipients.status_id', 1);

        return DataTables::of($recipients)
            ->setTransformer(new DistributorTransformer)
            ->make(true);
    }

    public function create(){
        $branches = Branch::all();

        $data = [
            'branches'      => $branches,
        ];

        return view('masters.distributor.create')->with($data);
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'code' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->all());
            }

            if($request->input('branch') < 1){
                Session::flash('error', 'Please select branch!');
                return back()->withInput($request->all());
            }

            if(DB::table('branch_recipients')
                ->where('code', '=', $request->input('code'))
                ->exists()){
                Session::flash('error', 'Distributor code already exists!');
                return back()->withInput($request->all());
            }

            $distributor = new BranchRecipient();
            $distributor->code = strtoupper($request->input('code'));
            $distributor->branch_id = $request->input('branch');
            $distributor->name = strtoupper($request->input('name'));
            $distributor->status_id = 1;
            $distributor->created_by = Auth::id();
            $distributor->save();

            Session::flash('success', 'A distributor has been successfully created!');
            return redirect()->route('distributor');
        }
        catch (\Exception $ex){
            Session::flash('error', 'Internal Server Error!' . $ex);
            return redirect()->back()->withInput($request->all());
        }
    }

    public function edit($id){
        $distributor = BranchRecipient::find($id);
        if(empty($distributor)){
            Session::flash('error', 'BAD REQUEST!');
            return redirect()->back();
        }

        $branches = Branch::all();

        $data = [
            'branches' => $branches,
            'distributor' => $distributor
        ];

        return view('masters.distributor.edit')->with($data);
    }

    public function update(Request $request, $id){
        try{
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'code' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->all());
            }

            if(DB::table('branch_recipients')
                ->where('id', '!=', $id)
                ->where('code', '=', $request->input('code'))
                ->exists()){
                Session::flash('error', 'Distributor code already exists!');
                return back()->withInput($request->all());
            }

            $distributor = BranchRecipient::find($id);
            if(empty($distributor)){
                Session::flash('error', 'BAD REQUEST!');
                return back()->withInput($request->all());
            }

            $distributor->code = strtoupper($request->input('code'));
            $distributor->branch_id = $request->input('branch');
            $distributor->name = strtoupper($request->input('name'));
            $distributor->updated_by = Auth::id();
            $distributor->save();

            Session::flash('success', 'A distributor has been successfully updated!');
            return redirect()->route('distributor');
        }
        catch (\Exception $ex){
            Session::flash('error', 'Internal Server Error!' . $ex);
            return redirect()->back()->withInput($request->all());
        }
    }

    public function delete(Request $request){
        try{
            $distributor = BranchRecipient::find($request->input('id'));
            $distributor->delete();

            return response([
                'success' => true
            ]);
        }
        catch (\Exception $ex){
            return response([
                'success' => false
            ]);
        }
    }
}
