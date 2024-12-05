<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" data-draft="false" role="dialog" aria-labelledby="Filter offers" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-danger draftEmail" style="margin: 0 0 15px 0;" data-url="{{ route("inbox.draftEmail") }}">Draft <i class="mdi mdi-file ms-1"></i></button>
                    <button type="button" class="btn btn-primary sendEmail" style="margin: 0 0 15px 0;" data-url="">Send <i class="fab fa-telegram-plane ms-1"></i></button>
                    @include("inbox.form_new_email", ['formName' => 'send-email-form'])
                    <button type="button" class="btn btn-danger draftEmail" style="margin: 0 0 15px 0;" data-url="{{ route("inbox.draftEmail") }}">Draft <i class="mdi mdi-file ms-1"></i></button>
                    <button type="button" class="btn btn-primary sendEmail" style="margin: 0 0 15px 0;" data-url="">Send <i class="fab fa-telegram-plane ms-1"></i></button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
        </div>
    </div>
</div>
