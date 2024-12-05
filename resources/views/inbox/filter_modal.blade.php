<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter contacts" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter emails</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            {!! Form::open(['route' => 'inbox.filterInbox', 'method' => 'GET']) !!}
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::label('filter_email', 'Enter (part of) email address') !!}
                        {!! Form::text('filter_email', null, ['id' => 'filter_email', 'class' => 'form-control autocomplete']) !!}
                        {!! Form::label('filter_keyword', 'Enter keyword for search in subject or email summary, case insensitive', ['class' => 'filter-keyword']) !!}
                        {!! Form::text('filter_keyword', null, ['id' => 'filter_keyword', 'class' => 'form-control autocomplete']) !!}
                    </div>
                </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Filter</button>
            <button type="button" class="btn btn-link" data-dismiss="modal" aria-label="Close">Cancel</button>
        </div>
        {!! Form::close() !!}

        </div>
    </div>
</div>
