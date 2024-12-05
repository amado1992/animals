<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $families = Classification::where('rank', 'family')->orderBy('common_name')->get();

        return view('classifications.index', compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('classifications.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Classification::create($request->all());

        return redirect(route('classifications.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Classification  $classification
     * @return \Illuminate\Http\Response
     */
    public function show(Classification $classification)
    {
        dump($classification->above->toArray());
        dump($classification->under->toArray());
        dd(1);

        return redirect(route('classifications.edit', [$classification->id]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Classification  $classification
     * @return \Illuminate\Http\Response
     */
    public function edit(Classification $classification)
    {
        return view('classifications.edit', compact('classification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Classification  $classification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classification $classification)
    {
        $classification->update($request->all());

        return redirect(route('classifications.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Classification  $classification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classification $classification)
    {
        $classification->delete();

        return redirect(route('classifications.index'));
    }

    public function getOrdersByClass(Request $request)
    {
        $classCode = '';
        $class     = Classification::where('rank', 'class')->where('id', $request->value)->first();
        if (!is_null($class)) {
            $classCode = $class->code;
        }

        $orders = Classification::where('rank', 'order')->where('belongs_to', $request->value)->orderBy('common_name')->pluck('id', 'common_name');
        //$orders = array_values($orders->toArray());
        return response()->json(['code_number_temp' => $classCode, 'orders' => $orders]);
    }

    public function getFamiliesByOrder(Request $request)
    {
        $code_number_temp = '';
        $orderCode        = '';
        $order            = Classification::where('rank', 'order')->where('id', $request->value)->first();
        if (!is_null($order)) {
            $orderCode        = $order->code;
            $code_number_temp = $order->above->code . $orderCode;
        }

        $families = Classification::where('rank', 'family')->where('belongs_to', $request->value)->orderBy('common_name')->pluck('id', 'common_name');
        //$families = array_values($families->toArray());
        return response()->json(['code_number_temp' => $code_number_temp, 'families' => $families]);
    }

    public function getGenusByFamily(Request $request)
    {
        $code_number_temp = '';
        $familyCode       = '';
        $family           = Classification::where('rank', 'family')->where('id', $request->value)->first();
        if (!is_null($family)) {
            $order            = $family->above;
            $familyCode       = $family->code;
            $code_number_temp = $order->above->code . $order->code . $familyCode;
        }

        $genuss = Classification::where('rank', 'genus')->where('belongs_to', $request->value)->orderBy('common_name')->pluck('id', 'common_name');
        //$genuss = array_values($genuss->toArray());
        return response()->json(['code_number_temp' => $code_number_temp, 'genuss' => $genuss]);
    }

    public function getGenusCode(Request $request)
    {
        $code_number_temp = '';
        $genusCode        = '';
        $genus            = Classification::where('rank', 'genus')->where('id', $request->value)->first();
        if (!is_null($genus)) {
            $family           = $genus->above;
            $order            = $family->above;
            $genusCode        = $genus->code;
            $code_number_temp = $order->above->code . $order->code . $family->code . $genusCode;
        }

        return response()->json(['code_number_temp' => $code_number_temp]);
    }

    public function getClassificationById(Request $request)
    {
        $classification = Classification::where('id', $request->id)->first();

        return response()->json(['classification' => $classification]);
    }

    public function saveOrEditClass(Request $request)
    {
        $classification_code = Classification::where('rank', $request->rank)->where('code', $request->code)->first();

        if ($request->classificationId == 0) {
            if ($classification_code) {
                return response()->json(['success' => false, 'message' => 'The ' . $request->rank . ' code already exist.']);
            } else {
                $new_classification                      = new Classification();
                $new_classification->common_name         = $request->commonName;
                $new_classification->common_name_slug    = Str::slug($request->commonName, '_');
                $new_classification->common_name_spanish = $request->spanishCommonName;
                $new_classification->scientific_name     = $request->scientificName;
                $new_classification->code                = $request->code;
                $new_classification->rank                = $request->rank;
                $new_classification->belongs_to          = $request->belongsTo;
                $new_classification->save();

                return response()->json(['success' => true, 'newClassificationId' => $new_classification->id]);
            }
        } else {
            $classification = Classification::where('id', $request->classificationId)->first();
            if ($classification) {
                if ($classification_code && $classification->code != $classification_code->code) {
                    return response()->json(['success' => false, 'message' => 'The ' . $request->rank . ' code already exist.']);
                } else {
                    $classification->common_name         = $request->commonName;
                    $classification->common_name_slug    = Str::slug($request->commonName, '_');
                    $classification->common_name_spanish = $request->spanishCommonName;
                    $classification->scientific_name     = $request->scientificName;
                    $classification->code                = $request->code;
                    $classification->update();
                }
            }

            return response()->json(['success' => true]);
        }
    }
}
