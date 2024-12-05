<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter offers" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Store in Offer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {!! Form::open(['method' => 'GET', 'name' => 'assing-offer-form', 'id' => 'assing-offer-form']) !!}
                        <div class="row mb-2">
                            <div class="col-md-12">
                                {!! Form::label('client', 'Client', ['class' => 'font-weight-bold']) !!}
                                <select class="contact-select2-inbox form-control contact-select2-client-offer" type="filter_offer_client" style="width: 100%" name="filter_client_id"></select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                {!! Form::label('animal', 'Species', ['class' => 'font-weight-bold']) !!}
                                <select class="animal-select2_inbox form-control" type="filter_offer_species" style="width: 100%" name="filter_animal_id"></select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                {!! Form::label('request_number', 'Offer number', ['class' => 'font-weight-bold']) !!}
                                {!! Form::number('filter_request_number', null, ['class' => 'form-control']) !!}
                            </div>
                           <div class="col-md-4">
                              {!! Form::label('filter_country', 'Country', ['class' => 'font-weight-bold']) !!}
                              {!! Form::select('filter_country', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                           </div>
                            <div class="col-md-4">
                                {!! Form::label('offer_year', 'Offer year', ['class' => 'font-weight-bold']) !!}
                                {!! Form::selectYear('filter_offer_year', 2020, 2040, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            </div>

                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                {!! Form::label('supplier', 'Supplier', ['class' => 'font-weight-bold']) !!}
                                <select class="contact-select2-inbox form-control" type="filter_offer_supplier" style="width: 100%" name="filter_supplier_id"></select>
                            </div>
                        </div>
                        <input type="hidden" name="items_email_offer" id="items_email_offer">
                    {!! Form::close() !!}
                    <div class="row">
                        <div class="col-md-12 assing-content" id="assing-offer-data">

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success assingOfferSelectedEmail">Store in Offer</button>
                    <button type="button" class="btn btn-link" data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>

        </div>
    </div>
</div>
