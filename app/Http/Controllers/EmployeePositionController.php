<?php

namespace App\Http\Controllers;

use App\Models\EmployeePosition;
use App\Transformer\EmployeePositionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class EmployeePositionController extends Controller
{
    public function index(){
        return view('masters.employee_position.index');
    }

    public function datatable(Request $request){
        $data = EmployeePosition::query()
            ->where('branch_id', Auth::user()->branch_id);

        return DataTables::of($data)
            ->setTransformer(new EmployeePositionTransformer)
            ->make(true);
    }

    public function select2(Request $request){
        $query = EmployeePosition::where(function ($query) use ($request){
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
        return view('masters.employee_position.create');
    }

    public function store(Request $request){
        try {
            $this->validate($request, [
                'code' => 'required|max:255',
                'name' => 'required|max:255',
            ], [
                'code.required' => 'Kode wajib diisi!',
                'code.max' => 'Maksimum karakter kode adalah 255!',
                'name.required' => 'Nama wajib diisi!',
                'name.max' => 'Maksimum karakter nama adalah 255!',
            ]);

            EmployeePosition::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'branch_id' => Auth::user()->branch_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'description' => $request->input('description'),
                'status_id' => 1,
            ]);

            Session::flash('success', 'Sukses membuat data Jabatan baru!');
            return \redirect()->route('position');
        } catch (\Exception $ex){
            Log::channel('position')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }

    public function show($id){
        $position = EmployeePosition::find($id);

        return view('masters.employee_position.show', compact('position'));
    }

    public function edit($id){
        $position = EmployeePosition::find($id);

        return view('masters.employee_position.edit', compact('position'));
    }

    public function update(Request $request){
        try {
            $this->validate($request, [
                'id' => 'required',
                'code' => 'required|max:255',
                'name' => 'required|max:255',
            ], [
                'code.required' => 'Kode wajib diisi!',
                'code.max' => 'Maksimum karakter kode adalah 255!',
                'name.required' => 'Nama wajib diisi!',
                'name.max' => 'Maksimum karakter nama adalah 255!',
            ]);

            $position = EmployeePosition::find($request->input('id'));

            $position->update([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'updated_by' => Auth::id(),
            ]);

            Session::flash('success', 'Sukses mengubah data Jabatan!');
            return \redirect()->route('position');
        } catch (\Exception $ex){
            Log::channel('position')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }
}
