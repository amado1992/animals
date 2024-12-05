@extends('layouts.admin')

@section('subnav-content')
    <ol class="breadcrumb border-0 m-0 bg-primary">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('countries.index') }}">Countries</a></li>
        <li class="breadcrumb-item active">{{ $country->name }}</li>
    </ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-6">
        <img src="{{ asset('img/countries/france.png') }}" class="float-left mr-4 rounded" style="max-width:180px;" alt="">
        <h1 class="h1 text-white">{{ $country->name }}</h1>
        <p class="text-white">Region: <a class="text-white" href="{{ route('regions.show', [$country->region->id])}}">{{ $country->region->name }}</a></p>
        <a href="{{ route('countries.edit', [$country->id]) }}" class="btn btn-light">
            <i class="fas fa-edit"></i>&nbsp;Edit
        </a>
    </div>

    <div class="col-md-3">
        <p class="text-white">
            <b>Country code:</b> {{ $country->country_code }}<br>
            <b>Phone code:</b> {{ $country->phone_code }}<br>
            <b>Language:</b> {{ $country->language }}
        </p>
    </div>
</div>

@endsection

@section('main-content')

<div class="row">
	<div class="col-md-8">
  		<div class="card shadow mb-4">
		    <div class="card-header">
		        <b>Institutions in {{ $country->name }}</b>
		    </div>
		    <div class="card-body p-1">
		      @unless($country->organisations->isEmpty())
		      	<table class="table table-condensed">
		      	  <thead>
		      	  	<tr>
		      	  		<th class="border-top-0">Name</th>
		      	  		<th class="border-top-0">City</th>
		      	  		<th class="border-top-0">Phone</th>
		      	  		<th class="border-top-0">Contact</th>
		      	  	</tr>
		      	  </thead>
		      	  <tbody>
			      @foreach($country->organisations as $organisation)
				      <tr>
				      	<td><a href="{{ route('organisations.show', [$organisation->id]) }}">{{ $organisation->name }}</a></td>
				      	<td>{{ $organisation->city }}</td>
				      	<td>{{ $organisation->phone }}</td>
				      	<td></td>
				      </tr>
			      @endforeach
			  	  </tbody>
			    </table>
			  @else
			    <p>No institutions are added to {{ $country->name }}</p>
			  @endunless
		    </div>
  		</div>
  	</div>

  	<div class="col-md-4">
  		<div class="card shadow mb-4">
		    <div class="card-header">
		        <b>Cities for cargo flights</b>
		    </div>
		    <div class="card-body p-1">
                @unless($country->airports->isEmpty())
                    <table class="table table-condensed">
                    @foreach($country->airports as $city)
                        <tr>
                            <td class="border-top-0">{{ $city->name }}</td>
                        </tr>
                    @endforeach
                    </table>
                @else
                    <p>No cities are added to this country</p>
                @endunless
		    </div>
        </div>

  		<div class="card shadow mb-4">
		    <div class="card-header">
		        <b>Airports</b>
		    </div>
		    <div class="card-body p-1">
                @unless($country->airports->isEmpty())
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th class="border-top-0">Name</th>
                                <th class="border-top-0">Iata code</th>
                            </tr>
                        </thead>
                        @foreach($country->airports as $airport)
                            <tr>
                                <td>{{ $airport->name }}</td>
                                <td>{{ $airport->iata_code }}</td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p>No airports are added to {{ $country->name }}</p>
                @endunless
		    </div>
  		</div>
  	</div>
</div>

@endsection

