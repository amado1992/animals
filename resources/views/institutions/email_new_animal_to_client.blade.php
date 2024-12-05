@extends('layouts.admin')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('vendor/progress-bar/asProgress.css') }}" type="text/css">
@endsection

@section('main-content')

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="newAnimalsTabs">
                        <li class="nav-item">
                            <a class="nav-link" id="list-tab" data-toggle="tab" href="#listTab" role="tab" aria-controls="listTab" aria-selected="true"><i class="fas fa-fw fa-paw"></i> Last inserted surplus records</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="email-tab" data-toggle="tab" href="#emailTab" role="tab" aria-controls="emailTab" aria-selected="false"><i class="fas fa-fw fa-mail-bulk"></i> View send email surplus records</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show" id="listTab" role="tabpanel" aria-labelledby="list-tab">'
                            <div class="row" style="margin: 0 0 12px 3px;">
                                <div class="col-md-3">
                                    {!! Form::label('updated_at_from', 'Create date', ['class' => 'font-weight-bold']) !!}
                                    {!! Form::date('filter_updated_at_from', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="table-responsive mb-2" id="table-new-animals">
                                @include('institutions.table-new-animals')
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="emailTab" role="tabpanel" aria-labelledby="email-tab">
                            <div id="form_send">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="mb-4">Email new animal to A-level institutions</h1>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button class="btn btn-primary btn-lg" id="exportEmails" type="button">Export emails from A-level institutions</button>
                                    </div>
                                </div>

                                @include('components.errorlist')

                                <div class="row mb-2">
                                    <div class="col-md-2">
                                        {!! Form::label('email_from', 'Email from:', ['class' => 'font-weight-bold']) !!}
                                    </div>
                                    <div class="col-md-10">
                                        {!! Form::text('email_from', $email_from, ['class' => 'form-control', 'required']) !!}
                                        <span class="invalid-feedback" role="alert">
                            <strong id="text_email_from"></strong>
                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-2">
                                        {!! Form::label('email_subject', 'Subject:', ['class' => 'font-weight-bold']) !!}
                                    </div>
                                    <div class="col-md-10">
                                        {!! Form::text('email_subject', $email_subject, ['class' => 'form-control', 'required']) !!}
                                        <span class="invalid-feedback" role="alert">
                            <strong id="text_email_subject"></strong>
                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        {!! Form::label('email_body', 'Email body:', ['class' => 'font-weight-bold']) !!}
                                    </div>
                                    <div class="col-md-10">
                                        {!! Form::textarea('email_body', "", ['id' => 'email_body', 'class' => 'form-control']) !!}
                                        <span class="invalid-feedback" role="alert">
                                            <strong id="text_email_body"></strong>
                                        </span>
                                    </div>
                                </div>

                                <hr class="mb-4">

                                <button class="btn btn-primary btn-lg" id="newAnimals" type="button">Send to A-level institutions</button>
                                <a href="{{ route('organisations.index') }}" class="btn btn-link" type="button">Cancel</a>
                            </div>

                            <div class="d-none" id="result_send">
                                <div class="progress_info">
                                    <span class="spinner-border spinner-progess spinner-border-sm" role="status" style="width: 10px; height: 10px; float: left; margin: 5px 3px 0 0;"></span>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="row d-none" id="spinner_finished">
                                    <div class="col-md-12 text-center">
                                        <span class="spinner-border spinner-border-sm" role="status" style="width: 30px; height: 30px;"></span>
                                    </div>
                                </div>
                                <hr>
                                <div id="text_report">
                                    <div class="row" style="overflow-y: scroll; height: 434px;">
                                        <div class="col-md-12">
                                            <h5><b>Emails sent successfully</b></h5>
                                            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                                                <tr style="vertical-align: top;">
                                                    <td style="font-weight: bold; margin-bottom: 10px; width: 300px;">
                                                        Emails
                                                    </td>
                                                    <td style="font-weight: bold; margin-bottom: 10px;">
                                                        Name
                                                    </td>
                                                </tr>
                                                <tbody id="body_report_success">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
    <script src="{{ asset('vendor/progress-bar/jquery-asProgress.js') }}"></script>

    <script type="text/javascript">

        var config = {
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
            height: 300,
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
        };

        var invoiceDocEditor = CKEDITOR.replace('email_body', config);

        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.progress').asProgress({
                'namespace': 'progress'
            });
        });

        $('#newAnimalsTabs a[href="#listTab"]').tab('show');

        $(".selector").prop("checked", false);

        $('#newAnimals').on('click', function(event) {
            event.preventDefault();
            var ids = [];
            $(".selector").each(function(){
                if(($(this).is(':checked')) ) {
                    ids.push($(this).val());
                }
            });
            $(".form-control").removeClass('is-invalid');
            var newAnimals = $("#newAnimals").html();
            $("#newAnimals").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('organisations.institutionSendNewAnimal') }}",
                data: {
                    email_from: $('[name=email_from]').val(),
                    email_subject: $('[name=email_subject]').val(),
                    email_body: $('[name=email_body]').html(),
                    item: ids
                },
                success:function(data) {
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        CKEDITOR.instances.email_body.setData(data.data.table_new_animals);
                        $("#table-new-animals").html(data.data.table_delete_animals);
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#5ba035', 'success');
                    }
                },complete: function(r){
                    $("#newAnimals").html(newAnimals);
                    $.each( r.responseJSON.errors, function( key, value ) {
                        $("[name="+key+"]").addClass('is-invalid');
                        $("#text_"+key+"").html(value[0]);
                    });
                }
            });
        });

        $('#exportEmails').on('click', function (event) {
            event.preventDefault();
            var url = "{{route('organisations.exportEmailInstitutionsLevelA')}}";
            window.location = url;
        });

        $("[name=filter_updated_at_from]").on("change", function (e) {
            var filter_updated_at_from =$(this).val();
            $("#spinner").removeClass("d-none");
            $.ajax({
                type:'GET',
                url:"{{ route('organisations.filterDateNewAnimals') }}",
                data:{filter_updated_at_from: filter_updated_at_from},
                success: function(data){
                    if (data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#5ba035', 'success');

                        $("#table-new-animals").html(data.table_new_animals);
                    }
                },complete: function(data){
                    $("#spinner").addClass("d-none");
                }
            });
        });
        function selector(){
            var status = [];
            var ids = [];
            $(".selector").each(function(){
                if(($(this).is(':checked')) ) {
                    ids.push($(this).val());
                }
            });
            var filter_updated_at_from =$("[name=filter_updated_at_from]").val();

            $("#spinner").removeClass("d-none");
            $.ajax({
                type:'POST',
                url:"{{ route('organisations.deleteItemsNewAnimals') }}",
                data:{item: ids, filter_updated_at_from: filter_updated_at_from},
                success: function(data){
                    if (data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#5ba035', 'success');

                        $("#email_body").html(data.table_new_animals);
                        CKEDITOR.instances.email_body.setData(data.table_new_animals);
                    }
                },complete: function(data){
                    $("#spinner").addClass("d-none");
                }
            });
        }
    </script>

@endsection
