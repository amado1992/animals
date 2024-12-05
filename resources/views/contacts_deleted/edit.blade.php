@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit contact {{ $contact->name }}</h1>

                {!! Form::model($contact, ['method' => 'PATCH', 'route' => ['contacts-deleted.update', $contact->id] ] ) !!}
                    @include('contacts_deleted._form', ['submitButtonText' => 'Create', 'submitContactButtonText' => 'Edit contact'])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

