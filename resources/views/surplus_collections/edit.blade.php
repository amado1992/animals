@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit surplus collection</h1>

                {!! Form::model($surplus, ['method' => 'PATCH', 'route' => ['surplus-collection.update', $surplus->id], 'id' => 'surplusCollectionForm'] ) !!}

                    @include('surplus_collections._form', ['submitButtonText' => 'Save surplus'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

