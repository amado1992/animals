<?php

namespace App\Enums;

class OrganisationOrderByOptions extends Enum
{
    const updated_at = 'Last modified';

    const email = 'Email';

    const name = 'Name';

    const country_id = 'Country';

    const city = 'City';

    const domain_name = 'Domain name';
}
