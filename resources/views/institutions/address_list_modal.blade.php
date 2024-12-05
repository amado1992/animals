
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

        {!! Form::open(['id' => $modalId]) !!}

        <div class="alert alert-danger" style="display:none"><ul></ul></div>

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create contact address list</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex flex-row mb-3">
                        <div class="col-md-4 text-right">
                            {!! Form::label('select_language', 'Language: ', ['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-8">
                            {!! Form::radio('language_option', 'all', true) !!}
                            {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                            {!! Form::radio('language_option', 'EN') !!}
                            {!! Form::label('english', 'English', ['class' => 'mr-2']) !!}
                            {!! Form::radio('language_option', 'ES') !!}
                            {!! Form::label('spanish', 'Spanish') !!}
                        </div>
                    </div>
                    <div class="d-flex flex-row mb-3">
                        <div class="col-md-4 text-right">
                            {!! Form::label('select_institution_level', 'Level: ', ['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::select('select_institution_level', $organization_levels, null, ['class' => 'form-control-sm', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="d-flex flex-row mb-3">
                        <div class="col-md-4 text-right">
                            {!! Form::label('select_world_region', 'Part of world: ', ['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-8">
                            {!! Form::radio('world_region', 'area', true) !!}
                            {!! Form::label('area', 'Area', ['class' => 'mr-2']) !!}
                            {!! Form::radio('world_region', 'region') !!}
                            {!! Form::label('region', 'Continent', ['class' => 'mr-2']) !!}
                            {!! Form::radio('world_region', 'country') !!}
                            {!! Form::label('country', 'Country') !!}
                            <select class="standard-multiple-select2 form-control" style="width: 100%" name="world_region_selection" multiple required>
                                <option value="0">All</option>
                                @foreach($areas as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <span class="spinner-border spinner-border-sm spinner-world_region d-none" role="status" style="position: absolute; margin: 7px 0 0 -21px;"></span>
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div id="regionsDiv" class="col-md-6 overflow-auto" style="height: 150px;">
                            {!! Form::label('excludeContinents', 'Exclude continents:', ['class' => 'font-weight-bold']) !!}
                            <select class="continent-select2 form-control" style="width: 100%" name="exclude_continents" multiple>
                                @foreach($regions as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="countriesDiv" class="col-md-6 overflow-auto" style="height: 150px;">
                            {!! Form::label('exclude_countries', 'Exclude countries:', ['class' => 'font-weight-bold']) !!}
                            <select class="country-select2 form-control" style="width: 100%" name="exclude_countries" multiple>
                                @foreach($countries as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="padding-left: 0;">
                    <div class="d-flex flex-row">
                        <div class="col-md-5 text-right addresslist-institution">
                            {!! Form::label('select_institution_type', 'Institution type: ', ['class' => 'font-weight-bold']) !!}

                            <!-- Associations exclude -->
                            <div class="addresslist-association overflow-auto">
                              {!! Form::label('exclude_associations', 'Exclude associations:', ['class' => 'font-weight-bold']) !!}
                              @foreach($associationsEmail as $key => $value)
                                 <div class="checkbox">
                                     <label>
                                         {!! Form::checkbox('exclude_associations', $key) !!} {{$value}}
                                     </label>
                                 </div>
                              @endforeach
                           </div>
                          <!-- Associations exclude -->

                        </div>
                        <div class="col-md-7 overflow-auto addresslist-institution-checkboxes">
                            <div class="checkbox">
                                <label for="all">
                                    {!! Form::checkbox('institution_type_selection_all', 'all') !!} All
                                </label>
                            </div>
                            <div class="checkbox">
                                <label for="Z">
                                    {!! Form::checkbox('institution_type_selection', "Z") !!} {{$organization_types["Z"]}}
                                </label>
                            </div>
                            <div class="checkbox">
                                <label for="PBF">
                                    {!! Form::checkbox('institution_type_selection', "PBF") !!} {{$organization_types["PBF"]}}
                                </label>
                            </div>
                            <div class="checkbox" style="margin: 0 0 10px 0;">
                                <label for="AS">
                                    {!! Form::checkbox('institution_type_selection', "AS") !!} {{$organization_types["AS"]}}
                                </label>
                            </div>
                            <div class="checkbox" style="margin: 0 0 10px 0;">
                                <label for="EDMAT">
                                    {!! Form::checkbox('institution_type_selection', "EDMAT") !!} {{$organization_types["EDMAT"]}}
                                </label>
                            </div>
                            @foreach($organization_types as $key => $value)
                            @if (!in_array($key, ['Z', 'PBF', 'AS', 'EDMAT']))
                                <div class="checkbox">
                                    <label for="{{ $key }}">
                                        {!! Form::checkbox('institution_type_selection', $key) !!} {{$value}}
                                    </label>
                                </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Create', ['class' => 'btn btn-primary']) !!}
            <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
