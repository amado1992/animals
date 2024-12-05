<?php

namespace App\Console\Commands;

use App\Http\Controllers\TaskController as TaskController;
use App\Mail\SendGeneralEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckTodayTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:today-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the tasks that are not done yet and if the due date is equal to or less than today, send an email to the task admin.';

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
        $todayTasks = TaskController::getTodayTasks();

        if ($todayTasks->count() > 0) {
            foreach ($todayTasks as $task) {
                if ($task->user != null && !empty($task->taskable) && $task->taskable->offer_status !== 'Cancelled') {
                    $email_from    = ($task->admin && $task->admin->email_domain == 'zoo-services.com') ? $task->admin->email : 'info@zoo-services.com';
                    $email_to      = $task->user->email;
                    $email_subject = 'CHECK: TASK REMINDER ' . $task->description;
                    $email_body    = view('emails.task-to-user', compact('task'))->render();

                    Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                    $this->info('Successfully sent task reminder to user in charge.');
                }
            }
        } else {
            $this->info('There are not tasks for today.');
        }
    }
}
