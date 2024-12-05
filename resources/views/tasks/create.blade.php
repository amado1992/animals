@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Create a new task</h1>

                {!! Form::open(['route' => 'tasks.store']) !!}

                    @include('tasks._form', ['submitButtonText' => 'Create task'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
