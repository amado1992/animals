@include('components.errorlist')

<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_from', 'Email from:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_from', $email_from, ['class' => 'form-control', 'required']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_to', 'Email to:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_to', $email_to, ['class' => 'form-control search-box', 'required', 'data-result' => 'suggesstion-box-email-to']) !!}
        <div id="suggesstion-box-email-to" class="d-none suggesstion-box suggesstion-box-email-to"></div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_cc', 'Email cc:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
	@if ($email_code === 'to_approve_by_john')
        {!! Form::text('email_cc', '', ['class' => 'form-control']) !!}
	@else
		{!! Form::text('email_cc', 'info@zoo-services.com', ['class' => 'form-control']) !!}
	@endif
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_subject', 'Subject:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_subject', $email_subject, ['class' => 'form-control', 'required', $active_subject ? '' : 'readonly']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_attachments', 'Attachments:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        @foreach ($email_attachments as $attachment)
            {{ $attachment }},
        @endforeach
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        {!! Form::label('email_body', 'Email body:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::textarea('email_body', $email_body, ['id' => 'email_body', 'class' => 'form-control']) !!}
        {!! Form::hidden('offer_id', $offer->id, ['id' => 'offer_id', 'class' => 'form-control']) !!}
        {!! Form::hidden('email_option', $email_code, ['class' => 'form-control']) !!}
        {!! Form::hidden('parent_view', $parent_view, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row mt-4">
    <div class="col-md-2">
        {!! Form::label('remind', 'Remind:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::radio('reminder', 'yes', ['class' => 'form-control', 'required' => 'required']) !!}
        {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
        {!! Form::radio('reminder', 'no', ['class' => 'form-control', 'required' => 'required', 'checked' => 'checked']) !!}
        {!! Form::label('no', 'No') !!}
    </div>
</div>
<div class="row mt-3 d-none due_date_reminder">
    <div class="col-md-2">
        {!! Form::label('specific', 'Specific date:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-2">
        {!! Form::date('due_date', null, ['class' => 'form-control mb-2']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if ($parent_view === 'main')
    <a href="{{ route('offers.index') }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('offers.show', $offer) }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".search-box").keyup(function() {
        var t = $(this);
        if(t.val().length > 3){
            t.removeClass("is-invalid");
            var result = t.attr("data-result");
            $("." + result).addClass("d-none");
            t.removeClass("search-box-correct");
            $("." + result).removeClass("d-none");
            $.ajax({
                type: "get",
                url: "{{ route('api.contacts-select2-filter-email') }}",
                data: {
                    q: t.val()
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
                            $container.find(".select2-result-repository__meta").attr("onclick", "set_value('" + email + "', '" + t.attr("id") + "', '" + result + "')");
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


    $("#find_bcc").keypress(function(e) {
        if(e.which == 13) {
            var value = $(this).val();
            value = value.replace("´", "'");
            var selectize = $(".selectize-input_bcc").html();
            selectize += '<div class="item" data-value="' + value + '">' + value + '<a href="#" onclick="selectize_input_remove(`' + value + '`)" tabindex="-1" title="Remove">×</a></div>';
            $(".selectize-input_bcc").html(selectize);
            $(".selectize-control_bcc").removeClass("d-none");
            var email_bcc = $("#email_bcc").val();
            email_bcc += value + ",";
            $("#email_bcc").val(email_bcc);
            $("#find_bcc").val('');
        }
    });

    $("#find_cc").keypress(function(e) {
        if(e.which == 13) {
            var value = $(this).val();
            value = value.replace("´", "'");
            var selectize = $(".selectize-input_cc").html();
            selectize += '<div class="item" data-value="' + value + '">' + value + '<a href="#" onclick="selectize_input_remove_cc(`' + value + '`)" tabindex="-1" title="Remove">×</a></div>';
            $(".selectize-input_cc").html(selectize);
            $(".selectize-control_cc").removeClass("d-none");
            var email_cc = $("#email_cc").val();
            email_cc += value + ",";
            $("#email_cc").val(email_cc);
            $("#find_cc").val('');
        }
    });


    CKEDITOR.replace('email_body', {
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
    $('[name=reminder]').on("change", function(){
        var value = $(this).val();
        if(value === "yes"){
            $('.due_date_reminder').removeClass("d-none");
        }else{
            $('.due_date_reminder').addClass("d-none");
        }
    });
});

function set_value(value, t, result){
    if(result == "suggesstion-box-email-bcc"){
        value = value.replace("´", "'");
        var selectize = $(".selectize-input_bcc").html();
        selectize += '<div class="item" data-value="' + value + '">' + value + '<a href="#" onclick="selectize_input_remove(`' + value + '`)" tabindex="-1" title="Remove">×</a></div>';
        $(".selectize-input_bcc").html(selectize);
        $(".selectize-control_bcc").removeClass("d-none");
        $("." + result).html("");
        $("." + result).addClass("d-none");
        $('[name=' + t + ']').val('');
        var email_bcc = $("#email_bcc").val();
        email_bcc += value + ",";
        $("[name=email_bcc]").val(email_bcc);
    }else if(result == "suggesstion-box-email-cc"){
        value = value.replace("´", "'");
        var selectize = $(".selectize-input_cc").html();
        selectize += '<div class="item" data-value="' + value + '">' + value + '<a href="#" onclick="selectize_input_remove_cc(`' + value + '`)" tabindex="-1" title="Remove">×</a></div>';
        $(".selectize-input_cc").html(selectize);
        $(".selectize-control_cc").removeClass("d-none");
        $("." + result).html("");
        $("." + result).addClass("d-none");
        $('[name=' + t + ']').val('');
        var email_cc = $("[name=email_cc]").val();
        email_cc += value + ",";
        $("[name=email_cc]").val(email_cc);
    }else{
        value = value.replace("´", "'");
        $('[name=' + t + ']').val(value);
        $("." + result).addClass("d-none");
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

</script>

@endsection
