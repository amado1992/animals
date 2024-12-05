<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Created selected contact" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 90% !important;" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create a new institution</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content" style="border: 0px !important; border-radius: 0px;">
            <div class="row">
                <div class="col-md-6" style="border-right: 1px solid #00000014; padding: 0 13px 0 13px;">
                    {!! Form::open(['route' => 'organisations.store', 'id' => 'institutionForm', 'name' => 'institutionForm']) !!}

                        @include('inbox.institution_form', ['submitButtonText' => 'Save institution'])
                        <input type="hidden" name="items_email_institution" id="items_email_institution">

                    {!! Form::close() !!}
                </div>
                <div class="col-md-6" style="padding: 0 13px 0 13px;">
                    <h5 class="font-18 create_email_subject"></h5>
                    <div class="d-flex align-items-start mb-3 mt-1">
                        <div class="flex-1">
                            <h6 class="m-0 font-14 create_email_name"></h6>
                            <small class="text-muted create_email_email"></small>
                        </div>
                    </div>
                    <hr>
                    <div class="create_email_body" style="height: 859px">
                    </div>
                </div>
            </div>
      </div>
    </div>
</div>
