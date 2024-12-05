@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit admin user {{ $user->name }}</h1>

                {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id]]) !!}

                    @include('users._form', ['submitButtonText' => 'Edit user'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

