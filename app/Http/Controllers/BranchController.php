<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Transformer\BranchTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function index()
    {
        return view('masters.branch.index');
    }

    public function datatable(Request $request)
    {
        $data = Branch::select('id', 'code', 'name', 'status_id')
            ->where('status_id', 1);

        return DataTables::of($data)
            ->setTransformer(new BranchTransformer())
            ->make();
    }

    public function select2(Request $request)
    {
        $query = Branch::where('status_id', 1)
            ->where(function ($query) use ($request) {
                return $query->where('code', 'like', '%'.$request->get('search').'%')
                    ->orWhere('name', 'like', '%'.$request->get('search').'%');
            });

        $data = [];
        foreach ($query->get() as $item) {
            $data[] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }

        return response($data);
    }

    public function create()
    {
        return view('masters.branch.create');
    }

    public function store(Request $request)
    {
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

            $newBranch = Branch::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'status_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $newBranch->save();

            Session::flash('success', 'Sukses membuat data Cabang baru!');

            return \redirect()->route('branch');
        } catch (\Exception $ex) {
            Log::channel('branch')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!',
            ]);
        }
    }

    public function show($id)
    {
        $branch = Branch::find($id);

        return view('masters.branch.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = Branch::find($id);
        $branches = Branch::where('status_id', 1)->get();

        return view('masters.branch.edit', compact('branch', 'branches'));
    }

    public function update(Request $request)
    {
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

            $branch = Branch::find($request->input('id'));

            $branch->update([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'latitude' => 0,
                'longitude' => 0,
                'updated_by' => Auth::id(),
            ]);

            $branch->save();

            Session::flash('success', 'Sukses mengubah data Cabang!');

            return \redirect()->route('branch');
        } catch (\Exception $ex) {
            Log::channel('branch')->error($ex);

            return \redirect()->back()->withInput()->withErrors([
                'Terjadi kesalahan di dalam server!',
            ]);
        }
    }
}
