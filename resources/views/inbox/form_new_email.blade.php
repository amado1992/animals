<form name="{{ $formName }}" method="post">
    <div class="mb-2">
        <label for="messageto-input" class="form-label">Email from:</label>
        <input type="email" class="form-control" name="email_from" id="messageto-input" @if(!empty($acount_sho)) readonly @endif value="{{ $acount_show ?? $email_from }}">
        <span class="invalid-feedback" role="alert">
            <strong id="email_from"></strong>
        </span>
    </div>
    <div class="mb-2">
        <label for="messageto-input" class="form-label">Email to:</label>
        <div class="frmSearch">
            <div class="invalid-feedback-tooltips d-none">
                <span id="invalid-canonical_name" role="alert">
                </span>
                <div class="invalid-arrow">
                </div>
            </div>
            <input type="email" class="form-control search-box" name="email_to" id="email_to" data-result="suggesstion-box-email-to" autocomplete="off">
            <div id="suggesstion-box-email-to" class="d-none suggesstion-box suggesstion-box-email-to"></div>
        </div>
        <span class="invalid-feedback" role="alert">
            <strong id="email_to"></strong>
        </span>
    </div>

    <div class="mb-2">
        <label for="messageto-input" class="form-label">Email cc: (<i>email addresses separated by comma</i>)</label>
        <div class="frmSearch">
            <div class="invalid-feedback-tooltips d-none">
                <span id="invalid-canonical_name" role="alert">
                </span>
                <div class="invalid-arrow">
                </div>
            </div>
            <input type="email" class="form-control search-box" name="find_cc" id="find_cc" data-result="suggesstion-box-email-cc">
            <input type="hidden" name="email_cc" id="email_cc">
            <div id="suggesstion-box-email-cc" class="d-none suggesstion-box suggesstion-box-email-cc"></div>
        </div>
        <div class="selectize-control selectize-control_cc multi d-none">
            <div class="selectize-input selectize-input_cc items not-full has-options has-items">

            </div>
            <div class="selectize-dropdown multi" style="display: none; width: 767.5px; top: 38px; left: 0px; visibility: visible;">
                <div class="selectize-dropdown-content">

                </div>
            </div>
        </div>
        <span class="invalid-feedback" role="alert">
            <strong id="email_cc"></strong>
        </span>
    </div>

    <div class="mb-2">
        <label for="messageto-input" class="form-label">Email bcc: (<i>email addresses separated by comma</i>)</label>
        <div class="frmSearch">
            <div class="invalid-feedback-tooltips d-none">
                <span id="invalid-canonical_name" role="alert">
                </span>
                <div class="invalid-arrow">
                </div>
            </div>
            <input type="email" class="form-control search-box" name="find_bcc" id="find_bcc" data-result="suggesstion-box-email-bcc">
            <input type="hidden" name="email_bcc" id="email_bcc">
            <div id="suggesstion-box-email-bcc" class="d-none suggesstion-box suggesstion-box-email-bcc"></div>
        </div>
        <div class="selectize-control selectize-control_bcc multi d-none">
            <div class="selectize-input selectize-input_bcc items not-full has-options has-items">

            </div>
            <div class="selectize-dropdown multi" style="display: none; width: 767.5px; top: 38px; left: 0px; visibility: visible;">
                <div class="selectize-dropdown-content">

                </div>
            </div>
        </div>
        <span class="invalid-feedback" role="alert">
            <strong id="email_bcc"></strong>
        </span>
    </div>

    <div class="mb-2 subject_show">
        <label for="subject-input" class="form-label">Subject:</label>
        <input type="text" class="form-control" name="email_subject" id="email_subject">
        <span class="invalid-feedback" role="alert">
            <strong id="email_subject"></strong>
        </span>
    </div>

    <div class="mb-2 body_show">
      <div class='row' style='margin: 26px -8px 5px 0'>
         <div class="col-md-5" style='padding-left:0 !important;'>
            <label for='subject-input' class='form-label'>Message</label>
         </div>
         <div class='col-md-7' style='margin-top: -5px;'>
            <div class="btn-group me-1">
               <button type='button' class='btn btn-sm btn-light dropdown-toggle waves-effect' data-toggle='dropdown' aria-expanded='false'>
                   Default Text
               </button>
               <div class='dropdown-menu'>
                   @if (!empty($std_text))
                       @foreach ($std_text as $row)
                           <a class='dropdown-item add_std_text' data-text='{{ $row->english_text }}' href='javascript: void(0);'>{{ substr($row->name, 0, 50) }}@if(strlen($row->name) >= 50)...@endif</a>
                       @endforeach
                   @endif
               </div>
            </div>
         </div>
      </div>
      <textarea name="email_body" id="email_body" data-body="" class="form-control"></textarea>
    </div>

    <div class="mb-2 attachment_new_email">

    </div>

    <input type="hidden" name="email_body_html" id="email_body_html">
    <input type="hidden" name="items_email_send" id="items_email_send">
    <input type="hidden" name="type_draft" id="type_draft" value="new">
    <input type="hidden" name="attachments_draft" id="attachments_draft">
    <input type="hidden" name="attachments_upload" id="attachments_upload">

</form>
<div class="d-flex gap-2 mb-2">
    <a class="btn btn-sm btn-light waves-effect attachment_show" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        <i class="mdi mdi-attachment align-bottom me-2" style="font-size: 21px;"></i>
    </a>
</div>
<div class="collapse" id="collapseExample">
    <form action="{{ route("inbox.uploadAttachment") }}" method="post" class="dropzoneEmail" id="myAwesomeDropzone" data-plugin="dropzone" data-previews-container="#file-previews"
    data-upload-preview-template="#uploadPreviewTemplate">
        @csrf
        <div class="fallback">
            <input name="file" type="file" multiple />
        </div>

        <div class="dz-message needsclick">
            <i class="h1 text-muted ri-upload-cloud-2-line"></i>
            <h3>Upload Attachments</h3>
        </div>
    </form>
    <div id="uploadPreviewTemplate">
        <div class="card mt-1 mb-0 shadow-none border">
            <div class="p-2 attachments_upload_item d-none scrroll_style">

            </div>
        </div>
    </div>
</div>
