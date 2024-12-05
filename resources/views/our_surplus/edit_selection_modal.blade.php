<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit selected standard surplus" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        {!! Form::open(['route' => 'our-surplus.editSelectedRecords']) !!}

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit selected standard surplus</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="row mb-2">
                        <div class="col-md-8">
                            {!! Form::label('availability', 'Availability', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('edit_selection_availability', $availability, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-8">
                            {!! Form::label('is_public', 'Public', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('edit_selection_is_public', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-8">
                            {!! Form::label('origin', 'Origin', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('edit_selection_origin', $origin, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-8">
                            {!! Form::label('age_group', 'Age group', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('edit_selection_age_group', $ageGroup, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::label('area', 'Area:', ['class' => 'font-weight-bold']) !!}<br>
                            @foreach($areas as $area)
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('edit_selection_area_id', $area->id) !!} {{$area->short_cut}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('cost_currency', 'Cost currency', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_cost_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('sale_currency', 'Sale currency', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_sale_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    {!! Form::label('add_to_stock_lists', 'Add to stock lists: ', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('selectionAddToStockLists', $ourSurplusLists->pluck('name', 'id'), null, ['id' => 'selectionAddToStockLists', 'class' => 'form-control', 'multiple']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('remove_from_stock_lists', 'Remove from stock lists: ', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('selectionRemoveFromStockLists', $ourSurplusLists->pluck('name', 'id'), null, ['id' => 'selectionRemoveFromStockLists', 'class' => 'form-control', 'multiple']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
          {!! Form::submit('Save surpluses', ['id' => 'sendEditSelectionForm', 'class' => 'btn btn-primary']) !!}
          <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
</div>
