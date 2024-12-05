<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {!! Form::open() !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add/Edit classification.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        {!! Form::label('common_name', 'Common name') !!}
                        {!! Form::text('classification_common_name', null, ['id' => 'classification_common_name', 'classId' => 0, 'class' => 'form-control', 'required']) !!}
                        {!! Form::hidden('classification_id', 0, ['class' => 'form-control']) !!}
                        {!! Form::hidden('classification_rank', null, ['class' => 'form-control']) !!}
                        {!! Form::hidden('classification_belongs_to', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::label('common_name_spanish', 'Common name spanish') !!}
                        {!! Form::text('classification_common_name_spanish', null, ['id' => 'classification_common_name_spanish', 'classId' => 0, 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        {!! Form::label('scientific_name', 'Scientific name') !!}
                        {!! Form::text('classification_scientific_name', null, ['id' => 'classification_scientific_name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {!! Form::label('code', 'Code') !!}
                        {!! Form::number('classification_code', null, ['id' => 'classification_code', 'class' => 'form-control', 'required']) !!}
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
