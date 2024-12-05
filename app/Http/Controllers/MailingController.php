<?php

namespace App\Http\Controllers;

use App\Http\Requests\MailingCreateRequest;
use App\Http\Requests\MailingUpdateRequest;
use App\Models\Mailing;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class MailingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mailings = Mailing::orderByDesc('date_sent_out');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('mailing.filter')) {
            $request = session('mailing.filter');

            if (isset($request['subject'])) {
                $mailings->where('subject', 'like', '%' . $request['subject'] . '%');

                $filterData = Arr::add($filterData, 'subject', 'Subject: ' . $request['subject']);
            }

            if (isset($request['filter_created_at_from'])) {
                $mailings->whereDate('date_created', '>=', $request['filter_created_at_from']);

                $filterData = Arr::add($filterData, 'filter_created_at_from', 'Created start at: ' . $request['filter_created_at_from']);
            }

            if (isset($request['filter_created_at_to'])) {
                $mailings->whereDate('date_created', '<=', $request['filter_created_at_to']);

                $filterData = Arr::add($filterData, 'filter_created_at_to', 'Created end at: ' . $request['filter_created_at_to']);
            }

            if (isset($request['filter_sent_at_from'])) {
                $mailings->whereDate('date_sent_out', '>=', $request['filter_sent_at_from']);

                $filterData = Arr::add($filterData, 'filter_sent_at_from', 'Date sent start at: ' . $request['filter_sent_at_from']);
            }

            if (isset($request['filter_sent_at_to'])) {
                $mailings->whereDate('date_sent_out', '<=', $request['filter_sent_at_to']);

                $filterData = Arr::add($filterData, 'filter_sent_at_to', 'Date sent end at: ' . $request['filter_sent_at_to']);
            }

            if (isset($request['remarks'])) {
                $mailings->where('remarks', 'like', '%' . $request['remarks'] . '%');

                $filterData = Arr::add($filterData, 'remarks', 'Remark: ' . $request['remarks']);
            }
        }

        $mailings = $mailings->paginate(20);

        return view('mailings.index', compact('mailings', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('mailing.filter');

        return redirect(route('mailings.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('mailings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MailingCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MailingCreateRequest $request)
    {
        if ($request->hasFile('relatedFile')) {
            $file = $request->file('relatedFile');

            //File Name
            $file_name                   = $file->getClientOriginalName();
            $request['mailing_template'] = $file_name;

            //File Extension. Only for information.
            $file_extension = $file->getClientOriginalExtension();

            //File Real Path. Only for information.
            $file_real_path = $file->getRealPath();

            //File Size. Only for information.
            $file_size = $file->getSize();

            //File Mime Type. Only for information.
            $file_mime_type = $file->getMimeType();

            $path = Storage::putFileAs(
                'public/mailings', $file, $file_name
            );
        }

        Mailing::create($request->all());

        return redirect(route('mailings.index'));
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
        $mailing = Mailing::findOrFail($id);

        $mailing->date_created  = date('Y-m-d', strtotime($mailing->date_created));
        $mailing->date_sent_out = ($mailing->date_sent_out != null) ? date('Y-m-d', strtotime($mailing->date_sent_out)) : null;

        return view('mailings.edit', compact('mailing'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\MailingUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MailingUpdateRequest $request, $id)
    {
        $mailingUpdate = Mailing::findOrFail($id);

        if ($request->hasFile('relatedFile')) {
            $file = $request->file('relatedFile');

            //File Name
            $oldRelatedFile              = $mailingUpdate->relatedFile;
            $file_name                   = $file->getClientOriginalName();
            $request['mailing_template'] = $file_name;

            if ($oldRelatedFile != null) {
                Storage::delete('public/mailings/' . $oldRelatedFile);
            }

            $path = Storage::putFileAs(
                'public/mailings', $file, $file_name
            );
        }

        $mailingUpdate->update($request->all());

        return redirect(route('mailings.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mailingDelete = Mailing::findOrFail($id);

        if ($mailingDelete->mailing_template != null) {
            Storage::delete('public/mailings/' . $mailingDelete->mailing_template);
        }

        $mailingDelete->delete();

        return redirect(route('mailings.index'));
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
                $mailingDelete = Mailing::findOrFail($id);

                if ($mailingDelete->mailing_template != null) {
                    Storage::delete('public/mailings/' . $mailingDelete->mailing_template);
                }

                $mailingDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter mailings
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['mailing.filter' => $request->query()]);

        return redirect(route('mailings.index'));
    }

    /**
     * Remove from mailing session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromMailingSession($key)
    {
        $query = session('mailing.filter');
        Arr::forget($query, $key);
        session(['mailing.filter' => $query]);

        return redirect(route('mailings.index'));
    }
}
