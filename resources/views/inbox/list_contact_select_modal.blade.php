<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter wanted" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">List Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="contact_detail">

                    </div>
                    <input type="hidden" name="items_email_contact_multiple" id="items_email_contact_multiple">
                    <div class="row">
                        <div class="col-md-12" id="list-contact-data">

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success listContactSelectedEmail">Changes</button>
                    <button type="button" class="btn btn-link" data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>

        </div>
    </div>
</div>
