@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('interesting-websites.create'))
        <a href="{{ route('interesting-websites.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" id="filterIWebButton" class="btn btn-light" data-toggle="modal" data-target="#filterIWeb">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('interesting-websites.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('interesting-websites.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info-circle mr-2"></i> {{ __('Interesting websites') }}</h1>
  <p class="text-white">List of all interesting websites</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('interesting-websites.removeFromInterestingWebsiteSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

        @unless($interestingWebs->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered datatable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th>Site name</th>
                    <th>Url</th>
                    <th>Remarks</th>
                    <th>Category</th>
                    <th>Login username</th>
                    <th>Login password</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $interestingWebs as $iweb )
                <tr @if (Auth::user()->hasPermission('interesting-websites.update')) data-url="{{ route('interesting-websites.edit', [$iweb->id]) }}" @endif>
                    <td class="no-click">
                        <input type="checkbox" class="selector" value="{{ $iweb->id }}" />
                    </td>
                    <td>{{ $iweb->siteName }}</td>
                    <td class="no-click"><a href="//{{ $iweb->siteUrl }}" target="_blank"><u>{{ $iweb->siteUrl }}</u></a></td>
                    <td>{{ $iweb->siteRemarks }}</td>
                    <td>{{ $iweb->siteCategory }}</td>
                    <td>{{ $iweb->loginUsername }}</td>
                    <td>{{ Crypt::decryptString($iweb->loginPassword) }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        @else

            <p> No interesting websites.</p>

        @endunless
    </div>
  </div>

  @include('interesting_websites._modal', ['modalId' => 'filterIWeb'])

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
                url:"{{ route('interesting-websites.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
