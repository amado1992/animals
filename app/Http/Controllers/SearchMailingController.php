<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchMailingCreateRequest;
use App\Http\Requests\SearchMailingUpdateRequest;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Country;
use App\Models\SearchMailing;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SearchMailingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mailings = SearchMailing::orderByDesc('date_sent_out');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('search_mailing.filter')) {
            $request = session('search_mailing.filter');

            if (isset($request['filter_animal_id'])) {
                $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                $mailings->where('animal_id', $filterAnimal->id);

                $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
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

        $areas      = AreaRegion::orderBy('name')->pluck('name', 'id');
        $to_country = Country::orderBy('name')->pluck('name', 'id');

        return view('search_mailings.index', compact('mailings', 'filterData', 'areas', 'to_country'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('search_mailing.filter');

        return redirect(route('search-mailings.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('search_mailings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect(route('search-mailings.index'));
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
        $mailing = SearchMailing::findOrFail($id);

        $mailing->date_sent_out    = date('Y-m-d', strtotime($mailing->date_sent_out));
        $mailing->next_reminder_at = ($mailing->next_reminder_at != null) ? date('Y-m-d', strtotime($mailing->next_reminder_at)) : null;

        return view('search_mailings.edit', compact('mailing'));
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
        $mailingUpdate = SearchMailing::findOrFail($id);

        $mailingUpdate->update($request->all());

        return redirect(route('search-mailings.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mailingDelete = SearchMailing::findOrFail($id);
        $mailingDelete->delete();

        return redirect(route('search-mailings.index'));
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
                $mailingDelete = SearchMailing::findOrFail($id);
                $mailingDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter search mailings
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['search_mailing.filter' => $request->query()]);

        return redirect(route('search-mailings.index'));
    }

    /**
     * Remove from search mailing session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromSearchMailingSession($key)
    {
        $query = session('search_mailing.filter');
        Arr::forget($query, $key);
        session(['search_mailing.filter' => $query]);

        return redirect(route('search-mailings.index'));
    }
}
