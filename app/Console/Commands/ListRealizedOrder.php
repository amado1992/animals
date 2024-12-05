<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ListRealizedOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:list-realized-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send list of realized orders';

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
        $orders = Order::where('realized_order_send', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        $orderByField = null;

        if (count($orders) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'Last realized orders';
            $email_title   = 'Realized Orders';
            $date_send     = false;
            $status        = 'realizaed';

            $email_body = view('emails.list-order-send', compact('orders', 'status', 'orderByField', 'email_title', 'date_send'))->render();
            //Mail::to('johnrens@zoo-services.com')->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            $this->info('Successfully sent order email to members.');
        } else {
            $this->info('There are not order to send.');
        }
    }
}
