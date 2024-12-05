<?php

namespace App\Http\Controllers;

use App\Exports\ZooAssociationsExport;
use App\Http\Requests\ZooAssociationCreateRequest;
use App\Http\Requests\ZooAssociationUpdateRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\ZooAssociation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class ZooAssociationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zooAssociations = ZooAssociation::select('*');

        $roles = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $users = User::whereRoleIs(Arr::pluck($roles, 'name'))->orderBy('name')->pluck('name', 'id');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('zoo_association.filter')) {
            $request = session('zoo_association.filter');

            if (isset($request['name'])) {
                $zooAssociations->where('name', 'like', '%' . $request['name'] . '%');

                $filterData = Arr::add($filterData, 'name', 'Name: ' . $request['name']);
            }

            if (isset($request['website'])) {
                $zooAssociations->where('website', 'like', '%' . $request['website'] . '%');

                $filterData = Arr::add($filterData, 'website', 'Website: ' . $request['website']);
            }

            if ($request['status'] != 'any') {
                $zooAssociations->where('status', $request['status']);

                $filterData = Arr::add($filterData, 'status', 'Status: ' . $request['status']);
            }

            if (isset($request['started_date'])) {
                $zooAssociations->where('started_date', '>=', $request['started_date']);

                $filterData = Arr::add($filterData, 'started_date', 'Start date: ' . $request['started_date']);
            }

            if (isset($request['checked_date'])) {
                $zooAssociations->where('checked_date', '>=', $request['checked_date']);

                $filterData = Arr::add($filterData, 'checked_date', 'Checked date: ' . $request['checked_date']);
            }

            if (isset($request['user_id'])) {
                $filterUser = User::where('id', $request['user_id'])->first();

                $zooAssociations->where('user_id', $filterUser->id);

                $filterData = Arr::add($filterData, 'user_id', 'User: ' . $filterUser->name);
            }
        }

        $zooAssociations = $zooAssociations->get();

        return view('zoo_associations.index', compact('zooAssociations', 'users', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('zoo_association.filter');

        return redirect(route('zoo-associations.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $users = User::whereRoleIs(Arr::pluck($roles, 'name'))->orderBy('name')->pluck('name', 'id');

        return view('zoo_associations.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ZooAssociationCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ZooAssociationCreateRequest $request)
    {
        ZooAssociation::create($request->all());

        return redirect(route('zoo-associations.index'));
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
        $zooAssociation               = ZooAssociation::findOrFail($id);
        $zooAssociation->started_date = date('Y-m-d', strtotime($zooAssociation->started_date));
        $zooAssociation->checked_date = date('Y-m-d', strtotime($zooAssociation->checked_date));

        $roles = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $users = User::whereRoleIs(Arr::pluck($roles, 'name'))->orderBy('name')->pluck('name', 'id');

        return view('zoo_associations.edit', compact('zooAssociation', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ZooAssociationUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ZooAssociationUpdateRequest $request, $id)
    {
        $updateItem = ZooAssociation::findOrFail($id);
        $updateItem->update($request->all());

        return redirect(route('zoo-associations.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteItem = ZooAssociation::findOrFail($id);
        $deleteItem->delete();

        return redirect(route('zoo-associations.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $deleteItems = ZooAssociation::findOrFail($id);
                $deleteItems->delete();
            }
        }

        return response()->json();
    }

    //Filter zoo associations
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['zoo_association.filter' => $request->query()]);

        return redirect(route('zoo-associations.index'));
    }

    /**
     * Remove from zoo_association session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromZooAssociationSession($key)
    {
        $query = session('zoo_association.filter');
        Arr::forget($query, $key);
        session(['zoo_association.filter' => $query]);

        return redirect(route('zoo-associations.index'));
    }

    //Export excel document with zoo associations
    public function export()
    {
        return Excel::download(new ZooAssociationsExport, 'zoo_associations.xlsx');
    }
}
