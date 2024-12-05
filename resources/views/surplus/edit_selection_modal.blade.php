<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit selected surplus" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        {!! Form::open(['route' => 'surplus.editSelectedRecords']) !!}

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit selected surplus</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('supplier', 'Supplier institution', ['class' => 'font-weight-bold']) !!}
                    <select class="institution-select2 form-control" type="default" style="width: 100%" name="edit_selection_supplier_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('origin', 'Origin', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_origin', $origin, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('age_group', 'Age group', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_age_group', $ageGroup, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('cost_currency', 'Cost currency', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_cost_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('sale_currency', 'Sale currency', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_sale_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('supplier_level', 'Supplier level', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_supplier_level', $levels, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('to_members', 'To members', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_to_members', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('surplus_status', 'Status', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_surplus_status', $surplus_status, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('organisation_level', 'Level', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_organisation_level', $organization_levels, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            @if (!Auth::user()->hasRole('office'))
                <div class="row mb-2">
                    <div class="col-md-12">
                        {!! Form::label('add_to_surplus_lists', 'Add to surplus lists: ', ['class' => 'font-weight-bold']) !!}
                        {!! Form::select('selectionAddToSurplusLists', $surplusLists->pluck('name', 'id'), null, ['id' => 'selectionAddToSurplusLists', 'class' => 'form-control', 'multiple']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::label('remove_from_surplus_lists', 'Remove from surplus lists: ', ['class' => 'font-weight-bold']) !!}
                        {!! Form::select('selectionRemoveFromSurplusLists', $surplusLists->pluck('name', 'id'), null, ['id' => 'selectionRemoveFromSurplusLists', 'class' => 'form-control', 'multiple']) !!}
                    </div>
                </div>
            @endif
        </div>

        <div class="modal-footer">
          {!! Form::submit('Save surpluses', ['id' => 'sendEditSelectionForm', 'class' => 'btn btn-primary']) !!}
          <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
</div>
