@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        <a href="{{ route('default-text-task.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Default Text') }}</h1>
    <p class="text-white">List of all Default Text</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('domain-name-link.removeFromDomainNameSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($default_text_task->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 3%;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th style="width: 12%;">Text</th>
                    <th style="width: 7%;">Date crated</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $default_text_task as $row )
                <tr id="link{{$row->id}}" data-url="{{ route('default-text-task.edit', [$row->id]) }}">
                    <td class="no-click">
                        <input type="checkbox" class="selector" value="{{ $row->id }}" />
                    </td>
                    <td>{{ $row->text ?? "--" }}</td>
                    <td>{{ ($row->created_at != null) ? date('d-m-Y', strtotime($row->created_at)) : '' }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$default_text_task->links()}}
      @else
        <p> No Default Text </p>
      @endunless
    </div>
  </div>

  @include('domain_name._modal', ['modalId' => 'filterDomainName'])

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
        var _btn = $(this);
        var txt = _btn.html();
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0){
            _btn.html(txt);
            alert("You must select items to delete.");
        }
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            _btn.html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>").data("disabled", true);
            $.ajax({
                type:'POST',
                url:"{{ route('default-text-task.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
