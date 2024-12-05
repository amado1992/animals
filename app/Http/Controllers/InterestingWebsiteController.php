<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterestingWebsiteCreateRequest;
use App\Http\Requests\InterestingWebsiteUpdateRequest;
use App\Models\InterestingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

class InterestingWebsiteController extends Controller
{
    public $categories;

    public function __construct()
    {
        $this->categories = Arr::where(InterestingWebsite::$enumCategories, function ($value, $key) {
            return $key != 'other-credentials' && $key != 'our-links';
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $interestingWebs = InterestingWebsite::where('siteCategory', '<>', 'other-credentials')
            ->where('siteCategory', '<>', 'our-links');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('interesting_website.filter')) {
            $request = session('interesting_website.filter');

            if (isset($request['siteCategory'])) {
                $interestingWebs->where('siteCategory', $request['siteCategory']);

                $filterData = Arr::add($filterData, 'siteCategory', 'Category: ' . $request['siteCategory']);
            }

            if (isset($request['siteName'])) {
                $interestingWebs->where('siteName', 'like', '%' . $request['siteName'] . '%');

                $filterData = Arr::add($filterData, 'siteName', 'Name: ' . $request['siteName']);
            }

            if (isset($request['siteUrl'])) {
                $interestingWebs->where('siteUrl', 'like', '%' . $request['siteUrl'] . '%');

                $filterData = Arr::add($filterData, 'siteUrl', 'Url: ' . $request['siteUrl']);
            }

            if (isset($request['siteRemarks'])) {
                $interestingWebs->where('siteRemarks', 'like', '%' . $request['siteRemarks'] . '%');

                $filterData = Arr::add($filterData, 'siteRemarks', 'Remarks: ' . $request['siteRemarks']);
            }

            if (isset($request['loginUsername'])) {
                $interestingWebs->where('loginUsername', 'like', '%' . $request['loginUsername'] . '%');

                $filterData = Arr::add($filterData, 'loginUsername', 'Username: ' . $request['loginUsername']);
            }

            if (isset($request['loginPassword'])) {
                $interestingWebs->where('loginPassword', 'like', '%' . $request['loginPassword'] . '%');

                $filterData = Arr::add($filterData, 'loginPassword', 'Password: ' . $request['loginPassword']);
            }
        }

        $interestingWebs = $interestingWebs->get();

        return view('interesting_websites.index', [
            'interestingWebs' => $interestingWebs,
            'filterData'      => $filterData,
            'categories'      => $this->categories,
        ]);
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('interesting_website.filter');

        return redirect(route('interesting-websites.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('interesting_websites.create', ['categories' => $this->categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\InterestingWebsiteCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InterestingWebsiteCreateRequest $request)
    {
        if ($request->has('loginPassword')) {
            $request['loginPassword'] = Crypt::encryptString($request->loginPassword);
        }
        InterestingWebsite::create($request->all());

        return redirect(route('interesting-websites.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InterestingWebsite  $interestingWebsite
     * @return \Illuminate\Http\Response
     */
    public function show(InterestingWebsite $interestingWebsite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InterestingWebsite  $interestingWebsite
     * @return \Illuminate\Http\Response
     */
    public function edit($id/*InterestingWebsite $interestingWebsite*/)
    {
        $interestingWebsite = InterestingWebsite::findOrFail($id);

        $categories = $this->categories;

        return view('interesting_websites.edit', [
            'interestingWebsite' => $interestingWebsite,
            'categories'         => $this->categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\InterestingWebsiteCreateRequest  $request
     * @param  \App\InterestingWebsite  $interestingWebsite
     * @return \Illuminate\Http\Response
     */
    public function update(InterestingWebsiteUpdateRequest $request, $id/*InterestingWebsite $interestingWebsite*/)
    {
        $updateItem = InterestingWebsite::findOrFail($id);

        if ($request->has('loginPassword') && $request->loginPassword != $updateItem->loginPassword) {
            $request['loginPassword'] = Crypt::encryptString($request->loginPassword);
        }

        $updateItem->update($request->all());

        return redirect(route('interesting-websites.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InterestingWebsite  $interestingWebsite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id/*InterestingWebsite $interestingWebsite*/)
    {
        $deleteItem = InterestingWebsite::findOrFail($id);
        $deleteItem->delete();

        return redirect(route('interesting-websites.index'));
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
                $deleteItem = InterestingWebsite::findOrFail($id);
                $deleteItem->delete();
            }
        }

        return response()->json();
    }

    public function filter(Request $request)
    {
        // Set session crate filter
        session(['interesting_website.filter' => $request->query()]);

        return redirect(route('interesting-websites.index'));
    }

    /**
     * Remove from code session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromInterestingWebsiteSession($key)
    {
        $query = session('interesting_website.filter');
        Arr::forget($query, $key);
        session(['interesting_website.filter' => $query]);

        return redirect(route('interesting-websites.index'));
    }
}
