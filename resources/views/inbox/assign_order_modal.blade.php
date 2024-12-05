<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter order" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Store in order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {!! Form::open(['method' => 'GET', 'name' => 'assing-order-form', 'id' => 'assing-order-form']) !!}
                        <div class="row mb-2">
                            <div class="col-md-12">
                                {!! Form::label('client', 'Client', ['class' => 'font-weight-bold']) !!}
                                <select class="contact-select2-inbox form-control contact-select2-client-order" type="filter_order_client" style="width: 100%" name="filter_client_id"></select>
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
                                {!! Form::label('order_number', 'Order number', ['class' => 'font-weight-bold']) !!}
                                {!! Form::number('filter_order_number', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('order_year', 'Order year', ['class' => 'font-weight-bold']) !!}
                                {!! Form::selectYear('filter_order_year', 2020, 2040, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('realized_year', 'Realized year', ['class' => 'font-weight-bold']) !!}
                                {!! Form::selectYear('filter_realized_year', 2020, 2040, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                {!! Form::label('filter_project_manager', 'Project manager', ['class' => 'font-weight-bold']) !!}
                                {!! Form::select('filter_project_manager', $admins, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Form::label('filter_order_company', 'Company', ['class' => 'font-weight-bold']) !!}
                                {!! Form::select('filter_order_company', $companies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                {!! Form::label('supplier', 'Supplier', ['class' => 'font-weight-bold']) !!}
                                <select class="contact-select2 form-control" type="filter_order_supplier" style="width: 100%" name="filter_supplier_id"></select>
                            </div>
                        </div>
                        <input type="hidden" name="items_email_order" id="items_email_order">
                    {!! Form::close() !!}
                    <div class="row">
                        <div class="col-md-12 assing-content" id="assing-order-data">

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success assingOrderSelectedEmail">Store in order</button>
                    <button type="button" class="btn btn-link" data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>

        </div>
    </div>
</div>
