<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {!! Form::open(['route' => $route]) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-3">
                        {!! Form::label('action', 'Action *') !!}
                        {!! Form::select('action', $task_actions, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                    </div>
                    <div class="col-md-9">
                        {!! Form::label('description', 'Description') !!}
                        {!! Form::text('description', null, ['class' => 'form-control', 'required']) !!}
                        {!! Form::hidden('id', null, ['class' => 'form-control']) !!}
                        {!! Form::hidden('task_id', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::label('user_in_charge', 'Action by *') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::label('due_date', 'Action ready on') !!}
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
                            {!! Form::date('due_date', null, ['class' => 'form-control mb-2']) !!}
                        </div>
                        <div>
                            <label>{!! Form::radio('quick_action_dates', 'none') !!} No date</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
