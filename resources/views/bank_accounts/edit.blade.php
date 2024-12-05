@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Edit bank account</h1>

                {!! Form::model($bank_account, ['method' => 'PATCH', 'route' => ['bank_accounts.update', $bank_account->id] ] ) !!}
                    @include('bank_accounts._form', ['submitButtonText' => 'Edit bank account'])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

