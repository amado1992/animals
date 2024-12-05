@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('origins.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add origin
      </a>
      <button type="button" id="deleteSelectedItems" class="btn btn-dark">
        <i class="fas fa-fw fa-window-close"></i> Delete
      </button>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-globe-americas mr-2"></i> {{ __('Origin') }}</h1>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($origins->isEmpty())
        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th></th>
              <th>Name</th>
              <th>Short name</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $origins as $origin )
            <tr>
                <td>
                    <input type="checkbox" class="selector" value="{{ $origin->id }}" />
                    <a href="{{ route('origins.edit', [$origin->id]) }}" title="Show institution" style="position: absolute; margin: -2px 0 3px 5px;"><i class="fas fa-fw fa-edit"></i></a>
                </td>
                <td>{{ $origin->name }}</td>
                <td>{{ $origin->short_cut }}</td>
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
                url:"{{ route('origins.deleteItems') }}",
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

