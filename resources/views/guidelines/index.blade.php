@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('guidelines.create'))
        <a href="{{ route('guidelines.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterGuideline">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('guidelines.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('guidelines.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Guidelines') }}</h1>
  <p class="text-white">List of all guidelines</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('guidelines.removeFromGuidelineSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($guidelines->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="5%;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <th style="10%;">Category</th>
                    <th style="25%;">Subject</th>
                    <th style="25%;">Remark</th>
                    <th style="35%;">Document</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $guidelines as $guideline )
                <tr id="link{{$guideline->id}}" @if (Auth::user()->hasPermission('guidelines.update')) data-url="{{ route('guidelines.edit', [$guideline->id]) }}" @endif>
                    <td class="no-click">
                        <input type="checkbox" class="selector" value="{{ $guideline->id }}" />
                    </td>
                    <td>{{ $guideline->category }}</td>
                    <td>{{ $guideline->subject }}</td>
                    <td>{{ $guideline->remark }}</td>
                    <td class="no-click">
                        <a href="{{ $guideline->file_url }}" target="_blank">
                            {{ $guideline->related_filename }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$guidelines->links()}}
      @else
        <p> No guidelines </p>
      @endunless
    </div>
  </div>

  @include('guidelines._modal', ['modalId' => 'filterGuideline'])

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
                url:"{{ route('guidelines.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
