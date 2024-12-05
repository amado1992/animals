@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        <a href="{{ route('roles.create') }}" class="btn btn-dark">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
    </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Roles') }}</h1>
  <p class="text-white">These are the roles a user can have in the system</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($roles->isEmpty())
      <div class="table-responsive">
        <table class="table clickable table-hover table-bordered datatable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Name</th>
              <th>No. of users</th>
              <th>No. of permissions</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $roles as $role )
            <tr data-url="{{ route('roles.edit', $role->id) }}">
              <td>{{ $role->name }}</td>
              <td>{{ $role->users()->count() }}</td>
              <td>{{ $role->permissions()->count() }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p> No roles are added yet </p>

      @endunless
    </div>
  </div>

@endsection
