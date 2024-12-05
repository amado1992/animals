<?php

namespace App\Importing;

use App\Models\Animal;
use App\Models\Cites;
use App\Models\Classification;
use App\Models\Crate;
use DateTimeHelper;
use DB;
use Illuminate\Support\Str;

class ImportAnimals
{
    /**
     * Create a new import instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $animals = DB::connection('mysql_old')->select('select * from animal order by id');

        $citesEurope = Cites::where('type', 'europe')->pluck('key', 'key');
        $citesGlobal = Cites::where('type', 'global')->pluck('key', 'key');

        $genuss = Classification::where('rank', 'genus')->orderBy('common_name')->pluck('id', 'common_name');
        $crates = Crate::orderBy('old_id')->pluck('id', 'old_id');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $skipped_records        = collect();

        try {
            DB::beginTransaction();

            foreach ($animals as $animal) {
                $iata_code        = null;
                $iata_code_letter = null;

                // check if iata is empty, second check if iata contains a -, third check if iata is just a number
                if (!$animal->iata || trim($animal->iata) == '') {
                    $iata_code        = null;
                    $iata_code_letter = null;
                } elseif (strpos($animal->iata, '-')) {
                    $iata             = explode('-', $animal->iata);
                    $iata_code        = isset($iata[0]) && is_int($iata[0]) ? $iata[0] : null;
                    $iata_code_letter = isset($iata[1]) && strlen($iata[1]) == 1 ? $iata[1] : null;
                } elseif (is_int($animal->iata)) {
                    $iata_code        = $animal->iata;
                    $iata_code_letter = null;
                }

                switch ($animal->citesglobal) {
                    case 'Cites I':
                        $cites_global = $citesGlobal['I'];
                        break;
                    case 'Cites II':
                        $cites_global = $citesGlobal['II'];
                        break;
                    case 'Cites III':
                        $cites_global = $citesGlobal['III'];
                        break;
                    default:
                        $cites_global = null;
                        break;
                }

                switch ($animal->citeseurope) {
                    case 'Cites A':
                        $cites_europe = $citesEurope['A'];
                        break;
                    case 'Cites B':
                        $cites_europe = $citesEurope['B'];
                        break;
                    case 'Cites C':
                        $cites_europe = $citesEurope['C'];
                        break;
                    default:
                        $cites_europe = null;
                        break;
                }

                $crates_array = [];
                if ($animal->crate_un != null && $animal->crate_un > 0 && isset($crates[$animal->crate_un])) {
                    array_push($crates_array, $crates[$animal->crate_un]);
                }
                if ($animal->crate_sm != null && $animal->crate_sm > 0 && isset($crates[$animal->crate_sm])) {
                    array_push($crates_array, $crates[$animal->crate_sm]);
                }
                if ($animal->crate_med != null && $animal->crate_med > 0 && isset($crates[$animal->crate_med])) {
                    array_push($crates_array, $crates[$animal->crate_med]);
                }
                if ($animal->crate_big != null && $animal->crate_big > 0 && isset($crates[$animal->crate_big])) {
                    array_push($crates_array, $crates[$animal->crate_big]);
                }

                $codeAlreadyExist = Animal::where('code_number', $animal->codenumber)->first();

                $slug = Str::slug($animal->scientificname, '_');
                if (Animal::where('scientific_name_slug', $slug)->first() != null) {
                    $slug = null;
                }

                $mappedArray = [
                    'old_id'               => $animal->id,
                    'code_number'          => ($codeAlreadyExist             != null) ? null : $animal->codenumber,
                    'common_name'          => (trim($animal->commonname)     != '') ? $this->content_iconv(trim($animal->commonname)) : null,
                    'common_name_alt'      => (trim($animal->commonname2)    != '') ? $this->content_iconv(trim($animal->commonname2)) : null,
                    'scientific_name'      => (trim($animal->scientificname) != '') ? $this->content_iconv(trim($animal->scientificname)) : null,
                    'scientific_name_slug' => $slug,
                    'scientific_name_alt'  => (trim($animal->scientificname2) != '') ? $this->content_iconv(trim($animal->scientificname2)) : null,
                    'spanish_name'         => (trim($animal->spanishname)     != '') ? $this->content_iconv(trim($animal->spanishname)) : null,
                    'cites_global_key'     => $cites_global,
                    'cites_europe_key'     => $cites_europe,
                    'genus_id'             => (!empty($animal->genuscommonname) && isset($genuss[$animal->genuscommonname])) ? $genuss[$animal->genuscommonname] : null,
                    'iata_code'            => $iata_code,
                    'iata_code_letter'     => $iata_code_letter,
                    'body_weight'          => $animal->bodyweight,
                    'catalog_pic'          => $animal->catalogimage,
                    'updated_at'           => ($animal->datemodified_animal != null && DateTimeHelper::validateDate($animal->datemodified_animal)) ? date('Y-m-d H:i:s', strtotime($animal->datemodified_animal)) : null,
                ];

                $animal = Animal::create($mappedArray);
                if (count($crates_array) > 0) {
                    $animal->crates()->sync($crates_array);
                }

                $count_imported_records++;
            }

            DB::commit();

            if (count($skipped_records) > 0) {
                \Storage::disk('local')->put('import_skip_animals', $skipped_records->toArray());
            }

            return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
        } catch (\Throwable $th) {
            DB::rollBack();

            echo 'Rolled back import animals after ' . $count_imported_records . ' succesfull records and skipped ' . $count_skipped_records . ' records';

            throw $th;
        }
    }

    /**
     * Test and change encoded
     *
     * @return int
     */
    public function content_iconv($data, $to = 'utf-8')
    {
        $encode_array = ['UTF-8', 'ASCII', 'GBK', 'GB2312', 'BIG5', 'JIS', 'eucjp-win', 'sjis-win', 'EUC-JP'];
        $encoded      = mb_detect_encoding($data, $encode_array);
        $to           = strtoupper($to);
        if ($encoded != $to) {
            dump($encoded);
            $data = mb_convert_encoding($data, 'utf-8', $encoded);
            dd($data);
        }

        return $data;
    }
}
