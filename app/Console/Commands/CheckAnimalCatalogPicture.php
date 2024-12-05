<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Surplus;
use App\Models\Wanted;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CheckAnimalCatalogPicture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:animal-without-catalog-picture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if a the animal of a new surplus or wanted does not have catalog picture.';

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
     * @return int
     */
    public function handle()
    {
        $date_before_today = Carbon::now()->subDay()->format('Y-m-d');
        $new_surpluses     = Surplus::with(['animal'])->whereDate('created_at', '=', $date_before_today)->get();
        $new_wanteds       = Wanted::with(['animal'])->whereDate('created_at', '=', $date_before_today)->get();

        $species_without_catalog_picture = [];
        foreach ($new_surpluses as $surplus) {
            if (!Arr::exists($species_without_catalog_picture, $surplus->animal->id) && $surplus->animal != null && $surplus->animal->catalog_pic != null && !Storage::exists('public/animals_pictures/' . $surplus->animal->id . '/' . $surplus->animal->catalog_pic)) {
                $species_without_catalog_picture = Arr::add($species_without_catalog_picture, $surplus->animal->id, $surplus->animal);
            }
        }

        foreach ($new_wanteds as $wanted) {
            if (!Arr::exists($species_without_catalog_picture, $wanted->animal->id) && $wanted->animal != null && $wanted->animal->catalog_pic != null && !Storage::exists('public/animals_pictures/' . $wanted->animal->id . '/' . $wanted->animal->catalog_pic)) {
                $species_without_catalog_picture = Arr::add($species_without_catalog_picture, $wanted->animal->id, $wanted->animal);
            }
        }

        if (count($species_without_catalog_picture) > 0) {
            $email_from    = 'request@zoo-services.com';
            $email_to      = 'izs@zoo-services.com';
            $email_subject = 'Animals without catalog picture.';
            $email_body    = view('emails.new-animals-without-catalog-picture', compact('species_without_catalog_picture'))->render();

            Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            $this->info('Successfully sent animals without catalog picture.');
        } else {
            $this->info('There are not animals without catalog picture.');
        }
    }
}
