@extends('layouts.admin')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('css/milton/app.min.css') }}" type="text/css">
    <link href="{{ asset('vendor/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .card-columns .card{
            padding: 20px;
        }
        .btn-soft-primary{
            color: #3bafda;
            background-color: transparent !important;
            border-color: rgba(59,175,218,.12);
            /* border-radius: 15px; */
            padding: 2px 10px 2px 10px;
        }
        .message-list li .col-mail-1 .title{
            left: 2px !important;
            top: -8px !important;
        }
        .message-list li .col-mail-2 .date, .message-list li .col-mail-2 .subject{
            top: -11px !important;
            font-size: 14px;
        }
        .message-list li .col-mail-2{
            top: 26px !important;
            left: 2px !important;
        }
        .table{
            color: #343a40 !important;
        }
        .btn-block{
            background-color: #edeff1;
            margin: 9px 5px;
            flex-basis: initial;
            flex-grow: initial;
            text-align: left;
            width: 100%!important;
            border-radius: 15px 4px 0 0 !important;
            font-size: 15px;
        }
        .btn-soft-primary{
            width: 100%;
            text-align: left !important;
            border-radius: 15px 4px 0 0 !important;
            margin: 9px 5px;
            padding: 2px 13px;
            font-size: 16px;
        }
        .card-columns{
            column-count: 5 !important;
        }
        .col-md-2{
            flex: 2 0 19.66667% !important;
            max-width: 23.66667% !important;
        }
        #content{
            height: 1000px !important;
        }

        .show_block{
            position: absolute !important;
            margin: 0 0 0 -8px !important;
            width: 100% !important;
            z-index: 100 !important;
        }
        .message-list li:hover{
            background: transparent;
        }
        .accordion-collapse{
            overflow-y: scroll;
            overflow-x: hidden;
            height: auto;
            max-height: 300px;
        }
        .accordion-collapse::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .accordion-collapse::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        .accordion-collapse::-webkit-scrollbar-thumb:hover {
            background: #b3b3b3;
            box-shadow: 0 0 2px 1px rgba(0, 0, 0, 0.2);
        }
        .accordion-collapse::-webkit-scrollbar-thumb:active {
            background-color: #999999;
        }
        .accordion-collapse::-webkit-scrollbar-track {
            background: #e1e1e1;
            border-radius: 4px;
        }
        .accordion-collapse::-webkit-scrollbar-track:hover,
        .accordion-collapse::-webkit-scrollbar-track:active {
        background: #d4d4d4;
        }
        .header-title a:hover {
            color: #54545c !important;
        }
        .body_email_inbox_block{
            border-top: transparent !important;
            background: #fff;
            max-height: 750px !important;
            padding: 0px !important;
            z-index: 100000;
        }

        .card_email_inbox_block{
            position: absolute !important;
            margin: 0 0 0 -8px !important;
            width: 500% !important;
            z-index: 10000 !important;
            background: transparent !important;
            border-top: transparent !important;
        }
        .header_show_email_inbox{
            width: 19.66667% !important
        }
        .close-block{
            position: absolute;
            right: -7px;
            top: -13px;
            background: #fff;
            border-radius: 50px;
            padding: 1px 3px 0px 3px;
            z-index: 1000;
        }

        .dropdown-item.active, .dropdown-item:active{
            color: #000000 !important;
            background: #eaeef0;
            border-radius: 5px;
        }
        .close_block_inbox_zindex{
            z-index: 1000000 !important;
        }

    </style>
