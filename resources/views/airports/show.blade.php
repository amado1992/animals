@extends('layouts.admin')

@section('subnav-content')
    <ol class="breadcrumb border-0 m-0 bg-primary">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('airports.index') }}">Airports</a></li>
        <li class="breadcrumb-item active">{{ $airport->name }}</li>
    </ol>
@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header">
        <div class="d-flex flex-row align-items-center">
            <h1>{{ $airport->name }}</h1>
            <a href="{{ route('airports.edit', [$airport->id]) }}" class="btn btn-dark ml-5">
                <i class="fas fa-fw fa-pen"></i> Edit
            </a>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>City</th>
                    <th>Country</th>
                    <th>IATA code</th>
                    <th>ICAO code</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $airport->city }}</td>
                    <td>{{ $airport->country->name }}</td>
                    <td>{{ $airport->iata_code }}</td>
                    <td>{{ $airport->icao_code }}</td>
                    <td>{{ $airport->lat }}</td>
                    <td>{{ $airport->lon }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

