@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterContactsDeleted">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('contacts-deleted.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        <a id="restoreSelectedItems" href="#" class="btn btn-light">
            <i class="fas fa-fw fa-trash-restore"></i> Restore
        </a>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Contacts deleted') }}</h1>
    <p class="text-white">Here you can manage the deleted contacts</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('contacts-deleted.removeFromContactDeletedSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($contacts->isEmpty())
      <div class="table-responsive mb-2" style="overflow-x: auto; overflow-y: hidden;">
        <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
                <th style="width: 20px;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                <th>Institution</th>
                <th>Institution type</th>
                <th>Country</th>
                <th>Contact Person</th>
                <th>Email address</th>
                <th>Phone number</th>
                <th>Mobile number</th>
                <th>Website</th>
                <th>Mailing category</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $contacts as $contact )
            <tr data-url="{{ route('contacts-deleted.show', [$contact->id]) }}">
                <td class="no-click">
                    <input type="checkbox" class="selector" value="{{ $contact->id }}" />
                </td>
                <td>{{ ($contact->organisation != null) ? $contact->organisation->name : '' }}</td>
                <td>{{ ($contact->organisation != null) ? $contact->organisation->organisation_type : '' }}</td>
                <td>{{ ($contact->country != null) ? $contact->country->name : '' }}</td>
                <td>{{ $contact->fullname }}</td>
                <td>{{ $contact->email }}</td>
                <td>{{ ($contact->organisation != null) ? $contact->organisation->phone : '' }}</td>
                <td>{{ $contact->mobile_phone }}</td>
                <td>{{ $contact->website }}</td>
                <td>{{ $contact->mailing_category }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{$contacts->links()}}
      @else
        <p> There are not deleted contacts. </p>
      @endunless
    </div>
  </div>

  @include('contacts_deleted.filter_modal', ['modalId' => 'filterContactsDeleted'])

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

    $('#restoreSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to restore.");
        else if(confirm("Are you sure that you want to restore the selected items?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('contacts-deleted.restore') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });
</script>

@endsection
