<?php

namespace App\Enums;

class ActionOrderCategory extends Enum
{
    const reservation = 'Reservation';

    const permit = 'Permit application';

    const veterinary = 'Veterinary actions';

    const crate = 'Crate construction';

    const transport = 'Transport booking';
}
