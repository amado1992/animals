@extends('layouts.admin')

@section('main-content')

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h1 class="mb-4">Create a new institution</h1>

                    {!! Form::open(['route' => 'organisations.store', 'id' => 'institutionForm']) !!}

                    @include('institutions._form', ['submitButtonText' => 'Save institution', 'preset' => compact('name','domain','city','country_id')])

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection
