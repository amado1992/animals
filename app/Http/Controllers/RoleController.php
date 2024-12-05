<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\RoleCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleCreateRequest $request)
    {
        $validator = Validator::make($request->toArray(), []);

        $sameRoleName = Role::where('name', $request->name)->first();

        if ($sameRoleName != null) {
            $validator->errors()->add('already_exist', 'A role with the same name already exist.');

            return redirect(route('roles.create'))->withInput($request->toArray())->withErrors($validator);
        } else {
            Role::create($request->all());

            return redirect(route('roles.index'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $validator = Validator::make($request->toArray(), []);

        $sameRoleName = Role::where('name', $request->name)->first();

        if ($sameRoleName != null) {
            $validator->errors()->add('already_exist', 'A role with the same name already exist.');

            return redirect(route('roles.edit', $role))->withInput($request->toArray())->withErrors($validator);
        } else {
            $role->update($request->all());

            return redirect(route('roles.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect(route('roles.index'));
    }
}
