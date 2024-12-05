<?php

namespace App\Enums;

class ContactApprovedStatus extends Enum
{
    const active = 'Yes, activate';

    const no_active = 'No, standard answer';

    const website_not_working = 'Website not working';

    const question = 'More institution info';

    const no_websites = 'More information';

    const cancel = 'Cancel';
}
