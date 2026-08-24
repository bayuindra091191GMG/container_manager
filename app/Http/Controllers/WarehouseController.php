<?php

namespace App\Http\Controllers;

use App\Libs\AutoNumberGenerator;
use App\Models\Branch;
use App\Models\Locker;
use App\Models\Status;
use App\Models\Warehouse;
use App\Transformer\WarehouseTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class WarehouseController extends Controller
{
    public function index(){
        return view('masters.warehouse.index');
    }

    public function datatable(){
        $user = Auth::user();
        $warehouses = Warehouse::with('branch')
            ->where('branch_id', $user->branch_id)
            ->where('status_id', 1);

        return DataTables::of($warehouses)
            ->setTransformer(new WarehouseTransformer)
            ->make(true);
    }

    public function create(){
        $user = Auth::user();
        if($user->is_super == 1){
            $branches = Branch::all();
        }
        else{
            $branches = Branch::find($user->branch_id);
        }

        $data = [
            'branches'      => $branches,
        ];

        return view('masters.warehouse.create')->with($data);
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'name'    => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->all());
            }

            $user = Auth::user();
            $warehouse = new Warehouse();
            // $warehouse->guid = Uuid::uuid4();
            $warehouse->code = $request->input('code');
            $warehouse->branch_id = $user->branch_id;
            $warehouse->name = $request->input('name');
            $warehouse->description = $request->input('description');
            $warehouse->status_id = 1;
            $warehouse->created_by = Auth::id();
            $warehouse->save();

//            AutoNumberGenerator::nextAutonumber('WH');

            Session::flash('success', 'Sukses membuat data gudang baru!');
            return redirect()->route('warehouse');
        }
        catch (\Exception $ex){
            Session::flash('error', 'Maaf kesalahan terjadi!' . $ex);
            return back()->withInput($request->all());
        }
    }

    public function edit($id){
        $user = Auth::user();
        if($user->is_super == 1){
            $branches = Branch::all();
        }
        else{
            $branches = Branch::find($user->branch_id);
        }
        $warehouse = Warehouse::where('id', $id)->first();
        $statuses = Status::where('id', 1)->orWhere('id', 2)->get();
        if($warehouse == null){
            Session::flash('error', 'Warehouse tidak ditemukan!');
            return back();
        }

        $data = [
            'warehouse'     => $warehouse,
            'branches'      => $branches,
            'statuses'      => $statuses,
        ];

        return view('masters.warehouse.edit')->with($data);
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'name'    => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->all());
            }

            $user = Auth::user();
            $warehouse = Warehouse::where('id', $request->input('id'))->first();
            if($warehouse == null){
                Session::flash('error', 'Gudang tidak ditemukan!');
                return back()->withInput($request->all());
            }
            $warehouse->branch_id = $user->branch_id;
            $warehouse->name = $request->input('name');
            $warehouse->description = $request->input('description');
            $warehouse->status_id = $request->input('status_id');
            $warehouse->updated_by = $user->id;
            $warehouse->save();

            Session::flash('success', 'Sukses mengubah data gudang!');
            return redirect()->route('warehouse');
        }
        catch (\Exception $ex){
            Session::flash('error', 'Maaf kesalahan terjadi!' . $ex);
            return back()->withInput($request->all());
        }
    }

    public function show($id){
        $warehouse = Warehouse::find($id);

        return view('masters.warehouse.show', compact('warehouse'));
    }

    public function delete(Request $request){
        try{
            $warehouse = Warehouse::where('id', $request->input('id'))->first();
            if($warehouse == null){
                Session::flash('error', 'Gudang tidak ditemukan!');
                return back();
            }
            $warehouse->status_id = 2;
            $warehouse->save();

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

    public function select(Request $request){
        $term = trim($request->search);

        $user = Auth::user();
        if($user->is_super == 1 && empty($user->branch_id)){
            $searchDatas = DB::table('warehouses')
                ->select('id','code','name')
                ->where(function($q) use ($term){
                    $q->Where('code', 'LIKE', '%'. $term. '%')
                        ->orWhere('name', 'LIKE', '%'. $term. '%');
                })
                ->orderBy('id')
                ->get();
        }
        else{
            $searchDatas = DB::table('warehouses')
                ->select('id','code','name')
                ->where(function($q) use ($term){
                    $q->Where('code', 'LIKE', '%'. $term. '%')
                        ->orWhere('name', 'LIKE', '%'. $term. '%');
                })
                ->where('branch_id', $user->branch_id)
                ->orderBy('id')
                ->get();
        }

        $formatted_tags = [];

        foreach ($searchDatas as $searchData) {
            $formatted_tags[] = ['id' => $searchData->id, 'text' => $searchData->name];
        }

        return Response::json($formatted_tags);
    }
}
