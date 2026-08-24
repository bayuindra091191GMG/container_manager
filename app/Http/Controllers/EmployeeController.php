<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Transformer\EmployeeTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index(){
        return view('masters.employee.index');
    }

    public function datatable(Request $request){
        $data = Employee::where('status_id', 1)
            ->where('branch_id', Auth::user()->branch_id);

        return DataTables::of($data)
            ->setTransformer(new EmployeeTransformer)
            ->make(true);
    }

    public function select2(Request $request){
        $query = Employee::where('status_id', 1)
            ->where(function ($query) use ($request){
                return $query->where('code', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('name', 'like', '%' . $request->get('search'). '%');
            });

        if($request->get('branch')){
            $query->where('branch_id', $request->get('branch'));
        }

        $data = [];
        foreach ($query->get() as $item){
            $data[] = [
                'id' => $item->id,
                'text' => $item->name
            ];
        }

        return response($data);
    }

    public function create(){
        return view('masters.employee.create');
    }

    public function store(Request $request){
        $this->validate($request, [
            'code' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required',
            'phone' => 'required',
            'join_date' => 'required',
            // 'division_id' => 'required',
            // 'hierarchy_id' => 'required',
            'position_id' => 'required',
        ], [
            'code.required' => 'Kode wajib diisi!',
            'code.max' => 'Maksimum karakter kode adalah 255!',
            'name.required' => 'Nama wajib diisi!',
            'name.max' => 'Maksimum karakter nama adalah 255!',
            'email.required' => 'Email wajib diisi!',
            'phone.required' => 'No Telp wajib diisi!',
            'join_date.required' => 'Tgl Bergabung wajib diisi!',
            // 'division_id.required' => 'Divisi wajib diisi!',
            // 'hierarchy_id.required' => 'Hierarki wajib diisi!',
            'position_id.required' => 'Jabatan wajib diisi!',
        ]);

        try {
            Employee::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                // 'division_id' => $request->input('division_id'),
                // 'hierarchy_id' => $request->input('hierarchy_id'),
                'position_id' => $request->input('position_id'),
                'id_card_no' => $request->input('id_card_no'),
                'npwp' => $request->input('npwp'),
                'address' => $request->input('address'),
                'join_date' => Carbon::createFromFormat('d-m-Y', $request->input('join_date'))->toDateTimeString(),
                'status_id' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'branch_id' => Auth::user()->branch_id,
            ]);

            Session::flash('success', 'Sukses membuat data Karyawan baru!');
            return \redirect()->route('employee');
        } catch (\Exception $ex){
            Log::channel('employee')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }

    public function show($id){
        $employee = Employee::find($id);

        return view('masters.employee.show', compact('employee'));
    }

    public function edit($id){
        $employee = Employee::find($id);

        return view('masters.employee.edit', compact('employee'));
    }

    public function update(Request $request){

        $this->validate($request, [
            'id' => 'required',
            'code' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required',
            'phone' => 'required',
            'join_date' => 'required',
            // 'division_id' => 'required',
            // 'hierarchy_id' => 'required',
            'position_id' => 'required',
        ], [
            'code.required' => 'Kode wajib diisi!',
            'code.max' => 'Maksimum karakter kode adalah 255!',
            'name.required' => 'Nama wajib diisi!',
            'name.max' => 'Maksimum karakter nama adalah 255!',
            'email.required' => 'Email wajib diisi!',
            'phone.required' => 'No Telp wajib diisi!',
            'join_date.required' => 'Tgl Bergabung wajib diisi!',
            // 'division_id.required' => 'Divisi wajib diisi!',
            // 'hierarchy_id.required' => 'Hierarki wajib diisi!',
            'position_id.required' => 'Jabatan wajib diisi!',
        ]);

        try {
            $employee = Employee::find($request->input('id'));

            $employee->update([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                // 'division_id' => $request->input('division_id'),
                // 'hierarchy_id' => $request->input('hierarchy_id'),
                'position_id' => $request->input('position_id'),
                'id_card_no' => $request->input('id_card_no'),
                'npwp' => $request->input('npwp'),
                'address' => $request->input('address'),
                'join_date' => Carbon::createFromFormat('d-m-Y', $request->input('join_date'))->toDateTimeString(),
                'updated_by' => Auth::id(),
            ]);

            Session::flash('success', 'Sukses mengubah data Karyawan!');
            return \redirect()->route('employee');
        } catch (\Exception $ex){
            Log::channel('employee')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }
}
