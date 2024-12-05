<?php

namespace App\Console\Commands;

use App\Models\Email;
use App\Services\GraphService;
use Illuminate\Console\Command;

class DeleteEmailsAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:emails-address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Emails Address';

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
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        $emails = Email::whereNotNull('delete_email_address')->where('is_delete', 0)->get();
        foreach ($emails as $key => $email) {
            $user_acount = json_decode($email['delete_email_address']);
            $userToken   = $GraphService->getAllUserToken($user_acount->user);
            if (!empty($userToken)) {
                foreach ($userToken as $row) {
                    $token   = $GraphService->getUserToken($row['id'], json_decode($row['token']));
                    $user_id = $GraphService->getUserByEmail($token, $user_acount->account);
                    if (!empty($token)) {
                        $result = $GraphService->updateDelete($token, $user_id->getId(), $email['guid']);
                        if (!empty($result)) {
                            $email['guid'] = $result['id'];
                        }
                        $email['is_delete']            = 1;
                        $email['archive']              = 0;
                        $email['is_send']              = 0;
                        $email['delete_email_address'] = null;
                        $email->save();
                    }
                }
            }
        }

        $this->info('The email was dalete successfully');
    }
}
