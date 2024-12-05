@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">
                    @if (!empty($email_code) && $email_code == "transport_quotation")
                        Freight Application
                    @elseif(!empty($email_code) && $email_code == "send_offer")
                        Send Offer to client
                    @elseif(!empty($email_code) && $email_code == "remind_1")
                        Offer reminder
                    @elseif(!empty($email_code) && $email_code == "not_available")
                        Species not available anymore
                    @elseif(!empty($email_code) && $email_code == "special_conditions")
                        Special conditions
                    @elseif(!empty($email_code) && $email_code == "to_approve")
                        Offer to aprove
                    @elseif(!empty($email_code) && $email_code == "apply_pictures_enclosures")
                        Apply pictures enclosure
                    @elseif(!empty($email_code) && $email_code == "apply_pictures_species")
                        Apply pictures animals
                    @elseif(!empty($email_code) && $email_code == "apply_veterinary_client")
                        Apply veterinary import conditions
                    @elseif(!empty($email_code) && $email_code == "apply_exterior_dimensions_crates")
                        Apply dimensions transport crates
                    @elseif(!empty($email_code) && $email_code == "to_approve_by_john")
                        Offer to approve by John
                    @else
                        Offer email to client
                    @endif
                </h1>

                {!! Form::open(['route' => 'offers.offerSendEmail']) !!}

                    @include('offers._email_form', ['submitButtonText' => 'Send email'])

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection
