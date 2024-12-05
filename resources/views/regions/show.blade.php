@extends('layouts.admin')

@section('subnav-content')
    <ol class="breadcrumb border-0 m-0 bg-primary">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('regions.index') }}">Regions</a></li>
        <li class="breadcrumb-item active">{{ $region->name }}</li>
    </ol>
@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header">
   	  <a href="{{ route('regions.edit', [$region->id]) }}" class="btn btn-primary float-right">Edit</a>
      <h1>{{ $region->name }}</h1>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
      <b>Countries in {{ $region->name }}</b>
    </div>
    <div class="card-body">

      @unless($region->countries->isEmpty())

      	<table class="table table-condensed">
      	  <thead>
      	  	<tr>
      	  		<th>Name</th>
      	  	</tr>
      	  </thead>
      	  <tbody>

	      @foreach($region->countries as $country)

		      <tr>
		      	<td><a href="{{ route('countries.show', [$country->id]) }}">{{ $country->name }}</a></td>
		      	<td></td>
		      </tr>

	      @endforeach

	  	  </tbody>
	    </table>

	  @else

	    <p>No countries are added to {{ $region->name }}</p>

	  @endunless
    </div>
	</div>



@endsection

