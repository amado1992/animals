<?php

namespace App\Http\Controllers;

use App\Models\StdText;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class WebsiteTextPictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $websiteTexts = StdText::where('category', 'website')->orderByDesc('updated_at');

        $selectedWebsiteTab = '#websiteTextsTab';

        $files = Storage::allFiles('public/website_images/');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('website_text.filter')) {
            $request = session('website_text.filter');

            if (isset($request['websiteTab'])) {
                $selectedWebsiteTab = $request['websiteTab'];
            }

            if (isset($request['remarks'])) {
                $websiteTexts->where('remarks', 'like', '%' . $request['remarks'] . '%');

                $filterData = Arr::add($filterData, 'remarks', 'Remarks: ' . $request['remarks']);
            }

            if (isset($request['english_text'])) {
                $websiteTexts->where('english_text', 'like', '%' . $request['english_text'] . '%');

                $filterData = Arr::add($filterData, 'english_text', 'English text: ' . $request['english_text']);
            }

            if (isset($request['spanish_text'])) {
                $websiteTexts->where('spanish_text', 'like', '%' . $request['spanish_text'] . '%');

                $filterData = Arr::add($filterData, 'spanish_text', 'Spanish text: ' . $request['spanish_text']);
            }
        }

        $websiteTexts = $websiteTexts->paginate(10);

        return view('website_texts_pictures.index', compact(
            'websiteTexts',
            'selectedWebsiteTab',
            'filterData',
            'files'
        ));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('website_text.filter');

        return redirect(route('website-texts.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('website_texts_pictures.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['category'] = 'website';
        StdText::create($request->all());

        return redirect(route('website-texts.index'));
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
        $websiteText = StdText::findOrFail($id);

        return view('website_texts_pictures.edit', compact('websiteText'));
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
        $websiteTextUpdate = StdText::findOrFail($id);
        $websiteTextUpdate->update($request->all());

        return redirect(route('website-texts.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $websiteTextDelete = StdText::findOrFail($id);
        $websiteTextDelete->delete();

        return redirect(route('website-texts.index'));
    }

    /**
     * Remove the selected website texts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteWebsiteTexts(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $websiteTextDelete = StdText::findOrFail($id);
                $websiteTextDelete->delete();
            }
        }

        return response()->json();
    }

    /**
     * Remove the selected website images.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteWebsiteImages(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $file_name) {
                Storage::delete('public/website_images/' . $file_name);
            }
        }

        return response()->json();
    }

    //Filter std text
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['website_text.filter' => $request->query()]);

        return redirect(route('website-texts.index'));
    }

    /**
     * Remove from std_text session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromWebsiteTextSession($key)
    {
        $query = session('website_text.filter');
        Arr::forget($query, $key);
        session(['website_text.filter' => $query]);

        return redirect(route('website-texts.index'));
    }

    /**
     * Save website selected tab in session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectedWebsiteTab(Request $request)
    {
        $query               = session('website_text.filter');
        $query['websiteTab'] = $request->websiteTab;
        session(['website_text.filter' => $query]);

        return response()->json();
    }

    /**
     * Upload file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload_file(Request $request)
    {
        if ($request->hasFile('file_to_upload')) {
            $file = $request->file('file_to_upload');

            //File Name
            $file_name = $file->getClientOriginalName();

            $path = Storage::putFileAs(
                'public/website_images/', $file, $file_name
            );
        }

        return redirect(route('website-texts.index'));
    }
}
