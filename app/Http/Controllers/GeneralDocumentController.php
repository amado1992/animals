<?php

namespace App\Http\Controllers;

use App\Helpers\FileSizeHelper;
use App\Models\GeneralDocument;
use App\Models\ItemDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = Storage::allFiles('public/general_docs/');

        return view('general_documents.index', compact('files'));
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

            $type = $file->getMimeType();
            $type = $this->getType($type);

            $path = Storage::putFileAs(
                'public/general_docs', $file, $file_name
            );

            $size = FileSizeHelper::bytesToHuman(Storage::size('public/general_docs/' . $file_name));

            $new_document         = new GeneralDocument();
            $new_document['path'] = $path;
            $new_document['name'] = $file_name;
            $new_document['size'] = $size;
            $new_document['type'] = $type;
            $new_document->save();

            if (!empty($request->parent_id)) {
                $new_item_dashboard                  = new ItemDashboard();
                $new_item_dashboard['itemable_id']   = $new_document->id;
                $new_item_dashboard['itemable_type'] = 'general_document';
                $new_item_dashboard['dashboard_id']  = $request->parent_id;
                $new_item_dashboard->save();

                return redirect()->back();
            }
        }

        return redirect(route('general_documents.index'));
    }

    /**
     * Upload file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addDashboardDocument(Request $request)
    {
        if (!empty($request->url_document && Storage::disk('public')->exists($request->url_document))) {
            //$file = Storage::disk('public')->get($request->url_document);
            $file_name = basename($request->url_document);
            $size      = Storage::disk('public')->size($request->url_document);
            $type      = Storage::disk('public')->mimeType($request->url_document);

            $new_document         = new GeneralDocument();
            $new_document['path'] = 'public' . $request->url_document;
            $new_document['name'] = $file_name;
            $new_document['size'] = $this->convertBystes($size);
            $new_document['type'] = $type;
            $new_document->save();

            if (!empty($request->parent_id)) {
                $new_item_dashboard                  = new ItemDashboard();
                $new_item_dashboard['itemable_id']   = $new_document->id;
                $new_item_dashboard['itemable_type'] = 'general_document';
                $new_item_dashboard['dashboard_id']  = $request->parent_id;
                $new_item_dashboard->save();
            }

            return response()->json(['error' => false, 'message' => 'The attachment was added correctly to the dashboard']);
        } else {
            return response()->json(['error' => true, 'message' => 'The attachment was not added correctly to the dashboard']);
        }
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
                Storage::delete('public/general_docs/' . $file_name);
            }
        }

        return response()->json();
    }

    public function getType($type)
    {
        if ($type == 'application/pdf') {
            return 'pdf';
        } elseif ($type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            return 'doc';
        } elseif ($type == 'application/octet-stream') {
            return 'xls';
        } elseif ($type == 'image/png') {
            return 'png';
        } elseif ($type == 'image/jpg') {
            return 'jpg';
        } else {
            return $type;
        }
    }

    public function convertBystes($bytes)
    {
        if ($bytes > 0) {
            $i     = floor(log($bytes) / log(1024));
            $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return sprintf('%.02F', round($bytes / pow(1024, $i), 1)) * 1 . ' ' . @$sizes[$i];
        } else {
            return 0;
        }
    }
}
