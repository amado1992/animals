<?php

namespace App\Enums;

class OurSurplusOrderByOptions extends Enum
{
    const updated_at = 'Last modified';

    const created_at = 'Creation date';

    const common_name = 'Common name';

    const scientific_name = 'Scientific name';

    const code_number = 'Taxonomy';
}
