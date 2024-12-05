<?php

namespace App\Http\Controllers;

use App\Models\StdText;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StdTextController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stdTexts = StdText::where('category', '<>', 'website')->orderByDesc('updated_at');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('std_text.filter')) {
            $request = session('std_text.filter');

            if (isset($request['category'])) {
                $stdTexts->where('category', $request['category']);

                $filterData = Arr::add($filterData, 'category', 'Category: ' . $request['category']);
            }

            if (isset($request['code'])) {
                $stdTexts->where('code', 'like', '%' . $request['code'] . '%');

                $filterData = Arr::add($filterData, 'code', 'Code: ' . $request['code']);
            }

            if (isset($request['name'])) {
                $stdTexts->where('name', 'like', '%' . $request['name'] . '%');

                $filterData = Arr::add($filterData, 'name', 'Name: ' . $request['name']);
            }

            if (isset($request['remarks'])) {
                $stdTexts->where('remarks', 'like', '%' . $request['remarks'] . '%');

                $filterData = Arr::add($filterData, 'remarks', 'Remarks: ' . $request['remarks']);
            }

            if (isset($request['english_text'])) {
                $stdTexts->where('english_text', 'like', '%' . $request['english_text'] . '%');

                $filterData = Arr::add($filterData, 'english_text', 'English text: ' . $request['english_text']);
            }

            if (isset($request['spanish_text'])) {
                $stdTexts->where('spanish_text', 'like', '%' . $request['spanish_text'] . '%');

                $filterData = Arr::add($filterData, 'spanish_text', 'Spanish text: ' . $request['spanish_text']);
            }
        }

        $stdTexts = $stdTexts->paginate(10);

        $stdTextsCategories = StdText::$enumCategories;

        return view('std_texts.index', compact(
            'stdTexts',
            'stdTextsCategories',
            'filterData'
        ));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('std_text.filter');

        return redirect(route('std-texts.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stdTextsCategories = StdText::$enumCategories;

        return view('std_texts.create', compact('stdTextsCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        StdText::create($request->all());

        return redirect(route('std-texts.index'));
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
        $stdText = StdText::findOrFail($id);

        $stdTextsCategories = StdText::$enumCategories;

        return view('std_texts.edit', compact('stdText', 'stdTextsCategories'));
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
        $stdTextUpdate = StdText::findOrFail($id);
        $stdTextUpdate->update($request->all());

        return redirect(route('std-texts.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stdTextDelete = StdText::findOrFail($id);
        $stdTextDelete->delete();

        return redirect(route('std-texts.index'));
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
                $stdTextDelete = StdText::findOrFail($id);
                $stdTextDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter std text
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['std_text.filter' => $request->query()]);

        return redirect(route('std-texts.index'));
    }

    /**
     * Remove from std_text session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromStdTextSession($key)
    {
        $query = session('std_text.filter');
        Arr::forget($query, $key);
        session(['std_text.filter' => $query]);

        return redirect(route('std-texts.index'));
    }
}
