<table id="assingData" class="table table-striped table-sm mb-0">
    <thead>
        <tr>
            <th></th>
            @foreach ($header_table as $row)
                <th nowrap>{{ $row }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($body_table as $row)
            <tr>
                <td><input type="checkbox" class="selector_assing" value="{{ $row["id"] }}" @if(!empty($row["type"])) data-type="{{ $row["type"] }}" @endif/></td>
                @foreach ($row as $key_item => $item)
                    @if ($key_item != "id" && $key_item != "species_ordered")
                        <td>{!! $item !!}</td>
                    @endif
                    @if ($key_item == "species_ordered")
                        <td>
                            @if (count($item) == 0)
                                <span style="color: red;">No species added yet</span>
                            @elseif (count($item) == 1)
                                <p>
                                    @foreach ($item as $species)
                                        {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                        @if ($species->oursurplus && $species->oursurplus->animal)
                                            {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
                                        @else
                                            ERROR - NO STANDARD SURPLUS
                                        @endif
                                    @endforeach
                                </p>
                            @elseif(count($item) > 1)
                                @php
                                    $species = $item[0];
                                @endphp
                                <p>{{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                        {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
                                    @else
                                        ERROR - NO STANDARD SURPLUS
                                    @endif
                                </p>
                                <p class="modal-toggle see-more" onclick="modal_toggle(this)">See More</p>
                                <div style="display: none" class="hidden-info">
                                    <table style="width: 100%;">
                                        <tbody>
                                            @foreach ($item as $species)
                                                <tr>
                                                    <td>
                                                        {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                                        @if ($species->oursurplus && $species->oursurplus->animal)
                                                            {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
                                                        @else
                                                            ERROR - NO STANDARD SURPLUS
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($species->oursurplus && $species->oursurplus->sale_currency)
                                                            {{ $species->oursurplus->sale_currency }}
                                                        @else
                                                            ERROR - NO STANDARD SURPLUS SALE CURRENCY
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ number_format($species->total_sales_price, 2, '.', '') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
<div class="new-modal">
    <div class="modal-overlay modal-toggle"></div>
    <div class="modal-wrapper modal-transition">
        <div class="modal-header">
            <button class="modal-close modal-toggle" onclick="modal_toggle_close(this)">
                &#10005;
            </button>
            <h2 class="modal-heading">more info</h2>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-content-p"></div>
            </div>
        </div>
    </div>
</div>
