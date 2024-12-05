@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        <a href="{{ route('users.create') }}" class="btn btn-dark">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-user mr-2"></i> {{ __('User') }}</h1>
    <p class="text-white">Users are accounts that can login to this application</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        @unless($users->isEmpty())
            <div class="table-responsive">
                <table class="table clickable table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Name</th>
                            <th style="width: 250px;">Email</th>
                            <th style="width: 350px;">Role</th>
                            <th style="width: 80px;">Last login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $users as $user )
                        <tr data-url="{{ route('users.edit', $user->id) }}">
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ($user->roles()->count() > 0) ? $user->roles()->first()->name : '' }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else

            <p> No users are added yet </p>

        @endunless
    </div>
  </div>

@endsection
