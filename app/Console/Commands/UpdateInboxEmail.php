<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\Email;
use App\Models\EmailToken;
use App\Models\Labels;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Organisation;
use App\Models\User;
use App\Services\GraphService;
use Beta\Microsoft\Graph\ExternalConnectors\Model\Label;
use Illuminate\Console\Command;

class UpdateInboxEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:update-inbox-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get update emails from Outlook';

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
     * @return mixed
     */
    public function handle()
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $emails = Email::all();
        if (!empty($emails)) {
            foreach ($emails as $key => $row) {
                $userToken = $GraphService->getAllUserTokenByEmail($row['to_email']);
                if (!empty($userToken)) {
                    $token   = $GraphService->getUserToken($row['id'], json_decode($userToken[0]['token']));
                    $user_id = $GraphService->getUserByEmail($token, $row['to_email']);
                    if (!empty($token)) {
                        $result = $GraphService->getEmailInfo($token, $user_id->getId(), $row['guid']);
                        if (!empty($result)) {
                            var_dump($result);
                        }
                    }
                }
            }
        }
    }
}
