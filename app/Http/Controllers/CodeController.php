<?php

namespace App\Http\Controllers;

use App\Enums\ConfirmOptions;
use App\Exports\CodesExport;
use App\Http\Requests\CodeCreateRequest;
use App\Http\Requests\CodeUpdateRequest;
use App\Models\InterestingWebsite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $codes = InterestingWebsite::where('siteCategory', 'other-credentials')->orderBy('siteName');

        $confirm_options = ConfirmOptions::get();

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('code.filter')) {
            $request = session('code.filter');

            if (isset($request['filter_siteName'])) {
                $codes->where('siteName', 'like', '%' . $request['filter_siteName'] . '%');

                $filterData = Arr::add($filterData, 'filter_siteName', 'Site name: ' . $request['filter_siteName']);
            }

            if (isset($request['filter_siteUrl'])) {
                $codes->where('siteUrl', 'like', '%' . $request['filter_siteUrl'] . '%');

                $filterData = Arr::add($filterData, 'filter_siteUrl', 'Site url: ' . $request['filter_siteUrl']);
            }

            if (isset($request['filter_siteRemarks'])) {
                $codes->where('siteRemarks', 'like', '%' . $request['filter_siteRemarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_siteRemarks', 'Site remarks: ' . $request['filter_siteRemarks']);
            }

            if (isset($request['filter_loginUsername'])) {
                $codes->where('loginUsername', 'like', '%' . $request['filter_loginUsername'] . '%');

                $filterData = Arr::add($filterData, 'filter_loginUsername', 'Login username: ' . $request['filter_loginUsername']);
            }

            if (isset($request['filter_loginPassword'])) {
                $codes->where('loginPassword', 'like', '%' . $request['filter_loginPassword'] . '%');

                $filterData = Arr::add($filterData, 'filter_loginPassword', 'Login password: ' . $request['filter_loginPassword']);
            }

            if (isset($request['filter_onlyForJohn'])) {
                $codes->where('only_for_john', ($request['filter_onlyForJohn'] == 'yes') ? true : false);

                $filterData = Arr::add($filterData, 'filter_onlyForJohn', 'Only for John: ' . $request['filter_onlyForJohn']);
            }
        }

        $codes = $codes->get();

        return view('codes.index', compact('codes', 'confirm_options', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('code.filter');

        return redirect(route('codes.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('codes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CodeCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CodeCreateRequest $request)
    {
        if ($request->has('loginPassword')) {
            $request['loginPassword'] = Crypt::encryptString($request->loginPassword);
        }

        $request['siteCategory']  = 'other-credentials';
        $request['only_for_john'] = $request->input('only_for_john') ? true : false;
        InterestingWebsite::create($request->all());

        return redirect(route('codes.index'));
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
        $code = InterestingWebsite::findOrFail($id);

        return view('codes.edit', compact('code'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CodeUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CodeUpdateRequest $request, $id)
    {
        $codeUpdate = InterestingWebsite::findOrFail($id);

        if ($request->has('loginPassword') && $request->loginPassword != $codeUpdate->loginPassword) {
            $request['loginPassword'] = Crypt::encryptString($request->loginPassword);
        }

        $request['only_for_john'] = $request->input('only_for_john') ? true : false;
        $codeUpdate->update($request->all());

        return redirect(route('codes.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $codeDelete = InterestingWebsite::findOrFail($id);
        $codeDelete->delete();

        return redirect(route('codes.index'));
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
                $codeDelete = InterestingWebsite::findOrFail($id);
                $codeDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter codes
    public function filter(Request $request)
    {
        // Set session code filter
        session(['code.filter' => $request->query()]);

        return redirect(route('codes.index'));
    }

    /**
     * Remove from code session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromCodeSession($key)
    {
        $query = session('code.filter');
        Arr::forget($query, $key);
        session(['code.filter' => $query]);

        return redirect(route('codes.index'));
    }

    //Export excel document with codes info.
    public function export(Request $request)
    {
        $file_name = 'Codes list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $export = new CodesExport(InterestingWebsite::whereIn('id', explode(',', $request->items))->orderBy('siteName')->get());

        return Excel::download($export, $file_name);
    }
}
