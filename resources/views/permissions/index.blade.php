@extends('layouts.admin')

@section('header-content')

  <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Permissions') }}</h1>
  <p class="text-white">These are all the permissions that can be given to users and roles</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex mb-2 align-items-center">
            {!! Form::open(['route' => 'permissions.filterPermissions', 'method' => 'GET', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::label('filter_permission_name', 'Name:') !!}
                    {!! Form::text('filter_permission_name', null, ['class' => 'form-control ml-1']) !!}
                </div>
                <div class="form-group ml-3">
                    {!! Form::submit('Search', ['class' => 'btn btn-secondary']) !!}
                </div>
            {!! Form::close() !!}
            <div class="ml-2">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    Show all
                </a>
            </div>
        </div>
      @unless($permissions->isEmpty())
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                <th>Name</th>
                @foreach ($roles as $role)
                    <th>
                        {{$role->display_name}}
                    </th>
                @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach( $permissions as $permission )
                <tr>
                <td>{{ $permission->display_name }}</td>
                    @foreach ($roles as $role)
                        <td style="width: 100px;">
                            <div class="custom-control custom-switch" permissionId="{{$permission->id}}" roleId="{{$role->id}}">
                                <input type="checkbox" class="custom-control-input" id="customSwitch{{$permission->id}}{{$role->id}}"
                                @if ($role->hasPermission($permission->name))
                                    checked
                                @endif>
                                <label class="custom-control-label" for="customSwitch{{$permission->id}}{{$role->id}}"></label>
                            </div>
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$permissions->links()}}
      @else
        <p> No permissions are added yet </p>
      @endunless
    </div>
  </div>

@endsection

@section('page-scripts')

<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.custom-switch').change(function() {
        var status = $(this).find('input').prop('checked') == true ? 1 : 0;
        var role_id = $(this).attr('roleId');
        var permission_id = $(this).attr('permissionId');
        
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('permissions.updateRolePermissions') }}",
            data: {
                status: status,
                role_id: role_id,
                permission_id: permission_id
            },
            success: function(data){
              console.log(data.success)
            }
        });

    })

</script>

@endsection
