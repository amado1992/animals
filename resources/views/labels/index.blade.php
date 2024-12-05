@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('labels.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add labels
      </a>
      <button type="button" id="deleteSelectedItems" class="btn btn-dark">
        <i class="fas fa-fw fa-window-close"></i> Delete
      </button>
  </div>

  <h1 class="h1 text-white">{{ __('Labels') }}</h1>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($labels->isEmpty())
        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th></th>
              <th>Tittle</th>
              <th>Name</th>
              <th>Color</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $labels as $label )
            <tr>
                <td>
                    <input type="checkbox" class="selector" value="{{ $label->id }}" />
                    <a href="{{ route('labels.edit', [$label->id]) }}" title="Show institution" style="position: absolute; margin: -2px 0 3px 5px;"><i class="fas fa-fw fa-edit"></i></a>
                </td>
                <td>{{ $label->title }}</td>
                <td>{{ $label->name }}</td>
                <td>
                    <div class="list-group b-0 mail-list">
                        <span class="mdi mdi-circle me-2" style="color: {{ $label->color }}"></span>
                    </div>
                </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p> No labels are added yet </p>
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
            var deleteSelectedItems = $('#deleteSelectedItems').html();
            $('#deleteSelectedItems').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('labels.deleteItems') }}",
                data:{items: ids},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        $('#deleteSelectedItems').html(deleteSelectedItems);
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

