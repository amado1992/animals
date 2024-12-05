<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Surplus;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NewSurplus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:new-surplus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the new surplus of suppliers records that were inserted.';

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
        $new_surpluses     = Surplus::whereDate('created_at', '=', $date_before_today)->get();

        if (count($new_surpluses) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_to      = 'johnrens@zoo-services.com';
            $email_cc      = 'development@zoo-services.com';
            $email_subject = 'CHECK: NEW SURPLUS2 INSERTED';
            $email_body    = view('emails.new-surplus-inserted-or-modified', compact('new_surpluses'))->render();

            Mail::to($email_to)->cc($email_cc)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            $this->info('Successfully sent daily new surplus.');
        } else {
            $this->info('There are not new surplus inserted.');
        }
    }
}
