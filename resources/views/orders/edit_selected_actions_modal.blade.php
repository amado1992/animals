
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        {!! Form::open(['route' => 'orders.editSelectedActions']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit selected actions</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-5">
                    {!! Form::label('toBeDoneBy', 'To be done by: ', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('toBeDoneBy', ['IZS' => 'IZS', 'Client' => 'Client', 'Supplier' => 'Supplier', 'Transport' => 'Transport'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    {!! Form::hidden('order_action_id', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::label('action_date', 'Action date: ', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('action_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5">
                    {!! Form::label('action_remind_date', 'Remind date: ', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('action_remind_date', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::label('action_date', 'Received date: ', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('action_received_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('remark', 'Remark: ', ['class' => 'font-weight-bold']) !!}
                    {!! Form::textarea('remark', null, ['class' => 'form-control', 'rows' => 3]) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Edit actions', ['id' => 'sendEditSelectedActions', 'class' => 'btn btn-primary']) !!}
            <button type="reset" class="btn btn-secondary">Reset</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
