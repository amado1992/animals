@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('zoo-associations.create'))
        <a href="{{ route('zoo-associations.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterZooAssociation">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('zoo-associations.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('zoo-associations.create'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
      @if (Auth::user()->hasPermission('zoo-associations.export-survey'))
        <a href="{{ route('zoo-associations.export') }}" class="btn btn-light">
            <i class="fas fa-fw fa-save"></i> Export
        </a>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Zoo associations') }}</h1>
  <p class="text-white">List of all zoo associations</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('zoo-associations.removeFromZooAssociationSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($zooAssociations->isEmpty())
      <div class="table-responsive">
        <table class="table clickable table-hover table-bordered datatable" style="width:100%" cellspacing="0">
          <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                <th>Area</th>
                <th>Name</th>
                <th>Website</th>
                <th>Status</th>
                <th>Started on</th>
                <th>Remark</th>
                <th>Checked on</th>
                <th>Checked by</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $zooAssociations as $za )
            <tr @if (Auth::user()->hasPermission('zoo-associations.update')) data-url="{{ route('zoo-associations.edit', [$za->id]) }}" @endif>
                <td class="no-click">
                    <input type="checkbox" class="selector" value="{{ $za->id }}" />
                </td>
                <td>{{ $za->area }}</td>
                <td>{{ $za->name }}</td>
                <td class="no-click"><a href="//{{ $za->website }}" target="_blank"><u>{{ $za->website }}</u></a></td>
                <td>{{ $za->status }}</td>
                <td>{{ ($za->started_date != null) ? date('Y-m-d', strtotime($za->started_date)) : '' }}</td>
                <td>{{ $za->remark }}</td>
                <td>{{ ($za->checked_date != null) ? date('Y-m-d', strtotime($za->checked_date)) : '' }}</td>
                <td>{{ ($za->user != null) ? $za->user->name : "" }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p>No zoo associations.</p>

      @endunless
    </div>
  </div>

  @include('zoo_associations._modal', ['modalId' => 'filterZooAssociation'])

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
                url:"{{ route('zoo-associations.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
