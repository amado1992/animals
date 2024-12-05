@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('guidelines.create'))
        <a href="{{ route('offers-reservations-contracts.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterStandardDocument">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('offers-reservations-contracts.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('guidelines.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Offers, reservations and contracts') }}</h1>
  <p class="text-white">List of all standard documents related with: offers, reservations and contracts</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('offers-reservations-contracts.removeFromOfferReservationContractSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($documents->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 5%;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th style="width: 20%;">Subject</th>
                    <th style="width: 30%;">Remark</th>
                    <th style="width: 25%;">Document</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $documents as $document )
                <tr id="link{{$document->id}}" @if (Auth::user()->hasPermission('guidelines.update')) data-url="{{ route('offers-reservations-contracts.edit', [$document->id]) }}" @endif>
                    <td class="no-click">
                        <input type="checkbox" class="selector" value="{{ $document->id }}" />
                    </td>
                    <td>{{ $document->subject }}</td>
                    <td>{{ $document->remark }}</td>
                    <td class="no-click">
                        <a href="{{ $document->file_url }}" target="_blank">
                            {{ $document->related_filename }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$documents->links()}}
      @else
        <p> No standard documents </p>
      @endunless
    </div>
  </div>

  @include('offers_reservations_contracts._modal', ['modalId' => 'filterStandardDocument'])

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
                url:"{{ route('offers-reservations-contracts.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
