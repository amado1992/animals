<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\Email;
use App\Models\EmailToken;
use App\Models\Labels;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Organisation;
use App\Models\Surplus;
use App\Models\User;
use App\Models\Wanted;
use App\Services\GraphService;
use Beta\Microsoft\Graph\ExternalConnectors\Model\Label;
use Illuminate\Console\Command;

class InboxEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:inbox-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get incoming emails from Outlook';

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
        $acouness = EmailToken::where('schedule_mail', '!=', '')->whereNotNull('schedule_mail')->get();
        if (!empty($acouness->toarray())) {
            foreach ($acouness->toarray() as $acount) {
                $email_user   = $acount['schedule_mail'];
                $GraphService = new GraphService();
                $GraphService->initializeGraphForUserAuth();
                $email_save = false;
                try {
                    $token   = $GraphService->getUserToken($acount['id'], json_decode($acount['token']));
                    $user_id = $GraphService->getUserByEmail($token, $email_user);
                    if (!empty($token)) {
                        $directories = ['inbox', 'archive', 'deleteditems'];
                        foreach ($directories as $directory) {
                            var_dump('Directory: ' . $directory);
                            if ($directory == 'archive') {
                                $last_email = Email::where('to_email', $email_user)->where('archive', 1)->orderBy('date_last_save', 'DESC')->first();
                            } elseif ($directory == 'deleteditems') {
                                $last_email = Email::where('to_email', $email_user)->where('is_delete', 1)->orderBy('date_last_save', 'DESC')->first();
                            } else {
                                $last_email = Email::where('to_email', $email_user)->where('is_delete', 0)->where('archive', 0)->orderBy('date_last_save', 'DESC')->first();
                            }
                            if (!empty($last_email)) {
                                $date_start = $last_email['date_last_save'];
                            } else {
                                $date_start = '1990-03-17 00:00:00';
                            }

                            $last_date = date('Y-m-dTH:i', strtotime($date_start));
                            $last_date = str_replace('UTC', 'T', (string) $last_date) . 'Z';
                            $messages  = $GraphService->getAllInboxByUserDirectory($token, $user_id->getId(), $last_date, $directory);

                            while (!empty($messages->getPage()) && !$messages->isEnd()) {
                                $last_date = date('Y-m-dTH:i', strtotime($date_start));
                                $last_date = str_replace('UTC', 'T', (string) $last_date) . 'Z';
                                $messages  = $GraphService->getAllInboxByUserDirectory($token, $user_id->getId(), $last_date, $directory);
                                foreach ($messages->getPage() as $message) {
                                    $email       = [];
                                    $exist_email = Email::where('guid', $message->getId())->first();

                                    if (empty($exist_email)) {
                                        $email_save = true;
                                        if (strpos($message->getFrom()->getEmailAddress()->getAddress(), '@') === 0) {
                                            $email['from_email'] = $message->getFrom()->getEmailAddress()->getAddress();
                                        } else {
                                            $email['from_email'] = substr($message->getFrom()->getEmailAddress()->getAddress(), 0, 100);
                                        }
                                        $email['guid']          = $message->getId();
                                        $email['body']          = '';
                                        $email['subject']       = $message->getSubject() ?? '';
                                        $email['name']          = $message->getFrom()->getEmailAddress()->getName();
                                        $email['created_at']    = $message->getReceivedDateTime();
                                        $email['updated_at']    = $message->getReceivedDateTime();
                                        $email['to_email']      = $email_user;
                                        $email['to_recipients'] = $message->getToRecipients()[0]['emailAddress']['address'];

                                        if (!empty($email['from_email'])) {
                                            $contact      = Contact::where('email', $message->getFrom()->getEmailAddress()->getAddress())->first();
                                            $organisation = Organisation::where('email', $message->getFrom()->getEmailAddress()->getAddress())->first();
                                            $user         = User::where('email', $message->getFrom()->getEmailAddress()->getAddress())->first();
                                            if (!empty($contact)) {
                                                $email['contact_id']      = $contact['id'];
                                                $email['organisation_id'] = $contact['organisation_id'];
                                            }
                                            if (!empty($organisation)) {
                                                $email['organisation_id'] = $organisation['id'];
                                                if (!empty($organisation->contacts)) {
                                                    foreach ($organisation->contacts as $row_contact) {
                                                        if ($row_contact->email == $email['from_email']) {
                                                            $email['contact_id'] = $row_contact->id;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if (!empty($email['subject'])) {
                                            $label_offer  = false;
                                            $label_oder   = false;
                                            $label_surplu = false;
                                            $label_wanted = false;
                                            if (strpos($email['subject'], 'offer') === false && strpos($email['subject'], 'Offer') === false) {
                                            } else {
                                                $subject = explode(' ', $email['subject']);
                                                foreach ($subject as $value) {
                                                    $offer_filter = explode('-', $value);
                                                    if (count($offer_filter) > 1) {
                                                        $offer = Offer::whereYear('created_at', '=', $offer_filter[0])->where('offer_number', $offer_filter[1])->first();
                                                        if (!empty($offer)) {
                                                            $email['offer_id'] = $offer['id'];
                                                            $label_offer       = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            if (strpos($email['subject'], '#OF') === false) {
                                            } else {
                                                $subject = explode('#', $email['subject']);
                                                if (!empty($subject[1])) {
                                                    $offer_filter = explode('-', $subject[1]);
                                                    if (count($offer_filter) > 1) {
                                                        $offer = Offer::whereYear('created_at', '=', $offer_filter[1])->where('offer_number', $offer_filter[2])->first();
                                                        if (!empty($offer)) {
                                                            $email['offer_id'] = $offer['id'];
                                                            $label_offer       = true;
                                                        }
                                                    }
                                                }
                                            }
                                            if (strpos($email['subject'], 'order') === false && strpos($email['subject'], 'Order') === false) {
                                            } else {
                                                $subject = explode(' ', $email['subject']);
                                                foreach ($subject as $value) {
                                                    $order_filter = explode('-', $value);
                                                    if (count($order_filter) > 1) {
                                                        $order = Order::whereYear('created_at', '=', $order_filter[0])->where('order_number', $order_filter[1])->first();
                                                        if (!empty($order)) {
                                                            $email['order_id'] = $order['id'];
                                                            $label_oder        = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            if (strpos($email['subject'], '#OR') === false) {
                                            } else {
                                                $subject = explode('#', $email['subject']);
                                                if (!empty($subject[1])) {
                                                    $order_filter = explode('-', $subject[1]);
                                                    if (count($order_filter) > 1) {
                                                        $order = Order::whereYear('created_at', '=', $order_filter[1])->where('order_number', $order_filter[2])->first();
                                                        if (!empty($order)) {
                                                            $email['order_id'] = $order['id'];
                                                            $label_oder        = true;
                                                        }
                                                    }
                                                }
                                            }
                                            if (strpos($email['subject'], '#SU') === false) {
                                            } else {
                                                $subject = explode('#', $email['subject']);
                                                if (!empty($subject[1])) {
                                                    $filter = explode('-', $subject[1]);
                                                    if (count($filter) > 1) {
                                                        $result = Surplus::find($filter[1]);
                                                        if (!empty($result)) {
                                                            $email['surplu_id'] = $result['id'];
                                                            $label_surplu       = true;
                                                        }
                                                    }
                                                }
                                            }
                                            if (strpos($email['subject'], '#WA') === false) {
                                            } else {
                                                $subject = explode('#', $email['subject']);
                                                if (!empty($subject[1])) {
                                                    $filter = explode('-', $subject[1]);
                                                    if (count($filter) > 1) {
                                                        $result = Wanted::find($filter[1]);
                                                        if (!empty($result)) {
                                                            $email['wanted_id'] = $result['id'];
                                                            $label_wanted       = true;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $email_new = Email::create($email);
                                        if ($directory == 'archive') {
                                            $email_new['archive'] = 1;
                                        }
                                        if ($directory == 'deleteditems') {
                                            $email_new['is_delete'] = 1;
                                        }
                                        $email_new['date_last_save'] = $message->getReceivedDateTime()->format('Y-m-d H:i:s');
                                        $email_new->save();

                                        if (empty($organisation) && empty($contact) && empty($user)) {
                                            $label = Labels::where('name', 'new_contact')->first();
                                            $email_new->labels()->attach($label);
                                        }

                                        if ($label_offer) {
                                            $label = Labels::where('name', 'offer')->first();
                                            $email_new->labels()->attach($label);
                                        }

                                        if ($label_oder) {
                                            $label = Labels::where('name', 'order')->first();
                                            $email_new->labels()->attach($label);
                                        }
                                    }
                                    $date_start = $message->getReceivedDateTime()->format('Y-m-d H:i:s');
                                }
                                var_dump($date_start);
                            }
                        }

                        $acount_update                  = EmailToken::find($acount['id']);
                        $acount_update['schedule_mail'] = null;
                        $acount_update->save();
                    } else {
                        return json_encode(false);
                    }
                } catch (\Throwable $th) {
                    continue;
                }
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function updateIsReaad($id, $isRead)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $GraphService->updateIsReadEmailInbox($id, $isRead);
    }
}
