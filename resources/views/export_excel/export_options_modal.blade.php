
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        {!! Form::open(['id' => $modalId]) !!}

        <div class="alert alert-danger" style="display:none"><ul></ul></div>

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Export options</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('select_option', 'Select option: ', ['class' => 'font-weight-bold']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::radio('export_option', 'selection', true) !!}
                    {!! Form::label('selected_records', 'Selected records') !!}
                    {!! Form::label('count_selected_records', ' ', ['class' => 'mr-2']) !!}
                    {!! Form::radio('export_option', 'all') !!}
                    {!! Form::label('page', 'Page records') !!}
                    {!! Form::label('count_page_records', ' ') !!}
                </div>
            </div>
            @if ($modalId === 'exportContacts')
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::radio('file_option', 'xlsx', true) !!}
                        {!! Form::label('excel_type', 'File (xlsx)', ['class' => 'mr-2']) !!}
                        {!! Form::radio('file_option', 'csv') !!}
                        {!! Form::label('csv_type', 'File (csv)') !!}
                    </div>
                </div>
            @endif
        </div>

        <div class="modal-footer">
            {!! Form::submit('Export', ['class' => 'btn btn-primary']) !!}
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
