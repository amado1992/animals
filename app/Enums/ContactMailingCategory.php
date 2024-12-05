<?php

namespace App\Enums;

class ContactMailingCategory extends Enum
{
    const all_mailings = 'All mailings';

    const no_mailings = 'No mailings';

    const unsubscribed = 'Unsubscribed';

    const not_approved_for_website = 'Not approved for website';

    const not_serious = 'Not serious';

    const not_valid_anymore = 'Not valid anymore';

    const only_for_supplying = 'Only for supplying';

    const unknown = 'Unknown';
}
