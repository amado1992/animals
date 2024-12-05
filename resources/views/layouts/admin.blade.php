<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Zoo Services">
    <meta name="author" content="Jump Innovations">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css?v=1706819324') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css?v=2') }}" rel="stylesheet">

    <!-- Favicon -->
    <link href="{{ asset('favicon.ico') }}" rel="icon">

    <!-- Sweet Alert-->
    <link href="{{asset('vendor/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('vendor/fullcalendar/main.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.css') }}" type="text/css">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/dropzone-5.7.0/dist/dropzone.css')}}" />
    <!-- Lightbox2 js -->
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/lightbox2-master/css/lightbox.min.css')}}" />
     <!-- Jquery Toast css -->
     <link href="{{asset('vendor/jquery-toast-plugin/jquery.toast.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('vendor/nestable2/jquery.nestable.min.css') }}" type="text/css">
    @yield('page-css')

    @yield('page-css')

</head>
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    @include('elements.sidebar')

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column" style="background-color: #e8e8e8;">

        <!-- Main Content -->
        <div id="content">

            @include('elements.topbar')

            @hasSection('subnav-content')
                <div class="subheader px-3 bg-primary">
                    @yield('subnav-content')
                </div>
            @endif

            @hasSection('header-content')
                <div class="page-header bg-gradient-primary pt-2 px-4 mb-4 shadow" style="padding-bottom: 50px">
                    @yield('header-content')
                </div>
            @endif

            <!-- Begin Page Content -->
            <div class="container-fluid @hasSection('header-content') @else mt-4 @endif" @hasSection('header-content') style="margin-top: -75px" @endif>
                @if(session('status'))
                    <div class="alert alert-success border-left-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('error_msg'))
                    <div class="alert alert-danger border-left-danger" role="alert">
                        {{ session('error_msg') }}
                    </div>
                @endif

                @yield('main-content')
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

    </div>
    <!-- End of Content Wrapper -->

</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('Ready to Leave?') }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-link" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a class="btn btn-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap-4-autocomplete/dist/bootstrap-4-autocomplete.min.js" crossorigin="anonymous"></script>-->
<script src="{{ asset('vendor/bootstrap/js/bootstrap-4-autocomplete.min.js') }}" crossorigin="anonymous"></script>
<script type="text/javascript" src="{{ asset('vendor/bootstrap/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('vendor/ckeditor-full/ckeditor.js') }}"></script>
<script src="{{ asset('vendor/fullcalendar/main.js') }}"></script>
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('vendor/dropzone-5.7.0/dist/dropzone.js') }}"></script>
<!-- Lightbox2 js -->
<script src="{{asset('vendor/lightbox2-master/js/lightbox.min.js')}}"></script>
<!-- Sweet Alerts js -->
<script src="{{asset('vendor/sweetalert2/sweetalert2.min.js')}}"></script>
<!-- Tost-->
<script src="{{asset('vendor/jquery-toast-plugin/jquery.toast.min.js')}}"></script>
<!-- toastr init js-->
<script src="{{asset('vendor/jquery-toast-plugin/toastr.init.js')}}"></script>

<script src="{{asset('js/app.js')}}"></script>

@include('components.alert')

