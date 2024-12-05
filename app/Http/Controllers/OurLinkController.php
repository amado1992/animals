<?php

namespace App\Http\Controllers;

use App\Http\Requests\OurLinkCreateRequest;
use App\Http\Requests\OurLinkUpdateRequest;
use App\Models\InterestingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

class OurLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ourLinks = InterestingWebsite::where('siteCategory', 'our-links')->orderBy('siteName');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('our_link.filter')) {
            $request = session('our_link.filter');

            if (isset($request['filter_siteName'])) {
                $ourLinks->where('siteName', 'like', '%' . $request['filter_siteName'] . '%');

                $filterData = Arr::add($filterData, 'filter_siteName', 'Site name: ' . $request['filter_siteName']);
            }

            if (isset($request['filter_siteUrl'])) {
                $ourLinks->where('siteUrl', 'like', '%' . $request['filter_siteUrl'] . '%');

                $filterData = Arr::add($filterData, 'filter_siteUrl', 'Site url: ' . $request['filter_siteUrl']);
            }

            if (isset($request['filter_siteRemarks'])) {
                $ourLinks->where('siteRemarks', 'like', '%' . $request['filter_siteRemarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_siteRemarks', 'Site remarks: ' . $request['filter_siteRemarks']);
            }

            if (isset($request['filter_loginUsername'])) {
                $ourLinks->where('loginUsername', 'like', '%' . $request['filter_loginUsername'] . '%');

                $filterData = Arr::add($filterData, 'filter_loginUsername', 'Login username: ' . $request['filter_loginUsername']);
            }

            if (isset($request['filter_loginPassword'])) {
                $ourLinks->where('loginPassword', 'like', '%' . $request['filter_loginPassword'] . '%');

                $filterData = Arr::add($filterData, 'filter_loginPassword', 'Login password: ' . $request['filter_loginPassword']);
            }
        }

        $ourLinks = $ourLinks->get();

        return view('our_links.index', compact('ourLinks', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('our_link.filter');

        return redirect(route('our-links.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('our_links.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OurLinkCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OurLinkCreateRequest $request)
    {
        if ($request->has('loginPassword')) {
            $request['loginPassword'] = Crypt::encryptString($request->loginPassword);
        }

        $request['siteCategory']  = 'our-links';
        $request['only_for_john'] = false;
        InterestingWebsite::create($request->all());

        return redirect(route('our-links.index'));
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
        $ourLink = InterestingWebsite::findOrFail($id);

        return view('our_links.edit', compact('ourLink'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OurLinkUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OurLinkUpdateRequest $request, $id)
    {
        $ourLinkUpdate = InterestingWebsite::findOrFail($id);

        if ($request->has('loginPassword') && $request->loginPassword != $ourLinkUpdate->loginPassword) {
            $request['loginPassword'] = Crypt::encryptString($request->loginPassword);
        }

        $ourLinkUpdate->update($request->all());

        return redirect(route('our-links.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ourLinkDelete = InterestingWebsite::findOrFail($id);
        $ourLinkDelete->delete();

        return redirect(route('our-links.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $ourLinkDelete = InterestingWebsite::findOrFail($id);
                $ourLinkDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter our links
    public function filter(Request $request)
    {
        // Set session our_link filter
        session(['our_link.filter' => $request->query()]);

        return redirect(route('our-links.index'));
    }

    /**
     * Remove from our_link session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromOurLinkSession($key)
    {
        $query = session('our_link.filter');
        Arr::forget($query, $key);
        session(['our_link.filter' => $query]);

        return redirect(route('our-links.index'));
    }
}
