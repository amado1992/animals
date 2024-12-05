@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('dashboards.create') }}?type=main" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add Main Block
      </a>
      <a href="{{ route('dashboards.create') }}?type=mainLink" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add Main Link
      </a>
      <a href="{{ route('dashboards.create') }}?type=dataList" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add Data List
      </a>
      <a href="{{ route('dashboards.create') }}?type=directory" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add Directory
      </a>
      <a href="{{ route('dashboards.create') }}?type=link" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add Link
      </a>
      <button type="button" id="deleteSelectedItems" class="btn btn-dark">
        <i class="fas fa-fw fa-window-close"></i> Delete
      </button>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-globe-americas mr-2"></i> {{ __('Dashboards') }}</h1>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($dashboards->isEmpty())
        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th></th>
              <th>Name</th>
              <th>Title</th>
              <th>Main</th>
              <th>Parent</th>
              <th>Type Style</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $dashboards as $dashboard )
                <tr>
                    <td>
                        <input type="checkbox" class="selector" value="{{ $dashboard->id }}" />
                        <a href="{{ route('dashboards.edit', [$dashboard->id]) }}" title="Show institution" style="position: absolute; margin: -2px 0 3px 5px;"><i class="fas fa-fw fa-edit"></i></a>
                    </td>
                    <td>{{ $dashboard->name }}</td>
                    <td>{{ $dashboard->title }}</td>
                    <td>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch" disabled name="main"
                            @if (!empty($dashboard->main) && $dashboard->main == 1)
                                checked
                            @endif>
                            <label class="custom-control-label disabled" for="customSwitch"></label>
                        </div>
                    </td>
                    <td>{{ $dashboard->parent->title ?? "" }}</td>
                    <td>{{ $dashboard->type_style ?? "" }}</td>
                </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p> No origin are added yet </p>
      @endunless
    </div>
  </div>

@endsection

@section('page-scripts')

<script type="text/javascript">
    $('#deleteSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $('#deleteSelectedItems').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('dashboards.deleteDashboards') }}",
                data:{items: ids},
                success: function(data){
                    if (data.message)
                        alert(data.message);

                    location.reload();
                }
            });
        }
    });
</script>

@endsection

