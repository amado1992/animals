<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\ItemDashboard;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ListOfferOverSevenDaysCreated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:list-offers-over-seven-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send list of offers over seven days created';

    protected $offerService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OfferService $offerService)
    {
        parent::__construct();
        $this->offerService = $offerService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date          = Carbon::now();
        $date2         = Carbon::now();
        $date          = $date->subDay(7);
        $date_second   = $date2->subDay(14);
        $offers_filter = Offer::where('offer_send_out', 1)
            ->orderBy("next_reminder_at", "DESC")
            ->whereDate('next_reminder_at', '<=', $date->format('Y-m-d'))
            ->get();
        $offers_filter_second = Offer::where('offer_send_out', 1)
            ->orderBy("next_reminder_at", "DESC")
            ->whereDate('next_reminder_at', '<=', $date_second->format('Y-m-d'))
            ->get();

        $offers = [];
        foreach ($offers_filter as $key => $row) {
            $offers[$key]                    = $this->offerService->calculate_offer_totals($row->id);
            $offers[$key]["reminder_second"] = 0;
            foreach ($offers_filter_second as $key_value => $value) {
                if ($value->id == $row->id) {
                    $offers[$key]["reminder_second"] = 1;
                    break;
                }
            }
        }

        if (count($offers) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'Offers older as 7 days to remind';
            $email_title   = "Offers older as 7 days to remind";
            $date_send     = true;

            $email_body = view('emails.list-offer-send-out', compact('offers', 'email_title', 'date_send'))->render();
            //Mail::to('johnrens@zoo-services.com')->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
            $this->createSentEmail($email_subject, $email_from, 'johnrens@zoo-services.com', $email_body);

            $this->info('Successfully sent offer email to members.');
        } else {
            $this->info('There are not offer to send.');
        }
    }

    public function createSentEmail($subject, $from, $email, $body)
    {
        $new_email               = new Email();
        $new_email["from_email"] = $from;
        $new_email["to_email"]   = $email;
        $new_email["body"]       = $body;
        $new_email["guid"]       = "";
        $new_email["subject"]    = $subject;
        $new_email["name"]       = "";
        $new_email["is_send"]    = 1;
        $new_email->save();

        $dashboard = Dashboard::where("filter_data", "offers_remind")->first();
        if (!empty($dashboard)) {
            $new_item_dashboard                  = new ItemDashboard();
            $new_item_dashboard["itemable_id"]   = $new_email->id;
            $new_item_dashboard["itemable_type"] = "email";
            $new_item_dashboard["dashboard_id"]  = $dashboard->id;
            $new_item_dashboard->save();
        }
    }
}
