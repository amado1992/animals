@include('components.errorlist')

<div class="form-row mb-2">
    {!! Form::label('action', 'Action *', ['class'=> 'col-md-3']) !!}
    <div class="col-md-4">
        {!! Form::select('action', $actions, null, ['class' => 'form-control ', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2 d-none">
    <div class="col-md-3">
        {!! Form::label('due_date', 'Date of today') !!}
    </div>
    <div class="col-md-6">
        <div>
            {!! Form::hidden('created_at', Carbon\Carbon::today()->format('Y-m-d'), ['class' => 'form-control mb-2']) !!}
        </div>
    </div>
</div>
<div class="form-row mb-2">
    <div class="col-md-3">

    </div>
    <div class="col-md-9">
        <div class="btn-group me-1">
            <button type="button" class="btn btn-sm btn-light dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">
                Default Text
            </button>
            <div class="dropdown-menu">
                @if (!empty($default_text))
                    @foreach ($default_text as $row)
                        <a class="dropdown-item add_default_text" data-text="{{ $row->text }}" href="javascript: void(0);">{{ substr($row->text, 0, 50) }}@if(strlen($row->text) >= 50)...@endif</a>
                    @endforeach
                @endif
            </div>
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
    <select class="form-control" style="width: 100%" name="user_id" placeholder="- select -">
        <option value="">- select -</option>
        <option value="sender">Sender</option>
        @if( !empty($users) )
            @foreach ($users as $key => $row)
                <option value="{{ $key }}">{{ $row }}</option>
            @endforeach
        @endif
    </select>
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
            {!! Form::date('due_date', null, ['class' => 'form-control mb-2', 'disabled']) !!}
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
            {!! Form::select('task_type', ['offer' => 'Offer', 'order' => 'Order'], (isset($task) ? $task->taskable_type : null), ['class' => 'form-control task_type-select mr-2', 'style' => 'width: 100px', 'placeholder' => '- select -']) !!}
            <select class="offer-order-select2 form-control" style="width: 100%" name="offer_order_id">
                @if( isset($task) && $task->taskable )
                    <option value="{{ $task->taskable->id }}" selected>{{ Str::upper($task->taskable_type) . ': ' . $task->taskable->full_number }}</option>
                @endif
            </select>
        </div>
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
<button type="button" class="btn btn-link" data-dismiss="modal" aria-label="Close">Cancel</button>
