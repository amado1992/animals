@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Add a role</h1>

                {!! Form::open(['route' => 'roles.store']) !!}

                    @include('roles._form', ['submitButtonText' => 'Add role'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

