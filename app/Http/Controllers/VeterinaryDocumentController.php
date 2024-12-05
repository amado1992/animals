<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VeterinaryDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = Storage::allFiles('public/veterinary_docs/');

        $offers = Offer::orderByDesc(DB::raw('YEAR(created_at)'))
            ->orderByDesc('offer_number')
            ->get();

        return view('veterinary_documents.index', compact('files', 'offers'));
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
                'public/veterinary_docs/', $file, $file_name
            );
        }

        return redirect(route('veterinary_documents.index'));
    }

    /**
     * Delete files.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_files(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $file_name) {
                Storage::delete('public/veterinary_docs/' . $file_name);
            }
        }

        return response()->json();
    }
}
