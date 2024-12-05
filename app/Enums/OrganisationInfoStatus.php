<?php

namespace App\Enums;

class OrganisationInfoStatus extends Enum
{
    const site_under_construction = 'Site under construction';

    const nothing_on_internet = 'Nothing on the internet';

    const is_closed = 'Is closed';

    const website_has_contact_form = 'Website has contact form';

    const no_website_facebook = 'On internet but no website/facebook';

    const has_only_facebook = 'Has facebook but no email';

    const email_exists = 'Email already exists';
}
