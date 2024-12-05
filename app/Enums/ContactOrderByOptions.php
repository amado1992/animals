<?php

namespace App\Enums;

class ContactOrderByOptions extends Enum
{
    const updated_at = 'Last modified';

    const email = 'Email';

    const domain_name = 'Domain name';

    const name = 'Name';
}
