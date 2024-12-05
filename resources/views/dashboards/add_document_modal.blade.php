
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        {!! Form::open(['route' => 'general_documents.upload_file', 'files' => 'true']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Dashboard</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::file('file_to_upload', ['id' => 'file_to_upload', 'class' => 'form-control', 'required']) !!}
                    {!! Form::hidden('id', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="btn-group mt-2">
                        <button type="button" class="btn btn-secondary dropdown-toggle button-parent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            -- Select Block --
                        </button>
                        <div class="dropdown-menu parent-dropdown">
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Upload', ['class' => 'btn btn-primary']) !!}
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
