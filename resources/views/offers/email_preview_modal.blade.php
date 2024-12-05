<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            {!! Form::open(['route' => 'offers.offerSendEmail']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Email preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-2">
                        {!! Form::label('email_from', 'Email from:', ['class' => 'font-weight-bold']) !!}
                    </div>
                    <div class="col-md-10">
                        {!! Form::text('email_from', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-2">
                        {!! Form::label('email_to', 'Email to:', ['class' => 'font-weight-bold']) !!}
                    </div>
                    <div class="col-md-10">
                        {!! Form::text('email_to', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-2">
                        {!! Form::label('email_cc', 'Email cc:', ['class' => 'font-weight-bold']) !!}
                    </div>
                    <div class="col-md-10">
                        {!! Form::text('email_cc', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-2">
                        {!! Form::label('email_subject', 'Subject:', ['class' => 'font-weight-bold']) !!}
                    </div>
                    <div class="col-md-10">
                        {!! Form::text('email_subject', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        {!! Form::label('email_body', 'Email body:', ['class' => 'font-weight-bold']) !!}
                    </div>
                    <div class="col-md-10">
                        {!! Form::textarea('email_body', null, ['id' => 'email_body', 'class' => 'form-control']) !!}
                        {!! Form::hidden('id_offer', null, ['id' => 'id_offer', 'class' => 'form-control']) !!}
                        {!! Form::hidden('email_option', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                {!! Form::submit('Send', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
