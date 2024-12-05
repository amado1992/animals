
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px;">
        <div class="modal-content">

            {!! Form::open(['route' => 'offers.addOfferSpecies']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add species</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-3 text-center">
                        <div class="row">
                            <div class="col pr-1">
                                {!! Form::label('males', 'M', ['class' => 'font-weight-bold']) !!}
                                {!! Form::number('quantityM', '0', ['class' => 'form-control pr-0', 'min' => 0]) !!}
                                {!! Form::hidden('offer_id', $offerId, ['class' => 'form-control']) !!}
                            </div>
                            <div class="col pl-1 pr-1">
                                {!! Form::label('females', 'F', ['class' => 'font-weight-bold']) !!}
                                {!! Form::number('quantityF', '0', ['class' => 'form-control pr-0', 'min' => 0]) !!}
                            </div>
                            <div class="col pl-1 pr-1">
                                {!! Form::label('females', 'U', ['class' => 'font-weight-bold']) !!}
                                {!! Form::number('quantityU', '0', ['class' => 'form-control pr-0', 'min' => 0]) !!}
                            </div>
                            <div class="col pl-1 pr-1">
                                {!! Form::label('females', 'P', ['class' => 'font-weight-bold']) !!}
                                {!! Form::number('quantityP', '0', ['class' => 'form-control pr-0', 'min' => 0]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        {!! Form::label('origin', 'Origin *', ['class' => 'font-weight-bold']) !!}
                        {!! Form::select('origin', $origin, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::label('continent', 'Continent', ['class' => 'font-weight-bold']) !!}
                        {!! Form::select('continent', $regionsNames, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                    <div class="col-md-4" style="margin: -12px 0 0 0px;">
                        {!! Form::label('oursurplus', 'Our surplus *', ['class' => 'font-weight-bold']) !!}
                        <select class="surpluses-filter-select2 form-control" style="width: 100%" name="select_surplus" disabled="disabled" data-region="" data-origin=""></select>
                        {!! Form::hidden('oursurplus_id', 0, ['class' => 'form-control']) !!}
                    </div>
                    <div>
                        <input type="hidden" name="region" id="region" value="">
                        <input type="hidden" name="region_text" id="region_text" value="">
                    </div>
                    <div class="col-md-1 align-self-end">
                        <a href="#" id="tempSelectedSpecies" class="btn btn-light">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                            {!! Form::label('selected_species', 'Selected species:', ['class' => 'font-weight-bold']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="selectedSpecies" class="table table-striped table-sm mb-0">
                            <thead>
                                <tr style="text-align: center;">
                                    <th></th>
                                    <th style="width: 50px;">M</th>
                                    <th style="width: 50px;">F</th>
                                    <th style="width: 50px;">U</th>
                                    <th style="width: 50px;">P</th>
                                    <th style="width: 100px;">Origin</th>
                                    <th style="width: 100px;">Continent</th>
                                    <th>Species</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="addSpeciesButton">
                {!! Form::submit('Save species', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
      </div>
  </div>