@endsection

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Dashboard') }}</h1>

    @if (session('status'))
        <div class="alert alert-success border-left-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row mb-2">
        @if (!empty($widget['dashboard_yellow']))
            @php
                $count_cols = 0;
            @endphp
            @foreach ($widget['dashboard_yellow'] as $dashboard)
                @if (!empty($dashboard->dashboards))
                    @include('dashboards.item_block')
                @endif
                @if ($dashboard->show_only == 1 && Auth::user()->id != 2)
                    @php
                        $count_cols = $count_cols + 1;
                    @endphp
                @endif
            @endforeach
            @for ($i = 0; $i < $count_cols; $i++)
                <div class="col-md-2 mb-2">
                </div>
            @endfor
            <div class="col-md-2 mb-2">
            </div>
            <div class="col-md-2 mb-2">
            </div>
        @endif
    </div>
    <div class="row mb-2">
        @if (!empty($widget['dashboard_blue']))
            @php
                $count_cols = 0;
            @endphp
            @foreach ($widget['dashboard_blue'] as $key => $dashboard)
                @if ($key == 4)
                    <div class="col-md-2 mb-2">
                    </div>
                    </div>
                    <div class="row mb-2">
                @endif
                @if (!empty($dashboard->dashboards))
                    @include('dashboards.item_block')
                @endif
                @if ($dashboard->show_only == 1 && Auth::user()->id != 2)
                    @php
                        $count_cols = $count_cols + 1;
                    @endphp
                @endif
            @endforeach
            @for ($i = 0; $i < $count_cols; $i++)
                <div class="col-md-2 mb-2">
                </div>
            @endfor
            <div class="col-md-2 mb-2">
            </div>
        @endif
    </div>
    <div class="row mb-2">
        @if (!empty($widget['dashboard_green']))
            @php
                $count_cols = 0;
            @endphp
            @foreach ($widget['dashboard_green'] as $dashboard)
                @if (!empty($dashboard->dashboards))
                    @include('dashboards.item_block')
                @endif
                @if ($dashboard->show_only == 1 && Auth::user()->id != 2)
                    @php
                        $count_cols = $count_cols + 1;
                    @endphp
                @endif
            @endforeach
            @for ($i = 0; $i < $count_cols; $i++)
                <div class="col-md-2 mb-2">
                </div>
            @endfor
            <div class="col-md-2 mb-2">
            </div>
        @endif
    </div>
    <div class="row mb-2">
        @if (!empty($widget['dashboard_red']))
            @php
                $count_cols = 0;
            @endphp
            @foreach ($widget['dashboard_red'] as $dashboard)
                @if (!empty($dashboard->dashboards))
                    @include('dashboards.item_block')
                @endif
                @if ($dashboard->show_only == 1 && Auth::user()->id != 2)
                    @php
                        $count_cols = $count_cols + 1;
                    @endphp
                @endif
            @endforeach
            @for ($i = 0; $i < $count_cols; $i++)
                <div class="col-md-2 mb-2">
                </div>
            @endfor
            <div class="col-md-2 mb-2">
            </div>
        @endif
    </div>
    @include('dashboards.add_document_modal', ['modalId' => 'uploadGeneralDoc'])
    @include('dashboards.body_email_modal', ['modalId' => 'emailBodyModal'])
    @include('inbox.new_email_modal', ['modalId' => 'formEmail'])
