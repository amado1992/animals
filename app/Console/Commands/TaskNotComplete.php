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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class TaskNotComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:task-not-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send list of tasks not complete';

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
            if ($row["id"] != 2) {
                $tasks_created = Task::whereDate('due_date', '<', Carbon::now()->format('Y-m-d'))
                    ->whereNull('finished_at')
                    ->where(function ($query) use ($row) {
                        $query->where("user_id", $row["id"])
                            ->orWhere("created_by", $row["id"]);
                    })
                    ->where('status', 'incomplete')
                    ->orderBy('due_date', 'DESC')
                    ->get();
                $this->sendTaskEmail($tasks_created, $row["email"], true);
            }
        }

        $this->info('Successfully sent tasks email to members.');

        //$this->sendTaskEmail($tasks);
    }

    public function sendTaskEmail($tasks, $user = 'johnrens@zoo-services.com', $create = false)
    {
        if (count($tasks) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'Tasks not complete for checking';
            $email_title   = "Tasks not complete for checking";
            $complete      = false;

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
