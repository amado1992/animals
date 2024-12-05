@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Create a new classification</h1>

                {!! Form::open(['route' => 'classifications.store']) !!}

                    @include('classifications._form', ['submitButtonText' => 'Create classification'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

