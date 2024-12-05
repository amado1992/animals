<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Created selected contact" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 90% !important;" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="subject_modal"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content" style="border: 0px !important; border-radius: 0px;">
            <div class="row">
                <div class="col-md-12" style="border-right: 1px solid #00000014; padding: 0 13px 0 13px;">
                    <div class="body-email-modal">

                    </div>
                </div>
            </div>
            <div class="mt-5 actions-email d-none">
                <a href="#" class="btn btn-secondary me-2 reply-btn-dashboard" data-url="{{ route('inbox.sendEmail') }}" data-email="" data-from="" data-id=""><i class="mdi mdi-reply me-1"></i> Reply</a>
                <a href="#" class="btn btn-secondary me-2 forward-btn-dashboard" data-url="{{ route('inbox.forwardEmail') }}" data-email="" data-from="" data-id="">Forward <i class="mdi mdi-forward ms-1"></i></a>
                <a href="#" class="btn btn-danger me-2 delete_item_body" data-id="">Delete Dashboard <i class="mdi mdi-delete-variant font-18"></i></a>
            </div>
      </div>
    </div>
</div>
