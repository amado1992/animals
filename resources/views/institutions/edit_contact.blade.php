@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h1 class="mb-4">Edit contact</h1>
                <div class="d-inline-block">
                    {!! Form::open(['method' => 'DELETE', 'route' => ['organisations.destroyContact', $contact->id, $organization->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="card-body">
                {!! Form::model($contact, ['method' => 'PATCH', 'route' => ['organisations.updateContact', $contact->id], 'id' => 'addContactForm'] ) !!}

                    @include('organisations.add_contact_form', ['submitButtonText' => 'Edit contact'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
