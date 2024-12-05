<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\ItemDashboard;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class TaskComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:task-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send list of new tasks complete';

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
        $roles = Role::where('name', '<>', 'website-user')->get();
        $users = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->get();

        foreach ($users as $row) {
            $tasks = Task::where('send_complete', 1)
                ->where('status', 'complete')
                ->where("user_id", $row["id"])
                ->orderBy("updated_at", "DESC")
                ->get();
            $this->sendTaskEmail($tasks, $row["email"], true);
        }

        $this->info('Successfully sent tasks email to members.');
    }

    public function sendTaskEmail($tasks, $user = 'johnrens@zoo-services.com', $create = false)
    {
        if (count($tasks) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'Tasks complete for checking';
            $email_title   = "Tasks complete for checking";
            $complete      = true;

            $email_body = view('emails.new-tasks-for-approval', compact('tasks', 'email_title', 'complete'))->render();
            if($user != 'johnrens@zoo-services.com'){
                //Mail::to($user)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
            }
            $this->createSentEmail($email_subject, $email_from, $user, $email_body);
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

        $dashboard = Dashboard::where("filter_data", "tasks_have_been_sent_out")->first();
        if (!empty($dashboard)) {
            $new_item_dashboard                  = new ItemDashboard();
            $new_item_dashboard["itemable_id"]   = $new_email->id;
            $new_item_dashboard["itemable_type"] = "email";
            $new_item_dashboard["dashboard_id"]  = $dashboard->id;
            $new_item_dashboard->save();
        }
    }
}
