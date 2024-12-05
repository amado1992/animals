<?php

namespace App\Enums;

class OrderStatus extends Enum
{
    const Pending = 'Pending';

    const ToSearch = 'To search';

    const Realized = 'Realized';

    const Cancelled = 'Cancelled';
}
