<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Models\CurrencyRate;
use App\Services\CurrencyService;
use Carbon\Carbon;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::get();
        $rates      = CurrencyRate::orderBy('date', 'desc')->take(10)->get();

        return view('currencies.index', compact('currencies', 'rates'));
    }

    /**
     * Reload the latest rates
     *
     * @return \Illuminate\Http\Response
     */
    public function rates()
    {
        $currencyService = new CurrencyService();

        for ($i = 0; $i <= 7; $i++) {
            $result = $currencyService->loadRatesForDate(Carbon::today()->subDays($i));
            $currencyService->saveRatesToDatabase($result['date'], $result['rates'], true);
        }

        return redirect()->back();
    }
}
