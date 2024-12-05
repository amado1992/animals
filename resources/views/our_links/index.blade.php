@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('interesting-websites.create'))
        <a href="{{ route('our-links.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" id="filterOurLinkButton" class="btn btn-light" data-toggle="modal" data-target="#filterOurLink">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('our-links.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('interesting-websites.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info-circle mr-2"></i> {{ __('Our links') }}</h1>
  <p class="text-white">List of all our links</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('our-links.removeFromOurLinkSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

        @unless($ourLinks->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered datatable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th>Site name</th>
                    <th>Url</th>
                    <th>Remarks</th>
                    <th>Login username</th>
                    <th>Login password</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $ourLinks as $ourLink )
                <tr @if (Auth::user()->hasPermission('interesting-websites.update')) data-url="{{ route('our-links.edit', [$ourLink->id]) }}" @endif>
                    <td class="no-click">
                        <input type="checkbox" class="selector" value="{{ $ourLink->id }}" />
                    </td>
                    <td>{{ $ourLink->siteName }}</td>
                    <td class="no-click"><a href="//{{ $ourLink->siteUrl }}" target="_blank"><u>{{ $ourLink->siteUrl }}</u></a></td>
                    <td>{{ $ourLink->siteRemarks }}</td>
                    <td>{{ $ourLink->loginUsername }}</td>
                    <td>{{ Crypt::decryptString($ourLink->loginPassword) }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        @else

            <p> No links.</p>

        @endunless
    </div>
  </div>

  @include('our_links._modal', ['modalId' => 'filterOurLink'])

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
                url:"{{ route('our-links.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
