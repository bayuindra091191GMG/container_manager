<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hierarchy;
use App\Transformer\HierarchyTransformers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;
use Yajra\DataTables\Facades\DataTables;

class HierarchyController extends Controller {
    public function index(){
        return view('admin.level.index');
    }

    public function datatable(Request $request){
        $data = Hierarchy::query();

        return DataTables::of($data)
            ->setTransformer(new HierarchyTransformers())
            ->make(true);
    }

    public function select2(Request $request){
        $query = Hierarchy::where(function ($query) use ($request){
            return $query->where('code', 'like', '%' . $request->get('search') . '%')
                ->orWhere('name', 'like', '%' . $request->get('search'). '%');
            })->where('code', '!=', 'DEV');

        $data = [];
        foreach ($query->get() as $item){
            $data[] = [
                'id' => $item->id,
                'text' => $item->name
            ];
        }

        return response($data);
    }

    public function add(){
        return view('admin.level.create');
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

            Hierarchy::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'description' => $request->input('description'),
                'status_id' => 1,
            ]);

            return \redirect()->route('level')->with([
                'success' => 'Successfully created new level!'
            ]);
        } catch (\Exception $ex){
            Log::channel('level')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }

    public function edit($id){
        $level = Hierarchy::find($id);

        return view('admin.level.edit', compact('level'));
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

            $hierarchy = Hierarchy::find($request->input('id'));

            $hierarchy->update([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'updated_by' => Auth::id(),
            ]);

            return \redirect()->route('level')->with([
                'success' => 'Level successfully updated!'
            ]);
        } catch (\Exception $ex){
            Log::channel('hierarchy')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!'
            ]);
        }
    }
}
