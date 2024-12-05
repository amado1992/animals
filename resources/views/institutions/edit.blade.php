@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit institution</h1>

                {!! Form::model($organisation, ['method' => 'PATCH', 'route' => ['organisations.update', $organisation->id], 'id' => 'institutionForm'] ) !!}

                    @include('institutions._form', ['submitButtonText' => 'Edit institution'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
