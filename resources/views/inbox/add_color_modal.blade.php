
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'inbox.createColor']) !!}
            <div class="modal-body">
                @include('components.errorlist')
                <div class="row mb-3">
                    <div class="col-md-8">
                        {!! Form::label('title', 'Name *', ['class' => 'font-weight-bold']) !!}
                        {!! Form::text('title', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        {!! Form::label('color', 'Color *', ['class' => 'font-weight-bold']) !!}
                        {!! Form::color('color', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
                    </div>
                </div>
            </div>
            <input type="hidden" name="color_email_ids" class="color_email_ids">

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="submitAddDashboard">Save Color</button>
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>
        {!! Form::close() !!}
        </div>
    </div>
</div>
