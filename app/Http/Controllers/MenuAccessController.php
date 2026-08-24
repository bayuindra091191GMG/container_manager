<?php

namespace App\Http\Controllers;

use App\Models\Hierarchy;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MenuAccessController extends Controller
{
    public function index()
    {
        $data = Menu::orderBy('sequence')->get();

        $menus = [];
        foreach ($data as $d){
            $actData = explode(',', $d->actions);
            $actions = [];
            foreach ($actData as $action){
                $actions[] = [
                    'code' => $d->code.'.'.$action,
                    'name' => str_replace('_', ' ', ucfirst($action)),
                    'checked' => false,
                ];
            }

            $menus[] = [
                'id' => $d->code,
                'name' => $d->name,
                'action' => $actions
            ];
        }

        return view('admin.menu_access.index', compact('menus'));
    }

    public function getPermission(Request $request){
        try {
            $hierarchy = Hierarchy::find($request->get('id'));

            $data = Menu::orderBy('sequence')->get();

            $menus = [];
            foreach ($data as $d){
                $actData = explode(',', $d->actions);
                $actions = [];
                foreach ($actData as $action){
                    $actions[] = [
                        'code' => $d->code.'.'.$action,
                        'name' => str_replace('_', ' ', ucfirst($action)),
                        'checked' => $hierarchy->hasPermissionTo($d->code.'.'.$action),
                    ];
                }

                $menus[] = [
                    'id' => $d->code,
                    'name' => $d->name,
                    'action' => $actions
                ];
            }

            return response([
                'success' => true,
                'menus' => $menus,
            ]);
        } catch (\Exception $ex){
            Log::channel('menu_access')->error($ex);

            return response([
                'success' => false,
            ]);
        }
    }

    public function setPermission(Request $request){
        try {
            $hierarchy = Hierarchy::find($request->input('hierarchy_id'));

            $hierarchy->syncPermissions($request->input('permission'));

            Session::flash('success', 'Successfully updated menu access privileges!');
            return \redirect()->back();
        } catch (\Exception $ex){
            Log::channel('menu_access')->error($ex);

            return \redirect()->back()->withErrors([
                'Terjadi kesalahan di dalam server = '. $ex->getMessage()
                // 'Terjadi kesalahan di dalam server!'
            ]);
        }
    }
}
