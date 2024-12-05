<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuidelineCreateRequest;
use App\Http\Requests\GuidelineUpdateRequest;
use App\Models\Guideline;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class OfferReservationContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Guideline::where('section', 'offers_reservations_contracts')->orderByDesc('updated_at');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('offer_reservation_contract.filter')) {
            $request = session('offer_reservation_contract.filter');

            if (isset($request['subject'])) {
                $documents->where('subject', 'like', '%' . $request['subject'] . '%');

                $filterData = Arr::add($filterData, 'subject', 'Subject: ' . $request['subject']);
            }

            if (isset($request['remark'])) {
                $documents->where('remark', 'like', '%' . $request['remark'] . '%');

                $filterData = Arr::add($filterData, 'remark', 'Remark: ' . $request['remark']);
            }
        }

        $documents = $documents->paginate(20);

        return view('offers_reservations_contracts.index', compact('documents', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('offer_reservation_contract.filter');

        return redirect(route('offers-reservations-contracts.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('offers_reservations_contracts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\GuidelineCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GuidelineCreateRequest $request)
    {
        $request['section'] = 'offers_reservations_contracts';

        if ($request->hasFile('relatedFile')) {
            $file = $request->file('relatedFile');

            //File Name
            $file_name                   = $file->getClientOriginalName();
            $request['related_filename'] = $file_name;

            //File Extension. Only for information.
            $file_extension = $file->getClientOriginalExtension();

            //File Real Path. Only for information.
            $file_real_path = $file->getRealPath();

            //File Size. Only for information.
            $file_size = $file->getSize();

            //File Mime Type. Only for information.
            $file_mime_type = $file->getMimeType();

            $path = Storage::putFileAs(
                'public/guidelines_docs/offers_reservations_contracts/', $file, $file_name
            );
        }

        Guideline::create($request->all());

        return redirect(route('offers-reservations-contracts.index'));
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
        $document = Guideline::findOrFail($id);

        return view('offers_reservations_contracts.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\GuidelineUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GuidelineUpdateRequest $request, $id)
    {
        $documentUpdate = Guideline::findOrFail($id);

        if ($request->hasFile('relatedFile')) {
            $file = $request->file('relatedFile');

            //File Name
            $oldRelatedFile              = $documentUpdate->relatedFile;
            $file_name                   = $file->getClientOriginalName();
            $request['related_filename'] = $file_name;

            if ($oldRelatedFile != null) {
                Storage::delete('public/guidelines_docs/offers_reservations_contracts/' . $oldRelatedFile);
            }

            $path = Storage::putFileAs(
                'public/guidelines_docs/offers-reservations-contracts/', $file, $file_name
            );
        }

        $documentUpdate->update($request->all());

        return redirect(route('offers-reservations-contracts.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guidelineDelete = Guideline::findOrFail($id);

        if ($guidelineDelete->related_filename != null) {
            Storage::delete('public/guidelines_docs/offers_reservations_contracts/' . $guidelineDelete->related_filename);
        }

        $guidelineDelete->delete();

        return redirect(route('offers-reservations-contracts.index'));
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
                $guidelineDelete = Guideline::findOrFail($id);

                if ($guidelineDelete->related_filename != null) {
                    Storage::delete('public/guidelines_docs/offers_reservations_contracts/' . $guidelineDelete->related_filename);
                }

                $guidelineDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter document
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['offer_reservation_contract.filter' => $request->query()]);

        return redirect(route('offers-reservations-contracts.index'));
    }

    /**
     * Remove from offer_reservation_contract session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromOfferReservationContractSession($key)
    {
        $query = session('offer_reservation_contract.filter');
        Arr::forget($query, $key);
        session(['offer_reservation_contract.filter' => $query]);

        return redirect(route('offers-reservations-contracts.index'));
    }
}
