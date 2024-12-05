@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row align-items-center">
					<div class="col-md-12 text-center" style="margin: 29px 0 36px 0px;">
                        <i class="mr-2 fas fa-check-circle callback-icon check-reset-success"></i>
                        <h3>Good Job!!</h3>
                        <p style="font-size: 16px;">The list of <strong>{{ $title_dash }}</strong> has been restarted correctly</p>
					</div>
				</div>

            </div>
        </div>
    </div>
</div>

@endsection
