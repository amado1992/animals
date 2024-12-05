
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter interesting website" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'interesting-websites.filter', 'method' => 'GET']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter interesting website</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('site_category', 'Site category *') !!}
                    {!! Form::select('siteCategory', $categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>

            <div class="mb-2">
                {!! Form::label('site_name', 'Site name') !!}
                {!! Form::text('siteName', null, ['class' => 'form-control']) !!}
            </div>

            <div class="mb-2">
                {!! Form::label('site_url', 'Site url') !!}
                {!! Form::text('siteUrl', null, ['class' => 'form-control']) !!}
            </div>

            <div class="mb-2">
                {!! Form::label('site_remarks', 'Site remarks') !!}
                {!! Form::text('siteRemarks', null, ['class' => 'form-control']) !!}
            </div>

            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('site_username', 'Login username') !!}
                    {!! Form::text('loginUsername', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('site_password', 'Login password') !!}
                    {!! Form::text('loginPassword', null, ['class' => 'form-control']) !!}
                </div>
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
