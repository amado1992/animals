@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('wanted.index') }}">Wanted of clients</a></li>
    <li class="breadcrumb-item active">{{ $wanted->animal->common_name }}</li>
</ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-9">
        <div class="d-flex align-items-center">
            @if ($wanted->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$wanted->animal->id.'/'.$wanted->animal->catalog_pic))
                <img src="{{ asset('storage/animals_pictures/'.$wanted->animal->id.'/'.$wanted->animal->catalog_pic) }}" class="float-left mr-4 rounded" style="max-width:70px;" alt="" />
            @else
                @if(!empty($wanted->animal->imagen_first))
                    <img src="{{ asset('storage/animals_pictures/'.$wanted->animal->id.'/'.$wanted->animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                @else
                    <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                @endif
            @endif
            <div class="d-flex align-items-center">
                <h1 class="h1 text-white">{{ $wanted->animal->common_name }}</h1>
                <span class="text-white">&nbsp;-&nbsp;{{ $wanted->animal->scientific_name }}</span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('main-content')

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <h4>Wanted details</h4>
                <div class="d-flex">
                    @if (Auth::user()->hasPermission('wanted-clients.update'))
                        <a href="{{ route('wanted.edit', [$wanted->id]) }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-fw fa-edit"></i> Edit
                        </a>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            @if (Auth::user()->hasPermission('animals.read'))
                                <a class="dropdown-item" href="{{ route('animals.show', [$wanted->animal_id]) }}">View Animal</a>
                            @endif

                            @if (Auth::user()->hasPermission('wanted-clients.delete'))
                                {!! Form::open(['method' => 'DELETE', 'route' => ['wanted.destroy', $wanted->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                    <a href="#" class="dropdown-item text-danger" onclick="$(this).closest('form').submit();">Delete wanted</a>
                                {!! Form::close() !!}
                            @endif

                            @if (Auth::user()->hasPermission('wanted-clients.wanted-mailing'))
                                <a class="dropdown-item" href="#" id="wantedEmailToSuppliers" idWanted="{{$wanted->id}}" idAnimal="{{$wanted->animal_id}}" title="Wanted email to suppliers">Send wanted email to suppliers</a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="border-top-0">Looking for</th>
                            <th class="border-top-0">Origin</th>
                            <th class="border-top-0">Age group</th>
                            <th class="border-top-0">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $wanted->looking_field }}</td>
                            <td>{{ $wanted->origin_field }}</td>
                            <td>{{ $wanted->age_field }}</td>
                            <td style="word-wrap: break-word; min-width: 120px;max-width: 120px; white-space:normal;">{{ $wanted->remarks ?: '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <ul class="nav nav-tabs card-header-tabs" id="orderTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="related-surpluses-tab" data-toggle="tab" href="#relatedSurplusesTab" role="tab" aria-controls="relatedSurplusesTab" aria-selected="false"><i class="fas fa-fw fa-paw"></i> Related wanted records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="email-received-tab" data-toggle="tab" href="#email-received" role="tab" aria-controls="email-received" aria-selected="true"><i class="fas fa-fw fa-envelope"></i> Received emails</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false"><i class="fas fa-fw fa-envelope"></i> Sent Emails</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="relatedSurplusesTab" role="tabpanel" aria-labelledby="related-surpluses-tab">
                        <h4>{{ $animalRelatedWanted->total() - 1 }} related wanted records</h4>
                        @unless($animalRelatedWanted->isEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover clickable table-bordered">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Client</th>
                                        <th class="border-top-0">Looking for</th>
                                        <th class="border-top-0">Origin</th>
                                        <th class="border-top-0">Age group</th>
                                        <th class="border-top-0">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($animalRelatedWanted as $w)
                                    @if($w->id !== $wanted->id)
                                    <tr data-url="{{ route('wanted.show', [$w->id]) }}">
                                        <td>
                                            @if ($wanted->organisation != null)
                                                <span class="card-title mb-0">{{ $wanted->organisation->name }}</span><br>
                                                <span><em>({{ $wanted->organisation->email }})</em></span>
                                            @else
                                                <span class="card-title mb-0 text-danger">INSTITUTION NOT DEFINED</span>
                                            @endif
                                        </td>
                                        <td>{{ $w->looking_field }}</td>
                                        <td>{{ $w->origin_field }}</td>
                                        <td>{{ $w->age_field }}</td>
                                        <td>{{ $w->created_at->toDateString() }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $animalRelatedWanted->links() }}
                        @else
                            This animal doesn't occur in more wanted of clients
                        @endunless
                    </div>
                    <div class="tab-pane fade show" id="email" role="tabpanel" aria-labelledby="email-tab">
                        @include('inbox.table_show', ['email_show' => $emails])
                    </div>
                    <div class="tab-pane fade show" id="email-received" role="tabpanel" aria-labelledby="email-received-tab">
                        @include('inbox.table_show', ['email_show' => $emails_received])
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Client institution</h5>
                <div class="d-flex">
                    @if($wanted->organisation)
                        <a href="{{ route('organisations.show', [$wanted->organisation_id]) }}" class="btn btn-sm btn-dark">
                            <i class="fas fa-fw fa-search"></i> Show
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    @if($wanted->organisation)
                        <tr>
                            <td class="border-top-0"><b>Institution</b></td>
                            <td class="border-top-0">{{ $wanted->organisation->name }}</td>
                        </tr>
                        <tr>
                            <td class="border-top-0"><b>Address</b></td>
                            <td class="border-top-0">{{ $wanted->organisation->address }}, {{ $wanted->organisation->city }}</td>
                        </tr>
                        <tr>
                            <td class="border-top-0"><b>Country</b></td>
                            <td class="border-top-0">{{ $wanted->organisation->country->name }}</td>
                        </tr>
                    @else
                        <span class="card-title mb-0 text-danger">INSTITUTION NOT DEFINED</span>
                    @endif
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Extra information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td class="border-top-0"><b>Created at</b></td>
                        <td class="border-top-0">{{ ($wanted->created_at) ? $wanted->created_at->toDateTimeString() : '' }}</td>
                    </tr>
                    <tr>
                        <td><b>Internal remarks</b></td>
                        <td>{{ $wanted->intern_remarks ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@include('wanted.select_continent_country_modal', ['modalId' => 'selectContinentCountryModal'])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#selectContinentCountryForMailing [name=select_area]').on('change', function () {
            var value = $(this).val();

            $.ajax({
                type:'POST',
                url:"{{ route('countries.getCountriesByArea') }}",
                data:{
                    value: value,
                },
                success:function(data){
                    $('[name=select_country]').empty();
                    $('[name=select_country]').append('<option value="">- select country -</option>');
                    $.each(data.countries, function(key, value) {
                        $('[name=select_country]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        });

        $(document).on('click', '#wantedEmailToSuppliers', function () {
            var idWanted = $(this).attr('idWanted');
            var idAnimal = $(this).attr('idAnimal');

            $('#selectContinentCountryForMailing').trigger('reset');

            $('#selectContinentCountryForMailing [name=triggered_id]').val(idWanted);
            $('#selectContinentCountryForMailing [name=animal_id]').val(idAnimal);
            $('#selectContinentCountryModal').modal('show');
        });

        $(document).on('submit', '#selectContinentCountryForMailing', function (event) {
            event.preventDefault();

            var triggeredFrom = "wanted";
            var idTriggered = $('#selectContinentCountryForMailing [name=triggered_id]').val();
            var idAnimal = $('#selectContinentCountryForMailing [name=animal_id]').val();
            var bodyText = $('#selectContinentCountryForMailing [name=select_body_text]:checked').val();
            var idArea = $('#selectContinentCountryForMailing [name=select_area]').val();
            var idCountry = $('#selectContinentCountryForMailing [name=select_country]').val();

            $('#selectContinentCountryModal').modal('hide');

            var url = "{{route('wanted.wantedEmailToSuppliers')}}?triggeredFrom=" + triggeredFrom + "&idTriggered=" + idTriggered + "&idAnimal=" + idAnimal + "&bodyText=" + bodyText + "&idArea=" + idArea + "&idCountry=" + idCountry;
            window.location = url;
        });
    });

</script>

@endsection
