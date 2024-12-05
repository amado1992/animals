@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit classification</h1>

                {!! Form::model($classification, ['method' => 'PATCH', 'route' => ['classifications.update', $classification->id] ] ) !!}

                    @include('classifications._form', ['submitButtonText' => 'Edit classification'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

