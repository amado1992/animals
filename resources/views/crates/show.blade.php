@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('crates.index') }}">Crates</a></li>
    <li class="breadcrumb-item active">{{ $crate->name }}</li>
</ol>
@endsection

@section('header-content')
<h1 class="h1 text-white mb-3"><i class="fas fa-fw fa-box-open mr-2"></i>{{ $crate->name }}</h1>
@endsection

@section('main-content')
<div class="row">
    <div class="col-md-7">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h4>Crate details</h4>
                @if (Auth::user()->hasPermission('crates.update'))
                <div class="d-inline-flex">
                    <a href="{{ route('crates.edit', [$crate->id]) }}" class="btn btn-dark ml-2">
                        <i class="fas fa-fw fa-pen"></i> Edit
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table class="table">
                            <tr>
                                <td class="border-top-0"><b>Currency</b></td>
                                <td class="border-top-0">{{ $crate->currency }}</td>
                            </tr>
                            <tr>
                                <td><b>Cost price</b></td>
                                <td @if ($crate->cost_price_changed) class='text-danger' @endif>
                                    {{ number_format($crate->cost_price, 2, '.', '') }}
                                </td>
                            </tr>
                            <tr>
                                <td><b>Sale Price</b></td>
                                <td @if ($crate->sale_price_changed) class='text-danger' @endif>
                                    {{ number_format($crate->sale_price, 2, '.', '') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col">
                        <table class="table">
                            <tr>
                                <td class="border-top-0"><b>Size</b></td>
                                <td class="border-top-0">{{ $crate->full_dimensions }}</td>
                            </tr>
                            <tr>
                                <td><b>Vol.weight</b></td>
                                <td>{{ number_format($crate->weight, 2, '.', '') }} Kg</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col">
                        <table class="table">
                            <tr>
                                <td class="border-top-0"><b>IATA code</b></td>
                                <td class="border-top-0">{{ $crate->iata_code }}</td>
                            </tr>
                            <tr>
                                <td><b>Quantity of animals</b></td>
                                <td>{{ $crate->animal_quantity }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h4>Last offers where crate is used</h4>
            </div>
            <div class="card-body">
                @unless($crate->offers_species->isEmpty())
                    <table class="table table-hover clickable table-condensed">
                        <thead>
                            <tr>
                                <th>Offer</th>
                                <th>Size</th>
                                <th>Sales price</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($crate->offers_species->take(5) as $offer_specie)
                            <tr data-url="{{ route('offers.show', [$offer_specie->offer_species->offer->id]) }}">
                                <td>{{ $offer_specie->offer_species->offer->offer_number }}</td>
                                <td>{{ $offer_specie->length }} x {{ $offer_specie->wide }} x {{ $offer_specie->height }} cm</td>
                                <td>{{ number_format($offer_specie->sale_price, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Crate is not used on any offer yet.</p>
                @endunless
            </div>
        </div>
    </div>

    <div class="col-md-5">

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Animals</h5>
                <div class="d-inline-flex">
                    <a href="#" class="btn btn-sm btn-dark mr-2" data-toggle="modal" data-target="#addAnimalsToCrate"><i class="fas fa-plus"></i> Add species</a>
                    @unless($animals->isEmpty())
                    <a href="#" id="deleteSpeciesFromCrate" data-id="{{$crate->id}}" class="btn btn-sm btn-dark "><i class="fas fa-window-close"></i> Delete</a>
                    @endunless
                </div>
            </div>
            <div class="card-body">
            @unless($animals->isEmpty())
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th class="border-top-0"><input type="checkbox" id="selectAllCrateSpecies" name="selectAllCrateSpecies" /></th>
                            <th class="border-top-0">Species</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($animals as $animal)
                        <tr>
                            <td class="border-top-0"><input type="checkbox" class="selector" value="{{ $animal->id }}" /></td>
                            <td class="border-top-0">{{ $animal->common_name }} ({{ $animal->scientific_name }})</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{ $animals->links() }}
            @else
                <p>No animals are selected for this crate yet.</p>
            @endunless
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Documents</h5>
            </div>
            <div class="card-body">
                @if (Auth::user()->hasPermission('crates.upload-files'))
                    <div class="mb-3">
                        <form action="{{ route('crates.upload') }}" class="dropzone" id="upload-dropzone">
                            @csrf
                            <input type="hidden" name="crateId" value="{{ $crate->id }}" />
                        </form>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table ">
                        @foreach(Storage::allFiles('public/crates_docs/'.$crate->id) as $doc)
                            @php
                                $file = pathinfo($doc);
                            @endphp
                            <tr>
                                <td><a href="{{Storage::url('crates_docs/'.$crate->id.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a></td>
                                <td class="text-right">
                                    @if (Auth::user()->hasPermission('crates.delete-files'))
                                        <a href="{{ route('crates.delete_file', [$crate->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('crates.add_species_to_crate_modal', ['modalId' => 'addAnimalsToCrate', 'crateId' => $crate->id])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $(':checkbox:checked').prop('checked', false);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //Select2 animal selection
        $('#addAnimalsToCrate [name=animal_id]').on('change', function () {
            var animalId = $(this).val();

            if(animalId != null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('api.animal-by-id') }}",
                    data: {
                        id: animalId,
                    },
                    success:function(data) {
                        // create the option and append to Select2
                        var newOption = new Option(data.animal.common_name.trim(), data.animal.id, true, true);
                        // Append it to the select
                        $('#addAnimalsToCrate [name=animal_id]').append(newOption);
                    }
                });
            }
        });

        $('#selectAllCrateSpecies').on('change', function () {
            $(":checkbox.selector").prop('checked', this.checked);
        });

        $('#addAnimalsToCrate').on('hidden.bs.modal', function () {
            $("#addAnimalsToCrate .animal-select2").val(null).trigger('change');
            $(this).find('form').trigger('reset');
        });

        $('#addAnimalsToCrate #tempSelectedSpecies').on('click', function () {
            var animalId = $('#addAnimalsToCrate [name=animal_id]').val();

            if (animalId == 0)
                alert("You must select a species.");
            else {
                var speciesAlreadyAdded = false;
                $('#addAnimalsToCrate #selectedSpecies tr.item').each(function(){
                    var row = $(this);
                    if(row.attr('animalId') == animalId) {
                        speciesAlreadyAdded = true;
                        return;
                    }
                });

                if(speciesAlreadyAdded)
                    alert("This species was already added.");
                else {
                    $.ajax({
                        type:'POST',
                        url:"{{ route('api.animal-by-id') }}",
                        data: {
                            id: animalId
                        },
                        success:function(data){
                            $("#addAnimalsToCrate #selectedSpecies").append('<tr class="item" animalId="' + animalId + '">' +
                                '<td><a href="#" class="remove-tr"><i class="fas fa-window-close"></i></a></td>' +
                                '<td>' + data.animal.scientific_name + '</td>' +
                                '<td></td>' +
                            '</tr>');

                            $("#addAnimalsToCrate [name=animal_id]").val(null).trigger('change');
                        }
                    });
                }
            }
        });

        $(document).on('click', '.remove-tr', function(){
            $(this).parents('tr').remove();
        });

        $('#addAnimalsToCrate').on('submit', function (event) {
            event.preventDefault();

            var selectedSpecies = [];
            $('#addAnimalsToCrate #selectedSpecies tr.item').each(function(){
                var row = $(this);
                selectedSpecies.push(row.attr('animalId'));
            });

            $.ajax({
                type:'POST',
                url:"{{ route('crates.addSpeciesToCrate') }}",
                data:{
                    items: selectedSpecies,
                    crateId: $('#addAnimalsToCrate [name=crate_id]').val()
                },
                success:function(data){
                    var url = data.url;
                    window.location = url;
                }
            });
        });

        $('#deleteSpeciesFromCrate').on('click', function () {
            var crateId = $(this).data('id');

            var selected_crate_species = [];
            $(":checked.selector").each(function(){
                selected_crate_species.push($(this).val());
            });

            if(selected_crate_species.length == 0)
                alert("You must select species to delete from this crate.");
            else if(confirm("Are you sure that you want to delete the selected species from this crate?")) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('crates.deleteSpeciesFromCrate') }}",
                    data:{
                        id: crateId,
                        items: selected_crate_species
                    },
                    success:function(data) {
                        var url = data.url;
                        window.location = url;
                    }
                });
            }
        });

    });
</script>

@endsection
