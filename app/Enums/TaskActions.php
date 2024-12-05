<?php

namespace App\Enums;

class TaskActions extends Enum
{
    const call = 'Call';

    const email = 'Send email';

    const remind = 'Send Whatsapp';

    const bo = 'Work in BO';

    const reminder = 'Reminder';
}
