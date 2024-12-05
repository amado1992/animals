@extends('layouts.admin')

@section('subnav-content')
    <ol class="breadcrumb border-0 m-0 bg-primary">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('bank_accounts.index') }}">Bank accounts</a></li>
        <li class="breadcrumb-item active">{{ $bank_account->full_name }}</li>
    </ol>
@endsection

@section('header-content')
    <div class="d-flex flex-row align-items-center">
        <h1 class="h1 text-white">{{ $bank_account->full_name }}</h1>
        <a href="{{ route('bank_accounts.edit', [$bank_account->id]) }}" class="btn btn-light ml-5">
            <i class="fas fa-fw fa-pen"></i> Edit
        </a>
    </div>
    <p class="text-white">{{ $bank_account->iban }}</p>
@endsection

@section('main-content')

<div class="row">
  <div class="col-md-4">

    <div class="card shadow mb-4">

        <div class="card-header">
            <h5>Beneficiary</h5>
        </div>
        <div class="card-body">

          <table class="table">
            <tr>
              <td class="font-weight-bold border-top-0">Name</td>
              <td class="border-top-0">{{ $bank_account->company_name }}</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Address</td>
              <td>{!! nl2br($bank_account->company_address) !!}</td>
            </tr>
          </table>

        </div>

    </div>

  </div>

  <div class="col-md-4">

    <div class="card shadow mb-4">

        <div class="card-header">
            <h5>Beneficiary bank</h5>
        </div>
        <div class="card-body">

          <table class="table">
            <tr>
              <td class="font-weight-bold border-top-0">Name</td>
              <td class="border-top-0">{{ $bank_account->beneficiary_name }}</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Address</td>
              <td>{!! nl2br($bank_account->beneficiary_address) !!}</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Account</td>
              <td>{{ $bank_account->beneficiary_account }}</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Swift code</td>
              <td>{{ $bank_account->beneficiary_swift }}</td>
            </tr>
          </table>

        </div>

    </div>

  </div>

  <div class="col-md-4">

    <div class="card shadow mb-4">

        <div class="card-header">
            <h5>Correspondent bank</h5>
        </div>
        <div class="card-body">

          <table class="table">
            <tr>
              <td class="font-weight-bold border-top-0">Name</td>
              <td class="border-top-0">{{ $bank_account->correspondent_name }}</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Address</td>
              <td>{!! nl2br($bank_account->correspondent_address) !!}</td>
            </tr>
          </table>

        </div>

  </div>
</div>

@endsection

