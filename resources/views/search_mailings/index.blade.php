@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterSearchMailing">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('search-mailings.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Search mailings sent out') }}</h1>
    <p class="text-white">List of all search mailings</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('search-mailings.removeFromSearchMailingSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($mailings->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 3%;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th style="width: 9%;">Date sent out</th>
                    <th style="width: 25%;">Related species</th>
                    <th style="width: 17%;">Belongs to</th>
                    <th style="width: 9%;">Reminder date</th>
                    <th style="width: 12%;">Times reminded</th>
                    <th style="width: 25%;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $mailings as $mailing )
                    <tr id="link{{$mailing->id}}" data-url="{{ route('search-mailings.edit', [$mailing->id]) }}" class="@if( $mailing->should_be_reminded ) text-danger @endif">
                        <td class="no-click">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="selector" value="{{ $mailing->id }}" />
                                @if (Auth::user()->hasPermission('wanted-clients.wanted-mailing'))
                                    <a class="ml-2" href="#" id="wantedEmailToSuppliers" idMailing="{{$mailing->id}}" idTriggered="{{$mailing->searchable_id}}" idAnimal="{{$mailing->animal_id}}" title="Wanted email to suppliers">
                                        <i class="fas fa-fw fa-envelope"></i>
                                    </a>
                                @endif
                                @if ($mailing->should_be_reminded)
                                    <i class="fas fa-bell text-danger ml-2" title="Remind offer before {{ $mailing->next_reminder_at }}"></i>
                                @endif
                            </div>
                        </td>
                        <td>{{ ($mailing->date_sent_out != null) ? date('d-m-Y', strtotime($mailing->date_sent_out)) : '' }}</td>
                        <td>{{ ($mailing->animal != null) ? $mailing->animal->common_name . '(' . $mailing->animal->scientific_name .')' : '' }}</td>
                        <td>
                            @if ($mailing->searchable_type != null)
                                {{ Str::upper($mailing->searchable_type) . (($mailing->searchable_type === 'offer') ? ': ' . $mailing->searchable->full_number : '') }}
                            @endif
                        </td>
                        <td>{{ ($mailing->next_reminder_at != null) ? date('d-m-Y', strtotime($mailing->next_reminder_at)) : '' }}</td>
                        <td>{{ $mailing->times_reminded }}</td>
                        <td>{{ $mailing->remarks }}</td>
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$mailings->links()}}
      @else
        <p> No search mailings </p>
      @endunless
    </div>
  </div>

  @include('search_mailings._modal', ['modalId' => 'filterSearchMailing'])

  @include('wanted.select_continent_country_modal', ['modalId' => 'selectContinentCountryModal'])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    //Select2 animal selection
    $('[name=filter_animal_id]').on('change', function () {
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
                    $('[name=filter_animal_id]').append(newOption);
                }
            });
        }
    });

    $('#deleteSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('search-mailings.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $(document).on('click', '#wantedEmailToSuppliers', function () {
        var idMailing = $(this).attr('idMailing');
        var idTriggered = $(this).attr('idTriggered');
        var idAnimal = $(this).attr('idAnimal');

        $('#selectContinentCountryForMailing').trigger('reset');

        $('#selectContinentCountryForMailing [name=search_mailing_id]').val(idMailing);
        $('#selectContinentCountryForMailing [name=triggered_id]').val(idTriggered);
        $('#selectContinentCountryForMailing [name=animal_id]').val(idAnimal);
        $('#selectContinentCountryModal').modal('show');
    });

    $(document).on('submit', '#selectContinentCountryForMailing', function (event) {
        event.preventDefault();

        var triggeredFrom = "search-mailings";
        var idMailing = $('#selectContinentCountryForMailing [name=search_mailing_id]').val();
        var idTriggered = $('#selectContinentCountryForMailing [name=triggered_id]').val();
        var idAnimal = $('#selectContinentCountryForMailing [name=animal_id]').val();
        var bodyText = $('#selectContinentCountryForMailing [name=select_body_text]:checked').val();
        var idArea = $('#selectContinentCountryForMailing [name=select_area]').val();
        var idCountry = $('#selectContinentCountryForMailing [name=select_country]').val();

        $('#selectContinentCountryModal').modal('hide');

        var url = "{{route('wanted.wantedEmailToSuppliers')}}?triggeredFrom=" + triggeredFrom + "&idTriggered=" + idTriggered + "&idMailing=" + idMailing + "&idAnimal=" + idAnimal + "&bodyText=" + bodyText + "&idArea=" + idArea + "&idCountry=" + idCountry;
        window.location = url;
    });

</script>

@endsection
