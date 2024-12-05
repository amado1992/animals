@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit task</h1>

                {!! Form::model($task, ['method' => 'PATCH', 'route' => ['tasks.update', $task->id] ] ) !!}

                    @include('tasks._form', ['submitButtonText' => 'Edit task'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

