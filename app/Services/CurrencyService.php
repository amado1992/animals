<?php

namespace App\Services;

//use Http;
use App\Models\CurrencyRate;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    /**
     * Retrieve latest rates from the api
     *
     * @return void
     */
    public function loadLatestRates()
    {
        $response = Http::get('http://api.exchangeratesapi.io/latest', [
            'symbols'    => 'USD,GBP,CAD',
            'access_key' => env('EXCHANGERATESAPI_ACCESS_KEY'),
        ]);

        return $this->responseJsonToArray($response);
    }

    /**
     * Retrieve rates for specific date from the api
     *
     * @return void
     */
    public function loadRatesForDate($date)
    {
        $response = Http::get('http://api.exchangeratesapi.io/' . $date->format('Y-m-d'), [
            'symbols'    => 'USD,GBP,CAD',
            'access_key' => env('EXCHANGERATESAPI_ACCESS_KEY'),
        ]);

        return $this->responseJsonToArray($response);
    }

    /**
     * Process json response to array
     *
     * @return array
     */
    private function responseJsonToArray($response)
    {
        if ($response->failed()) {
            $response->throw();
        }

        return (array) json_decode($response->body());
    }

    /**
     * Save loaded rates to the database
     *
     * @return void
     */
    public function saveRatesToDatabase($date, $rates, bool $overwrite = false)
    {
        if ($this->dateHasRates($date)) {
            if ($overwrite === false) {
                throw new \Exception('Rates are already set for this date');
            }

            $this->deleteRatesFromDate($date);
        }

        CurrencyRate::create([
            'date'    => $date,
            'EUR_USD' => $rates->USD,
            'EUR_GBP' => $rates->GBP,
            'EUR_CAD' => $rates->CAD,
            'USD_EUR' => (1 / $rates->USD),
            'USD_GBP' => (1 / $rates->USD * $rates->GBP),
            'USD_CAD' => (1 / $rates->USD * $rates->CAD),
            'GBP_EUR' => (1 / $rates->GBP),
            'GBP_USD' => (1 / $rates->GBP * $rates->USD),
            'GBP_CAD' => (1 / $rates->GBP * $rates->CAD),
            'CAD_EUR' => (1 / $rates->CAD),
            'CAD_USD' => (1 / $rates->CAD * $rates->USD),
            'CAD_GBP' => (1 / $rates->CAD * $rates->GBP),
        ]);
    }

    /**
     * Delete rates from given date
     *
     * @return void
     */
    private function deleteRatesFromDate($date)
    {
        $rates = CurrencyRate::where('date', $date)->firstOrFail();
        $rates->delete();
    }

    /**
     * Check if a given date has rates
     *
     * @param date
     * @return bool
     */
    public function dateHasRates($date): bool
    {
        if (CurrencyRate::where('date', $date)->exists()) {
            return true;
        }

        return false;
    }
}
