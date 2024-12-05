
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter Domain Name" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'domain-name-link.filter', 'method' => 'GET']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter Domain Name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="mb-2">
            {!! Form::label('domain_name', 'Domain Name', ['class' => 'font-weight-bold']) !!}
            {!! Form::text('domain_name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="mb-2">
          {!! Form::label('canonical_name', 'Canonical Name', ['class' => 'font-weight-bold']) !!}
          {!! Form::text('canonical_name', null, ['class' => 'form-control']) !!}
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
