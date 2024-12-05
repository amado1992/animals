<?php

namespace App\Enums;

class InvoiceFrom extends Enum
{
    const species = 'Species';

    const transport = 'Transport';

    const crates = 'Crates';

    const transport_crates = 'Transport+Crates';

    const species_crates = 'Species+Crates';

    const fixed_costs = 'Fixed costs';

    const feeding_costs = 'Feeding Costs';

    const all = 'All';
}
