@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        <a href="{{ route('mailings.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterMailing">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('mailings.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Mailings sent out') }}</h1>
    <p class="text-white">List of all mailings</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('mailings.removeFromMailingSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($mailings->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 3%;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th style="width: 12%;">Subject</th>
                    <th style="width: 7%;">Date crated</th>
                    <th style="width: 7%;">Date sent out</th>
                    <th style="width: 7%;">Language</th>
                    <th style="width: 4%;">Level</th>
                    <th style="width: 10%;">Institution type</th>
                    <th style="width: 10%;">Part of world</th>
                    <th style="width: 10%;">Exclude continents</th>
                    <th style="width: 10%;">Exclude countries</th>
                    <th style="width: 10%;">Remarks</th>
                    <th style="width: 10%;">Template</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $mailings as $mailing )
                <tr id="link{{$mailing->id}}" data-url="{{ route('mailings.edit', [$mailing->id]) }}">
                    <td class="no-click">
                        <input type="checkbox" class="selector" value="{{ $mailing->id }}" />
                    </td>
                    <td>{{ $mailing->subject }}</td>
                    <td>{{ ($mailing->date_created != null) ? date('d-m-Y', strtotime($mailing->date_created)) : '' }}</td>
                    <td>{{ ($mailing->date_sent_out != null) ? date('d-m-Y', strtotime($mailing->date_sent_out)) : '' }}</td>
                    <td>{{ $mailing->language }}</td>
                    <td>{{ $mailing->institution_level }}</td>
                    <td>{{ $mailing->institution_types }}</td>
                    <td>{{ $mailing->part_of_world }}</td>
                    <td>{{ $mailing->exclude_continents }}</td>
                    <td>{{ $mailing->exclude_countries }}</td>
                    <td>{{ $mailing->remarks }}</td>
                    <td class="no-click">
                        <a href="{{ $mailing->file_url }}" target="_blank">
                            {{ $mailing->mailing_template }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$mailings->links()}}
      @else
        <p> No mailings </p>
      @endunless
    </div>
  </div>

  @include('mailings._modal', ['modalId' => 'filterMailing'])

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
                url:"{{ route('mailings.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