<script type="text/javascript">
    Dropzone.autoDiscover = false;
    // "uploadDropzone" is the camelized version of the HTML element's ID
    /*Dropzone.options.uploadDropzone = {
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 10 // MB
    };*/

    $(function() {
        // Catch window close
        /*window.addEventListener('beforeunload', (event) => {
            var confirmationMessage = "Are you sure you want to leave?";
            event.returnValue = confirmationMessage;
            return confirmationMessage;
        });*/

        $('.datatable').DataTable({
            /*"dom": 'rt<"bottom"p><"clear">'*/
            "dom": '<"top">rt<"bottom"pi><"clear">',
            "scrollX": true,
            "pageLength": 25
        });

        $('table.clickable tbody').on('click', 'tr', function (e) {
            if( !$(e.target).hasClass('no-click') && !$(e.target).parents().hasClass('no-click') ) {
                window.location.href = $(this).attr('data-url');
            }
        });

        $('.animal-select2').select2({
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
            templateResult: formatAnimal
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

        $('.contact-select2').select2({
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
                return repo.email ? repo.email : repo.id + ' (contact without email)';
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

        $('.institution-select2').select2({
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
            templateResult: formatInstitution
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

        $('.surplus-select2').select2({
            ajax: {
                url: "{{ route('api.surpluses-select2') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
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
            placeholder: '- search surplus -',
            minimumInputLength: 3,
            templateResult: formatSurplus,
            templateSelection: formatSurplusSelection
        });

        $('.surpluses-filter-select2').select2({
            ajax: {
                url: "{{ route('api.surpluses-filter-select2') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var region = $('.surpluses-filter-select2').attr("data-region");
                    var origin = $('.surpluses-filter-select2').attr("data-origin");
                    return {
                        q: params.term, // search term
                        page: params.page,
                        region: region,
                        origin: origin
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
            placeholder: '- search surplus -',
            minimumInputLength: 3,
            templateResult: formatSurplusFilter,
            templateSelection: formatSurplusSelection
        });

        function formatSurplus (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var APP_URL = {!! json_encode(url('/')) !!};

            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__avatar'><img class='float-right mr-4 rounded' style='max-width:50px;' src='" + APP_URL + "/storage/animals_pictures/" + repo.animal.id + "/" + repo.animal.catalog_pic + "' /></div>" +
                "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__region'></div>" +
                    "<div class='select2-result-repository__info'></div>" +
                    "<div class='select2-result-repository__quantities'></div>" +
                    "<div class='select2-result-repository__prices'></div>" +
                    "<div class='select2-result-repository__date'></div>" +
                "</div>" +
                "</div>"
            );

            var region = (repo.region) ? repo.region.name : '';
            var info = 'Sales prices in ' + repo.sale_currency + ' - ' + ((repo.is_public) ? 'Published' : 'Not published');
            var quantities = 'Availability: ' + repo.availability + ' - ' + repo.animal.common_name + ' (' + repo.animal.scientific_name + ')';
            var prices = 'M: ' + repo.salePriceM.toFixed(2) + ' F: ' + repo.salePriceF.toFixed(2) + ' U: ' + repo.salePriceU.toFixed(2) + ' P: ' + repo.salePriceP.toFixed(2);
            var date = 'Date: ' + repo.surplus_date;

            $container.find(".select2-result-repository__region").text(region);
            $container.find(".select2-result-repository__info").text(info);
            $container.find(".select2-result-repository__quantities").text(quantities);
            $container.find(".select2-result-repository__prices").text(prices);
            $container.find(".select2-result-repository__date").text(date);

            return $container;
        }

        function formatSurplusFilter (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var APP_URL = {!! json_encode(url('/')) !!};

            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__avatar'><img class='float-right mr-4 rounded' style='max-width:50px;' src='" + APP_URL + "/storage/animals_pictures/" + repo.animal.id + "/" + repo.animal.catalog_pic + "' /></div>" +
                "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__quantities'></div>" +
                "</div>" +
                "</div>"
            );

            var name = repo.animal.common_name + ' (' + repo.animal.scientific_name + ')';

            $container.find(".select2-result-repository__quantities").text(name);

            return $container;
        }

        function formatSurplusSelection (repo) {
            if (repo.animal)
                return repo.animal.common_name + ' (' + repo.animal.scientific_name + ')';
            else
                return repo.text;
        }

        $('.standard-multiple-select2').select2({
            maximumSelectionLength: 5,
            placeholder: '- select -'
        });

        $('.country-select2').select2({
            maximumSelectionLength: 5,
            placeholder: '- select -'
        });

        $('.continent-select2').select2({
            maximumSelectionLength: 5,
            placeholder: '- select -'
        });

        if ($('.dropzone').length) {
            // Now that the DOM is fully loaded, create the dropzone, and setup the
            // event listeners
            var surplusDropzone = new Dropzone(".dropzone", {
                maxFilesize: 10 // 10mb
                /*autoProcessQueue: false*/
            });
            surplusDropzone.on("complete", function(file) {
                surplusDropzone.removeFile(file);
                if(file.xhr.responseText){
                    var error = JSON.parse(file.xhr.responseText);
                    if(error.errors){
                        $.each( error.errors, function( key, value ) {
                            $.NotificationApp.send("Error message!", value, 'top-right', '#fff', 'error');
                        });
                    }else{
                        $.NotificationApp.send("Success message!", "File uploaded successfully", 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }

        /***************************TOP RIGHT TASKS*************************************/
        $(':checkbox:checked.selector-task').prop('checked', false);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#selector-top-task .slds-timeline__trigger").attr("onclick", "");

        $('#selectAllTasks').on('change', function () {
            $("#selector-top-task :checkbox.selector").prop('checked', this.checked);
        });

        $('#markSelectedTasksAsDone').on('click', function () {
            var ids = [];
            $(":checked.selector-task").each(function(){
                ids.push($(this).val());
            });

            if(ids.length == 0)
                alert("You must select items to delete.");
            else if(confirm("Are you sure that you want to mark the selected tasks as finished?")) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('tasks.markSelectedTasksAsFinishedOrNot') }}",
                    data:{items: ids},
                    success:function(data){
                        $(":checked.selector-task").each(function(){
                            $("#task"+$(this).val()).remove();
                        });
                    }
                });
            }
        });


        $('.updateStatus').on('click', function () {
            var ids = [];
            var status = $(this).attr("data-status");
            var button = $(this).html();
            $(":checked.selector").each(function () {
                ids.push($(this).val());
            });

            if (ids.length == 0)
                alert("You must select items to delete.");
            else if (confirm("Are you sure that you want to update status the selected taks?")) {
                $(this).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                $(this).attr("disabled", "disabled");
                $.ajax({
                    type: 'POST',
                    url: "{{ route('tasks.updateStatus') }}",
                    data: {items: ids, status: status},
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });

        $("#showMenu").on("click", function(){
            var show = $(this).attr("data-show");
            var t = $(this);
            if(show == "true"){
                $(".siderbar-content").addClass("show_sidebar");
                $(".siderbar-content").removeClass("hide_sidebar");
                t.attr("data-show", "false");
            }else{
                $(".siderbar-content").addClass("hide_sidebar");
                $(".siderbar-content").removeClass("show_sidebar");
                t.attr("data-show", "true");
            }
        });

        $(".addDasboard").on('click', function (e) {
            var url = $(this).attr("data-url");

            $("#url_document").val(url);

            $("#uploadGeneralDoc").modal("show");
        });

        $(".submitAddDashboardDocument").on('click', function (e) {
            e.preventDefault();
            var form = document.forms.namedItem("addDashboardDocument");
            var formdata = new FormData(form);
            var btn_save = $('.submitAddDashboardDocument').html();
            $('.submitAddDashboardDocument').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                url: "{{ route('general_documents.addDashboardDocument') }}",
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
                    $('.submitAddDashboardDocument').html(btn_save);
                },
                complete: function(r){
                    $('.submitAddDashboardDocument').html(btn_save);
                    $.each( r.responseJSON.errors, function( key, value ) {
                        $(".alert-danger").removeClass('d-none');
                        $(".alert-danger").html("<p>" + value + "</p>");
                        $('#spinner').addClass('d-none');
                    });
                }
            });

        });
        /*******************************************************************************/
    });
    function showEmail(id){
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
            $(".email-content-" + id).removeClass('d-none');
            $(".body_length_" + id).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
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
                        }
                    }
                });
            }else{
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
                   // Deduct 1 from total unread mail
                   $('.total-inbox').text($('.total-inbox').text()-data);
                }
            });
        }
    }
</script>

@yield('page-scripts')

</body>
<script>
    window.addEventListener('onbeforeunload', function (event) {
        event.stopImmediatePropagation();
    });

    window.addEventListener('beforeunload', function (event) {
        event.stopImmediatePropagation();
    });
</script>
</html>
