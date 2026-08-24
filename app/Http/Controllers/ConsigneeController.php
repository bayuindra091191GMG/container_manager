<?php

namespace App\Http\Controllers;

use App\Libs\AutoNumberGenerator;
use App\Models\Branch;
use App\Models\Consignee;
use App\Models\Locker;
use App\Models\Status;
use App\Transformer\ConsigneeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class ConsigneeController extends Controller
{
    public function index(){
        return view('masters.consignee.index');
    }

    public function datatable(){
        $consignees = DB::table('consignees')
            ->join('branches', 'consignees.branch_id', '=', 'branches.id')
            ->select('consignees.id as id',
                'consignees.code as code',
                'consignees.name as name',
                'consignees.status_id as status_id',
                'consignees.created_at as created_at',
                'branches.name as branch_name')
            ->where('consignees.status_id', 1);

        return DataTables::of($consignees)
            ->setTransformer(new ConsigneeTransformer)
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

        return view('masters.consignee.create')->with($data);
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
            $consignee = new Consignee();
            // $consignee->guid = Uuid::uuid4();
            $consignee->branch_id = $request->input('branch_id');
            $consignee->code = $request->input('code');
            $consignee->name = $request->input('name');
            // $consignee->province_id = $request->input('province_id');
            // $consignee->location = $request->input('location');
            // $consignee->province_name = $request->input('province_id');
            $consignee->description = $request->input('description');
            $consignee->status_id = 1;
            $consignee->created_by = Auth::id();
            $consignee->save();

//            AutoNumberGenerator::nextAutonumber('WH');

            Session::flash('success', 'Sukses membuat data gudang baru!');
            return redirect()->route('consignee');
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
        $consignee = Consignee::where('id', $id)->first();
        $statuses = Status::where('id', 1)->orWhere('id', 2)->get();
        if($consignee == null){
            Session::flash('error', 'Consignee tidak ditemukan!');
            return back();
        }

        $data = [
            'consignee'     => $consignee,
            'branches'      => $branches,
            'statuses'      => $statuses,
        ];

        return view('masters.consignee.edit')->with($data);
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
            $consignee = Consignee::where('id', $request->input('id'))->first();
            if($consignee == null){
                Session::flash('error', 'Gudang tidak ditemukan!');
                return back()->withInput($request->all());
            }
            $consignee->branch_id = $request->input('branch_id');
            $consignee->code = $request->input('code');
            $consignee->name = $request->input('name');
            // $consignee->location = $request->input('location');
            $consignee->updated_by = $user->id;
            $consignee->save();

            Session::flash('success', 'Sukses mengubah data gudang!');
            return redirect()->route('consignee');
        }
        catch (\Exception $ex){
            Session::flash('error', 'Maaf kesalahan terjadi!' . $ex);
            return back()->withInput($request->all());
        }
    }

    public function show($id){
        $consignee = Consignee::find($id);

        return view('masters.consignee.show', compact('consignee'));
    }

    public function delete(Request $request){
        try{
            $consignee = Consignee::where('id', $request->input('id'))->first();
            if($consignee == null){
                Session::flash('error', 'Gudang tidak ditemukan!');
                return back();
            }
            $consignee->status_id = 2;
            $consignee->save();

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

        $provinceId = 0;
        if($request->province_id != null){
            $provinceId = intval($request->province_id);
        }

        $branchId = 0;
        if($request->branch_id != null){
            $branchId = intval($request->branch_id);
        }

        // $user = Auth::user();

        $searchDatas = DB::table('consignees')
            ->select('id','code','name')
            ->where(function($q) use ($term){
                $q->Where('code', 'LIKE', '%'. $term. '%')
                    ->orWhere('name', 'LIKE', '%'. $term. '%');
            });

        if($provinceId > 0){
            $searchDatas = $searchDatas->where('province_id', '=', $provinceId);
        }

        if($branchId > 0){
            $searchDatas = $searchDatas->where('branch_id', '=', $branchId);
        }

        $searchDatas = $searchDatas->orderBy('id')
            ->get();

        // if($user->is_super == 1){
        //     $searchDatas = DB::table('consignees')
        //         ->select('id','code','name')
        //         ->where(function($q) use ($term){
        //             $q->Where('code', 'LIKE', '%'. $term. '%')
        //                 ->orWhere('name', 'LIKE', '%'. $term. '%');
        //         });

        //     if($provinceId > 0){
        //         $searchDatas = $searchDatas->where('province_id', '=', $provinceId);
        //     }

        //     if($branchId > 0){
        //         $searchDatas = $searchDatas->where('branch_id', '=', $branchId);
        //     }

        //     $searchDatas = $searchDatas->orderBy('id')
        //         ->get();
        // }
        // else{
        //     $searchDatas = DB::table('consignees')
        //         ->select('id','code','name')
        //         ->where(function($q) use ($term){
        //             $q->Where('code', 'LIKE', '%'. $term. '%')
        //                 ->orWhere('name', 'LIKE', '%'. $term. '%');
        //         })
        //         ->where('branch_id', $user->branch_id);

        //     if($branchId > 0){
        //         $searchDatas = $searchDatas->where('branch_id', '=', $branchId);
        //     }

        //     $searchDatas = $searchDatas->orderBy('id')
        //         ->get();
        // }

        $formatted_tags = [];

        foreach ($searchDatas as $searchData) {
            $formatted_tags[] = ['id' => $searchData->id, 'text' => $searchData->name];
        }

        return Response::json($formatted_tags);
    }

    public function provinceSelect(Request $request){
        $term = trim($request->search);

        $user = Auth::user();
        $searchDatas = DB::table('address_provinces')
            ->select('id','code','name')
            ->where(function($q) use ($term){
                $q->Where('code', 'LIKE', '%'. $term. '%')
                    ->orWhere('name', 'LIKE', '%'. $term. '%');
            })
            ->get();

        $formatted_tags = [];

        foreach ($searchDatas as $searchData) {
            $formatted_tags[] = ['id' => $searchData->id, 'text' => $searchData->name];
        }

        return Response::json($formatted_tags);
    }

}
