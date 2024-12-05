@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit contact {{ $contact->name }}</h1>

                {!! Form::model($contact, ['method' => 'PATCH', 'id' => 'contactForm', 'route' => ['contacts.update', $contact->id] ] ) !!}
                    @include('contacts._form', ['submitButtonText' => 'Create', 'submitContactButtonText' => 'Edit contact'])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