@endsection
@section('page-scripts')
<script type="text/javascript">
   $(".add-document").on("click", function(e){
        var dataId = $(this).attr('data-id');

        $.ajax({
            type:'GET',
            url:"{{ route('dashboards.getDashboardParent') }}",
            data:{dataId: dataId},
            success: function(data){
                $("#uploadGeneralDoc").modal("show");

                if (data.error){

                }else{
                    if(data.dashboards != ''){
                        $(".parent-dropdown").html(data.dashboards);
                    }
                }
            }
        });
    });

    $('.delete_item').on('click', function () {
        var ids = [];
        var dataId = $(this).attr('data-id');
        $(":checked.delete_item_select").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $('.menu-item-' + dataId).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('dashboards.deleteItems') }}",
                data:{items: ids},
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

    $('.delete_item_body').on('click', function () {
        var dataId = $(this).attr('data-id');
        var ids = [];
        if(typeof dataId != 'undefined'){
            ids.push(dataId);
        }

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $(this).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('dashboards.deleteItems') }}",
                data:{items: ids},
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

    function showEmailDashboard(id){
        var body_name = "";
        var body_name = $(".email_body_show_" + id).attr("name");
        var _dt = $(".email-item-" + id);
        var show = _dt.attr("data-show");
        if(show === "true"){
            $(".email-content-" + id).addClass('d-none');
            $(".body_length_" + id).removeClass('d-none');
            _dt.attr("data-show", false);
            var scroll_position = $(document).scrollTop();
            $('html, body').animate({
                scrollTop: scroll_position
            }, 200);
        }else{
            $("#emailBodyModal").modal("show");
            $(".body-email-modal").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            if($(".body_iframe_" + id).attr("srcdoc").length == 0){
                $(".body-email-show_" + id).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                $.ajax({
                    type:'POST',
                    url:"{{ route('inbox.getBodyEmail') }}",
                    data: {
                            id: id
                    },
                    dataType: "JSON",
                    success:function(data) {
                        if(data.error){
                            $(".body_length_" + id).html('');

                        }else{
                            $(".body-email-show_" + id).html('');
                            var html_body = data.body;
                            $(".body_iframe_" + id).attr("srcdoc", html_body);
                            $(".body_length_" + id).html(html_body);
                            var body_length = $(".body_length_" + id).height();
                            $(".body_iframe_" + id).attr("height", body_length + 40);
                            $(".body_length_" + id).html("");
                            $(".attachments_" + id).html(data.attachments);
                            $(".body-email-modal").html($(".email-content-" + id).html());
                            $("#subject_modal").html(data.subject);
                            $(".reply-btn-dashboard").attr("data-email", data.email.from_email);
                            $(".reply-btn-dashboard").attr("data-id", data.email.id);
                            $(".reply-btn-dashboard").attr("data-from", data.email.to_email);
                            $(".forward-btn-dashboard").attr("data-email", data.email.from_email);
                            $(".forward-btn-dashboard").attr("data-id", data.email.id);
                            $(".forward-btn-dashboard").attr("data-from", data.email.to_email);
                            var item = $(".email_id_item_" + id).val();
                            $(".delete_item_body").attr("data-id", item);
                            $(".actions-email").removeClass("d-none");
                        }
                    }
                });
            }else{
                $("#emailBodyModal").modal("show");
                $.ajax({
                    type:'POST',
                    url:"{{ route('inbox.getBodyEmail') }}",
                    data: {
                            id: id
                    },
                    dataType: "JSON",
                    success:function(data) {
                        if(data.error){
                            $(".body_length_" + id).html('');

                        }else{
                            $(".body-email-show_" + id).html('');
                            var html_body = data.body;
                            $(".body_iframe_" + id).attr("srcdoc", html_body);
                            $(".body_length_" + id).html(html_body);
                            var body_length = $(".body_length_" + id).height();
                            $(".body_iframe_" + id).attr("height", body_length + 40);
                            $(".body_length_" + id).html("");
                            $(".attachments_" + id).html(data.attachments);
                            $(".body-email-modal").html($(".email-content-" + id).html());
                            $("#subject_modal").html(data.subject);
                            $(".reply-btn-dashboard").attr("data-email", data.email.from_email);
                            $(".reply-btn-dashboard").attr("data-id", data.email.id);
                            $(".reply-btn-dashboard").attr("data-from", data.email.to_email);
                            $(".forward-btn-dashboard").attr("data-email", data.email.from_email);
                            $(".forward-btn-dashboard").attr("data-id", data.email.id);
                            $(".forward-btn-dashboard").attr("data-from", data.email.to_email);
                            var item = $(".email_id_item_" + id).val();
                            $(".delete_item_body").attr("data-id", item);
                            $(".actions-email").removeClass("d-none");
                        }
                    }
                });
            }

            _dt.attr("data-show", true);
            $(".li-item-" + id).removeClass("unread");
            $.ajax({
                type:'POST',
                url:"{{ route('inbox.updateIsReaad') }}",
                data: {
                        id: id
                },
                dataType: "JSON",
                success:function(data) {
                }
            });
        }
    }
    $(".accordion-flush").hide();

    $(".show_data").click(function () {
        var t = $(this);
        var id= $(this).attr("data-id");
        var show = $(this).attr("data-show");
        if(show == "true"){
            idSetTimeout = setTimeout(function (element) {
                t.find(".data-card-" + id).toggle(0, function() {
                    t.find(".data-card-" + id).show();
                    t.addClass("show_block");
                });
            }, 5, t);
            t.attr("data-show", "false");
            $(".close-show-block_" + id).removeClass("d-none");
        }
    });

    $(".close-block").click(function () {
        var id= $(this).attr("data-id");
        var t = $(".show_block_" + id);
        var show = t.attr("data-show");
        if(show == "false"){
            idSetTimeout = setTimeout(function (element) {
                t.find(".data-card-" + id).toggle(0, function() {
                        t.find(".data-card-" + id).hide();
                        t.removeClass("show_block");
                    });
            }, 5, t);
            $(".close-show-block_" + id).addClass("d-none");
            t.attr("data-show", "true");
            $(".email_inbox").addClass("d-none");
            $(".body_email_inbox_block").addClass("d-none");
            $(".show_email_inbox").removeClass("card_email_inbox_block");
            $(".header_inbox").removeClass("header_show_email_inbox");
        }
    });

    $(".show_email_inbox").click(function () {
        var id= $(this).attr("data-id");
        $(".close-show-block_" + id).addClass("close_block_inbox_zindex");
        $(".email_inbox").removeClass("d-none");
        $(".body_email_inbox_block").removeClass("d-none");
        $(".show_email_inbox").addClass("card_email_inbox_block");
        $(".header_inbox").addClass("header_show_email_inbox");
    });


    $(".filter_data_action").on("click", function () {
        var t = $(this);
        var id = t.attr("data-id");
        var show = t.attr("data-show");
        if(show == "true"){
            $("#flush-collapse-" + id + " .accordion-body").html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            $.ajax({
                type:'POST',
                url:"{{ route('dashboards.getFilterData') }}",
                data: {
                        id: id
                },
                dataType: "JSON",
                success:function(data) {
                    if(data.error){
                        if(data.account){
                            Swal.fire({
                                title: "Add account !",
                                html: 'To be able to see your emails, you have to add your email account in the inbox view of the main menu',
                                icon: 'warning',
                                showCancelButton: true,
                                cancelButtonText: "Done",
                                cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                                buttonsStyling: false,
                                closeOnConfirm: true,
                                showConfirmButton: false,
                                closeOnCancel: true,
                            });
                            $("#flush-collapse-" + id + " .accordion-body").html("");
                        }
                    }else{
                        $("#flush-collapse-" + id + " .accordion-body").html(data.content);
                    }
                }
            });

            t.attr("data-show", "false");
        }else{
            t.attr("data-show", "true");
        }
    });
    $(".accordion-header").on("click", function(){
        var id = $(this).attr("data-id");
        setTimeout(function(t, show){
            var height_block = $(".show_block").height();
            if(height_block < 700){
                $('#content').attr("style", "height:" + 1000 + "px !important;");
            }else{
                $('#content').attr("style", "height:" + (height_block + 300) + "px !important;");
            }
        }, 500);
    });

    $(".reply-btn-dashboard").on("click", function(){
        var url = $(this).attr("data-url");
        var email  = $(this).attr("data-email");
        var dataId = $(this).attr("data-id");
        var email_from = $(this).attr("data-from");
        $("#formEmail").modal("show");
        $("#email_to").val('');
        $("#email_to").val(email);
        $("[name=email_from]").val(email_from);
        var body = $(".body_iframe_" + dataId).attr("srcdoc");
        var subject = $(".email-item-subject-" + dataId).html();
        var from  = $(".email-item-from-" + dataId).html();
        var html  = " From: " + from + "<br>";
        html  += " Subject: " + subject + "<br>";
        body = "<br><br><hr>" + html + body;
        $("#email_subject").val("");
        $("#email_subject").val("RE: " + subject);
        $('.sendEmail').attr("data-url", url);
        var attachments = $(".attachments_" + dataId).html();
        $(".attachment_new_email").html(attachments);
        editor.setData(body);
    });

    $(".forward-btn-dashboard").on("click", function(){
        var url = $(this).attr("data-url");
        var email  = $(this).attr("data-email");
        var dataId = $(this).attr("data-id");
        var email_from = $(this).attr("data-from");
        $("#items_email_send").val(dataId);
        $("#email_to").val('');
        $("#formEmail").modal("show");
        $("[name=email_from]").val(email_from);
        var body = $(".body_iframe_" + dataId).attr("srcdoc");
        var subject = $(".email-item-subject-" + dataId).html();
        var from  = $(".email-item-from-" + dataId).html();
        var html  = " From: " + from + "<br>";
        html  += " Subject: " + subject + "<br>";
        body = "<hr>" + html + body;
        $("#email_subject").val("");
        $("#email_subject").val(subject);
        $('.sendEmail').attr("data-url", url);
        var attachments = $(".attachments_" + dataId).html();
        $(".attachment_new_email").html(attachments);
        $("#attachments_draft").val(attachments);
        editor.setData(body);
    });

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
                    $("#formEmail").attr("data-draft", "true");
                    $("#formEmail").modal("hide");
                    Swal.fire({
                        title: "Email Sent !",
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
                    $("#formEmail").attr("data-draft", "true");
                    $("#formEmail").modal("hide");
                    Swal.fire({
                        title: "Email Sent !",
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


</script>

@endsection
