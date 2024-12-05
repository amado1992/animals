@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('interesting-websites.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add
      </a>
      <button type="button" id="filterIWebButton" class="btn btn-light" data-toggle="modal" data-target="#filterIWeb">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('interesting-websites.index') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      <button type="button" id="deleteSelectedItems" class="btn btn-light">
        <i class="fas fa-fw fa-window-close"></i> Delete
      </button>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info-circle mr-2"></i> {{ __('Interesting websites') }}</h1>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        <div role="tabpanel">
            <ul class="nav nav-tabs" id="iWebsTab" role="tablist">
                @foreach($iWebsCategories as $key => $value)

                    <li class="nav-item">
                        <a class="nav-link @if($key == $tab) active @endif" href="#{{ $key }}" role="tab" aria-controls="{{ $key }}"  data-toggle="tab">{{ $value }}</a>
                    </li>

                @endforeach
            </ul>
        </div>
        <div class="tab-content">
            @foreach($iWebsCategories as $key => $value)

                <div class="tab-pane fade @if($key == $tab) show active @endif" id="{{ $key }}" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                                    <th></th>
                                    <th>Site name</th>
                                    <th>Url</th>
                                    <th>Remarks</th>
                                    <th>Login username</th>
                                    <th>Login password</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $iwebs as $iweb )
                                    @if($iweb->siteCategory == $key)
                                        <tr>
                                            <td><input type="checkbox" class="selector" value="{{ $iweb->id }}" /></td>
                                            <td>
                                                <a href="{{ route('iwebs.edit', [$iweb->id]) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                            <td>{{ $iweb->siteName }}</td>
                                            <td>{{ $iweb->siteUrl }}</td>
                                            <td>{{ $iweb->siteRemarks }}</td>
                                            <td>{{ $iweb->loginUsername }}</td>
                                            <td>{{ $iweb->loginPassword }}</td>
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

  @include('iwebs._modal', ['modalId' => 'filterIWeb'])

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

    $(function () {
        $('#iWebsTab li:a').tab('show');
    })

    $('#filterIWebButton').on('click', function () {
        var selectedCategory = $('.tab-content .active').attr('id');

        $('#filterIWeb [name=category]').val(selectedCategory);
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
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('iwebs.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
