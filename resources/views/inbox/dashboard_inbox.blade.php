@extends('layouts.admin')

@section('page-css')
    <!-- Plugins css -->
    <link href="{{ asset('vendor/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/mohithg-switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .select2-container--default .select2-selection--single .select2-selection__clear{
            margin-right: 35px !important;
            margin-top: 3px !important;
        }
        .assing-content{
            max-height: 500px !important;
            overflow-y: scroll !important;
        }
        .dropdown-item.active, .dropdown-item:active{
            color: #000000 !important;
            background: #eaeef0;
            border-radius: 5px;
        }
        .navbar{
            display: none;
        }
        #content-wrapper{
            background-color: #fff !important;
        }
        .container-fluid{
            padding: 0px !important;
            margin-top: 0px !important;
        }
        .navbar-nav{
            display: none !important;
        }
    </style>
@endsection

@section('main-content')
<!-- Pre-loader -->
<div id="preloader">
    <div id="status">
        <div class="spinner">Loading...</div>
    </div>
</div>
<div class="row">

    <!-- Right Sidebar -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Left sidebar -->
                <div class="inbox-leftbar">
                    <div class="d-grid">
                        <button type="button" class="btn btn-danger waves-effect waves-light composerModal" data-url="{{ route('inbox.sendEmail') }}" style="width: 100%;">
                            New Email
                        </button>
                    </div>
                    <div class="mail-list mt-4">
                        <a href="{{ route('inbox.emailDashboard') }}" class="dropdown-item {{ $type_page == "inbox" && $label == "false" ? "active" : "" }}"><i class="ri-inbox-fill align-bottom me-2"></i>Inbox @if($total_unread)<span class="badge badge-soft-danger float-end ms-2 total-inbox" style="color: #fff;background: #f1556c;">{{ $total_unread }}</span>@endif</a>
                        <a href="{{ route('inbox.emailDashboard') }}?is_send=1" class="dropdown-item {{ $type_page == "sentitems" ? "active" : "" }}"><i class="ri-send-plane-2-line align-bottom me-2"></i>Sent Mail</a>
                        <a href="{{ route('inbox.emailDashboard') }}?archive=1" class="dropdown-item {{ $type_page == "archive" ? "active" : "" }}"><i class="mdi mdi-archive align-bottom me-2"></i>Archive</a>
                        <a href="{{ route('inbox.emailDashboard') }}?is_spam=1" class="dropdown-item {{ $type_page == "junkemail" ? "active" : "" }}"><i class="ri-error-warning-line align-bottom me-2"></i>Spam</a>
                        <a href="{{ route('inbox.emailDashboard') }}?is_draft=1" class="dropdown-item {{ $type_page == "drafts" ? "active" : "" }}"><i class="mdi mdi-file align-bottom me-2"></i>Draft</a>
                        <a href="{{ route('inbox.emailDashboard') }}?is_delete=1" class="dropdown-item {{ $type_page == "deleteditems" ? "active" : "" }}"><i class="ri-delete-bin-line align-bottom me-2"></i>Trash</a>
                    </div>
                    @if (!empty($labels))
                        <h6 class="mt-4">Labels</h6>

                        <div class="list-group b-0 mail-list">
                            @foreach ($labels as $row )
                                <a class="dropdown-item {{ $label == $row['id'] ? 'active' : '' }}" href="{{ route('inbox.index') }}?label={{ $row->id }}" title="{{ $row["title"] }}"><span class="mdi mdi-circle me-2" style="color: {{ $row["color"] }}"></span>{{ substr($row["title"], 0, 17) }} {{ strlen($row["title"]) > 17 ? "..." : "" }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <!-- End Left sidebar -->

                <div class="inbox-rightbar">
                    <div class="row scroll-option-bar">
                        <div class="col-md-12">
                            <div class="btn-group me-1">
                                <button type="button" class="btn btn-sm btn-light waves-effect filter_emails" title="Filter Emails"><i class="fas fa-search font-18"></i></button>
                                <button type="button" class="btn btn-sm btn-light waves-effect delete_emails" title="Delete Emails"><i class="mdi mdi-delete-variant font-18"></i></button>
                                <button type="button" class="btn btn-sm btn-light waves-effect archive_emails" onclick="archiveEmails('')" title="Archive Emails"><i class="mdi mdi-archive font-18"></i></button>
                                <button type="button" class="btn btn-sm btn-light waves-effect spam_emails" title="Spam Emails"><i class="ri-error-warning-line align-bottom me-2"></i></button>
                                <button type="button" class="btn btn-sm btn-light waves-effect exportInboxRecords" title="Export Emails"><i class="fas fa-fw fa-save font-16"></i></button>
                                <button type="button" class="btn btn-sm btn-light waves-effect" onclick="assingAttachmentDashboard('')" title="Add Dashboard"><i class="mdi mdi-view-dashboard"></i></button>
                                <button type="button" class="btn btn-sm btn-light waves-effect addColor" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-format-color-fill"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="scrroll_style">
                                        @foreach ($colors as $row)
                                            <a class="dropdown-item addEmailColor" data-id="{{ $row->id }}" href="#">
                                                <div class="list-group b-0 mail-list">
                                                    <span class="mdi mdi-circle me-2" style="color: {{ $row->color }}"></span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item addNewColor" href="#">Add New Color</a>
                                    <a class="dropdown-item removeColor" href="#">Remove color from emails</a>
                                </div>
                            </div>
                            <div class="btn-group me-1">
                                <button type="button" id="updateLabelSelectedEmail" class="btn btn-sm btn-light dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false" title="Update Label">
                                    <i class="mdi mdi-label font-18"></i>
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <span class="dropdown-header">Label as:</span>
                                    @foreach ($labels as $row )
                                        <a class="dropdown-item updateLabelSelectedEmail" href="#" data-id="{{ $row["id"] }}"><span class="mdi mdi-circle me-2" style="color: {{ $row["color"] }}"></span>{{ $row["title"] }}</a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="btn-group me-1">
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle waves-effect more-actions" data-toggle="dropdown" aria-expanded="false" title="More Actions">
                                    <i class="mdi mdi-dots-horizontal font-18"></i> More
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <span class="dropdown-header">More Option :</span>
                                    <a class="dropdown-item create_contact_email" onclick="createContactEmail('')" href="javascript: void(0);">Create Contact</a>
                                    <a class="dropdown-item create_institution_email" onclick="createInstitutionEmail('')" href="javascript: void(0);">Create Institutions</a>
                                    <a class="dropdown-item create_task_email" onclick="createTaskEmail('')" href="javascript: void(0);">Cr Task</a>
                                    <a class="dropdown-item assing_offer_email" onclick="assingOfferEmail('')" href="javascript: void(0);">Store in Offer</a>
                                    <a class="dropdown-item assing_order_email" onclick="assingOrderEmail('')" href="javascript: void(0);">Store in Order</a>
                                    <a class="dropdown-item assing_surplu_email" onclick="assingSurpluEmail('')" href="javascript: void(0);">Store in Surplu</a>
                                    <a class="dropdown-item assing_wanted_email" onclick="assingWantedEmail('')" href="javascript: void(0);">Store in Wanted</a>
                                    <a class="dropdown-item delete_address_emails" href="javascript: void(0);">Delete email address</a>
                                </div>
                            </div>

                            <div class="btn-group me-1">
                                <button type="button" onclick="forwardBtnBulk('')" data-url="{{ route('inbox.forwardEmailBulk') }}" class="btn btn-sm btn-light">Forward selected</button>
                                <button type="button" class="btn btn-sm btn-light btn-forward-attachment-email" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item forwardBtnAttachmentEmail" data-url="{{ route('inbox.sendEmail') }}" href="#">Forward selected as attachment</a>
                                </div>
                            </div><!-- /btn-group -->

                            <div class="btn-group me-1">
                                <select name="acount_show" id="acount_show" class="form-control">
                                    <option value="">- select acount -</option>
                                    @foreach ($acount as $row)
                                        <option value="{{ $row }}" @if ($row == $acount_show) selected @endif>{{ $row }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="btn-group me-1">
                                <span class="dropdown-header">Show Cc :</span>
                                <input type="checkbox" class="d-none" data-plugin="switchery" data-color="#64b0f2" data-size="small" {{ !empty($filter_cc) && $filter_cc == "true" ? "checked" : "" }} {{ empty($filter_cc) ? "checked" : "" }}/>
                            </div>


                            <div class="btn-group me-1" style="float: right;">
                                <a href="#" class="btn btn-success text-white waves-effect waves-light" id="get-all-email-account">
                                    Get all email from account
                                </a>
                                <a href="#" class="btn btn-danger text-white waves-effect waves-light add-account">
                                    Add Account
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($user_token > 0)
                        <div class="mt-3" id="list-content-message">
                            @include('inbox.table')
                        </div>
                        <!-- end .mt-4 -->

                        <div class="row mb-4 pagination_info">
                            <div class="col-sm-6">
                                @if ($emails->lastPage() > 1)
                                    <div>
                                        <h5 class="font-14 text-body">Showing  {{$emails->currentPage()}} page of {{$emails->lastPage()}}</h5>
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <div class="float-sm-end">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-50">
                                            {{ $emails->links('vendor.pagination.bootstrap-4') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row-->
                    @endif



                </div>
                <!-- end inbox-rightbar-->

                <div class="clearfix"></div>
            </div>
        </div> <!-- end card-box -->

    </div> <!-- end Col -->
</div><!-- End row -->

@include('inbox.create_contact_modal', ['modalId' => 'createdContactModal'])
@include('inbox.create_institution_modal', ['modalId' => 'createdInstitutionModal'])
@include('inbox.add_account_modal', ['modalId' => 'addAccountModal'])
@include('inbox.assign_offer_modal', ['modalId' => 'assignOfferModal'])
@include('inbox.assign_order_modal', ['modalId' => 'assignOrderModal'])
@include('inbox.assign_surplu_modal', ['modalId' => 'assignSurpluModal'])
@include('inbox.assign_wanted_modal', ['modalId' => 'assignWantedModal'])
@include('inbox.create_task_modal', ['modalId' => 'createdTaskModal'])
@include('inbox.list_contact_select_modal', ['modalId' => 'listContactModal'])
@include('inbox.new_email_modal', ['modalId' => 'composerModal'])
@include('export_excel.export_options_modal', ['modalId' => 'exportInbox'])
@include('inbox.filter_modal', ['modalId' => 'filterModal'])
@include('inbox.add_document_modal', ['modalId' => 'uploadGeneralDoc'])
@include('inbox.new_forward_email_modal', ['modalId' => 'forwardEmail'])
@include('inbox.add_color_modal', ['modalId' => 'addColorEmail'])
@endsection

@section('page-scripts')

<script src="{{ asset('vendor/mohithg-switchery/switchery.min.js') }}"></script>
<script type="text/javascript">
    // Preloader
    $(window).on('load', function () {
        $('#status').fadeOut();
        $('#preloader').fadeOut('slow');
    });
    $('[data-plugin="switchery"]').each(function (idx, obj) {
        new Switchery($(this)[0], $(this).data());
    });
    var page_number = "{{ $page_number }}";
    var type_page = "{{ $type_page }}";
    var user_token = "{{ $user_token }}";
    var test_token = "{{ $test_token }}";
    var label = "{{ $label }}";

    $('[data-plugin="switchery"]').on("change", function (e) {
        var filter_cc = false;
        if($(this).is(":checked")) {
            filter_cc = true;
        }
        $('#list-content-message').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $('.pagination_info').html('');
        var url = "{{route('inbox.filterInbox')}}?filter_cc=" + filter_cc;
        window.location = url;
    });

    if(user_token === "0"){
        Swal.fire({
                    title: "Add account",
                    html: "To be able to see the emails you have to add your account",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: "Done",
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false,
                    closeOnConfirm: true,
                    showConfirmButton: false,
                    closeOnCancel: true,
                });
    }

    if(test_token === "0"){
        Swal.fire({
                    title: "Add account",
                    html: 'To be able to see the emails you have to add account "test@zoo-services.com"',
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: "Done",
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false,
                    closeOnConfirm: true,
                    showConfirmButton: false,
                    closeOnCancel: true,
                });
        $("#list-content-message").html('');
    }

    $(".selector").prop("checked", false)

    function updateEmail(){
        var acount = $("#acount_show").val();
        var type_page = "{{ $type_page }}"
        $.ajax({
                type:'GET',
                url:"{{ route('inbox.getUpdateEmail') }}",
                data:{acount_show: acount, type_page: type_page},
                dataType: "JSON",
                success:function(data) {
                    if(!data.error){
                        $("#list-content-message").html(data.content);
                        if(type_page == "inbox"){
                            $(".total-inbox").html(data.total_unread);
                        }
                    }
                },complete: function(r){
                    setTimeout(updateEmail, 300000);
                }
            });
    }

    function changeEmail(){
        var acount = $("#acount_show").val();
        var guids = [];
        $(".guids_email").each(function(){
            guids.push($(this).val());
        });
        var type_page = "{{ $type_page }}"
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type:'POST',
            url:"{{ route('inbox.updateEmailsChanges') }}",
            data:{acount_show: acount, guids: guids, type_page: type_page, _token: token },
            dataType: "JSON",
            success:function(data) {
                if(!data.error){
                    $("#list-content-message").html(data.content);
                    $(".total-inbox").html(data.total_unread);
                }
            },complete: function(r){
                setTimeout(changeEmail, 300000);
            }
        });
    }

    function replyBtn(id){
        var url = $(".reply-btn-" + id).attr("data-url");
        var dataId = $(".reply-btn-" + id).attr("data-id");
        var body = $(".body_iframe_" + dataId).attr("srcdoc");
        $("#items_email_send").val(dataId);
        $('.sendEmail').attr("data-url", url);
        $('#type_draft').val("reply");
        $("#composerModal").modal("show");
        var subject = $(".email-item-subject-" + dataId).html();
        var from  = $(".email-item-from-" + dataId).html();
        var date  = $(".email-item-date-" + dataId).html();
        var email  = $(".reply-btn-" + id).attr("data-email");
        var html  = " From: " + from + "<br>";
        html  += " Subject: " + subject + "<br>";
        html  += " Date: " + date + "<br>";
        body = $("#general_signature").val() + "<br><br><hr>" + html + body;
        $("#email_subject").val("RE: " + subject);
        var attachments = $(".attachments_" + dataId).html();
        $("#email_to").val(email);
        $(".attachment_new_email").html('');
        $(".attachments_upload_item").html('');
        $("#attachments_upload").val('');
        $("#composerModal").attr("data-draft", "false");
        editor.setData(body);
    }

    function forwardBtn(id){
        var url = $(".forward-btn-" + id).attr("data-url");
        var dataId = $(".forward-btn-" + id).attr("data-id");
        var body = $(".body_iframe_" + dataId).attr("srcdoc");
        $("#items_email_send").val(dataId);
        $('.sendEmail').attr("data-url", url);
        $('#type_draft').val("forward");
        $("#composerModal").modal("show");
        var subject = $(".email-item-subject-" + dataId).html();
        var from  = $(".email-item-from-" + dataId).html();
        var date  = $(".email-item-date-" + dataId).html();
        var email  = $(".forward-btn-" + id).attr("data-email");
        var html  = " From: " + from + "<br>";
        var attachments = $(".attachments_" + dataId).html();
        $(".attachment_new_email").html(attachments);
        $("#attachments_draft").val(attachments);
        html  += " Subject: " + subject + "<br>";
        html  += " Date: " + date + "<br>";
        body = $("#general_signature").val() + "<br><br><hr>" + html + body;
        $("#email_subject").val(subject);
        $(".attachment_new_email").html('');
        $(".attachments_upload_item").html('');
        $("#attachments_upload").val('');
        $("#composerModal").attr("data-draft", "false");
        editor.setData(body);
    }

    function forwardBtnBulk(){
        var url = $(".forward-btn").attr("data-url");
        var dataId = $(".forward-btn").attr("data-id");
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        if(ids.length == 0)
            alert("You must one select email.");
        else{
            $("#forwardEmail #items_email_send").val(ids);
            $('#forwardEmail .sendEmailForward').attr("data-url", url);
            $('#forwardEmail #type_draft').val("forward");
            $('#forwardEmail [name=email_to]').val("");
            $('#forwardEmail [name=find_cc]').val("");
            $('#forwardEmail [name=email_cc]').val("");
            $('#forwardEmail [name=find_bcc]').val("");
            $('#forwardEmail [name=email_bcc]').val("");
            $('#forwardEmail [name=email_bcc]').val("");
            $("#forwardEmail").modal("show");
            $("#forwardEmail #cke_email_body").addClass('d-none');
            $("#forwardEmail .subject_show").addClass('d-none');
            $("#forwardEmail .body_show").addClass('d-none');
            $("#forwardEmail .attachment_new_email").addClass('d-none');
            $("#forwardEmail .attachment_show").addClass('d-none');
            $("#forwardEmail #collapseExample").addClass('d-none');
            $("#forwardEmail #forwardEmail").attr("data-draft", "false");
        }
    }

    $(".forwardBtnAttachmentEmail").on("click", function(){
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        var account = $("#acount_show").val();

        if(ids.length == 0)
            alert("You must select items to delete.");
        else{
            var btn_forward_attachment_email = $('.btn-forward-attachment-email').html();
            $('.btn-forward-attachment-email').html('<span class="spinner-border spinner-border-sm" role="status" style="margin: 6px 0 6px 0 !important;"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.attachmentEmailMime') }}",
                data:{items: ids, account: account, type_page: type_page},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        $('.btn-forward-attachment-email').html(btn_forward_attachment_email);
                    }else{
                        var url = $(".forwardBtnAttachmentEmail").attr("data-url");
                        $('.sendEmail').attr("data-url", url);
                        $("#composerModal").modal("show");
                        if(data.htmlAttachments.length > 0){
                            $("#collapseExample").addClass('show');
                        }else{
                            $(".attachments_upload_item").html('');
                            $("#attachments_upload").val('');
                            $('.attachments_upload_item').addClass("d-none");
                        }
                        $(".attachments_upload_item").removeClass("d-none");
                        $(".attachments_upload_item").html(data.htmlAttachments);
                        $("#attachments_upload").val(data.attachments_id);
                        $('.btn-forward-attachment-email').html(btn_forward_attachment_email);
                    }
                }
            });
        }
    });


    function createInstitutionEmail(id){
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var dataId = $(".create_institution_email_" + id).attr('data-id');
        var ids = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
            $("#items_email_institution").val(ids);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        if(ids.length != 1)
            alert("You must one select items.");
        else{
            $("#createdInstitutionModal").modal("show");
            $(".create_email_body").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'GET',
                url:"{{ route('inbox.getSelectEmailCreate') }}",
                data:{items: ids},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $(".create_email_subject").html(data.email.subject);
                        $(".create_email_name").html(data.email.name);
                        $(".create_email_email").html(data.email.from_email);
                        $("#institutionForm #email").val(data.email.from_email);

                        $.ajax({
                            type:'POST',
                            url:"{{ route('inbox.getBodyEmail') }}",
                            data: {
                                    id: data.email.id
                            },
                            dataType: "JSON",
                            success:function(data) {
                                if(data.error){
                                    $(".create_email_body").html('');
                                }else{
                                    var html_body = data.body;
                                    $(".create_email_body").html(html_body);
                                }
                            }
                        });
                    }
                }
            });
        }

    }

    function createTaskEmail(id) {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var dataId = $(".create_task_email_" + id).attr('data-id');
        var ids = [];
        var email = $(".create_task_email_" + id).attr("data-email");
        /*if(email != "false"){
            $('.client_task_email').html('<option value="' + email + '" selected></option>');
        }*/
        var taskType = $(".create_task_email_" + id).attr('data-taskType');
        var offerOrderId = $(".create_task_email_" + id).attr('data-offerOrderId');
        var textTableType = $(".create_task_email_" + id).attr('data-textTableType');
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
            $("#items_email_task").val(ids);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $("#items_email_task").val(ids);

        if(ids.length != 1)
            alert("You must one select items.");
        else{
            $("#createdTaskModal").modal("show");

            $('.task_type-select option[value="' + taskType + '"]').attr("selected",true);
            $('.offer-order-select2').html('<option value="' + offerOrderId + '" selected>' + textTableType + '</option>');
        }
    }

    function createContactEmail(id){
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var dataId = $(".create_contact_email-" + id).attr('data-id');
        var ids = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
            $("#items_email_contact").val(ids);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        if(ids.length != 1)
            alert("You must one select items.");
        else{
            $("#createdContactModal").modal("show");
            $(".create_email_body").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'GET',
                url:"{{ route('inbox.getSelectEmailCreate') }}",
                data:{items: ids},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $(".create_email_subject").html(data.email.subject);
                        $(".create_email_name").html(data.email.name);
                        $(".create_email_email").html(data.email.from_email);
                        $("#contact_email").val(data.email.from_email);

                        $.ajax({
                            type:'POST',
                            url:"{{ route('inbox.getBodyEmail') }}",
                            data: {
                                    id: data.email.id
                            },
                            dataType: "JSON",
                            success:function(data) {
                                if(data.error){
                                    $(".create_email_body").html('');
                                }else{
                                    var html_body = data.body;
                                    $(".create_email_body").html(html_body);
                                }
                            }
                        });
                    }
                }
            });
        }
    }

    function assingOfferEmail(id) {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var dataId = $(".assing_offer_email_" + id).attr('data-id');
        var ids = [];
        var email = $(".assing_offer_email_" + id).attr("data-email");
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $("#items_email_offer").val(ids);


        if(ids.length == 0)
            alert("You must select items.");
        else{
            $("#assignOfferModal").modal("show");

            if(id != ''){
                $('.contact-select2-client-offer').html('<option value="' + email + '" selected></option>');
            }

            $.ajax({
                type:'GET',
                url:"{{ route('api.contacts-select2') }}",
                data: {
                    q: email, // search term
                },
                dataType: "JSON",
                success:function(data) {
                    if(data.error){
                        $(".create_email_body").html('');
                    }else{
                        if(data.items[0].id && id != ''){
                            $('.contact-select2-client-offer').html('<option value="' + data.items[0].id + '" selected></option>');
                        }

                        var form = document.forms.namedItem("assing-offer-form");
                        var formdata = new FormData(form);
                        var html_data = $('#assing-offer-data').html();
                        $('#assing-offer-data').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                        $.ajax({
                            url: "{{ route('inbox.getOfferAssing') }}",
                            method: "post",
                            data: formdata,
                            contentType: false,
                            cache: false,
                            processData:false,
                            dataType: "JSON",
                            success: function (r) {
                                console.log(r);
                                if(r.error){
                                    $('#assing-offer-data').html("There is no offer to show");
                                }else{
                                    $('#assing-offer-data').html(r.content);
                                }
                            }
                        });
                        if(id != ''){
                            $('.contact-select2-client-offer').html('<option value="' + email + '" selected></option>');
                        }
                    }
                }
            });

        }
    }

    function assingOrderEmail(id) {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var dataId = $(".assing_order_email_" + id).attr('data-id');
        var ids = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $("#items_email_order").val(ids);

        if(ids.length == 0)
            alert("You must select items.");
        else{
            $("#assignOrderModal").modal("show");
        }
    }

    function assingSurpluEmail(id) {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var dataId = $(".assing_surplu_email_" + id).attr('data-id');
        var ids = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $("#items_email_surplu").val(ids);

        if(ids.length == 0)
            alert("You must select items.");
        else{
            $("#assignSurpluModal").modal("show");
        }
    }

    function assingWantedEmail(id) {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var dataId = $(".assing_wanted_email_" + id).attr('data-id');
        var ids = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $("#items_email_wanted").val(ids);

        if(ids.length == 0)
            alert("You must select items.");
        else{
            $("#assignWantedModal").modal("show");
        }
    }

    function archiveEmails(id) {
        var dataId = $(".archive_emails_" + id).attr('data-id');
        var ids = [];
        var account = $("#acount_show").val();
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        if(ids.length == 0)
            alert("You must select items to archived.");
        else if(confirm("Are you sure that you want to archived the selected items?")) {
            $('.archive_emails').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.archiveItems') }}",
                data:{items: ids, account: account},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }
    }


    function assingAttachmentDashboard(id) {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var ids = [];
        var email_guid = [];
        var to_email = [];
        $(":checked.selector_attachment").each(function(){
            ids.push($(this).val());
            email_guid.push($(this).attr("data_email_guid"));
            to_email.push($(this).attr("data_to_email"));
        });

        if(ids.length > 0){
            $("#items_attachment").val(ids);
            $("#email_guid").val(email_guid);
            $("#to_email").val(to_email);
            $("#email_ids").val("");
            if(ids.length != 1)
                alert("You must select one items.");
            else{
                $("#uploadGeneralDoc").modal("show");
            }
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
            $("#email_ids").val(ids);
            $("#items_attachment").val("");
            $("#email_guid").val("");
            $("#to_email").val("");
            if(ids.length == 0)
                alert("You must select items.");
            else{
                $("#uploadGeneralDoc").modal("show");
            }
        }


    }


    $(".filter_emails").on("click", function(){
        $("#filterModal").modal("show");
    });

    if(type_page !== "sentitems" && user_token > 0 && label == "false" && page_number <= 1){
        changeEmail();
        updateEmail();
    }

    if(type_page == "sentitems" && label == "false"){
        updateEmail();
    }



    $('.updateLabelSelectedEmail').on('click', function () {
        var ids = [];
        var _dt = $(this);
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        var label = _dt.attr("data-id");

        if(ids.length == 0)
            alert("You must select items to delete.");
        else{
            $('#updateLabelSelectedEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            $.ajax({
                type:'POST',
                url:"{{ route('inbox.updateLabels') }}",
                data:{items: ids, label: label},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }
    });

    $('.updateDirectorySelectedEmail').on('click', function () {
        var ids = [];
        var _dt = $(this);
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        var directory = _dt.attr("data-id");

        if(ids.length == 0)
            alert("You must select items to delete.");
        else{
            $('#updateDirectorySelectedEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            $.ajax({
                type:'POST',
                url:"{{ route('inbox.updateDirectory') }}",
                data:{items: ids, directory: directory},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }
    });


    $('.assingOfferSelectedEmail').on('click', function () {
        var dataId = $("#items_email_offer").val();
        var ids = [];
        var assing = [];
        $(":checked.selector_assing").each(function(){
            assing.push($(this).val());
        });
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }


        if(assing.length != 1){
            alert("You must select one offer to assing.");
        }else{
            if(ids.length == 0)
                alert("You must select email to assing.");
            else{
                $('.assingOfferSelectedEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

                $.ajax({
                    type:'POST',
                    url:"{{ route('inbox.assingOffer') }}",
                    data:{items: ids, offer_id: assing[0]},
                    dataType: "JSON",
                    success: function(data){
                        if(data.error){
                            $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        }else{
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                            location.reload();
                        }
                    }
                });
            }
        }

    });

    $('.assingOrderSelectedEmail').on('click', function () {
        var dataId = $("#items_email_order").val();
        var ids = [];
        var assing = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $(":checked.selector_assing").each(function(){
            assing.push($(this).val());
        });
        if(assing.length != 1){
            alert("You must select one offer to assing.");
        }else{
            if(ids.length == 0)
                alert("You must select email to assing.");
            else{
                $('.assingOrderSelectedEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

                $.ajax({
                    type:'POST',
                    url:"{{ route('inbox.assingOrder') }}",
                    data:{items: ids, order_id: assing[0]},
                    dataType: "JSON",
                    success: function(data){
                        if(data.error){
                            $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        }else{
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                            location.reload();
                        }
                    }
                });
            }
        }

    });

    $('.assingSurpluSelectedEmail').on('click', function () {
        var dataId = $("#items_email_surplu").val();
        var ids = [];
        var assing = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $(":checked.selector_assing").each(function(){
            assing.push($(this).val());
        });
        if(assing.length != 1){
            alert("You must select one surplu to assing.");
        }else{
            if(ids.length == 0)
                alert("You must select email to assing.");
            else{
                $('.assingSurpluSelectedEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

                $.ajax({
                    type:'POST',
                    url:"{{ route('inbox.assingSurplu') }}",
                    data:{items: ids, surplu_id: assing[0]},
                    dataType: "JSON",
                    success: function(data){
                        if(data.error){
                            $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        }else{
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                            location.reload();
                        }
                    }
                });
            }
        }

    });

    $('.assingWantedSelectedEmail').on('click', function () {
        var dataId = $("#items_email_wanted").val();
        var ids = [];
        var assing = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }else{
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }

        $(":checked.selector_assing").each(function(){
            assing.push($(this).val());
        });
        if(assing.length != 1){
            alert("You must select one wanted to assing.");
        }else{
            if(ids.length == 0)
                alert("You must select email to assing.");
            else{
                $('.assingWantedSelectedEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

                $.ajax({
                    type:'POST',
                    url:"{{ route('inbox.assingWanted') }}",
                    data:{items: ids, wanted_id: assing[0]},
                    dataType: "JSON",
                    success: function(data){
                        if(data.error){
                            $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        }else{
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                            location.reload();
                        }
                    }
                });
            }
        }

    });

    $('.listContactSelectedEmail').on('click', function () {
        var dataId = $("#items_email_contact_multiple").val();
        var assing = [];
        var type = "";

        $(":checked.selector_assing").each(function(){
            assing.push($(this).val());
            type = $(this).attr("data-type");
        });
        if(assing.length != 1){
            alert("You must select one offer to change.");
        }else{
            if(dataId.length == 0)
                alert("You must select email to change.");
            else{
                $('.listContactSelectedEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

                $.ajax({
                    type:'POST',
                    url:"{{ route('inbox.changeContact') }}",
                    data:{items: dataId, change_id: assing[0], type: type},
                    dataType: "JSON",
                    success: function(data){
                        if(data.error){
                            $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        }else{
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                            location.reload();
                        }
                    }
                });
            }
        }

    });

    $('.delete_emails').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        var account = $("#acount_show").val();

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $('.delete_emails').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.deleteItems') }}",
                data:{items: ids, account: account, type_page: type_page},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }
    });

    $('.spam_emails').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        var account = $("#acount_show").val();

        if(ids.length == 0)
            alert("You must select items to spam.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $('.spam_emails').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.updateSpam') }}",
                data:{items: ids, account: account, type_page: type_page},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }
    });


    $('.delete_address_emails').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        var account = $("#acount_show").val();

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected email address?")) {
            var more_actions = $('.more-actions').html();
            $('.more-actions').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.deleteAddressItems') }}",
                data:{items: ids, account: account},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        Swal.fire({
                            title: "Delete email address",
                            html: data.message,
                            icon: 'success',
                            showCancelButton: true,
                            cancelButtonText: "Done",
                            confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
                            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                            buttonsStyling: false,
                            closeOnConfirm: true,
                            showConfirmButton: false,
                            closeOnCancel: true,
                        });
                        $('.more-actions').html(more_actions);
                    }
                }
            });
        }
    });

    $('.composerModal').on('click', function () {
        $(".draftEmail").removeClass("d-none");
        var url = $(this).attr("data-url");
        var body = $("#email_body").attr("data-body");
        $("#items_email_send").val('');
        $('.sendEmail').attr("data-url", url);
        $("#composerModal").modal("show");
        $("#email_subject").val("");
        $("#email_to").val("");
        $("#email_cc").val("");
        $("#email_bcc").val("");
        $(".selectize-input").html('');
        $(".attachment_new_email").html('');
        $(".attachments_upload_item").html('');
        $("#attachments_upload").val('');
        $('.selectize-control').addClass("d-none");
        $("#composerModal").attr("data-draft", "false");
        editor.setData(body);
    });

    function editDraftEmail(id){
        var item_email = $('.li-item-' + id).html();
        $('.li-item-' + id).html('<span class="spinner-border spinner-border-sm" role="status" style="margin: 0 37px 0 16px !important;"></span>');
        $.ajax({
            type:'GET',
            url:"{{ route('inbox.getDraftEmail') }}",
            data:{id: id},
            dataType: "JSON",
            success: function(data){
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $("#composerModal").attr("data-draft", "true");
                    $('.li-item-' + id).html(item_email);
                    var url = "{{ route('inbox.sendEmail') }}";
                    if(data.email.type_draft == "forward"){
                        url = "{{ route('inbox.forwardEmail') }}";
                        $("[name=items_email_send]").val(data.email.guid);
                    }else{
                        $("[name=items_email_send]").val('');
                    }
                    $(".draftEmail").addClass("d-none");
                    $('.sendEmail').attr("data-url", url);
                    $("#composerModal").modal("show");
                    $("[name=email_from]").val(data.email.from_email);
                    if(data.email.attachments_draft.length > 0){
                        $("#collapseExample").addClass('show');
                    }else{
                        $(".attachments_upload_item").html('');
                        $("#attachments_upload").val('');
                        $('.attachments_upload_item').addClass("d-none");
                    }
                    $(".attachments_upload_item").removeClass("d-none");
                    $(".attachments_upload_item").html(data.email.attachments_draft);
                    $("#attachments_upload").val(data.attachments_id);
                    $("[name=email_to]").val(data.email.to_email);
                    $("[name=email_cc]").val(data.email.cc_email);
                    $(".selectize-input").html("")
                    $('.selectize-control').addClass("d-none");
                    if(data.email.cc_email){
                        $('.selectize-control_cc').removeClass("d-none");
                        var cc_email = data.email.cc_email.split(",");
                        var selectize_cc = $(".selectize-input_cc").html();
                        for (let i = 0; i < cc_email.length; i++) {
                            if(cc_email[i] != ""){
                                selectize_cc += '<div class="item" data-value="' + cc_email[i] + '">' + cc_email[i] + '<a href="#" onclick="selectize_input_remove_cc(`' + cc_email[i] + '`)" tabindex="-1" title="Remove">×</a></div>';
                            }
                        }
                    }
                    if(data.email.bcc_email){
                        $('.selectize-control_bcc').removeClass("d-none");
                        var bcc_email = data.email.bcc_email.split(",");
                        var selectize_bcc = $(".selectize-input_bcc").html();
                        for (let i = 0; i < bcc_email.length; i++) {
                            if(bcc_email[i] != ""){
                                selectize_bcc += '<div class="item" data-value="' + bcc_email[i] + '">' + bcc_email[i] + '<a href="#" onclick="selectize_input_remove(`' + bcc_email[i] + '`)" tabindex="-1" title="Remove">×</a></div>';
                            }
                        }
                    }
                    $(".selectize-input_cc").html(selectize_cc);
                    $(".selectize-input_bcc").html(selectize_bcc);
                    $("[name=email_bcc]").val(data.email.bcc_email);
                    $("[name=email_subject]").val(data.email.subject);
                    editor.setData(data.email.body);
                }
            }
        });
    }

    $('.sendEmail').on('click', function () {
        var url = $(this).attr("data-url");
        var body = editor.getData();
        $("#email_body").html(body);
        $("#email_body_html").val(body);
        var form = document.forms.namedItem("send-email-form");
        var formdata = new FormData(form);
        var btn_save = $('.sendEmail').html();
        $('.sendEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'POST',
            url: url,
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function(data){
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $("#composerModal").attr("data-draft", "true");
                    $("#composerModal").modal("hide");
                    Swal.fire({
                        title: "Email sent",
                        html: data.message,
                        icon: 'success',
                        showCancelButton: true,
                        cancelButtonText: "Done",
                        confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
                        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                        buttonsStyling: false,
                        closeOnConfirm: true,
                        showConfirmButton: false,
                        closeOnCancel: true,
                    });
                }
            },complete: function(r){
                $('.sendEmail').html(btn_save);
                $.each( r.responseJSON.errors, function( key, value ) {
                    $("[name="+key+"]").addClass('is-invalid');
                    $("#"+key+"").html(value);
                });
            }
        });
    });

    $('.sendEmailForward').on('click', function () {
        var url = $(this).attr("data-url");
        var form = document.forms.namedItem("send-email-form-forward");
        var formdata = new FormData(form);
        var btn_save = $('.sendEmailForward').html();
        $('.sendEmailForward').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'POST',
            url: url,
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function(data){
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $("#forwardEmail").attr("data-draft", "true");
                    $("#forwardEmail").modal("hide");
                    Swal.fire({
                        title: "Email sent",
                        html: data.message,
                        icon: 'success',
                        showCancelButton: true,
                        cancelButtonText: "Done",
                        confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
                        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                        buttonsStyling: false,
                        closeOnConfirm: true,
                        showConfirmButton: false,
                        closeOnCancel: true,
                    });
                }
            },complete: function(r){
                $('.sendEmailForward').html(btn_save);
                $.each( r.responseJSON.errors, function( key, value ) {
                    $("[name="+key+"]").addClass('is-invalid');
                    $("#"+key+"").html(value);
                });
            }
        });
    });

    $('.draftEmail').on('click', function () {
        var url = $(this).attr("data-url");
        var body = editor.getData();
        $("#email_body_html").val(body);
        var form = document.forms.namedItem("send-email-form");
        var formdata = new FormData(form);
        var btn_save = $('.draftEmail').html();
        $('.draftEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'POST',
            url: url,
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function(data){
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $("#composerModal").attr("data-draft", "true");
                    $("#composerModal").modal("hide");
                    Swal.fire({
                        title: "Email draft saved",
                        html: data.message,
                        icon: 'success',
                        showCancelButton: true,
                        cancelButtonText: "Done",
                        confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
                        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                        buttonsStyling: false,
                        closeOnConfirm: true,
                        showConfirmButton: false,
                        closeOnCancel: true,
                    });
                }
            },complete: function(r){
                $('.draftEmail').html(btn_save);
                $.each( r.responseJSON.errors, function( key, value ) {
                    $("[name="+key+"]").addClass('is-invalid');
                    $("#"+key+"").html(value);
                });
            }
        });
    });

    $('#composerModal').on('hidden.bs.modal', function () {
        var dataDraft = $(this).attr('data-draft');
        if(dataDraft == "false"){
            var url = $(this).attr("data-url");
            var body = editor.getData();
            $("#email_body_html").val(body);
            var form = document.forms.namedItem("send-email-form");
            var formdata = new FormData(form);
            var btn_save = $('.draftEmail').html();
            $('.draftEmail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url: "{{ route("inbox.draftEmail") }}",
                data: formdata,
                contentType: false,
                cache: false,
                processData:false,
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $("#composerModal").modal("hide");
                    }
                },complete: function(r){
                    $('.draftEmail').html(btn_save);
                    $.each( r.responseJSON.errors, function( key, value ) {
                        $("[name="+key+"]").addClass('is-invalid');
                        $("#"+key+"").html(value);
                    });
                }
            });
        }
    });

    $('.list_contact_assing').on('click', function () {
        var dataId = $(this).attr('data-id');

        $("#items_email_contact_multiple").val(dataId);

        if(dataId.length == 0)
            alert("You must select items.");
        else{
            $("#listContactModal").modal("show");
            $('#contact_detail').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $("#list-contact-data").html("");
            $.ajax({
                type:'GET',
                url:"{{ route('inbox.multipleContact') }}",
                data:{id: dataId},
                dataType: "JSON",
                success: function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $("#contact_detail").html(data.content_contact);
                        $("#list-contact-data").html(data.content);
                    }
                }
            });
        }
    });

    $('.add-account').on('click', function () {
        var add_account = $('.add-account').html();
        $('.add-account').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'GET',
            url:"{{ route('inbox.addAccount') }}",
            dataType: "JSON",
            success: function(data){
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $("#addAccountModal").modal("show");
                    $(".device_message").html(data.device_message);

                    $.ajax({
                        type:'GET',
                        url:"{{ route('inbox.authGraph') }}",
                        dataType: "JSON",
                        success: function(data){
                            if(data.error){
                                $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                            }else{
                                $(".account-success").removeClass("d-none");
                                $(".device_message").addClass("d-none");
                            }
                        }
                    });
                }
            },complete: function (data){
                $('.add-account').html(add_account);
            }
        });

    });


    $('.institution-select2_inbox').select2({
        ajax: {
            url: "{{ route('api.institutions-select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    type: $(this).attr('type'),
                    page: params.page
                };
            },
            processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        /*more: (params.page * 10) < data.total_count*/
                        more: false
                    }
                };
            },
            cache: true
        },
        allowClear: true,
        placeholder: '- search institution -',
        minimumInputLength: 3,
        templateResult: formatInstitution,
        templateSelection: formatInstitutionSelection
    });

    function formatInstitution (repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__name'></div>" +
                "<div class='select2-result-repository__type'></div>" +
                "<div class='select2-result-repository__email'></div>" +
                "<div class='select2-result-repository__level'></div>" +
            "</div>" +
            "</div>"
        );

        var name = (repo.name) ? repo.name : '';
        var type = (repo.organisation_type) ? repo.organisation_type : '';
        var email = (repo.email) ? repo.email : '';
        var level = (repo.level) ? repo.level : '';

        $container.find(".select2-result-repository__name").text("Institution: " + name);
        $container.find(".select2-result-repository__type").text("Type: " + type);
        $container.find(".select2-result-repository__email").text("Email: " + email);
        $container.find(".select2-result-repository__level").text("Level: " + level);

        return $container;
    }

    function formatInstitutionSelection(repo) {
            if (repo.id)
                return repo.email ? repo.email : repo.id + ' (institution without email)';
            return repo.text;
    }


    $('.animal-select2_inbox').select2({
        ajax: {
            url: "{{ route('api.animals-select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    type: $(this).attr('type'),
                    page: params.page
                };
            },
            processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        /*more: (params.page * 10) < data.total_count*/
                        more: false
                    }
                };
            },
            cache: true
        },
        allowClear: true,
        placeholder: '- search species -',
        minimumInputLength: 3,
        templateResult: formatAnimal,
        templateSelection: formatAnimalSelection
    });

    function formatAnimal (repo) {
        if (repo.loading) {
            return repo.text;
        }

        var APP_URL = {!! json_encode(url('/')) !!};

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            //"<div class='select2-result-repository__avatar'><img class='float-left mr-4 rounded' style='max-width:50px;' src='" + APP_URL + "/storage/animals_pictures/" + repo.id + "/" + repo.catalog_pic + "' /></div>" +
            "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__common_name'></div>" +
                "<div class='select2-result-repository__scientific_name'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__common_name").text(repo.common_name);
        $container.find(".select2-result-repository__scientific_name").text(repo.scientific_name);

        return $container;
    }

    function formatAnimalSelection(repo) {
            if (repo.id)
                return repo.common_name ? repo.common_name : repo.scientific_name;
            return repo.text;
    }

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#contactForm');
        original = form.serialize();

        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        var selectInstitutionOption = $('[name=select_institution_option]:checked').val();
        if (selectInstitutionOption == 'matched_institution') {
            $("[name=select_institution]").prop('disabled', false);
            $("[name=organisation_id]").prop('disabled', true);
        }
        else if (selectInstitutionOption == 'searched_institution') {
            $("[name=select_institution]").prop('disabled', true);
            $("[name=organisation_id]").prop('disabled', false);
        }
        else {
            $("[name=select_institution]").prop('disabled', true);
            $("[name=organisation_id]").prop('disabled', true);
        }

        $('[name=select_institution_option]').click(function() {
            if ($(this).val() == 'matched_institution') {
                $("[name=select_institution]").prop('disabled', false);
                $("[name=organisation_id]").prop('disabled', true);
            }
            else if ($(this).val() == 'searched_institution') {
                $("[name=select_institution]").prop('disabled', true);
                $("[name=organisation_id]").prop('disabled', false);
            }
            else {
                $("[name=select_institution]").prop('disabled', true);
                $("[name=organisation_id]").prop('disabled', true);
            }
        });

        $('[name=contact_email]').keyup(function() {
            if ($('[name=contact_email]').val().trim() != '') {
                $.ajax({
                    type:'POST',
                    url:"{{ route('api.check-contact') }}",
                    data: {
                        id: $('[name=contact_id]').val(),
                        email: $('[name=contact_email]').val(),
                        city: $('[name=city]').val()
                    },
                    success:function(data) {
                        console.log(data);

                        if(data.contact) {
                            $('#contactMessage').removeClass("d-none");
                            $('#contactMessage').html("Contact email already in.");
                        }
                        else
                            $('#contactMessage').addClass("d-none");
                        /***********************************************************/
                        var selected = '';

                        $('[name=select_institution]').empty();
                        $('[name=select_institution]').append('<option value="">- select -</option>');
                        $.each(data.matchedInstitutions, function(key, value) {
                            if ($('[name=contact_id]').val() != null && data.contact && data.contact.organisation)
                                selected = (key == data.contact.organisation.id) ? 'selected' : '';

                            $('[name=select_institution]').append('<option value="'+ key +'" ' + selected + '>' + value +'</option>');
                        });
                        $('#matched_institutions').html(data.matchedInstitutionsTotal);
                    }
                });
            }
        });

        $('[name=city]').keyup(function() {
            $.ajax({
                type:'POST',
                url:"{{ route('api.institutions-by-domain-city') }}",
                data: {
                    id: $('[name=contact_id]').val(),
                    email: $('[name=contact_email]').val(),
                    city: $('[name=city]').val()
                },
                success:function(data) {
                    var selected = '';

                    $('[name=select_institution]').empty();
                    $('[name=select_institution]').append('<option value="">- select -</option>');
                    $.each(data.matchedInstitutions, function(key, value) {
                        if ($('[name=contact_id]').val() != null && data.contact && data.contact.organisation)
                            selected = (key == data.contact.organisation.id) ? 'selected' : '';

                        $('[name=select_institution]').append('<option value="'+ key +'" ' + selected + '>' + value +'</option>');
                    });
                    $('#matched_institutions').html(data.matchedInstitutionsTotal);
                }
            });
        });

        //Select2 institution selection
        $('[name=organisation_id]').on('change', function () {
            var organisationId = $(this).val();

            if(organisationId != null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('api.institution-by-id') }}",
                    data: {
                        id: organisationId,
                    },
                    success:function(data) {
                        // create the option and append to Select2
                        var newOption = new Option(data.institution.name.trim(), data.institution.id, true, true);
                        // Append it to the select
                        $('[name=organisation_id]').append(newOption);
                    }
                });
            }
        });

        $("#submitBtnContact").on('click', function (e) {
            e.preventDefault();
            var form = document.forms.namedItem("contactForm");
            var formdata = new FormData(form);
            var btn_save = $('#submitBtnContact').html();
            $('#submitBtnContact').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                url: "{{ route('contacts.store') }}",
                method: "post",
                contentType: false,
                cache: false,
                processData:false,
                data: formdata,
                success: function (r) {
                    if(r.error){
                        $.NotificationApp.send("Error message!", "Contact is successfully created", 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", "Contact is successfully created", 'top-right', '#fff', 'success');
                        location.reload();
                    }
                },
                complete: function(r){
                    $('#submitBtnContact').html(btn_save);
                    $.each( r.responseJSON.errors, function( key, value ) {
                        $(".alert-danger").removeClass('d-none');
                        $(".alert-danger").html("<p>" + value + "</p>");
                        $('#spinner').addClass('d-none');
                    });
                }
            });

        });

        $("#submitBtnInstitution").on('click', function (e) {
            e.preventDefault();
            var form = document.forms.namedItem("institutionForm");
            var formdata = new FormData(form);
            var btn_save = $('#submitBtnInstitution').html();
            $('#submitBtnInstitution').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                url: "{{ route('organisations.store') }}",
                method: "post",
                contentType: false,
                cache: false,
                processData:false,
                data: formdata,
                success: function (r) {
                    if(r.error){
                        $.NotificationApp.send("Error message!", "Contact is successfully created", 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", "Contact is successfully created", 'top-right', '#fff', 'success');
                        location.reload();
                    }
                },
                complete: function(r){
                    $('#submitBtnInstitution').html(btn_save);
                    $.each( r.responseJSON.errors, function( key, value ) {
                        $(".alert-danger").removeClass('d-none');
                        $(".alert-danger").html("<p>" + value + "</p>");
                        $('#spinner').addClass('d-none');
                    });
                }
            });

        });

        $("#submitAddDashboard").on('click', function (e) {
            e.preventDefault();
            var form = document.forms.namedItem("addDashboard");
            var formdata = new FormData(form);
            var btn_save = $('#submitAddDashboard').html();
            $('#submitAddDashboard').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                url: "{{ route('inbox.addDashboard') }}",
                method: "post",
                contentType: false,
                cache: false,
                processData:false,
                data: formdata,
                success: function (r) {
                    if(r.error){
                        $.NotificationApp.send("Error message!", r.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", r.message, 'top-right', '#fff', 'success');
                        $("#uploadGeneralDoc").modal("hide");
                    }
                    $('#submitAddDashboard').html(btn_save);
                    $(".selector").prop("checked", false)
                    $(".selector_attachment").prop("checked", false)
                },
                complete: function(r){
                    $('#submitAddDashboard').html(btn_save);
                    $.each( r.responseJSON.errors, function( key, value ) {
                        $(".alert-danger").removeClass('d-none');
                        $(".alert-danger").html("<p>" + value + "</p>");
                        $('#spinner').addClass('d-none');
                    });
                }
            });

        });

    });

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#institutionForm');
        original = form.serialize();

        form.submit(function() {
            window.onbeforeunload = null
        })

        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        $('[name=email]').trigger('keyup');

        $("#submmit_institucion").hover(function(){
            alert_city();
        });

        $("#submmit_institucion").focus(function(){
            alert_city();
        });

        $('[name=city]').on("change", function(){
            alert_city();
        });

        $('[name=city]').keyup(function(){
            $('[name=city]').removeClass("is-invalid");
        });

        $('[name=task_type]').change( function () {
            $('.offer-order-select2').val(null).trigger('change');
        });

        $('.offer-order-select2').select2({
            ajax: {
                url: "{{ route('api.offers-orders-select2') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        type: $('[name=task_type]').val(),
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            /*more: (params.page * 10) < data.total_count*/
                            more: false
                        }
                    };
                },
                cache: true
            },
            allowClear: true,
            placeholder: '- search offer or order -',
            minimumInputLength: 3,
            templateResult: formatOfferOrder
        });

        function formatOfferOrder (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__number'></div>" +
                    "<div class='select2-result-repository__client'></div>" +
                    "<div class='select2-result-repository__supplier'></div>" +
                "</div>" +
                "</div>"
            );

            var client_full_name = '';
            if(repo.client.title)
                client_full_name += repo.client.title + " ";
            if(repo.client.first_name)
                client_full_name += repo.client.first_name + " ";
            if(repo.client.last_name)
                client_full_name += repo.client.last_name;

            var client_email = (repo.client.email) ? repo.client.email : '';

            var supplier_full_name = '';
            if(repo.supplier.title)
                supplier_full_name += repo.supplier.title + " ";
            if(repo.supplier.first_name)
                supplier_full_name += repo.supplier.first_name + " ";
            if(repo.supplier.last_name)
                supplier_full_name += repo.supplier.last_name;

            var supplier_email = (repo.supplier.email) ? repo.supplier.email : '';

            $container.find(".select2-result-repository__number").text(repo.projectNumber);
            $container.find(".select2-result-repository__client").text("Client: " + $.trim(client_full_name) + " (" + client_email + ")");
            $container.find(".select2-result-repository__supplier").text("Supplier: " + $.trim(supplier_full_name) + " (" + supplier_email + ")");

            return $container;
        }

    });

    $('input[name=quick_action_dates]').change(function() {
        var quickActionDate = $('input[name=quick_action_dates]:checked').val();

        if (quickActionDate == 'specific')
            $("[name=due_date]").prop('disabled', false);
        else
            $("[name=due_date]").prop('disabled', true);
    });

    //Select2 animal selection
    $('[name=offer_order_id]').on('change', function () {
        var projectId = $(this).val();

        if(projectId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.offer-order-by-id') }}",
                data: {
                    id: projectId,
                    type: $('[name=task_type]').val()
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.project.projectNumber, data.project.id, true, true);
                    // Append it to the select
                    $('[name=offer_order_id]').append(newOption);
                }
            });
        }
    });

    function alert_city(){
        let val_city = $('[name=city]').val();
        if(val_city.length === 0){
            $('[name=city]').addClass("is-invalid");
        }else{
            $('[name=city]').removeClass("is-invalid");
        }
    }

    $('[name=email]').keyup(function() {
        if ($('[name=email]').val().trim() != '') {
            $.ajax({
                type:'POST',
                url:"{{ route('api.check-institution') }}",
                data: {
                    id: $('[name=organization_id]').val(),
                    email: $('[name=email]').val()
                },
                success:function(data) {
                    if(data.already_in) {
                        $('[name=domain_name]').val('');
                        $('#institutionMessage').removeClass("d-none");
                        $('#institutionMessage').html("Institution email already in.");
                    }
                    else if (data.generic_domain) {
                        $('[name=domain_name]').val('');
                        $('#institutionMessage').removeClass("d-none");
                        $('#institutionMessage').html("Email domain is a generic domain. Check if the institution has other main email if is possible.");
                    }
                    else {
                        $('[name=domain_name]').val(data.domain_name);
                        $('#institutionMessage').addClass("d-none");
                    }
                }
            });
        }
    });

    $('[name=email]').on("change", function() {
        var email = $(this);
        if (email.val().trim() != '') {
            $.ajax({
                type:'GET',
                url:"{{ route('api.search-domain-name') }}",
                data: {
                    email: email.val()
                },
                beforeSend: function() {
                    email.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
                },
                success:function(data) {
                    if(data.domain_name != null){
                        $("#search-box").removeClass("search-box-correct");
                        $('[name=canonical_name]').val(data.domain_name.canonical_name);
                    }
                },complete: function() {
                    email.css("background", "#FFF");
                }
            });
        }
    });

    $("#search-box").keyup(function() {
        $("#search-box").removeClass("is-invalid");
        $("#suggesstion-box").addClass("d-none");
        $("#search-box").removeClass("search-box-correct");
        validateCanonical($(this).val());
        $.ajax({
            type: "get",
            url: "{{ route('api.canonical-name-select2') }}",
            data: {
                search: $(this).val(),
                email: $("[name=email]").val()
            },
            beforeSend: function() {
                $("#search-box").css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
            },
            success: function(data) {
                if(typeof data.items !== "undefined" && data.items.length > 0) {
                    var $container = $(
                        "<div class='select2-result-repository clearfix'>" +
                        "<div class='select2-result-repository__meta'>" +
                            "<div class='select2-result-repository__name'></div>" +
                            "<div class='select2-result-repository__canonical_name'></div>" +
                        "</div>" +
                        "</div>"
                    );
                    var html = "";
                    $.each(data.items, function(key, value) {
                        if(typeof value.name !== "undefined" && value.name !== ""){
                            $container.find(".select2-result-repository__name").text("Institution: " + value.name);
                        }
                        if(typeof value.domain_name !== "undefined" && value.domain_name !== ""){
                            $container.find(".select2-result-repository__name").text("Domain name: " + value.domain_name);
                        }
                        $container.find(".select2-result-repository__canonical_name").text("Canonical Name: " + value.canonical_name);
                        var canonical_name = value.canonical_name.replace("'", "´");
                        $container.find(".select2-result-repository__meta").attr("onclick", "set_value('" + canonical_name + "')");
                        html += $container.html();
                    });
                    $("#suggesstion-box").removeClass("d-none");
                    $("#suggesstion-box").html(html);
                }else{
                    $("#suggesstion-box").addClass("d-none");
                }
            },complete: function() {
                $("#search-box").css("background", "#FFF");
            },
        });
    });

    $("#assing-offer-form").on('change', function (e) {
        e.preventDefault();
        var form = document.forms.namedItem("assing-offer-form");
        var formdata = new FormData(form);
        var html_data = $('#assing-offer-data').html();
        $('#assing-offer-data').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            url: "{{ route('inbox.getOfferAssing') }}",
            method: "post",
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function (r) {
                console.log(r);
                if(r.error){
                    $('#assing-offer-data').html("<p class='norecselected'>There is no offer to show</p>");
                }else{
                    $('#assing-offer-data').html(r.content);
                }
            }
        });

    });

    $("#assing-order-form").on('change', function (e) {
        e.preventDefault();
        var form = document.forms.namedItem("assing-order-form");
        var formdata = new FormData(form);
        var html_data = $('#assing-order-data').html();
        $('#assing-order-data').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            url: "{{ route('inbox.getOrderAssing') }}",
            method: "post",
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function (r) {
                console.log(r);
                if(r.error){
                    $('#assing-order-data').html("<p class='norecselected'>There is no order to show</p>");
                }else{
                    $('#assing-order-data').html(r.content);
                }
            }
        });

    });

    $("#assing-surplu-form").on('change', function (e) {
        e.preventDefault();
        var form = document.forms.namedItem("assing-surplu-form");
        var formdata = new FormData(form);
        var html_data = $('#assing-surplu-data').html();
        $('#assing-surplu-data').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            url: "{{ route('inbox.getSurpluAssing') }}",
            method: "post",
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function (r) {
                console.log(r);
                if(r.error){
                    $('#assing-surplu-data').html("<p class='norecselected'>There is no order to show</p>");
                }else{
                    $('#assing-surplu-data').html(r.content);
                }
            }
        });

    });

    $("#assing-wanted-form").on('change', function (e) {
        e.preventDefault();
        var form = document.forms.namedItem("assing-wanted-form");
        var formdata = new FormData(form);
        var html_data = $('#assing-wanted-data').html();
        $('#assing-wanted-data').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            url: "{{ route('inbox.getWantedAssing') }}",
            method: "post",
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function (r) {
                console.log(r);
                if(r.error){
                    $('#assing-wanted-data').html("<p class='norecselected'>There is no order to show</p>");
                }else{
                    $('#assing-wanted-data').html(r.content);
                }
            }
        });

    });

    $("#get-all-email-account").on('click', function (e) {
        e.preventDefault();
        var account = $("#acount_show").val();
        var get_all_email_account = $('#get-all-email-account').html();
        $('#get-all-email-account').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'POST',
            url:"{{ route('inbox.getAllEmailAccount') }}",
            data: {
                account: account
            },
            success:function(data) {
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    $('#get-all-email-account').html(get_all_email_account);
                }else{
                    Swal.fire({
                        title: "Get all email",
                        html: data.message,
                        icon: 'success',
                        showCancelButton: true,
                        cancelButtonText: "Done",
                        confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
                        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                        buttonsStyling: false,
                        closeOnConfirm: true,
                        showConfirmButton: false,
                        closeOnCancel: true,
                    });
                    $('#get-all-email-account').html(get_all_email_account);
                }
            }
        });
    });

    $("#acount_show").on("change", function(e){
        var acount_show = $(this).val();
        var url_search = $(location).attr('search');
        var url = $(location).attr('href');
        url = url.replace("#", "");
        $('#list-content-message').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $(".pagination_info").addClass("d-none");
        if(!url.includes("acount_show")){
            if(url.includes("?")){
                url += "&acount_show=" + acount_show;
            }else{
                url += "?acount_show=" + acount_show;
            }
        }else{
            if(type_page !== "inbox"){
                if(type_page === "deleteditems"){
                    url = "{{route('inbox.emailDashboard')}}?is_delete=1&acount_show=" + acount_show;
                }
                else if(type_page === "drafts"){
                    url = "{{route('inbox.emailDashboard')}}?is_draft=1&acount_show=" + acount_show;
                }else if(type_page === "sentitems"){
                    url = "{{route('inbox.emailDashboard')}}?is_send=1&acount_show=" + acount_show;
                }else{
                    url = "{{route('inbox.emailDashboard')}}?" + type_page +"=1&acount_show=" + acount_show;
                }
            }else{
                url = "{{route('inbox.emailDashboard')}}?acount_show=" + acount_show;
            }

        }

        window.location = url;
    });

    function validateCanonical(value = null){
        $.ajax({
            type: "get",
            url: "{{ route('organisations.validateCanonical') }}",
            data: {
                search: value,
                email: $("[name=email]").val()
            },
            beforeSend: function() {
                $("#search-box").css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
            },
            success: function(data) {
                if(data.error){
                    $("#search-box").addClass("is-invalid");
                    $("#search-box").removeClass("is-valid");
                    $("#invalid-canonical_name").removeClass("d-none");
                    $("#invalid-canonical_name").html(data.message);
                    $("#search-box").removeClass("search-box-correct");
                    $(".invalid-feedback-tooltips").removeClass("d-none");
                    $("#search-box").attr("data-validate", "false");
                }else{
                    $("#search-box").removeClass("is-invalid");
                    $("#search-box").addClass("is-valid");
                    $("#invalid-canonical_name").html("");
                    $("#search-box").addClass("search-box-correct");
                    $(".invalid-feedback-tooltips").addClass("d-none");
                    $("#search-box").attr("data-validate", "true")
                }
            },complete: function(r) {
                $("#search-box").css("background", "#FFF");
            },
        });
    }

    $("html").click(function() {
        $("#suggesstion-box").addClass("d-none");
    });

    $(".saveInstitution").on("click", function(e) {
        if($("#search-box").attr("data-validate") === "false"){
            e.preventDefault();
        }
    });

    $(".add_default_text").on("click", function(){
        var text = $(this).attr("data-text");
        var description = $("#createdTaskModal #description").html();
        description += " " + text;
        $("#createdTaskModal #description").html(description);
    });

    $('.add_std_text').on('click', function(){
        var text = $(this).attr('data-text');
        email_body = CKEDITOR.instances.email_body.getData(email_body);
        email_body = text + '<br /><br />' + email_body;
        CKEDITOR.instances.email_body.setData(email_body);
    });

    function modal_toggle(e){
        let text = $(e).parent().find('.hidden-info').html()
        $('.modal-content-p').html(text)
        $('.new-modal').toggleClass('is-visible');
    }

    function modal_toggle_close(e){
        $('.new-modal').removeClass('is-visible');
    }

    $('.contact-select2-inbox').select2({
        ajax: {
            url: "{{ route('api.contacts-select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    type: $(this).attr('type'),
                    page: params.page
                };
            },
            processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        /*more: (params.page * 10) < data.total_count*/
                        more: false
                    }
                };
            },
            cache: true
        },
        allowClear: true,
        placeholder: '- search contact -',
        minimumInputLength: 3,
        templateResult: formatContact,
        templateSelection: formatContactSelection
    });

    function formatContactSelection(repo) {
        if (repo.id)
            return repo.email ? repo.email : repo.id;
        return repo.text;
    }

    function formatContact (repo) {

        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__fullname'></div>" +
                "<div class='select2-result-repository__email'></div>" +
                "<div class='select2-result-repository__cellnumber'></div>" +
                "<div class='select2-result-repository__level'></div>" +
            "</div>" +
            "</div>"
        );

        var full_name = '';
        if(repo.title)
            full_name += repo.title + " ";
        if(repo.first_name)
            full_name += repo.first_name + " ";
        if(repo.last_name)
            full_name += repo.last_name;
        if(repo.name)
            full_name += repo.name;

        var email = (repo.email) ? repo.email : '';
        var mobile_phone = (repo.mobile_phone) ? repo.mobile_phone : '';
        mobile_phone = (repo.phone) ? repo.phone : '';
        var level = "";
        if(repo.organisation){
            level = repo.organisation.level;
        }
        if(repo.level){
            level = repo.level;
        }

        $container.find(".select2-result-repository__fullname").text("Contact: " + $.trim(full_name));
        $container.find(".select2-result-repository__email").text("Email: " + email);
        $container.find(".select2-result-repository__cellnumber").text("Mobile: " + mobile_phone);
        $container.find(".select2-result-repository__level").text("Level: " + level);

        return $container;
    }

    $(document).on("scroll", function(){
        var scroll_position = $(document).scrollTop();
        if(scroll_position >= 100){
            $(".scroll-option-bar").addClass("scroll-option-bar-fixed");
        }else{
            $(".scroll-option-bar").removeClass("scroll-option-bar-fixed");
        }
    });

    $('.exportInboxRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#recordsPerPage').val();
        $("label[for='count_selected_records']").html('(' + count_selected_records + ')');
        $("label[for='count_page_records']").html('(' + count_page_records + ')');

        $('#exportInbox').modal('show');
    });

    $('#exportInbox').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportInbox [name=export_option]:checked').val();

        var ids = [];
        if (export_option == "selection") {
            $(":checked.selector").each(function () {
                ids.push($(this).val());
            });
        } else {
            $(".selector").each(function () {
                ids.push($(this).val());
            });
        }

        if (ids.length == 0)
            alert("There are not records to export.");
        else {
            var url = "{{route('inbox.export')}}?items=" + ids;
            window.location = url;

            $('#exportInbox').modal('hide');
        }
    });

    /**
     * This event-handler only works on those elements that exist in the Dom when the code was executed
     * Since the emails page is refreshed with ajax-calls, we need to use delegation based event handlers here
     */
    $(document).on('change', '#selectAll', function(){
        $(".message-list :checkbox.selector").prop('checked', this.checked);
    });

    $(".submitFilterEmail").on('click', function (e) {
        e.preventDefault();
        var form = document.forms.namedItem("filter-email-form");
        var formdata = new FormData(form);
        $('#list-content-message').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $(this).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            url: "{{ route('inbox.filterInbox') }}",
            method: "post",
            data: formdata,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function (r) {
                if(r.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    location.reload();
                }
            }
        });
    });

    $(".search-box").keyup(function() {
        var t = $(this);
        var wc = $(this).val();
        var fldinput = t.attr("id");
        if (this.id === 'find_cc' || this.id === 'find_bcc') {
            wc = wc.split(",").pop();
            if (this.id === 'find_cc') {
               fldinput = 'email_cc';
               $('#email_cc').val($(this).val());
            } else if (this.id === 'find_bcc') {
               fldinput = 'email_bcc';
               $('#email_bcc').val($(this).val());
            }
        }
        if (wc.length >= 3) {
            t.removeClass("is-invalid");
            var result = t.attr("data-result");
            $("." + result).addClass("d-none");
            t.removeClass("search-box-correct");
            $("." + result).removeClass("d-none");
            $.ajax({
                type: "get",
                url: "{{ route('api.contacts-select2-filter-email') }}",
                data: {
                  q: wc
                },
                beforeSend: function() {
                    t.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
                },
                success: function(data) {
                    if(typeof data.items !== "undefined" && data.items.length > 0) {
                        var $container = $(
                            "<div class='select2-result-repository clearfix'>" +
                            "<div class='select2-result-repository__meta'>" +
                                "<div class='select2-result-repository__name'></div>" +
                                "<div class='select2-result-repository__instituto'></div>" +
                                "<div class='select2-result-repository__email'></div>" +
                            "</div>" +
                            "</div>"
                        );
                        var html = "";
                        $.each(data.items, function(key, value) {
                            if(value.first_name != "undefined" && value.first_name != null && value.last_name != "undefined" && value.last_name != null){
                                $container.find(".select2-result-repository__name").text("Contact Name: " + value.first_name + " " + value.last_name);
                            }
                            if(value.organisation != "undefined" && value.organisation != null){
                                $container.find(".select2-result-repository__instituto").text("Institution name: " + value.organisation);
                                $container.find(".select2-result-repository__name").addClass("d-none");
                            }
                            if(value.email != "undefined" && value.email != null){
                                $container.find(".select2-result-repository__email").text("Email: " + value.email);
                            }

                            var email = value.email.replace("'", "´");
                            $container.find(".select2-result-repository__meta").attr("onclick", "set_value('" + email + "','" + fldinput + "', '" + result + "')");                          
                            html += $container.html();
                        });

                        $("." + result).html(html);
                    }else{
                        $("." + result).addClass("d-none");
                    }
                },complete: function() {
                    t.css("background", "#FFF");
                },
            });
        }
    });

    function set_value(value, t, result){
         if (t === 'email_cc' || t === 'email_bcc') {
            value = value.replace("´", "'");
            $('#' + t).val(value);
            emailSaveOriginal(t);
        }else{
            value = value.replace("´", "'");
            $('[name=' + t + ']').val(value);
            $("." + result).addClass("d-none");
        }
    }

    function emailSaveOriginal(t) {
       if (t === 'email_cc') {
          var fldemail = 'find_cc';
          $("#suggesstion-box-email-cc").addClass("d-none");
       } else {
          var fldemail = 'find_bcc';
          $("#suggesstion-box-email-bcc").addClass("d-none");
        }
       var arr = $('#' + fldemail).val().split(',');
       wc = arr.pop();
       arr = $.grep(arr, function(value) {
         return $.trim(value) != $.trim(wc);
    });
       if (arr.length > 0) {
         $('#' + fldemail).val(arr.join(',') + ',' + $('#' + t).val());
       } else {
         $('#' + fldemail).val($('#' + t).val());
        }
    }

    function selectize_input_remove(value){
        $(".selectize-input_bcc .item[data-value='" + value + "']").remove();
        var bcc_email = $("#email_bcc").val();
        bcc_email = bcc_email.split(",");
        var new_bcc = "";
        for (let i = 0; i < bcc_email.length; i++) {
            if(value != bcc_email[i] && bcc_email[i] != ""){
                new_bcc += bcc_email[i] + ",";
            }
        }
        $("#email_bcc").val(new_bcc);
        if($(".selectize-input_bcc").html() == ''){
            $(".selectize-control_bcc").addClass("d-none");
        }
    }

    function selectize_input_remove_cc(value){
        $(".selectize-input_cc .item[data-value='" + value + "']").remove();
        var cc_email = $("#email_cc").val();
        cc_email = cc_email.split(",");
        var new_cc = "";
        for (let i = 0; i < cc_email.length; i++) {
            if(value != cc_email[i] && cc_email[i] != ""){
                new_cc += cc_email[i] + ",";
            }
        }
        $("#email_cc").val(new_cc);
        if($(".selectize-input_cc").html() == ''){
            $(".selectize-input_cc").addClass("d-none");
        }
    }


    var editor = CKEDITOR.replace('email_body', {
        // Define the toolbar groups as it is a more accessible solution.
        toolbarGroups: [{
            "name": "document",
            "groups": ["mode"]
            },
            {
            "name": "basicstyles",
            "groups": ["basicstyles"]
            },
            {
            "name": "links",
            "groups": ["links"]
            },
            {
            "name": "paragraph",
            "groups": ["list", "align"]
            },
            {
            "name": "insert",
            "groups": ["insert"]
            },
            {
            "name": "styles",
            "groups": ["styles"]
            },
            {
            "name": "colors",
            "groups": ["colors"]
            }
        ],
        extraPlugins: 'stylesheetparser',
        height: 200,
        // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
    });

    // Dropzone
    if ($('.dropzoneEmail').length) {
        // Now that the DOM is fully loaded, create the dropzone, and setup the
        // event listeners
        var surplusDropzone = new Dropzone(".dropzoneEmail", {
            maxFilesize: 10 // 10mb
            /*autoProcessQueue: false*/
        });
        surplusDropzone.on("complete", function(file) {
            surplusDropzone.removeFile(file);
            if(file.xhr.responseText){
                var error = JSON.parse(file.xhr.responseText);
                if(error.error){
                    $.each( error.error, function( key, value ) {
                        $.NotificationApp.send("Error message!", value, 'top-right', '#fff', 'error');
                    });
                }else{
                    $(".attachments_upload_item").removeClass("d-none");
                    var attachments = $(".attachments_upload_item").html();
                    var attachments_id = $("#attachments_upload").val();
                    attachments_id = error.attachment_id + "," + attachments_id;
                    attachments += error.attachments;
                    $(".attachments_upload_item").html(attachments);
                    $("#attachments_upload").val(attachments_id);
                }
            }
        });
    }

    function delete_attachment(id){
        var button = $(".delete_attachment-" + id).html();
        $(".delete_attachment-" + id).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'POST',
            url:"{{ route('inbox.deleteAttachment') }}",
            data: {
                id: id
            },
            success: function (r) {
                if(r.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    var attachments_id = $("#attachments_upload").val();
                    var value = id;

                    attachments_id = attachments_id.split(",");
                    var new_attachment = "";
                    for (let i = 0; i < attachments_id.length; i++) {
                        if(value != attachments_id[i] && attachments_id[i] != ""){
                            new_attachment += attachments_id[i] + ",";
                        }
                    }
                    $("#attachments_upload").val(new_attachment);
                    $(".item-attachment-" + value).remove();
                    if(new_attachment == ''){
                        $(".attachments_upload_item").addClass("d-none");
                    }
                    $(".delete_attachment-" + id).html(button);
                }
            }
        });
    }
    $(".inbox-leftbar a").on("click", function(e){
        $('#list-content-message').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $(".pagination_info").addClass("d-none");
    });

    $(".addNewColor").on("click", function(){
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        if(ids.length == 0)
            alert("You must select one items.");
        else{
            $(".color_email_ids").val(ids);
            $("#addColorEmail").modal("show");
        }
    })

    $(".addEmailColor").on("click", function(){
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        var idColor = $(this).attr("data-id");
        if(ids.length == 0)
            alert("You must select one items.");
        else{
            var button = $(".addColor").html();
            $(".addColor").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.addColorEmail') }}",
                data: {
                    ids: ids,
                    idColor: idColor
                },
                success: function (r) {
                    if(r.error){
                        $.NotificationApp.send("Error message!", r.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", r.message, 'top-right', '#fff', 'success');
                        $(".addColor").html(button);
                        location.reload();
                    }
                }
            });
        }
    })

    $(".removeColor").on("click", function(){
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });
        if(ids.length == 0)
            alert("You must select one items.");
        else{
            var button = $(".addColor").html();
            $(".addColor").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.removeColor') }}",
                data: {
                    ids: ids
                },
                success: function (r) {
                    if(r.error){
                        $.NotificationApp.send("Error message!", r.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", r.message, 'top-right', '#fff', 'success');
                        $(".addColor").html(button);
                        location.reload();
                    }
                }
            });
        }
    })

</script>
@endsection
