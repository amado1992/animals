<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Print options" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open() !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Print options</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-2 text-right">
                    {!! Form::label('document', 'Document: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_document_type', 'pdf', false, ['id' => 'pdf']) !!}
                    {!! Form::label('pdf', 'PDF', ['class' => 'mr-2']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_document_type', 'html', false, ['id' => 'html']) !!}
                    {!! Form::label('html', 'HTML') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2 text-right">
                    {!! Form::label('prices', 'Prices: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_prices', 'yes', false, ['id' => 'with_prices']) !!}
                    {!! Form::label('with_prices', 'With prices', ['class' => 'mr-2']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_prices', 'no', false, ['id' => 'without_prices']) !!}
                    {!! Form::label('without_prices', 'Without prices') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2 text-right">
                    {!! Form::label('language', 'Language: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_language', 'english', false, ['id' => 'english_language']) !!}
                    {!! Form::label('english_language', 'English', ['class' => 'mr-2']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_language', 'spanish', false, ['id' => 'spanish_language']) !!}
                    {!! Form::label('spanish_language', 'Spanish') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2 text-right">
                    {!! Form::label('pictures', 'Pictures: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-10">
                    {!! Form::radio('print_pictures', 'yes', false, ['id' => 'with_images']) !!}
                    {!! Form::label('with_images', 'With general images', ['class' => 'mr-2']) !!}
                </div>
                <div class="col-md-2"> </div>
                <div class="col-md-10">
                    {!! Form::radio('print_pictures', 'surplus', false, ['id' => 'with_images_surplus']) !!}
                    {!! Form::label('with_images_surplus', 'With images surplus or wanted', ['class' => 'mr-2']) !!}
                </div>
                <div class="col-md-2"> </div>
                <div class="col-md-10">
                    {!! Form::radio('print_pictures', 'no', false, ['id' => 'without_images']) !!}
                    {!! Form::label('without_images', 'Without images') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2 text-right">
                    {!! Form::label('stuffed', 'Stuffed: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_stuffed', 'yes', false, ['id' => 'with_text_stuffed']) !!}
                    {!! Form::label('with_text_stuffed', 'With text stuffed', ['class' => 'mr-2']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_stuffed', 'no', false, ['id' => 'without_text_stuffed']) !!}
                    {!! Form::label('without_text_stuffed', 'Without text stuffed', ['class' => 'mr-2']) !!}
                </div>
            </div>
            <div class="row mb-2 d-none print_client">
                <div class="col-md-2 text-right">

                </div>
                <div class="col-md-10">
                    <select class="contact-select2 form-control" type="filter_offer_client" style="width: 100%" name="filter_client_id"></select>
                </div>
            </div>
            <hr class="mb-4">
            <div class="row mb-2">
                <div class="col-md-2 text-right">
                    {!! Form::label('wanted', 'Wanted: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_wanted', 'yes', false, ['id' => 'with_wanted']) !!}
                    {!! Form::label('with_wanted', 'With wanted', ['class' => 'mr-2']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::radio('print_wanted', 'no', false, ['id' => 'without_wanted']) !!}
                    {!! Form::label('without_wanted', 'Without wanted') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2 text-right">
                    {!! Form::label('wanted_lists', 'Wanted lists: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-9">
                    {!! Form::select('print_wanted_list', $theWantedLists, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <hr class="mb-4">
            <div class="row mb-2">
                <div class="col-md-3 text-right">
                    {!! Form::label('select_option', 'Select option: ', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-9">
                    <div class="d-flex align-items-baseline">
                        <label class="mr-2">{!! Form::radio('export_option', 'selection', true) !!} Selected records{!! Form::label('count_selected_records', ' ') !!}</label>
                        <label class="mr-2">{!! Form::radio('export_option', 'page') !!} Page records{!! Form::label('count_page_records', ' ') !!}</label>
                        <label>{!! Form::radio('export_option', 'all') !!} All records</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="printOptionsDialogButton">
            {!! Form::hidden('route', $route) !!}
            {!! Form::submit('Select cover animals', ['class' => 'btn btn-primary']) !!}
            <button type="reset" class="btn btn-secondary">Reset</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
