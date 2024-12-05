<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" data-draft="false" role="dialog" aria-labelledby="Filter offers" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Forward Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include("inbox.form_new_email", ['formName' => 'send-email-form-forward'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="button" class="btn btn-primary sendEmailForward" data-url="">Send <i class="fab fa-telegram-plane ms-1"></i></button>
                </div>
        </div>
    </div>
</div>
