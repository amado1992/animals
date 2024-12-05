
<div class="row" style="margin: 6px 0 -10px 0;">
    <div class="col-md-2" style="margin: 0 0 0 27px;">
        <div class="row">
            <div class="col-md-2">
                <p><strong>M</strong></p>
            </div>
            <div class="col-md-2">
                <p><strong>F</strong></p>
            </div>
            <div class="col-md-2">
                <p><strong>U</strong></p>
            </div>
            <div class="col-md-3">
                <p><strong></strong></p>
            </div>
            <div class="col-md-3">
                <p><strong></strong></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="row">
            <div class="col-md-3">
                <p><strong>M</strong></p>
            </div>
            <div class="col-md-3">
                <p><strong>F</strong></p>
            </div>
            <div class="col-md-3">
                <p><strong>U</strong></p>
            </div>
            <div class="col-md-3">
                <p><strong>P</strong></p>
            </div>
        </div>
    </div>
    <div class="col-md-3" style="max-width: 29% !important; flex: 0 0 29%;">
        <div class="row">
            <div class="col-md-6" style="max-width: 56% !important;  flex: 0 0 56%;">
                <p><strong>Supplier</strong></p>
            </div>
            <div class="col-md-2">
                <p><strong>Level</strong></p>
            </div>
            <div class="col-md-3">
                <p><strong>Location</strong></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="row">
            <div class="col-md-2">
                <p><strong>Origin</strong></p>
            </div>
            <div class="col-md-4">
                <p><strong>Age group</strong></p>
            </div>
            <div class="col-md-3">
                <p><strong>Size</strong></p>
            </div>
            <div class="col-md-3">
                <p><strong>Date</strong></p>
            </div>
        </div>
    </div>
</div>

@foreach($oursurplus->animal_related_surplus as $relatedSurplus)

    <div class="row" style="border-top: 1px solid #00000014; padding: 7px 0 0px 0;">
        <div class="col-md-2" style="margin: 0 0 0 27px;">
            <div class="row">
                <div class="col-md-2" style="margin: 0 -4px 0 13px;">
                    <p>{{ $relatedSurplus->quantityM >= 0 ? $relatedSurplus->quantityM : 'x' }}</p>
                </div>
                <div class="col-md-2">
                    <p>{{ $relatedSurplus->quantityF >= 0 ? $relatedSurplus->quantityF : 'x' }}</p>
                </div>
                <div class="col-md-2">
                    <p>{{ $relatedSurplus->quantityU >= 0 ? $relatedSurplus->quantityU : 'x' }}</p>
                </div>
                <div class="col-md-3">
                    <p>
                        <strong>
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                Costs:<br>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                Sales:
                            @endif
                        </strong>
                    </p>
                </div>
                <div class="col-md-3" style="margin: 0 0 0 -11px;">
                    <p>
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                            {{ $relatedSurplus->cost_currency }}<br>
                        @endif
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                            {{ $relatedSurplus->sale_currency }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3">
                    <p>
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                            {{ number_format($relatedSurplus->costPriceM) }}.00<br>
                        @endif
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                            {{ number_format($relatedSurplus->salePriceM) }}.00
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                            {{ number_format($relatedSurplus->costPriceF) }}.00<br>
                        @endif
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                            {{ number_format($relatedSurplus->salePriceF) }}.00
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                            {{ number_format($relatedSurplus->costPriceU) }}.00<br>
                        @endif
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                            {{ number_format($relatedSurplus->salePriceU) }}.00
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                            {{ number_format($relatedSurplus->costPriceP) }}.00<br>
                        @endif
                        @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                            {{ number_format($relatedSurplus->salePriceP) }}.00
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3" style="max-width: 29% !important; flex: 0 0 29%;">
            <div class="row">
                <div class="col-md-6" style="max-width: 56% !important;  flex: 0 0 56%;">
                    <p>
                        @if ($relatedSurplus->organisation != null)
                            {{ $relatedSurplus->organisation->name  }}<br>
                            {{ (!empty($relatedSurplus->organisation->city)) ? $relatedSurplus->organisation->city . ', ' : '' }} {{($relatedSurplus->organisation->country) ? $relatedSurplus->organisation->country->name : ''}}
                        @elseif ($relatedSurplus->contact != null)
                            {{ $relatedSurplus->contact->full_name }}<br>
                            {{ ($relatedSurplus->contact->city) ? $relatedSurplus->contact->city . ', ' : '' }}{{ ($relatedSurplus->contact->country) ? $relatedSurplus->contact->country->name : '' }}
                        @endif
                        @if ($relatedSurplus->organisation != null)
                            @if($relatedSurplus->organisation->contacts)
                                @if($relatedSurplus->organisation->contacts[0]->email)
                                    <br>
                                    @if (!empty($type) && $type == "offer")
                                        <a href="{{ route('offers.sendEmailOption', [$id_data, 'to_email_link', 'details']) }}?email_to={{ $relatedSurplus->organisation->contacts[0]->email }}" style="color: #4e73df; !important">{{ $relatedSurplus->organisation->contacts[0]->email }}</a>
                                    @endif
                                    @if (!empty($type) && $type == "order")
                                        <a href="{{ route('orders.sendEmailOption', [$id_data, 'to_email_link', 'details']) }}?email_to={{ $relatedSurplus->organisation->contacts[0]->email }}" style="color: #4e73df; !important">{{ $relatedSurplus->organisation->contacts[0]->email }}</a>
                                    @endif
                                @endif
                                <br>
                            @endif
                        @else
                            <span class="card-title mb-0 text-danger">INSTITUTION NOT DEFINED</span>
                            <br>
                        @endif
                        @if ($relatedSurplus->organisation != null)
                            @if($relatedSurplus->organisation->contacts)
                                @if($relatedSurplus->organisation->contacts[0]->mobile_phone){{ $relatedSurplus->organisation->contacts[0]->mobile_phone }} @endif
                            @endif
                        @endif
                    </p>
                </div>
                <div class="col-md-2">
                    <p>
                        @if ($relatedSurplus->organisation != null)
                            {{ $relatedSurplus->organisation->level ?? ""  }}<br>
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        {!! ($relatedSurplus->country != null) ? $relatedSurplus->country->name . '<br>' : '' !!}
                        {{ ($relatedSurplus->area_region != null) ? $relatedSurplus->area_region->name : '' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-2">
                    <p>
                        {{ $relatedSurplus->origin_field }}
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        {{ $relatedSurplus->age_field }}
                        {!! ($relatedSurplus->bornYear != null) ? ' / ' . $relatedSurplus->bornYear : '' !!}
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        {{ $relatedSurplus->size_field }}
                    </p>
                </div>
                <div class="col-md-3" style="margin: 0 0 0 -7px;">
                    <p>
                        {{ $relatedSurplus->created_at ?: '' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

@endforeach
{{ $oursurplus->animal_related_surplus->links() }}
