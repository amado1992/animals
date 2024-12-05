<?php

namespace App\Http\Controllers;

use App\Http\Requests\DomainNameCreateRequest;
use App\Http\Requests\DomainNameUpdateRequest;
use App\Models\DomainNameLink;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DomainNameLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domain_names = DomainNameLink::orderByDesc('updated_at');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('domain_name.filter')) {
            $request = session('domain_name.filter');

            if (isset($request['domain_name'])) {
                $domain_names->where('domain_name', 'like', '%' . $request['domain_name'] . '%');

                $filterData = Arr::add($filterData, 'domain_name', 'Domain Name: ' . $request['domain_name']);
            }

            if (isset($request['canonical_name'])) {
                $domain_names->where('canonical_name', 'like', '%' . $request['canonical_name'] . '%');

                $filterData = Arr::add($filterData, 'canonical_name', 'Canonical Name: ' . $request['canonical_name']);
            }
        }

        $domain_names = $domain_names->paginate(20);

        return view('domain_name.index', compact('domain_names', 'filterData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('domain_name.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomainNameCreateRequest $request)
    {
        DomainNameLink::create($request->all());

        return redirect(route('domain-name-link.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $domain_name             = DomainNameLink::findOrFail($id);
        $domain_name->created_at = date('Y-m-d', strtotime($domain_name->created_at));

        return view('domain_name.edit', compact('domain_name'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DomainNameUpdateRequest $request, $id)
    {
        $domain_name = DomainNameLink::findOrFail($id);
        $domain_name->update($request->all());

        return redirect(route('domain-name-link.index'));
    }

    //Filter mailings
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['domain_name.filter' => $request->query()]);

        return redirect(route('domain-name-link.index'));
    }

    /**
     * Remove from mailing session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromDomainNameSession($key)
    {
        $query = session('domain_name.filter');
        Arr::forget($query, $key);
        session(['domain_name.filter' => $query]);

        return redirect(route('domain-name-link.index'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('domain-name-link.filter');

        return redirect(route('domain-name-link.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $domain_name = DomainNameLink::findOrFail($id);
                $domain_name->delete();
            }
        }

        return response()->json();
    }
}
