
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        {!! Form::open(['id' => $modalId]) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add order actions</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-row mb-3">
                        <div class="col-md-2">
                            {!! Form::label('select_action_category', 'Category: ', ['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::select('select_action_category', $action_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div class="col-md-2">
                            {!! Form::label('select_actions', 'Actions: ', ['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-10">
                            <select class="standard-multiple-select2 form-control" style="width: 100%" name="action_selection" multiple></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Add actions', ['class' => 'btn btn-primary']) !!}
            <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
