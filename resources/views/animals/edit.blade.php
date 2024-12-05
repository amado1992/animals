@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">

                <div class="d-inline-block float-right">
                    @if (Auth::user()->hasPermission('animals.delete'))
                        {!! Form::open(['method' => 'DELETE', 'route' => ['animals.destroy', $animal->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                            <a href="#" onclick="$(this).closest('form').submit();" class="btn btn-danger ml-2">
                                <i class="fas fa-fw fa-window-close"></i> Delete
                            </a>
                        {!! Form::close() !!}
                    @endif
                </div>
                <h1 class="mb-4">Edit animal</h1>

                {!! Form::model($animal, ['method' => 'PATCH', 'route' => ['animals.update', $animal->id], 'id' => 'animalForm'] ) !!}

                    @include('animals._form', ['submitButtonText' => 'Edit animal'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
