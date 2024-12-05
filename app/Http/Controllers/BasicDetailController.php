<?php

namespace App\Http\Controllers;

use App\Models\InterestingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BasicDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sitesCodes = InterestingWebsite::where('only_for_john', true)
            ->where(function ($query) {
                $query->where('siteUrl', 'like', '%zoo-services%')
                    ->orWhere('siteUrl', 'like', '%portal.office%');
            })
            ->orderBy('siteName')
            ->get();

        $gmailCodes = InterestingWebsite::where('loginUsername', 'like', '%gmail%')->orderBy('siteName')->get();

        $letterheadFiles = Storage::files('public/basic_details/letterhead');

        $signatureStampFiles = Storage::files('public/basic_details/signature_stamp');

        $invoicePaperFiles = Storage::files('public/basic_details/invoice_paper');

        return view('basic_details.index', compact('sitesCodes', 'gmailCodes', 'letterheadFiles', 'signatureStampFiles', 'invoicePaperFiles'));
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
