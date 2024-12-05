<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\Email;
use App\Models\Labels;
use App\Models\Organisation;
use Illuminate\Console\Command;

class UpdateNewContactNewToEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:label-new-contact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update label new contact in email inbox';

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
        $email = Email::whereHas('labels', function ($query) {
            $query->where('name', 'new_contact');
        })->get();
        $label = Labels::where('name', 'new_contact')->first();
        if (!empty($email->toArray())) {
            foreach ($email as $key => $row) {
                $contact             = Contact::where('email', $row['from_email'])->first();
                $organisation        = Organisation::where('email', $row['from_email'])->first();
                $contact_update      = false;
                $organisation_update = false;
                if (!empty($contact)) {
                    $row['contact_id']      = $contact['id'];
                    $row['organisation_id'] = $contact['organisation_id'];
                    $contact_update         = true;
                }
                if (!empty($organisation)) {
                    $row['organisation_id'] = $organisation['id'];
                    if (!empty($organisation->contacts)) {
                        foreach ($organisation->contacts as $row_contact) {
                            if ($row_contact->email == $row['from_email']) {
                                $row['contact_id'] = $row_contact->id;
                                break;
                            }
                        }
                    }
                    $organisation_update = true;
                }
                if ($contact_update || $organisation_update) {
                    $row->labels()->detach($label);
                    $row->save();
                }
            }
        }

        return 0;
    }
}
