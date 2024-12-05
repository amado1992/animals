@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('surplus.index') }}">Surplus of surpliers</a></li>
    <li class="breadcrumb-item active">{{ $surplus->animal->common_name }}</li>
</ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-9">
        <div class="d-flex align-items-center">
            @if ($surplus->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic))
                <img src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic) }}" class="float-left mr-4 rounded" style="max-width:70px;" alt="" />
            @else
                @if(!empty($surplus->animal->imagen_first))
                    <img src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                @else
                    <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                @endif
            @endif
            <div class="d-flex align-items-center">
                <h1 class="h1 text-white">{{ $surplus->male_quantity }} - {{ $surplus->female_quantity }} - {{ $surplus->unknown_quantity }} | {{$surplus->animal->common_name}}</h1>
                <span class="text-white">&nbsp;-&nbsp;{{ $surplus->animal->scientific_name }}</span>
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
                <h4>Surplus details</h4>
                <div class="d-flex">
                    @if (Auth::user()->hasPermission('surplus-suppliers.update'))
                        <a href="{{ route('surplus.edit', [$surplus->id]) }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-fw fa-edit"></i> Edit
                        </a>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            @if (Auth::user()->hasPermission('animals.read'))
                            <a class="dropdown-item" href="{{ route('animals.show', [$surplus->animal_id]) }}">View Animal</a>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.delete'))
                                {!! Form::open(['method' => 'DELETE', 'route' => ['surplus.destroy', $surplus->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                    <a href="#" class="dropdown-item text-danger" onclick="$(this).closest('form').submit();">Delete surplus</a>
                                {!! Form::close() !!}
                            @endif
                            @if (!Auth::user()->hasRole('office'))
                                <a class="dropdown-item" href="{{ route('surplus.createOurSurplus', [$surplus->id]) }}">Create standard surplus</a>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.surplus-mailing'))
                                <a class="dropdown-item" href="{{ route('surplus.surplusEmailToClients', $surplus->id) }}">Send surplus email to clients</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('surplus.askMoreSurplusDetails', $surplus->id) }}">Ask supplier for more details</a>
                            <a class="dropdown-item" href="{{ route('surplus.detailsSurplusSpecimens', $surplus->id) }}">Ask more details of surplus-specimens</a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="border-top-0" style="width: 70px;">M</th>
                            <th class="border-top-0" style="width: 70px;">F</th>
                            <th class="border-top-0" style="width: 70px;">U</th>
                            <th class="border-top-0">Area</th>
                            <th class="border-top-0">Country</th>
                            <th class="border-top-0">Origin</th>
                            <th class="border-top-0">Age group</th>
                            <th class="border-top-0">Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $surplus->male_quantity }}</td>
                            <td>{{ $surplus->female_quantity }}</td>
                            <td>{{ $surplus->unknown_quantity }}</td>
                            <td>{{ ($surplus->area_region != null) ? $surplus->area_region->name : '' }}</td>
                            <td>{{ ($surplus->country != null) ? $surplus->country->name : '' }}</td>
                            <td>{{ $surplus->origin_field }}</td>
                            <td>{{ $surplus->age_field ?: '-' }}</td>
                            <td>{{ $surplus->size ?: '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <ul class="nav nav-tabs card-header-tabs" id="orderTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="related-surpluses-tab" data-toggle="tab" href="#relatedSurplusesTab" role="tab" aria-controls="relatedSurplusesTab" aria-selected="false"><i class="fas fa-fw fa-paw"></i> Related surpluses</a>
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
                        <h4>{{ $animalRelatedSurplus->total() - 1 }} related surpluses</h4>
                        @unless($animalRelatedSurplus->isEmpty())
                        <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                            <table class="table table-hover clickable table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="align-top text-center">Quantity</th>
                                        <th rowspan="2" class="align-top">Origin</th>
                                        <th rowspan="2" class="align-top">Age group</th>
                                        <th rowspan="2" class="align-top">Continent</th>
                                        <th colspan="5" class="align-top text-center">Cost price</th>
                                        <th rowspan="2" class="align-top">Location</th>
                                        <th rowspan="2" class="align-top">Created</th>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th class="text-center">M</th>
                                        <th class="text-center">F</th>
                                        <th class="text-center">U</th>
                                        <th></th>
                                        <th class="text-center">M</th>
                                        <th class="text-center">F</th>
                                        <th class="text-center">U</th>
                                        <th class="text-center">P</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($animalRelatedSurplus as $related)
                                    @if($related->id !== $surplus->id)
                                        <tr data-url="{{ route('surplus.show', [$related->id]) }}">
                                            <td class="text-center">{{ $related->quantityM >= 0 ? $related->quantityM : 'x' }}</td>
                                            <td class="text-center">{{ $related->quantityF >= 0 ? $related->quantityF : 'x' }}</td>
                                            <td class="text-center">{{ $related->quantityU >= 0 ? $related->quantityU : 'x' }}</td>
                                            <td>{{ $related->origin_field }}</td>
                                            <td>{{ $related->age_field }}</td>
                                            <td>{{ $related->location }}</td>
                                            <td>{{ $related->cost_currency }}</td>
                                            <td class="text-right">{{ number_format($related->costPriceM) }}.00</td>
                                            <td class="text-right">{{ number_format($related->costPriceF) }}.00</td>
                                            <td class="text-right">{{ number_format($related->costPriceU) }}.00</td>
                                            <td class="text-right">{{ number_format($related->costPriceP) }}.00</td>
                                            <td>@if($related->contact && $related->contact->organisation) {{ Str::limit($related->contact->organisation->city, 15) }}, {{ $related->contact->organisation->country->country_code }} @else - @endif</td>
                                            <td>@if ($related->created_at !== null) {{ $related->created_at->toDateString() }} @else Something went wrong, there is nog created date @endif</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $animalRelatedSurplus->links() }}
                        @else
                            This animal doesn't occur in more surplus of surpliers
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
                <h5>Surplier</h5>
                <div class="d-flex">
                    @if($surplus->organisation)
                        <a href="{{ route('organisations.show', [$surplus->organisation_id]) }}" class="btn btn-sm btn-dark">
                            <i class="fas fa-fw fa-search"></i> Show
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    @if($surplus->organisation)
                        <tr>
                            <td class="border-top-0"><b>Institution</b></td>
                            <td class="border-top-0">{{ $surplus->organisation->name }}</td>
                        </tr>
                        <tr>
                            <td class="border-top-0"><b>Address</b></td>
                            <td class="border-top-0">{{ $surplus->organisation->address }}, {{ $surplus->organisation->city }}</td>
                        </tr>
                        <tr>
                            <td class="border-top-0"><b>Country</b></td>
                            <td class="border-top-0">{{ $surplus->organisation->country->name }}</td>
                        </tr>
                        @if (!empty($surplus->organisation->level))
                            <tr>
                                <td class="border-top-0"><b>Level</b></td>
                                <td class="border-top-0">{{ $surplus->organisation->level ?? "" }}</td>
                            </tr>
                        @endif
                    @else
                        <tr>
                            <td colspan="2" class="border-top-0 text-danger"><b>No institution defined.</b></td>
                        </tr>
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
                        <td class="border-top-0">{{ ($surplus->created_at) ? $surplus->created_at->toDateTimeString() : '' }}</td>
                    </tr>
                    <tr>
                        <td><b>Remarks</b></td>
                        <td>{{ $surplus->remarks ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><b>Internal remarks</b></td>
                        <td>{{ $surplus->intern_remarks ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><b>Special conditions</b></td>
                        <td>{{ $surplus->special_conditions ?: '-' }}</td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>General Information</h5>
            </div>
            <div class="card-body">
                @if (Auth::user()->hasPermission('surplus-suppliers.upload-files'))
                    <div class="mb-3">
                        <form action="{{ route('surplus.upload') }}" class="dropzone" id="upload-dropzone">
                            @csrf
                            <input type="hidden" name="surplusId" value="{{ $surplus->id }}" />
                        </form>
                    </div>
                @endif
                <div class="row scrroll_style">
                    @foreach($surplus->surplus_pictures as $doc)
                        @php
                            $file = pathinfo($doc);
                        @endphp
                        @if ($file['extension'] == "jpeg" || $file['extension'] == "jpg" || $file['extension'] == "png" || $file['extension'] == "gif")
                            <div class="col-lg-3 col-sm-6 mb-1">
                                <div class="thumbnail">
                                    <div class="thumb" style="overflow: hidden; height: 95px;">
                                        <a href="{{Storage::url('surpluses_docs/'.$surplus->id.'/'.$file['basename'])}}" data-lightbox="1">
                                            <img src="{{Storage::url('surpluses_docs/'.$surplus->id.'/'.$file['basename'])}}" alt="" style="width: 100% !important; height: 100% !important; object-fit: cover;" class="img-fluid img-thumbnail">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if (Auth::user()->hasPermission('surplus-suppliers.delete-files'))
                                <a href="{{ route('surplus.delete_file', [$surplus->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')" style="margin: -9px 0px 0 -17px; z-index: 100; font-size: 16px;"><i class="mdi mdi-close-circle"></i></a>
                            @endif
                        @endif
                    @endforeach

                    @foreach($surplus->surplus_pictures as $doc)
                        @php
                            $file = pathinfo($doc);
                        @endphp
                        <div class="col-lg-12 col-sm-12 mb-1 mt-2">
                            @if ($file['extension'] != "jpeg" && $file['extension'] != "jpg" && $file['extension'] != "png" && $file['extension'] != "gif")
                                <img src="/img/file-icons/file.svg" height="25" alt="icon" class="me-2">
                                <a href="{{Storage::url('surpluses_docs/'.$surplus->id.'/'.$file['basename'])}}" class="text-dark" target="_blank">{{$file['basename']}}</a>
                                @if (Auth::user()->hasPermission('surplus-suppliers.delete-files'))
                                    <a href="{{ route('surplus.delete_file', [$surplus->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')" style="color: red;">(x)</a>,
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Picture surpluslist</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            @if (!empty($surplus->surplus_pictures_catalog))
                                @foreach($surplus->surplus_pictures_catalog as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if ($file['extension'] == "jpeg" || $file['extension'] == "jpg" || $file['extension'] == "png" || $file['extension'] == "gif")
                                        <div class="col-lg-12 col-sm-6 mb-1">
                                            <div class="thumbnail">
                                                <div class="thumb" style="overflow: hidden; height: 146px;">
                                                    <a href="{{Storage::url('surpluses_pictures/'.$surplus->id.'/'.$file['basename'])}}" data-lightbox="1">
                                                        <img src="{{Storage::url('surpluses_pictures/'.$surplus->id.'/'.$file['basename'])}}" alt="" style="width: 100% !important; height: 100% !important; object-fit: cover;" class="img-fluid img-thumbnail">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @if (Auth::user()->hasPermission('surplus-suppliers.delete-files'))
                                            <a href="{{ route('surplus.delete_file_catalog', [$surplus->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')" style="margin: -9px 0px 0 -17px; z-index: 100; font-size: 16px;"><i class="mdi mdi-close-circle"></i></a>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <div class="col-lg-12 col-sm-6 mb-1">
                                    <div class="thumbnail">
                                        <div class="thumb" style="overflow: hidden; height: 146px;">
                                            <img src="/img/catalog_default.jpg" alt="" style="width: 100% !important; height: 100% !important; object-fit: cover;" class="img-fluid img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="col-md-9">
                        @if (Auth::user()->hasPermission('surplus-suppliers.upload-files'))
                            <div class="mb-3">
                                <form action="{{ route('surplus.uploadPicture') }}" class="dropzone" id="upload-dropzone-catalog">
                                    @csrf
                                    <input type="hidden" name="surplusId" value="{{ $surplus->id }}" />
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if ($('#upload-dropzone-catalog').length) {
            // Now that the DOM is fully loaded, create the dropzone, and setup the
            // event listeners
            var surplusDropzone = new Dropzone("#upload-dropzone-catalog", {
                maxFilesize: 10 // 10mb
                /*autoProcessQueue: false*/
            });
            surplusDropzone.on("complete", function(file) {
                surplusDropzone.removeFile(file);
                if(file.xhr.responseText){
                    var error = JSON.parse(file.xhr.responseText);
                    if(error.errors){
                        $.each( error.errors, function( key, value ) {
                            $.NotificationApp.send("Error message!", value, 'top-right', '#fff', 'error');
                        });
                    }else{
                        $.NotificationApp.send("Success message!", "File uploaded successfully", 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }

    });

</script>

@endsection
