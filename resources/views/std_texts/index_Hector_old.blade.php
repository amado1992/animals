@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('standard-texts.create'))
        <a href="{{ route('std-texts.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" id="filterStdTextButton" class="btn btn-light" data-toggle="modal" data-target="#filterStdText">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('std-texts.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('standard-texts.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Standard texts') }}</h1>
  <p class="text-white">List of all standard texts</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('std-texts.removeFromStdTextSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

        <div role="tabpanel">
            <ul class="nav nav-tabs" id="stdTextsTab" role="tablist">
                @foreach($stdTextsCategories as $key => $value)
                    <li class="nav-item">
                        <a class="nav-link @if($key == $tab) active @endif" href="#{{ $key }}" role="tab" aria-controls="{{ $key }}" data-toggle="tab">{{ $value }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="tab-content">
            @foreach($stdTextsCategories as $key => $value)
                <div class="tab-pane fade @if($key == $tab) show active @endif" id="{{ $key }}" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 10px;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                                    <th style="width: 40px;">Date modified</th>
                                    <th style="width: 30px;">Code</th>
                                    <th style="width: 80px;">Name</th>
                                    <th style="width: 80px;">Remarks</th>
                                    <th style="width: 150px;">English text</th>
                                    <th style="width: 150px;">Spanish text</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $stdTexts as $stdText )
                                    @if($stdText->category == $key)
                                        <tr @if (Auth::user()->hasPermission('standard-texts.update')) data-url="{{ route('std-texts.edit', [$stdText->id]) }}" @endif>
                                            <td class="no-click">
                                                <input type="checkbox" class="selector" value="{{ $stdText->id }}" />
                                            </td>
                                            <td>{{ ($stdText->updated_at != null) ? date('Y-m-d', strtotime($stdText->updated_at)) : '' }}</td>
                                            <td>{{ $stdText->code }}</td>
                                            <td>{{ $stdText->name }}</td>
                                            <td>{{ $stdText->remarks }}</td>
                                            <td>{!! $stdText->english_text !!}</td>
                                            <td>{!! $stdText->spanish_text !!}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
  </div>

  @include('std_texts._modal', ['modalId' => 'filterStdText'])

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

    /*$(function () {
        $('#stdTextsTab li:a').tab('show');
    })*/

    $('#filterStdTextButton').on('click', function () {
        var selectedCategory = $('.tab-content .active').attr('id');

        $('#filterStdText [name=category]').val(selectedCategory);
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
                url:"{{ route('std-texts.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection

