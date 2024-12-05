<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles       = Role::where('name', '<>', 'admin')->where('name', '<>', 'website-user')->get();
        $permissions = Permission::orderBy('name')->paginate(25);

        return view('permissions.index', compact('permissions', 'roles'));
    }

    //Filter permissions
    public function filterPermissions(Request $request)
    {
        $permissions = Permission::orderBy('name');

        if ($request->has('filter_permission_name')) {
            $permissions->where('name', 'like', '%' . $request->filter_permission_name . '%');
        }

        $permissions = $permissions->paginate(25);

        $roles = Role::where('name', '<>', 'admin')->where('name', '<>', 'website-user')->get();

        return view('permissions.index', compact('permissions', 'roles'));
    }

    //Update role permissions
    public function updateRolePermissions(Request $request)
    {
        $role       = Role::where('id', $request->role_id)->first();
        $permission = Permission::where('id', $request->permission_id)->first();

        $users = User::whereRoleIs($role->name)->get();
        foreach ($users as $user) {
            if ($request->status == true) {
                $role->attachPermission($permission);
                $user->attachPermission($permission);
            } else {
                $role->detachPermission($permission);
                $user->detachPermission($permission);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
