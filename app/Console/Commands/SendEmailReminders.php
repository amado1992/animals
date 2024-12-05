<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\ItemDashboard;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;

class SendEmailReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email-due-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email due reminders';

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
        $emails = Email::whereDate('remind_due_date', ">=", Carbon::now()->format('Y-m-d'))->where('is_remind', 1)->get();
        if(!empty($emails)){
            foreach($emails as $row){
                $subject = $row->subject . " #RM-" . $row->id;
                Mail::to($row->to_email)->send(new SendGeneralEmail($row->from_email, $subject, $row->body));
                $this->createSentEmail($subject, $row->from_email, $row->to_email, $row->body);
            }

            $this->info('Successfully sent email reminders.');
        }else{
            $this->info('Not sent email reminders.');
        }
    }

    public function createSentEmail($subject, $from, $email, $body)
    {
        $new_email = new Email();
        $new_email["from_email"] = $from;
        $new_email["to_email"] = $email;
        $new_email["body"] = $body;
        $new_email["guid"] = "";
        $new_email["subject"] = $subject;
        $new_email["name"] = "";
        $new_email["is_send"] = 1;
        $new_email->save();
        return;
    }
}
