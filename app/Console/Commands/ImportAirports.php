<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\Country;
use Illuminate\Console\Command;
use Storage;

class ImportAirports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:import-airports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import airports through JSON file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // if file doesn't exists
        if (!Storage::disk('local')->exists('airports.json')) {
            exit;
        }

        $countries = Country::pluck('id', 'country_code');

        $json_file = Storage::disk('local')->get('airports.json');

        $airports = (array) json_decode($json_file);

        foreach ($airports as $key => $airport) {
            // if iata code is empty, skip the airport (it is very small)
            if ($airport->iata === '' or !$airport->iata) {
                continue;
            }

            // if airport already exists, skip the import
            if (Airport::where('icao_code', $key)->exists()) {
                continue;
            }

            // find the country id, but if you cannot find it, skip this row
            $country_id = $countries->get(strtolower($airport->country));

            if (!$country_id) {
                continue;
            }

            Airport::create([
                'icao_code'  => $airport->icao,
                'iata_code'  => $airport->iata,
                'name'       => $airport->name,
                'city'       => $airport->city,
                'country_id' => $country_id,
                'lat'        => $airport->lat,
                'long'       => $airport->lon,
            ]);
        }
    }
}
