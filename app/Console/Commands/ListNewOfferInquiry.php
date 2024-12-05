<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\ItemDashboard;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ListNewOfferInquiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:list-offers-inquiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send list of new offers inquiry';

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
        $offers_filter = Offer::where('new_offer_inquiry', 1)
            ->orderBy("created_at", "DESC")
            ->get();

        $offers = [];
        foreach ($offers_filter as $key => $row) {
            $offers[$key] = $this->offerService->calculate_offer_totals($row->id);
        }

        if (count($offers) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'Offers inquiry';
            $email_title   = "Offers inquiry";
            $date_send     = false;
            $offer_send    = true;

            $email_body = view('emails.list-offer-inquiry', compact('offers', 'email_title', 'date_send', 'offer_send'))->render();
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

        $dashboard = Dashboard::where("filter_data", "offers_inquiry")->first();
        if (!empty($dashboard)) {
            $new_item_dashboard                  = new ItemDashboard();
            $new_item_dashboard["itemable_id"]   = $new_email->id;
            $new_item_dashboard["itemable_type"] = "email";
            $new_item_dashboard["dashboard_id"]  = $dashboard->id;
            $new_item_dashboard->save();
        }
    }
}
