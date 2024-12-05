@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('guidelines.create'))
        <a href="{{ route('protocols.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterProtocols">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('protocols.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('guidelines.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Protocols') }}</h1>
  <p class="text-white">List of all protocols</p>

  <div class="d-flex flex-row items-center mb-2">
    <label class="text-white  mr-1">Category:</label>
    {!! Form::open(['id' => 'protocolsCategoryForm', 'route' => 'protocols.categoryProtocols', 'method' => 'GET']) !!}
        <select class="custom-select custom-select-sm w-auto" id="categoryField" name="categoryField">
            @foreach ($categories as $categoryKey => $categoryValue)
                <option value="{{ $categoryKey }}" @if(isset($categoryField) && $categoryField == $categoryKey) selected @endif>{{ Str::ucfirst($categoryValue) }}</option>
            @endforeach
        </select>
    {!! Form::close() !!}
  </div>
@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('protocols.removeFromProtocolSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($protocols->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 5%;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 20%;">Subject</th>
                    <th style="width: 30%;">Remark</th>
                    <th style="width: 25%;">Document</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $protocols as $protocol )
                <tr id="link{{$protocol->id}}" @if (Auth::user()->hasPermission('guidelines.update')) data-url="{{ route('protocols.edit', [$protocol->id]) }}" @endif>
                    <td class="no-click">
                        <input type="checkbox" class="selector" value="{{ $protocol->id }}" />
                    </td>
                    <td>{{ Str::ucfirst($protocol->section_field) }}</td>
                    <td>{{ $protocol->subject }}</td>
                    <td>{{ $protocol->remark }}</td>
                    <td class="no-click">
                        <a href="{{ $protocol->file_url }}" target="_blank">
                            {{ $protocol->related_filename }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$protocols->links()}}
      @else
        <p> No protocols </p>
      @endunless
    </div>
  </div>

  @include('protocols._modal', ['modalId' => 'filterProtocols'])

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

    $('#categoryField').on('change', function () {
        $('#protocolsCategoryForm').submit();
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
                url:"{{ route('protocols.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
