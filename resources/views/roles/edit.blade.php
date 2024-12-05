@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit role {{ $role->display_name }}</h1>

                {!! Form::model($role, ['method' => 'PATCH', 'route' => ['roles.update', $role->id] ] ) !!}

                    @include('roles._form', ['submitButtonText' => 'Edit role'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

