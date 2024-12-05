    @include('components.errorlist')

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card shadow">
                @if (isset($offer) && isset($offer->client))
                    <div class="card-header pl-2 pt-1 pb-0">
                        {!! Form::label('client_info', 'CLIENT: ' . $offer->client->full_name . ' (' . $offer->client->email .')', ['class' => 'text-danger']) !!}
                    </div>
                @elseif (isset($offer))
					<div class="card-header pl-2 pt-1 pb-0">
                        {!! Form::label('client_info', 'CLIENT: no added yet.', ['class' => 'text-danger']) !!}
                    </div>
                @endif
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::radio('filter_client_option', 'institution_client', true) !!}
                            {!! Form::label('institution_contact', 'Institution client', ['class' => 'mr-2']) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('institution_client', 'Institution client*') !!}
                                {!! Form::hidden('hidden_client_id', ((isset($offer->client)) ? $offer->client->id : null), ['class' => 'form-control']) !!}
                                <select class="institution-select2 form-control" type="default" style="width: 100%" name="institution_client_id">
                                    @if (isset($offer) && $offer->client != null && $offer->client->organisation != null)
                                        <option value="{{ $offer->client->organisation->id }}" selected>{{ $offer->client->organisation->name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('offer_client', 'Contact client *') !!}
                                {!! Form::select('client_id', array(), null, ['class' => 'form-control', 'placeholder' => '- select client -']) !!}
                                <div id="clientSelect2" class="d-none">
                                    <select class="contact-select2 form-control" type="default" style="width: 100%;" name="contact_client_id"></select>
                                </div>
                                {!! Form::hidden('hidden_offer_id', isset($offer) ? $offer->id : null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                @if (isset($offer) && isset($offer->supplier))
                    <div class="card-header pl-2 pt-1 pb-0">
                        {!! Form::label('supplier_info', 'SUPPLIER: ' . $offer->supplier->full_name . ' (' . $offer->supplier->email .')', ['class' => 'text-danger']) !!}
                    </div>
                @endif
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::radio('filter_supplier_option', 'institution_supplier', true) !!}
                            {!! Form::label('institution_contact', 'Institution supplier', ['class' => 'mr-2']) !!}
                            {!! Form::radio('filter_supplier_option', 'supplier_contact') !!}
                            {!! Form::label('specific_contact', 'Specific contact') !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('institution_supplier', 'Institution supplier*') !!}
                                <select class="institution-select2 form-control" type="default" style="width: 100%" name="institution_supplier_id">
                                    @if (isset($offer) && $offer->supplier != null && $offer->supplier->organisation != null)
                                        <option value="{{ $offer->supplier->organisation->id }}" selected>{{ $offer->supplier->organisation->name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('offer_supplier', 'Contact supplier *') !!}
                                {!! Form::label('offer_supplier_msg', '(You must select a contact from below box.)', ['class' => 'text-danger']) !!}
                                {!! Form::select('supplier_id', array(), null, ['class' => 'form-control', 'placeholder' => '- select supplier -']) !!}
                                <div id="supplierSelect2" class="d-none">
                                    <select class="contact-select2 form-control" type="default" style="width: 100%;" name="contact_supplier_id"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('airfreight_agent', 'Airfreight agent') !!}
                <select class="contact-select2 form-control" type="filter_transport_offer" style="width: 100%;" name="airfreight_agent_id">
                    @if (isset($offer) && $offer->airfreight_agent != null)
                        <option value="{{ $offer->airfreight_agent_id }}" selected>{{ $offer->airfreight_agent->email }}</option>
                    @endif
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('currency', 'Currency *') !!}
                {!! Form::select('offer_currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        @if ( isset($offer) )
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('status', 'Status *') !!}
                    {!! Form::select('offer_status', $status, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div id="fieldStatusLevel" style="display:none;" class="col-md-2">
                {!! Form::label('status_level', 'Status Level') !!}
                {!! Form::select('status_level', $offerStatusesLevel, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
        @endif
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('manager', 'Manager *') !!}
                {!! Form::select('manager_id', $admins, $offer->manager_id ?? null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('offer_type', 'Offer type *') !!}
                {!! Form::select('sale_price_type', $price_type, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
    </div>

    <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('cost_price_status', 'Cost price status *') !!}
                    {!! Form::select('cost_price_status', ['Estimation' => 'Estimation', 'Exactly' => 'Exactly'], null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('country', 'Destination country *') !!}
                    {!! Form::select('delivery_country_id', $countries, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('airport', 'Destination airport *') !!}
                    {!! Form::select('delivery_airport_id', array(), null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                    {!! Form::hidden('hidden_delivery_airport_id', isset($offer) ? $offer->delivery_airport_id : null, ['class' => 'form-control']) !!}
                </div>
            </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('remarks', 'Remarks') !!}
                {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => '2']) !!}
            </div>
        </div>
    </div>

<button class="btn btn-primary btn-lg" id="edit_offer" type="submit">{{ $submitButtonText }}</button>
@if ($id == "newOfferForm")
    <a href="{{ route('offers.index') }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('offers.show', $offer) }}" class="btn btn-link" type="button">Cancel</a>
@endif
