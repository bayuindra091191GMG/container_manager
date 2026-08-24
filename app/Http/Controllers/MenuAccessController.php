<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuAccessController extends Controller
{
    private function permissionGroups(Role $role): array
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => Str::before($permission->name, '.'))
            ->map(function ($permissions, $code) use ($role) {
                return [
                    'id' => $code,
                    'name' => Str::headline($code),
                    'action' => $permissions->map(fn (Permission $permission) => [
                        'code' => $permission->name,
                        'name' => Str::headline(Str::after($permission->name, '.')),
                        'checked' => $role->hasPermissionTo($permission->name),
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function index()
    {
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.menu_access.index', [
            'roles' => $roles,
            'menus' => [],
        ]);
    }

    public function getPermission(Request $request)
    {
        try {
            $role = Role::where('guard_name', 'web')
                ->findOrFail($request->integer('id'));

            return response([
                'success' => true,
                'menus' => $this->permissionGroups($role),
            ]);
        } catch (\Exception $ex) {
            Log::channel('menu_access')->error($ex);

            return response([
                'success' => false,
            ]);
        }
    }

    public function setPermission(Request $request)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'permission' => ['nullable', 'array'],
            'permission.*' => ['string', 'exists:permissions,name'],
        ]);

        try {
            $role = Role::where('guard_name', 'web')
                ->findOrFail($validated['role_id']);
            $role->syncPermissions($validated['permission'] ?? []);

            Session::flash('success', 'Successfully updated role permissions!');

            return redirect()->back();
        } catch (\Exception $ex) {
            Log::channel('menu_access')->error($ex);

            return redirect()->back()->withErrors([
                'Terjadi kesalahan di dalam server = '.$ex->getMessage(),
            ]);
        }
    }
}
