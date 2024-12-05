@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('organisations.index') }}">Institutions</a></li>
    <li class="breadcrumb-item active">{{ $organisation->name }}</li>
</ol>
@endsection

@section('header-content')
    <div class="d-flex align-items-center">
        <h1 class="h1 text-white">{{ $organisation->name }}</h1>
        <span class="text-white">@if($organisation->type) &nbsp;-&nbsp;{{ $organisation->type->label }} @else &nbsp;-&nbsp;Unknown type @endif</span>
    </div>
@endsection

@section('main-content')

<div class="row">
  <div class="col-md-8">

    <div class="card shadow mb-4">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Institution details</h5>
            <div class="d-inline-flex">
                @if (Auth::user()->hasPermission('institutions.update'))
                    <a href="{{ route('organisations.edit', [$organisation->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-fw fa-pen"></i> Edit
                    </a>
                @endif
                @if (Auth::user()->hasPermission('institutions.delete'))
                    {!! Form::open(['method' => 'DELETE', 'url' => '#']) !!}
                        {!! Form::button('Delete', ['type'=> 'button', 'onclick' => 'destroyOrganisation()', 'class' => 'btn btn-danger ml-2']) !!}
                    {!! Form::close() !!}
                @endif
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table">
                <tr>
                    <td class="font-weight-bold border-top-0" style="width: 20%;">Name:</td>
                    <td class="border-top-0" style="width: 30%;">{{ $organisation->name }}</td>
                    <td class="font-weight-bold border-top-0" style="width: 20%;">Canonical Name:</td>
                    <td class="border-top-0" style="width: 30%;">{{ $organisation->canonical_name }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold border-top-0" style="width: 20%;">Synonyms:</td>
                    <td class="border-top-0" style="width: 30%;">{{ $organisation->synonyms }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold border-top-0" style="width: 20%;">Type:</td>
                    <td class="border-top-0" style="width: 30%;">{{ ($organisation->type) ? $organisation->type->label : ''}}</td>
                    <td class="font-weight-bold">Country:</td>
                    <td>{{ ($organisation->country) ? $organisation->country->name : ''}}<td>
                </tr>
                <tr>
                    <td class="font-weight-bold">City:</td>
                    <td>{{ $organisation->city }}</td>
                    <td class="font-weight-bold">Address:</td>
                    <td>{{ $organisation->address }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Zip code:</td>
                    <td>{{ $organisation->zipcode }}</td>
                    <td class="font-weight-bold">Main email:</td>
                    <td><a href="mailto:{{ $organisation->email }}"><u>{{ $organisation->email }}</u></a></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Zip code:</td>
                    <td>{{ $organisation->zipcode }}</td>
                    <td class="font-weight-bold">Website:</td>
                    <td><a href="//{{$organisation->website}}" target="_blank"><u>{{ $organisation->website }}</u></a></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Facebook page:</td>
                    <td><a href="//{{$organisation->facebook_page}}" target="_blank"><u>{{ $organisation->facebook_page }}</u></a></td>
                    <td class="font-weight-bold">Main phone:</td>
                    <td>{{ $organisation->phone }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Main fax:</td>
                    <td>{{ $organisation->fax }}</td>
                    <td class="font-weight-bold">VAT number:</td>
                    <td>{{ $organisation->vat_number }}</td>
                    <td class="font-weight-bold">Level:</td>
                    <td>
                        {{ Form::select('level', config("constants.contacts.level_type"), $organisation->level ?? "", array('class' => 'custom-select custom-select-sm form-control form-control-sm display-input-filter level_type level-'. $organisation->level ?? "" .'', 'data_organisation_id' => $organisation->id ?? '', 'data_value_old' => $organisation->level ?? '', 'style' => 'width: 53px;')) }}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Level:</td>
                    <td>{{ $organisation->level }}</td>
                    <td class="font-weight-bold">Remarks:</td>
                    <td>{{ $organisation->remarks }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Interest in:</td>
                    <td>
                        @foreach($organisation->interest as $section)
                            <span>{{ $section->label }}&nbsp;-</span>
                        @endforeach
                    </td>
                    <td class="font-weight-bold">Info status:</td>
                    <td>{{ $organisation->info }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Associations:</td>
                    <td>
                        @foreach($organisation->associations as $association)
                            <span>{{ $association->label }}&nbsp;-</span>
                        @endforeach
                    </td>
                    <td class="font-weight-bold">Indications:</td>
                    <td><span class="self-cursor" title="{{ $organisation->active_state_str }}">{{ $organisation->active_state }}</span></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Mailing Categories</td>
                    <td>{{ !empty($organisation->mailing_category) ? $mailing_categories[$organisation->mailing_category] : "--" }}</td>
                    <td class="font-weight-bold"></td>
                    <td>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div class="card shadow mb-2">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" id="email-received-tab" data-toggle="tab" href="#email-received" role="tab" aria-controls="email-received" aria-selected="false">Received emails</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Sent Emails</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="surpluses-tab" data-toggle="tab" href="#surpluses" role="tab" aria-controls="surpluses" aria-selected="false">Surpluses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="wanteds-tab" data-toggle="tab" href="#wanteds" role="tab" aria-controls="wanteds" aria-selected="false">Wanteds</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pending-offers-tab" data-toggle="tab" href="#pending-offers" role="tab" aria-controls="pending-offers" aria-selected="false">Pending offers</a>
                </li>
                @if (Auth::user()->hasPermission('orders.read'))
                    <li class="nav-item">
                        <a class="nav-link" id="pending-orders-tab" data-toggle="tab" href="#pending-orders" role="tab" aria-controls="pending-orders" aria-selected="false">Pending orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="realized-orders-tab" data-toggle="tab" href="#realized-orders" role="tab" aria-controls="realized-orders" aria-selected="false">Realized orders</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show" id="surpluses" role="tabpanel" aria-labelledby="surpluses-tab">
                    @unless($organizationSurpluses->isEmpty())
                        <table class="table table-condensed">
                            <thead>
                                <tr class="table-primary">
                                    <th style="width: 20%;">Species</th>
                                    <th style="width: 5%;">M</th>
                                    <th style="width: 5%;">F</th>
                                    <th style="width: 5%;">U</th>
                                    <th style="width: 5%;">Curr</th>
                                    <th style="width: 10%;">M</th>
                                    <th style="width: 10%;">F</th>
                                    <th style="width: 10%;">U</th>
                                    <th style="width: 10%;">P</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($organizationSurpluses as $surplus)
                                <tr>
                                    <td><a href="{{ route('surplus.show', $surplus->id) }}" title="Show surplusr">{{ ($surplus->animal != null) ? $surplus->animal->common_name : '-' }}</a></td>
                                    <td>{{ $surplus->male_quantity }}</td>
                                    <td>{{ $surplus->female_quantity }}</td>
                                    <td>{{ $surplus->unknown_quantity }}</td>
                                    <td>{{ $surplus->cost_currency }}</td>
                                    <td>{{ number_format($surplus->costPriceM, 2, '.', '') }}</td>
                                    <td>{{ number_format($surplus->costPriceF, 2, '.', '') }}</td>
                                    <td>{{ number_format($surplus->costPriceU, 2, '.', '') }}</td>
                                    <td>{{ number_format($surplus->costPriceP, 2, '.', '') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $organizationSurpluses->links() }}
                    @else
                        <p>No surplus related with this institution.</p>
                    @endunless
                </div>

                <div class="tab-pane fade show" id="wanteds" role="tabpanel" aria-labelledby="wanteds-tab">
                    @unless($organizationWanteds->isEmpty())
                        <table class="table table-condensed">
                            <thead>
                                <tr class="table-primary">
                                    <th style="width: 25%;">Species</th>
                                    <th style="width: 20%;">Looking for</th>
                                    <th style="width: 10%;">Origin</th>
                                    <th style="width: 20%;">Age</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($organizationWanteds as $wanted)
                                <tr>
                                   <td><a href="{{ route('wanted.show', $wanted->id) }}" title="Show wanted animal">{{ ($wanted->animal != null) ? $wanted->animal->common_name : '-' }}</a></td>
                                    <td>{{ $wanted->looking_field }}</td>
                                    <td>{{ $wanted->origin_field }}</td>
                                    <td>{{ $wanted->age_field }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $organizationWanteds->links() }}
                    @else
                        <p>No wanted records related with this institution.</p>
                    @endunless
                </div>

                <div class="tab-pane fade show" id="pending-offers" role="tabpanel" aria-labelledby="pending-offers-tab">
                    @unless($organizationPendingOffers->isEmpty())
                        <table class="table table-condensed">
                            <thead>
                                <tr class="table-primary">
                                    <th style="width: 15%;">Req No.</th>
                                    <th style="width: 50%;">Quant. & Species</th>
                                    <th style="width: 35%;">Client</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($organizationPendingOffers as $contactPendingOffer)
                                <tr>
                                    <td><a href="{{ route('offers.show', $contactPendingOffer->id) }}" title="Show pending offer">{{ $contactPendingOffer->full_number }}</a></td>
                                    <td>
                                        @if ($contactPendingOffer->offer_species->count() == 0)
                                            <span style="color: red;">No species added yet</span>
                                        @else
                                            @foreach ($contactPendingOffer->species_ordered as $species)
                                                {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td><a href="mailto:{{ $contactPendingOffer->client->email }}">{{ $contactPendingOffer->client->email }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $organizationPendingOffers->links() }}
                    @else
                        <p>No pending offers related with this institution.</p>
                    @endunless
                </div>

                <div class="tab-pane fade show" id="pending-orders" role="tabpanel" aria-labelledby="pending-orders-tab">
                    @unless($organizationPendingOrders->isEmpty())
                        <table class="table table-condensed">
                            <thead>
                                <tr class="table-primary">
                                    <th style="width: 15%;">Order No.</th>
                                    <th style="width: 45%;">Quant. & Species</th>
                                    <th style="width: 20%;">Client</th>
                                    <th style="width: 20%;">Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($organizationPendingOrders as $contactPendingOrder)
                                <tr>
                                    <td><a href="{{ route('orders.show', $contactPendingOrder->id) }}" title="Show pending order">{{ $contactPendingOrder->full_number }}</a></td>
                                    <td>
                                        @if ($contactPendingOrder->offer->offer_species->count() == 0)
                                            <span style="color: red;">No species added yet</span>
                                        @else
                                            @foreach ($contactPendingOrder->offer->species_ordered as $species)
                                                {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td><a href="mailto:{{ $contactPendingOrder->client->email }}">{{ $contactPendingOrder->client->email }}</a></td>
                                    <td><a href="mailto:{{ ($contactPendingOrder->supplier) ? $contactPendingOrder->supplier->email : 'izs@zoo-services.com' }}">{{ ($contactPendingOrder->supplier) ? $contactPendingOrder->supplier->email : 'izs@zoo-services.com' }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $organizationPendingOrders->links() }}
                    @else
                        <p>No pending orders related with this institution.</p>
                    @endunless
                </div>

                <div class="tab-pane fade show" id="realized-orders" role="tabpanel" aria-labelledby="realized-orders-tab">
                    @unless($organizationRealizedOrders->isEmpty())
                        <table class="table table-condensed">
                            <thead>
                                <tr class="table-primary">
                                    <th style="width: 15%;">Order No.</th>
                                    <th style="width: 45%;">Quant. & Species</th>
                                    <th style="width: 20%;">Client</th>
                                    <th style="width: 20%;">Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($organizationRealizedOrders as $contactRealizedOrder)
                                <tr>
                                   <td><a href="{{ route('orders.show', $contactRealizedOrder->id) }}" title="Show realized order">{{ $contactRealizedOrder->full_number }}</a></td>
                                    <td>
                                        @if ($contactRealizedOrder->offer->offer_species->count() == 0)
                                            <span style="color: red;">No species added yet</span>
                                        @else
                                            @foreach ($contactRealizedOrder->offer->species_ordered as $species)
                                                {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td><a href="{{ $contactRealizedOrder->client->email }}">{{ $contactRealizedOrder->client->email }}</a></td>
                                    <td><a href="{{ ($contactRealizedOrder->supplier) ? $contactRealizedOrder->supplier->email : 'izs@zoo-services.com' }}">{{ ($contactRealizedOrder->supplier) ? $contactRealizedOrder->supplier->email : 'izs@zoo-services.com' }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $organizationRealizedOrders->links() }}
                    @else
                        <p>No realized orders related with this institution.</p>
                    @endunless
                </div>
                <div class="tab-pane fade show" id="email" role="tabpanel" aria-labelledby="email-tab">
                    @include('inbox.table_show', ['email_show' => $emails])
                </div>
                <div class="tab-pane fade show active" id="email-received" role="tabpanel" aria-labelledby="email-received-tab">
                    @include('inbox.table_show', ['email_show' => $emails_received])
                </div>
            </div>
        </div>
    </div>

  </div>

  <div class="col-md-4">

  <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Contacts in institution</h5>
            @if (Auth::user()->hasPermission('contacts.create'))
                <div class="d-inline-block">
                    <a href="{{ route('organisations.createContact', [$organisation->id]) }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-fw fa-plus"></i> Add contact
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body p-1">
          @unless($organisation->contacts->isEmpty())
            <table class="table table-hover clickable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="border-top-0" style="width: 33%;">Name</th>
                        <th class="border-top-0" style="width: 33%;">Mobile</th>
                        <th class="border-top-0" style="width: 33%;">Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organisation->contacts as $contact)
                    <tr data-url="{{ route('contacts.show', $contact) }}">
                        <td>{{ $contact->full_name }}</td>
                        <td>{{ $contact->mobile_phone }}</td>
                        <td style="word-wrap: break-word; min-width: 110px;max-width: 110px; white-space:normal;">{{ $contact->email }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
          @else
            <p>No contacts added to this institution</p>
          @endunless
        </div>
    </div>

    @if (count($related_organizations) > 0 && !Auth::user()->hasRole('office'))
    <div class="card shadow border-left-danger mb-4">
        <div class="card-header">
            <h5>Duplicated institutions</h5>
        </div>
        <div class="card-body">
            There are institutions that has the same name or domain.<br />
            Do you want to merge?

            <select class="custom-select w-50" id="relatedOrganizationSelected" name="relatedOrganizationSelected">
                <option value="0">--Select institution--</option>
                @foreach ($related_organizations as $related_organization)
                    <option value="{{ $related_organization->id }}">{{ ($related_organization->type) ? $related_organization->name .'-'. $related_organization->type->key : $related_organization->name }}</option>
                @endforeach
            </select>
            <a href="#" id="mergeOption" class="btn btn-light" organizationId="{{$organisation->id}}">
                <i class="fas fa-fw fa-copy"></i> Merge
            </a>
        </div>
    </div>
    @endif

</div>

@endsection

@section('page-scripts')
<style>
    .searchOption {
        text-align: left;
        padding: 12px 5px;
        border-bottom: darkgray solid 1px;
        cursor: pointer;
    }
    .searchOption:hover {
        background: beige;
    }
</style>

<script type="text/javascript">

    window.axios.defaults.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content')

    const instituteContactSearchHandler = _.debounce((e) => {
        let resultsContainer = document.getElementById('results');
        let inputValue = e.target.value;
        const url = "{{ route('organisations.search') }}";

        if (inputValue.trim() !== '') {
            resultsContainer.innerHTML = '';

            axios.get(`${url}?query=${inputValue.trim()}`)
                .then(res => {
                    res.data.data.forEach(item => {
                        let option = document.createElement('p');
                        option.classList.add("searchOption");
                        if (item.name.trim() === ""){
                            option.textContent = `${item.type} #${item.id}`;
                        } else {
                            option.textContent = item.name;
                        }
                        option.id = `${item.id}-option`
                        option.onclick = () => {
                            document.getElementById("selectedLiveSearch").dataset.selected = item.id;
                            document.getElementById("selectedLiveSearch").dataset.type = item.type;
                            if (item.name.trim() === ""){
                                document.getElementById("selectedLiveSearch").textContent = `Selected: ${item.type} #${item.id}`;
                            } else {
                                document.getElementById("selectedLiveSearch").textContent = `Selected: ${item.name}`;
                            }
                        }
                        resultsContainer.appendChild(option);
                    })
                })
        } else {
            resultsContainer.innerHTML = '';
        }

    }, 300);

    const provideReplacementForOrganisation = async (id) => {
        let html = `
            <p>These records need to be transferred to another parent. These include: contacts, surplus, wanteds</p>
            <p id="selectedLiveSearch" style="font-weight: 500"></p>
            <small style="color:gray;">Search for institution or contact (min. 3 characters)</small>
            <input id="liveSearch" style="margin-top: 0;" placeholder="Search..." class="swal2-input">
            <input type="hidden" id="deleted_id" name="deleted_id" value="${id}"/>
            <div id="results" style="max-height: 200px; overflow-y: scroll;"></div>
        `;

        Swal.fire({
            title: "Orphan records detected",
            html: html,
            didOpen: () =>  document.getElementById('liveSearch').addEventListener('input', instituteContactSearchHandler),
            allowOutsideClick: false,
            showCloseButton: true
        }).then(res => {
            if (res.isConfirmed) {
                let to_delete_id = document.getElementById("deleted_id").value;
                let handover_id = document.getElementById("selectedLiveSearch").dataset.selected;
                let handover_type = document.getElementById("selectedLiveSearch").dataset.type;
                let query = new URLSearchParams({
                    handover_id,
                    handover_type,
                    to_delete_id
                })

                let url = "{{ route('organisations.destroy', ['organisation' => 'to_replace']) }}"
                    .replace("to_replace", to_delete_id + "?") + query.toString();

                axios.delete(url)
                    .then(res => {
                        window.location = "{{ route('organisations.index') }}";
                    })
            }
        })
    }

    const destroyOrganisation = async (e) => {
        const {isConfirmed} = await ConfirmChoice("Are you sure you want to delete this item?");

        if (isConfirmed) {
            let url = "{{ route('organisations.destroy', ['organisation' => $organisation->id]) }}";

            axios.delete(url)
                .then(res => {
                    if (!res.data.status) {
                        provideReplacementForOrganisation({{ $organisation->id }});
                    } else {
                        SuccessModal("Deleted organisation");
                    }
                })
        }
    }

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

          //Select2 level updated
          $('.level_type').on('change', function () {
            var level = $(this).val();
            var id = $(this).attr("data_organisation_id");
            var value_old = $(this).attr("data_value_old") || level;
            $(this).removeClass("level-" + value_old);
            $(this).addClass("level-" + level);
            $(this).attr("data_value_old", level);
            if(level !== null && id !== null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('organisations.editLevel') }}",
                    data: {
                        id: id,
                        level: level
                    },
                    success:function(data) {
                        if(typeof data.message != "undefined"){
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        }
                    }
                });
            }
        });

        // Catch window close
        window.addEventListener('beforeunload', (event) => {
            (event || window.event).preventDefault();
            (event || window.event).returnValue = "Are you sure you want to leave?";
        });

        $('#mergeOption').on('click', function() {
            var organization_id = $(this).attr('organizationId');
            var selected_organization = $('#relatedOrganizationSelected').val();

            if(selected_organization == 0)
                alert("You must select an institution to merge.");
            else {
                var url = '{{ route("organisations.compare", ["id1", "id2", "organization", "id3"]) }}';
                url = url.replace('id1', organization_id);
                url = url.replace('id2', selected_organization);
                url = url.replace('id3', organization_id);
                window.location = url;
            }
        });

    });

</script>

@endsection
