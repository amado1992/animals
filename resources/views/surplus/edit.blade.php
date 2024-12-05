@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit surplus</h1>

                {!! Form::model($surplus, ['method' => 'PATCH', 'route' => ['surplus.update', $surplus->id], 'id' => 'surplusForm'] ) !!}

                    @include('surplus._form', ['submitButtonText' => 'Save surplus'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

