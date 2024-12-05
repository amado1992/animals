@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('directories.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add Directory
      </a>
      <button type="button" id="deleteSelectedItems" class="btn btn-dark">
        <i class="fas fa-fw fa-window-close"></i> Delete
      </button>
  </div>

  <h1 class="h1 text-white">{{ __('Directories') }}</h1>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($directories->isEmpty())
        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th></th>
              <th>Title</th>
              <th>Name</th>
              <th>Icon</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $directories as $directory )
            <tr>
                <td>
                    <input type="checkbox" class="selector" value="{{ $directory->id }}" />
                    <a href="{{ route('directories.edit', [$directory->id]) }}" title="Show institution" style="position: absolute; margin: -2px 0 3px 5px;"><i class="fas fa-fw fa-edit"></i></a>
                </td>
                <td>{{ $directory->title }}</td>
                <td>{{ $directory->name }}</td>
                <td>
                    <div class="list-group b-0 mail-list">
                        <span class="{{ $directory->icon }} me-2" style="color: #000;"></span>
                    </div>
                </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p> No directories are added yet </p>
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
                url:"{{ route('directories.deleteItems') }}",
                data:{items: ids},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }
    });
</script>

@endsection

