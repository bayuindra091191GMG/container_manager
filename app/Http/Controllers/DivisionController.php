<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Transformer\DivisionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller
{

    public function index(){
        return view('masters.division.index');
    }

    public function datatable(Request $request){
        $data = Division::where('status_id', 1)
            ->where('branch_id', Auth::user()->branch_id);

        return DataTables::of($data)
            ->setTransformer(new DivisionTransformer)
            ->make(true);
    }

    public function select2(Request $request){
        $query = Division::where('status_id', 1)
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
        return view('masters.division.create');
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

            Division::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'branch_id' => Auth::user()->branch_id,
                'status_id' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            Session::flash('success', 'Sukses membuat data Divisi baru!');
            return \redirect()->route('division');
        } catch (\Exception $ex){
            Log::channel('division')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }

    public function show($id){
        $division = Division::find($id);

        return view('masters.division.show', compact('division'));
    }

    public function edit($id){
        $division = Division::find($id);

        return view('masters.division.edit', compact('division'));
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

            $division = Division::find($request->input('id'));

            $division->update([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'updated_by' => Auth::id(),
            ]);

            Session::flash('success', 'Sukses mengubah data Divisi!');
            return \redirect()->route('division');
        } catch (\Exception $ex){
            Log::channel('division')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }
}
