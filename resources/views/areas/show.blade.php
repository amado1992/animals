@extends('layouts.admin')

@section('subnav-content')
    <ol class="breadcrumb border-0 m-0 bg-primary">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('areas.index') }}">Areas</a></li>
        <li class="breadcrumb-item active">{{ $area->name }}</li>
    </ol>
@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header">
   	  <a href="{{ route('areas.edit', [$area->id]) }}" class="btn btn-primary float-right">Edit</a>
      <h1>{{ $area->name }}</h1>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
      <b>Regions in {{ $area->name }}</b>
    </div>
    <div class="card-body">
      @unless($area->regions->isEmpty())
      	<table class="table table-condensed">
      	  <thead>
      	  	<tr>
      	  		<th>Name</th>
      	  	</tr>
      	  </thead>
      	  <tbody>
            @foreach($area->regions as $region)
                <tr>
                    <td>{{ $region->name }}</td>
                </tr>
            @endforeach
	  	  </tbody>
	    </table>
	  @else
	    <p>No regions are added to {{ $area->name }}</p>
	  @endunless
    </div>
</div>

@endsection

