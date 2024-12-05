
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter guidelines" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'guidelines.filter', 'method' => 'GET']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter guidelines</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row mb-2">
            <div class="col-md-4">
                {!! Form::label('category', 'Category') !!}
                {!! Form::select('category', ['any' => 'Any', 'general' => 'General', 'requests' => 'Requests', 'orders' => 'Orders', 'contacts' => 'Contacts', 'mailing' => 'Mailing'], 'any', ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="mb-2">
            {!! Form::label('subject', 'Subject') !!}
            {!! Form::text('subject', null, ['class' => 'form-control']) !!}
        </div>

        <div>
            {!! Form::label('remark', 'Remark *') !!}
            {!! Form::text('remark', null, ['class' => 'form-control']) !!}
        </div>
      </div>
      <div class="modal-footer">

        {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>

      </div>

      {!! Form::close() !!}

    </div>
  </div>
</div>
