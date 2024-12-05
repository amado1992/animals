@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Order email</h1>

                {!! Form::open(['route' => 'orders.orderSendEmail']) !!}

                    @include('orders._email_form', ['submitButtonText' => 'Send email'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
