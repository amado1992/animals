@include('components.errorlist')

<div class="form-row mb-2">
    {!! Form::label('action', 'Action *', ['class'=> 'col-md-3']) !!}
    <div class="col-md-4">
        {!! Form::select('action', $actions, null, ['class' => 'form-control ', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('due_date', 'Date of today') !!}
    </div>
    <div class="col-md-6">
        <div>
            {!! Form::date('created_at', Carbon\Carbon::today()->format('Y-m-d'), ['class' => 'form-control mb-2']) !!}
        </div>
    </div>
</div>

<div class="form-row mb-2">
    {!! Form::label('description', 'Description *', ['class'=> 'col-md-3']) !!}
    <div class="col-md-9">
        {!! Form::textarea('description', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
    </div>
</div>
<div class="form-row mb-2">
    {!! Form::label('user_in_charge', 'Action by *', ['class'=> 'col-md-3']) !!}
    <div class="col-md-4">
    {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('due_date', 'Action ready on *') !!}
    </div>
    <div class="col-md-6">
        <div>
            <label>{!! Form::radio('quick_action_dates', 'today') !!} Today</label>
        </div>
        <div>
            <label>{!! Form::radio('quick_action_dates', 'tomorrow') !!} Tomorrow</label>
        </div>
        <div>
            <label>{!! Form::radio('quick_action_dates', 'week') !!} End of this week</label>
        </div>
        <div>
            <label>{!! Form::radio('quick_action_dates', 'month') !!} End of this month</label>
        </div>
        <div>
            <label>{!! Form::radio('quick_action_dates', 'specific') !!} Specific date</label>
            {!! Form::date('due_date', !empty($task) && $task->never == 0 ? $task->due_date : '', ['class' => 'form-control mb-2', 'disabled']) !!}
        </div>
        <div>
            <label>{!! Form::radio('quick_action_dates', 'never') !!} Never should be done by</label>
            {!! Form::date('due_date_never', !empty($task) && $task->never == 1 ? $task->due_date : '', ['class' => 'form-control mb-2', 'disabled']) !!}
        </div>
        <div>
            <label>{!! Form::radio('quick_action_dates', 'none') !!} No date</label>
        </div>
    </div>
</div>
<hr />

<div class="row">
    <div class="col-md-3">
        {!! Form::label('offer_order', 'Connect task to: ') !!}
    </div>
    <div class="col-md-9">
        {!! Form::label('offer_order', 'Select offer or order to search') !!}
        <div class="d-flex align-items-start">
            {!! Form::select('task_type', ['offer' => 'Offer', 'order' => 'Order'], (isset($task) ? $task->taskable_type : null), ['class' => 'form-control mr-2', 'style' => 'width: 100px', 'placeholder' => '- select -']) !!}
            <select class="offer-order-select2 form-control" style="width: 100%" name="offer_order_id">
                @if( isset($task) && $task->taskable )
                    <option value="{{ $task->taskable->id }}" selected>{{ Str::upper($task->taskable_type) . ': ' . $task->taskable->full_number }}</option>
                @endif
            </select>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('comment', 'Comment') !!}
        {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => '4']) !!}
    </div>
</div>

{!! Form::hidden('calendar_view', $calendar_view, ['class' => 'form-control']) !!}

@if ($calendar_view)
<div class="mb-2">
    {!! Form::checkbox('finish_task', false) !!}
    {!! Form::label('finish_task', 'Mark as finished') !!}
</div>
@endif

<hr class="mb-3">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if ($calendar_view)
    <a href="{{ route('tasks.indexCalendar') }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('tasks.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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

        if (quickActionDate == 'never')
            $("[name=due_date_never]").prop('disabled', false);
        else
            $("[name=due_date_never]").prop('disabled', true);
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

    var editor = CKEDITOR.replace('comment', {
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

</script>

@endsection
