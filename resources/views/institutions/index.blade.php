@extends('layouts.admin')

@section('header-content')
    <div class="float-right">
        @if (Auth::user()->hasPermission('institutions.create'))
            <a href="{{ route('organisations.create') }}" id="create-button" class="btn btn-dark">
                <i class="fas fa-fw fa-plus"></i> Add new
            </a>
        @endif
        <button id="filterOrganizationsBtn" type="button" class="btn btn-secondary" data-toggle="modal"
                data-target="#filterOrganizations">
            <i class="fas fa-filter"></i> Filter
        </button>
        @if (!Auth::user()->hasRole('office'))
            <a href="{{ route('organisations.doublesView') }}" class="btn btn-secondary">
                <i class="fas fa-clone"></i> Find duplicates
            </a>
        @endif
        <a href="{{ route('organisations.showAll') }}" class="btn btn-secondary">
            <i class="fas fa-window-restore"></i> Show all
        </a>
        @if (Auth::user()->hasPermission('contacts.approve-members'))
           <a href="{{ route('contacts-approve.index') }}" class="btn btn-dark">
              <i class="fas fa-fw fa-edit"></i> Contacts to approve
              <span class="badge badge-soft-danger float-end ms-2 total-inbox" style="color: #fff;background: #f1556c;">{{ $nrContactsToApprove }}</span>
           </a>
        @endif
        @if (Auth::user()->hasPermission('institutions.update'))
            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#editSelectedRecords">
                <i class="fas fa-fw fa-edit"></i> Edit selection
            </button>
        @endif
        <button type="button" id="mergeOption" class="btn btn-dark">
            <i class="fas fa-fw fa-building"></i> Compare & Merge
        </button>
        <button type="button" id="convert" class="btn btn-dark">
            <i class="fas fa-fw fa-retweet"></i> Convert
        </button>
        @if (Auth::user()->hasPermission('institutions.delete'))
            <button type="button" id="deleteSelectedItems" class="btn btn-dark">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </button>
        @endif
        @if (Auth::user()->hasPermission('contacts.export-contacts'))
            <a id="exportInstitutionsRecords" href="#" class="btn btn-light" data-toggle="modal"
               data-target="#exportInstitutions">
                <i class="fas fa-fw fa-save"></i> Export
            </a>
        @endif
        @if (Auth::user()->hasPermission('contacts.contacts-address-list'))
            <a id="createMailingAddressList" href="#" class="btn btn-light" data-toggle="modal"
               data-target="#createAddressList">
                <i class="fas fa-fw fa-save"></i> Address list
            </a>
        @endif
        <a href="{{ route('organisations.showNewAnimals') }}" class="btn btn-light">
            <i class="fas fa-fw fa-envelope"></i> Send new animals to A-level institutions
        </a>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-building mr-2"></i> Institutions & contacts</h1>

    <div class="d-flex justify-content-between mb-2">
        <div class="d-flex align-items-center text-white">
            <label class="pr-2 pt-1">Order by:</label>
            {!! Form::open(['id' => 'organizationsOrderByForm', 'route' => 'organisations.filterOrganizations', 'method' => 'GET']) !!}
            <select class="custom-select custom-select-sm w-auto" id="orderByField" name="orderByField">
                @foreach ($orderByOptions as $orderByKey => $orderByValue)
                    <option value="{{ $orderByKey }}"
                            @if(isset($orderByField) && $orderByField == $orderByKey) selected @endif>{{$orderByValue}}</option>
                @endforeach
            </select>
            <select id="orderByDirection" name="orderByDirection" class="custom-select custom-select-sm w-auto">
                <option @if(!isset($orderByDirection)) selected @endif value="desc">Descending</option>
                <option @if(isset($orderByDirection) && $orderByDirection == 'asc') selected @endif value="asc">
                    Ascending
                </option>
            </select>
            
            @foreach ($filterDataKeyVal as $filterKey => $filterValue)
               {!! Form::hidden(str_replace('hidden_', '', $filterKey), $filterValue) !!}
            @endforeach
            
            {!! Form::close() !!}
        </div>
        <div class="d-flex align-items-center text-white">
            Page: {{$organisations->currentPage()}} | Records:&nbsp;
            @if (Auth::user()->hasPermission('contacts.see-all-contacts'))
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'organisations.recordsPerPage', 'method' => 'GET']) !!}
                {!! Form::text('recordsPerPage', $organisations->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
            @else
                {{$organisations->count()}}
            @endif
            &nbsp;| Total: {{$organisations->total()}}
        </div>
    </div>

    @if (Auth::user()->hasPermission('contacts.see-all-contacts'))
        <div class="float-right ml-2">
            {{ $organisations->links() }}
        </div>
    @endif
@endsection

@section('main-content')

    <div class="card shadow mb-2">
        <div class="card-body selectall">
            <div class="d-flex flex-row items-center">
                <div class="d-flex align-items-center">
                    <input type="checkbox" id="selectAll" name="selectAll"/>&nbsp;Select all
                    <input type="hidden" id="countInstitutionsVisible"
                           value="{{ ($organisations->count() > 0) ? $organisations->count() : 0 }}"/>
                </div>

                <div class="d-flex align-items-center">
                    <span class="ml-3 mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('organisations.removeFromOrganizationSession', $key) }}"
                           class="btn btn-sm btn-secondary btn-icon-split mr-1"><span
                                class="text">{{$value}}</span><span class="icon text-white-50"><i
                                    class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-2">
        <div class="card-body">
            @unless($organisations->isEmpty())
                <div class="table-responsive mb-2">
                     <table class="table table-striped table-sm mb-0">
                         <thead>
                         <tr>
                            <th style="width: 4%"></th>
                            <th class="text-center" nowrap>I/C</th>
                            <th style="min-width:10%">Name</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">In</th>
                            <th>Institution</th>
                            <th class="text-center">Phone</th>
                            <th>City</th>
                            <th>Country</th>
                            <th style="min-width:5%">Email address</th>
                            <th class="text-center">Web</th>
                            <th>Level</th>
                            <th>Assoc</th>
                            <th>Mailing</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($organisations as $organisation)
                            @php
                                if ($organisation->model_type === "I") {
                                    $model = \App\Models\Organisation::find($organisation->id);
                                } else{
                                    $model = \App\Models\Contact::find($organisation->id);
                                }

                                $phone = str_replace(["#", "/", "-", "(", ")", " ", ".", "Tel", "privat", "T", "Mobile", "Home", "handy", "â€“"], '', $organisation->phone);

                                $phone = explode(",", $phone);
                                if(count($phone) > 1){
                                    if($phone[0] != null){
                                        $phone = $phone[0];
                                    }else{
                                        $phone = $phone[1];
                                    }
                                }else{
                                    $phone = $phone[0];
                                }
                                $phone = explode("und", $phone);
                                if(count($phone) > 1){
                                    if($phone[0] != null){
                                        $phone = $phone[0];
                                    }else{
                                        $phone = $phone[1];
                                    }
                                }else{
                                    $phone = $phone[0];
                                }
                                $phone = explode("Cell", $phone);
                                if(count($phone) > 1){
                                    if($phone[0] != null){
                                        $phone = $phone[0];
                                    }else{
                                        $phone = $phone[1];
                                    }
                                }else{
                                    $phone = $phone[0];
                                }
                                $phone = explode("or", $phone);
                                if(count($phone) > 1){
                                    if($phone[0] != null){
                                        $phone = $phone[0];
                                    }else{
                                        $phone = $phone[1];
                                    }
                                }else{
                                    $phone = $phone[0];
                                }
                                $phone = explode("_", $phone);
                                if(count($phone) > 1){
                                    if($phone[0] != null){
                                        $phone = $phone[0];
                                    }else{
                                        $phone = $phone[1];
                                    }
                                }else{
                                    $phone = $phone[0];
                                }

                                if ($phone === ""){
                                    $phone = "-";
                                }
                            @endphp
                            <tr>
                              <td style="width: 4rem;" class="d-flex flex-row text-align-center justify-content-around pr-0">
                                    @if($organisation->model_type === "I")
                                        <input type="checkbox" class="selector-organisation" value="{{ $organisation->id }}"/>
                                    @else
                                        <input type="checkbox" class="selector-contact" value="{{ $organisation->id }}"/>
                                    @endif
                                    @if (Auth::user()->hasPermission('institutions.read'))
                                        @if(($organisation->model_type === "I"))
                                            <a href="{{ route('organisations.show', [$organisation->id]) }}"
                                               title="Show institution">
                                                <i class="fas fa-search"></i>
                                            </a>
                                            @if($model->contacts()->exists())
                                                <i title="Show contacts for this instition"
                                                   onclick="showContactsFor({{ $organisation->id }})"
                                                   style="cursor: pointer;"
                                                   class="fas fa-address-book"></i>
                                            @else
                                            &nbsp;&nbsp;&nbsp;
                                            @endif
                                        @else
                                            <a href="{{ route('contacts.show', [$organisation->id]) }}"
                                               title="Show contact"><i class="fas fa-search"></i></a>
                                            &nbsp;&nbsp;&nbsp;
                                        @endif
                                    @endif
                                </td>
                                <td style="font-weight: 500" class="text-center">{{ $organisation->model_type }}</td>
                                <td style="white-space: wrap" class="tooltipizs">
                                   @if (strlen($organisation->name) > 1)
                                      {{ substr($organisation->name, 0, 15) }}
                                      <span class="tooltipizs_text" style="min-width:200px;padding-left:7px;text-align:left;">{{ $organisation->name }}</span>
                                   @endif
                                </td>
                                <td class="text-center">
                                    @if($organisation->type_label)
                                        <span class="self-cursor" title="{{ $organisation->type_label }}">
                                            {{ $organisation->type_key }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="self-cursor" title="{{ $model->active_state_str ?? "" }}">
                                        {{ $model->active_state ?? "" }}
                                    </span>
                                </td>
                                <td>
                                   @php
                                   if (!empty($organisation->organisation_name)) {
                                      $contactInstitute = explode(';', $organisation->organisation_name);
                                   @endphp
                                      <a href="{{ route('organisations.show', [$contactInstitute[0]]) }}" title="Show institution">
                                         <u>{{ $contactInstitute[1] }}</u>
                                      </a>
                                   @php
                                   }
                                   @endphp
                                </td>
                                <td style="white-space: nowrap;" class="text-center tooltipizs">
                                   @if (strlen($phone) > 1)
                                      <i class="fa fa-phone"></i>
                                      <span class="tooltipizs_text">{{ $phone }}</span>
                                   @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    {{ $organisation->city }}
                                </td>
                                <td style="white-space: wrap;">
                                    {{ $model->country->name ?? '' }}
                                </td>
                                <td style="white-space: nowrap;">
                                    <a href="mailto:{{ $organisation->email }}">
                                        <u>{{ $organisation->email }}</u>
                                    </a>
                                </td>
                                <td style="white-space: wrap;" class="text-center tooltipizs">
                                   @if (strlen($organisation->website) > 1)
                                      <i class="fas fa-globe"></i>
                                      <span class="tooltipizs_text" style="left:-290px;padding-left:7px;text-align:left;width:300px"><a href="//{{$organisation->website}}" target="_blank">
                                         <u>{{ $organisation->website }}</u>
                                      </a></span>
                                   @endif
                                </td>
                                <td>
                                    @if(($model instanceof \App\Models\Organisation))
                                        {{$organisation->level}}
                                    @else
                                        @if(isset($model->association_level))
                                            {{ $model->association_level ?: "555" }}
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(($model instanceof \App\Models\Organisation))
                                        @if($model->associations()->exists())
                                            {{ $model->associations()->first()->label ?: "-" }}
                                        @else
                                            -
                                        @endif
                                    @else
                                        @if(isset($model->association_label))
                                            {{ $model->association_label ?: "-" }}
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ !empty($organisation->mailing_category) ? $mailing_categories[$organisation->mailing_category] : "--" }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="10"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if (Auth::user()->hasPermission('contacts.see-all-contacts'))
                    <div class="float-right">
                        {{ $organisations->links() }}
                    </div>
                @endif
            @else
                <p> No institution found </p>
            @endunless
        </div>
    </div>

    @include('institutions.filter_modal', ['modalId' => 'filterOrganizations'])

    @include('institutions.contacts_modal', ['modalId' => 'contactDetails'])

    @include('institutions.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

    @include('export_excel.export_options_modal', ['modalId' => 'exportInstitutions'])

    @include('institutions.address_list_modal', ['modalId' => 'createAddressList'])

@endsection

@section('page-scripts')
    <style>
        .searchOption {
            text-align: left;
            padding: 12px 5px;
            border-bottom: darkgray solid 1px;
            cursor: pointer;
        }
        .searchOption:hover {
            background: beige;
        }

        .preflightOptionContainer {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            row-gap: 10px;
        }

        .preflightOption {
            text-decoration: underline;
            font-weight: 500;
            padding: 12px;
            border-radius: 9px;
            width: 60%;
            cursor: pointer;
        }

        .preflightOption:hover {
            color: #bdbdbd;
        }
    </style>

    <script type="text/javascript">
        window.axios.defaults.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content')

        const preflightCreationCheck = (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();

            let formHtml = `
                <p>Check if the institution exists before creating.</p>
                <div class="modal-body" style="text-align: left;">
                   {!! Form::label('name', 'Name *') !!}
                   {!! Form::text('preflight_check_name', null, ['id' => 'preflight_check_name', 'class' => 'form-control', 'required']) !!}

                   {!! Form::label('domain', 'Domain') !!}
                   {!! Form::text('preflight_check_domain', null, ['id' => 'preflight_check_domain', 'class' => 'form-control', 'required']) !!}

                   {!! Form::label('city', 'City *') !!}
                   {!! Form::text('preflight_check_city', null, ['id' => 'preflight_check_city', 'class' => 'form-control', 'required']) !!}

                   {!! Form::label('country', 'Country *') !!}
                   {!! Form::select('preflight_check_country', $countries, null, ['id' => 'preflight_check_country', 'class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                </div>
            `;

            Swal.fire({
               title: "Pre-creation check",
               html: formHtml,
               confirmButtonText: "Check for existing institutes",
               preConfirm: () => {
                   let name = document.getElementById('preflight_check_name').value;
                   let domain = document.getElementById('preflight_check_domain').value;
                   let city = document.getElementById('preflight_check_city').value;
                   let country = document.getElementById('preflight_check_country').value;

                   if (name && city && country){
                       return new Promise(resolve => resolve([name, domain, city, country]));
                   } else {
                       return Swal.showValidationMessage("Name, city & country are required")
                   }
               },
            })
            .then((result) => {
                let name = result.value[0];
                let domain = result.value[1];
                let city = result.value[2];
                let country = result.value[3];

                let url = "{{ route('organisations.checkForExistence') }}";
                url += `?name=${name}&domain=${domain}&city=${city}&country=${country}`;

                axios.get(url)
                    .then(res => {
                        let organisations = res.data.organisations;
                        let contacts = res.data.contacts;

                        if (organisations.length || contacts.length) {
                            let selectHtml = "<div><p>Click on the institute to view it</p>";

                            if (organisations.length) {
                                selectHtml += "<div class='preflightOptionContainer'>";
                                organisations.forEach(organisation => {
                                    selectHtml += `
                                    <a href="/organisations/${organisation.id}" class='preflightOption'>
                                         ${organisation.name}
                                    </a>`;
                                });
                                selectHtml += "</div>";
                            }

                            if (contacts.length) {
                                selectHtml += "<h2>Contacts</h2>";
                                selectHtml += "<div class='preflightOptionContainer'>";
                                contacts.forEach(contact => {
                                    selectHtml += `
                                    <a href="/contacts/${contact.id}" class='preflightOption'>
                                         ${contact.first_name ?? ""} ${contact.last_name ?? ""}
                                    </a>`;
                                });
                                selectHtml += "</div>";
                            }

                            selectHtml += "</div>";

                            Swal.fire({
                                title: "Already exists",
                                html: selectHtml,
                                showConfirmButton: true,
                                confirmButtonText: "Close"
                            });
                        } else {
                            Swal.fire({
                                title            : "Create contact or institute?",
                                confirmButtonText: "Contact",
                                denyButtonText   : "Institute",
                                cancelButtonText : "Close",
                                showDenyButton: true,
                                showCancelButton: true,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `{{ route('contacts.create') }}?preset=1&first_name=${name}&last_name= &domain=${domain}&city=${city}&country_id=${country}`;
                                } else if (result.isDenied) {
                                    window.location.href = `{{ route('organisations.create') }}?preset=1&name=${name}&domain=${domain}&city=${city}&country_id=${country}`;
                                }
                            });
                        }
                    })
            })
            .catch(err => {
                Swal.showValidationMessage("Name, city & country are required")
                document.getElementById("swalValidationErrorSpan").textContent = "Name, city & country are required";
            });
        }

        const instituteContactSearchHandler = _.debounce((e) => {
            let resultsContainer = document.getElementById('results');
            let inputValue = e.target.value;
            const url = "{{ route('organisations.search') }}";

            if (inputValue.trim() !== '') {
                resultsContainer.innerHTML = '';

                axios.get(`${url}?query=${inputValue.trim()}`)
                    .then(res => {
                        res.data.data.forEach(item => {
                            let option = document.createElement('p');
                            option.classList.add("searchOption");
                            if (item.name.trim() === ""){
                                option.textContent = `${item.type} #${item.id}`;
                            } else {
                                option.textContent = item.name;
                            }
                            option.id = `${item.id}-option`
                            option.onclick = () => {
                                document.getElementById("selectedLiveSearch").dataset.selected = item.id;
                                document.getElementById("selectedLiveSearch").dataset.type = item.type;
                                if (item.name.trim() === ""){
                                    document.getElementById("selectedLiveSearch").textContent = `Selected: ${item.type} #${item.id}`;
                                } else {
                                    document.getElementById("selectedLiveSearch").textContent = `Selected: ${item.name}`;
                                }
                            }
                            resultsContainer.appendChild(option);
                        })
                    })
            } else {
                resultsContainer.innerHTML = '';
            }

        }, 300);

        const provideReplacementForOrganisation = async (id, isContact) => {
            let html = `
            <p>These records need to be transferred to another parent. These include: contacts, surplus, wanteds</p>
            <p id="selectedLiveSearch" style="font-weight: 500"></p>
            <small style="color:gray;">Search for institution or contact (min. 3 characters)</small>
            <input id="liveSearch" style="margin-top: 0;" placeholder="Search..." class="swal2-input">
            <input type="hidden" id="deleted_id" name="deleted_id" value="${id}"/>
            <div id="results" style="max-height: 200px; overflow-y: scroll;"></div>
        `;

            Swal.fire({
                title: "Orphan records detected",
                html: html,
                didOpen: () =>  document.getElementById('liveSearch').addEventListener('input', instituteContactSearchHandler),
                allowOutsideClick: false,
                showCloseButton: true
            }).then(res => {
                if (res.isConfirmed) {
                    let to_delete_id = document.getElementById("deleted_id").value;
                    let handover_id = document.getElementById("selectedLiveSearch").dataset.selected;
                    let handover_type = document.getElementById("selectedLiveSearch").dataset.type;
                    let query = new URLSearchParams({
                        handover_id,
                        handover_type,
                        to_delete_id
                    })

                    let url;

                    if (!isContact) {
                        url = "{{ route('organisations.destroy', ['organisation' => 'to_replace']) }}"
                                    .replace("to_replace", to_delete_id + "?") + query.toString();

                        axios.delete(url)
                            .then(res => {
                                if (res.data.success) {
                                    SuccessModal("Deleted organisation");
                                } else {
                                    ErrorModal('Something went wrong')
                                }
                            })
                            .catch(e => {
                                ErrorModal('Something went wrong')
                            });
                    } else {
                        url = "{{ route('contacts.destroy', ['contact' => 'to_replace']) }}"
                                    .replace("to_replace", to_delete_id + "?") + query.toString();

                        axios.delete(url)
                            .then(res => {
                                if (res.data.success) {
                                    SuccessModal("Deleted contact");
                                } else {
                                    ErrorModal('Something went wrong')
                                }
                            })
                            .catch(e => {
                                ErrorModal('Something went wrong')
                            });
                    }
                }
            })
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('create-button').addEventListener('click', preflightCreationCheck);

            document.getElementById('deleteSelectedItems').addEventListener('click', async () => {
                let organisationIds = [];
                let contactIds = [];

                document.querySelectorAll('input.selector-organisation:checked').forEach(element => {
                    organisationIds.push(element.value);
                });

                document.querySelectorAll('input.selector-contact:checked').forEach(element => {
                    contactIds.push(element.value);
                });

                if (organisationIds.length === 0 && contactIds.length === 0) {
                    InfoModal("You must select at least 1 item to delete");
                    return;
                }

                const {isConfirmed} = await ConfirmChoice("Are you sure you want to delete these items?");

                if (isConfirmed) {
                    if (organisationIds.length !== 0) {
                        let url = "{{ route('organisations.destroy', ['organisation' => 'replace_by_id']) }}";

                        organisationIds.forEach((item) => {
                            axios.delete(url.replace("replace_by_id", item))
                                .then(res => {
                                    if (!res.data.success) {
                                        provideReplacementForOrganisation(item, false);
                                    } else {
                                        SuccessModal("Deleted organisation");
                                    }
                                })
                        });
                    }
                    if (contactIds.length !== 0) {
                        let url = "{{ route('contacts.destroy', ['contact' => 'replace_by_id']) }}";

                        contactIds.forEach((item) => {
                            axios.delete(url.replace("replace_by_id", item))
                                .then(res => {
                                    if (!res.data.success) {
                                        provideReplacementForOrganisation(item, true);
                                    } else {
                                        SuccessModal("Deleted contact");
                                    }
                                })
                        });
                    }
                }
            });
        })

        const showContactsFor = id => {
            document.getElementById('contact_details_organisation_id').value = id;
            document.getElementById('contact_details_organisation_id').dispatchEvent(new Event('change'));
            $('#contactDetails').modal('show');
        }

        $(document).ready(function () {
            $(':checkbox:checked').prop('checked', false);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#selectAll').on('change', function () {
            $(":checkbox.selector-organisation").prop('checked', this.checked);
            $(":checkbox.selector-contact").prop('checked', this.checked);
        });

        $('#orderByField').on('change', function () {
            $('#organizationsOrderByForm').submit();
        });

        $('#orderByDirection').on('change', function () {
            $('#organizationsOrderByForm').submit();
        });

        $('#recordsPerPage').on('keypress', function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13')
                $('#recordsPerPageForm').submit();

            event.stopPropagation();
        });

        //Select2 level updated
        $('.level_type').on('change', function () {
            var level = $(this).val();
            var id = $(this).attr("data_organisation_id");
            var value_old = $(this).attr("data_value_old") || level;
            $(this).removeClass("level-" + value_old);
            $(this).addClass("level-" + level);
            $(this).attr("data_value_old", level);
            if (level !== null && id !== null) {
                $.ajax({
                    type   : 'POST',
                    url    : "{{ route('organisations.editLevel') }}",
                    data   : {
                        id   : id,
                        level: level
                    },
                    success: function (data) {
                        if (typeof data.message != "undefined") {
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        }
                    }
                });
            }
        });

        $('#editSelectedRecords').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
        });

        $('#sendEditSelectionForm').on('click', function (event) {
            event.preventDefault();

            var ids = [];
            $(":checked.selector-organisation").each(function () {
                ids.push($(this).val());
            });

            var associations = [];
            $('#editSelectedRecords [name=association]:checked').each(function () {
                associations.push($(this).val());
            });

            if (ids.length == 0)
                alert("You must select items to edit.");
            else {
                $(".modal-footer").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                $.ajax({
                    type   : 'POST',
                    url    : "{{ route('organisations.editSelectedRecords') }}",
                    data   : {
                        items                : ids,
                        institution_type     : $('#editSelectedRecords [name=organisation_type]').val(),
                        level                : $('#editSelectedRecords [name=level]').val(),
                        country_id           : $('#editSelectedRecords [name=country_id]').val(),
                        city                 : $('#editSelectedRecords [name=city]').val(),
                        mailing_category     : $('#editSelectedRecords [name=mailing_category]').val(),
                        make_institution_name: $('#editSelectedRecords [name=makeInstitutionNameBasedOnCityAndType]').is(":checked") ? 1 : 0,
                        make_website         : $('#editSelectedRecords [name=makeWebsiteFromEmailDomain]').is(":checked") ? 1 : 0,
                        canonical_name       : $('#editSelectedRecords [name=canonical_name]').val(),
                        associations         : associations
                    },
                    success: function (data) {
                        if (typeof data.message != "undefined") {
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        }
                        location.reload();
                    }
                });
            }
        });

        $('#filterOrganizationsBtn').on('click', function () {
            $('#filterOrganizations').modal('show');

            $("#filterOrganizations form input[type=hidden]").each(function(index) {
               if ($(this).attr('name').substr(0,6) === 'hidden') {
                  if ($(this).attr('name') === 'hidden_filter_model_type') {
                     $("#filterOrganizations input[name=filter_model_type]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                     if ($(this).val() === 'C') {
                        $('.hidecontact').hide();
                        $('.only-contacts').show();
                        $('.extraline').show();
                     } else {
                        $('.only-contacts').hide();
                        $('.hidecontact').show();
                        $('.extraline').hide();
                     }
                  }
                  if ($(this).attr('name') === 'hidden_filter_organisation_type') {
                     $("#filterOrganizations select[name=filter_organisation_type]").val($(this).val());
                  }
                  if ($(this).attr('name') === 'hidden_filter_name_empty') {
                     $("#filterOrganizations input[name=filter_name]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_name_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_institution_name_empty') {
                     $("#filterOrganizations input[name=filter_institution_name]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_institution_name_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_canonical_name_empty') {
                     $("#filterOrganizations input[name=filter_canonical_name]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_canonical_name_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_level') {
                     $("#filterOrganizations select[name=filter_level]").val($(this).val());
                  }
                  if ($(this).attr('name') === 'hidden_filter_email_empty') {
                     $("#filterOrganizations input[name=filter_email]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_email_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_domain_name_empty') {
                     $("#filterOrganizations input[name=filter_domain_name]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_domain_name_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_phone_empty') {
                     $("#filterOrganizations input[name=filter_phone]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_phone_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_city_empty') {
                     $("#filterOrganizations input[name=filter_city]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_city_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_has_website') {                     
                     $("#filterOrganizations input[name=filter_has_website]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                     if ($(this).val() === 'no') {
                        $("#filterOrganizations input[name=filter_website]").prop('disabled', true);
                     }
                  }
                  if ($(this).attr('name') === 'hidden_filter_association') {
                     $("#filterOrganizations select[name=filter_association").val($(this).val());
                  } 
                  if ($(this).attr('name') === 'hidden_filter_vat_empty') {
                     $("#filterOrganizations input[name=filter_vat_number]").prop('disabled', true);
                     $("#filterOrganizations input[name=filter_vat_empty]").prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_mailing_category') {
                     $("#filterOrganizations select[name=filter_mailing_category]").val($(this).val());
                  }
                  if ($(this).attr('name') === 'hidden_filter_relation_type') {  
                     $("#filterOrganizations input[name=filter_relation_type]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_has_surplus') {  
                     $("#filterOrganizations input[name=filter_has_surplus]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_has_requests') {  
                     $("#filterOrganizations input[name=filter_has_requests]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_has_invoices') {  
                     $("#filterOrganizations input[name=filter_has_invoices]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_has_wanted') {  
                     $("#filterOrganizations input[name=filter_has_wanted]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_has_orders') {  
                     $("#filterOrganizations input[name=filter_has_orders]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                  }
                  if ($(this).attr('name') === 'hidden_filter_has_collection') {  
                     $("#filterOrganizations input[name=filter_has_collection]").filter('[value=' + $(this).val() + ']').prop('checked', true);
                  }  
               }
            });
        });

        $('#filterOrganizations input[name=filter_name_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
                $("#filterOrganizations input[name=filter_name]").val('');
                $("#filterOrganizations input[name=filter_name]").prop('disabled', true);
             } else {
                $("#filterOrganizations input[name=filter_name]").prop('disabled', false);
             }
        });
        $('#filterOrganizations input[name=filter_canonical_name_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
                $("#filterOrganizations input[name=filter_canonical_name]").val('');
                $("#filterOrganizations input[name=filter_canonical_name]").prop('disabled', true);
             } else {
                $("#filterOrganizations input[name=filter_canonical_name]").prop('disabled', false);
             }
        });
        $('#filterOrganizations input[name=filter_institution_name_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
                $("#filterOrganizations input[name=filter_institution_name]").val('');
                $("#filterOrganizations input[name=filter_institution_name]").prop('disabled', true);
             } else {
                $("#filterOrganizations input[name=filter_institution_name]").prop('disabled', false);
             }
        });

        $('#filterOrganizations input[name=filter_email_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
               $("#filterOrganizations input[name=filter_email]").val('');
               $("#filterOrganizations input[name=filter_email]").prop('disabled', true);
            } else {
                $("#filterOrganizations input[name=filter_email]").prop('disabled', false);
            }
        });

        $('#filterOrganizations input[name=filter_domain_name_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
                $("#filterOrganizations input[name=filter_domain_name]").val('');
                $("#filterOrganizations input[name=filter_domain_name]").prop('disabled', true);
             } else {
                $("#filterOrganizations input[name=filter_domain_name]").prop('disabled', false);
             }
        });
        $('#filterOrganizations input[name=filter_phone_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
                $("#filterOrganizations input[name=filter_phone]").val('');
                $("#filterOrganizations input[name=filter_phone]").prop('disabled', true);
             } else {
                $("#filterOrganizations input[name=filter_phone]").prop('disabled', false);
             }
        });

        $('#filterOrganizations input[name=filter_city_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
                $("#filterOrganizations input[name=filter_city]").val('');
                $("#filterOrganizations input[name=filter_city]").prop('disabled', true);
             } else {
                $("#filterOrganizations input[name=filter_city]").prop('disabled', false);
             }
        });

        $('#filterOrganizations input[name=filter_has_website]').change(function () {
            var checkedOption = $('#filterOrganizations input[name=filter_has_website]:checked').val();

            if (checkedOption == 'no') {
                $("#filterOrganizations input[name=filter_website]").val('');
                $("#filterOrganizations [name=filter_website]").prop('disabled', true);
            } else {
                $("#filterOrganizations [name=filter_website]").prop('disabled', false);
            }
        });

        $('#filterOrganizations input[name=filter_vat_empty]:checkbox').change(function () {
            if ($(this).is(':checked')) {
                $("#filterOrganizations input[name=filter_vat_number]").val('');
                $("#filterOrganizations input[name=filter_vat_number]").prop('disabled', true);
             } else {
                $("#filterOrganizations input[name=filter_vat_number]").prop('disabled', false);
             }
        });

        $('#filterOrganizations input[name=filter_mailing_category]:checkbox').change(function () {
            if ($(this).is(':checked'))
                $("#filterOrganizations input[name=filter_mailing_category]").prop('disabled', true);
            else
                $("#filterOrganizations input[name=filter_mailing_category]").prop('disabled', false);
        });

        $("#filterOrganizations #resetBtn").click(function () {
           
           $(':input','#filterOrganizations form')
               .not(':button, :submit, :reset, :hidden')
               .val('')
               .prop('checked', false)
               .prop('selected', false);
        });

        $('#exportInstitutionsRecords').on('click', function () {
            var count_selected_records = $(":checked.selector-organisation").length;
            count_selected_records += $(":checked.selector-contact").length;
            var count_page_records = $('#countInstitutionsVisible').val();
            $("label[for='count_selected_records']").html('(' + count_selected_records + ')');
            $("label[for='count_page_records']").html('(' + count_page_records + ')');

            $('#exportInstitutions').modal('show');
        });

        $('#exportInstitutions').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
        });

        $('#exportInstitutions').on('submit', function (event) {
            event.preventDefault();

            var export_option = $('#exportInstitutions [name=export_option]:checked').val();

            var oids = [];
            var cids = [];
            if (export_option == "selection") {
                $(":checked.selector-organisation").each(function () {
                    oids.push($(this).val());
                });
                $(":checked.selector-contact").each(function () {
                    cids.push($(this).val());
                });
            } else {
                $(".selector-organisation").each(function () {
                    oids.push($(this).val());
                });
                $(".selector-contact").each(function () {
                    cids.push($(this).val());
                });
            }

            if (cids.length == 0 && oids.length == 0)
                alert("There are no records to export.");
            else {
                var url = "{{route('organisations.export')}}?oitems=" + oids + '&citems=' + cids;
                window.location = url;

                $('#exportInstitutions').modal('hide');
            }
        });

        $('#convert').on('click', function () {
            let ids = [];

            $(":checked.selector-organisation").each(function () {
                ids.push({
                    'organisation': $(this).val()
                });
            });

            $(":checked.selector-contact").each(function () {
                ids.push({
                    'contact': $(this).val()
                });
            });

            if (ids.length === 1) {
                let id, type, url;

                let hasContactObject = ids.some(id => 'contact' in id);

                if (hasContactObject) {
                    type = "contact";
                    id = ids[0]['contact'];
                    let query = new URLSearchParams({id, type})

                    url = '{{ route("organisations.convert") }}';
                    window.location = url + "?" + query.toString();
                } else {
                    type = "organisation";
                    id = ids[0]['organisation'];
                    let query = new URLSearchParams({id, type})

                    url = '{{ route("organisations.convert") }}';
                    window.location = url + "?" + query.toString();
                }
            } else if (ids.length > 1) {
                async function fetchProcess(ids) {
                    let responses = [];

                    for (let item of ids) {
                        for (let [key, value] of Object.entries(item)) {
                            let type, id;

                            if (key === 'contact') {
                                type = 'contact';
                                id = value;
                            } else if (key === 'organisation') {
                                type = 'organisation';
                                id = value;
                            }

                            if (type && id) {
                                try {
                                    let response = await axios.post(`{{ route("organisations.convert-mass") }}`, {type, id});
                                    responses.push(response);
                                } catch(error) {
                                    ErrorModal('Something went wrong while converting records');
                                }

                                await new Promise(resolve => setTimeout(resolve, 500));
                            }
                        }
                    }

                    if (responses.length > 0) {
                        SuccessModal(`Converted ${responses.length} records, reloading page`);

                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                }

                fetchProcess(ids);
            } else {
                alert(`Select atleast 1 item to convert (currently selected: ${ids.length})`);
            }
        });

        $('#mergeOption').on('click', function () {
            let ids = [];

            $(":checked.selector-organisation").each(function () {
                ids.push({
                    'organisation': $(this).val()
                });
            });

            $(":checked.selector-contact").each(function () {
                ids.push({
                    'contact': $(this).val()
                });
            });

            if (ids.length < 2 || ids.length > 2) {
                alert(`Select 2 items to merge (currently selected: ${ids.length})`);
            } else {
                let organization_id1, organization_id2, url;

                let hasContactObject = ids.some(id => 'contact' in id);

                if (hasContactObject) {
                    organization_id1 = ids[0]['organisation'];
                    organization_id2 = ids[1]['contact'];
                    let query = new URLSearchParams({
                        'extra-merge': true,
                        'contact': 1
                    })

                    url = '{{ route("organisations.compare", ["id1", "id2", "organizations", 0]) }}';
                    url = url.replace('id1', organization_id1);
                    url = url.replace('id2', organization_id2);
                    window.location = url + "?" + query.toString();
                } else {
                    organization_id1 = ids[0]['organisation'];
                    organization_id2 = ids[1]['organisation'];
                    url = '{{ route("organisations.compare", ["id1", "id2", "organizations", 0]) }}';
                    url = url.replace('id1', organization_id1);
                    url = url.replace('id2', organization_id2);
                    window.location = url;
                }
            }
        });

        $('#createMailingAddressList').on('click', function () {
            $('#createAddressList').modal('show');

            $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
            $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

            $('#createAddressList [name=exclude_continents]').prop('disabled', false);
            $('#createAddressList [name=exclude_countries]').prop('disabled', false);
        });

        $('#createAddressList').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');

            $("#createAddressList [name=institution_type_selection_all]").prop('checked', false);
            $("#createAddressList [name=institution_type_selection_all]").trigger('change');
        });

        $("#createAddressList [name=institution_type_selection_all]").change(function () {
            if ($(this).prop('checked')) {
                $('#createAddressList [name=institution_type_selection]').each(function () {
                    $(this).prop('checked', false);
                    $(this).prop('disabled', true);
                });
            } else {
                $('#createAddressList [name=institution_type_selection]').each(function () {
                    $(this).prop('checked', false);
                    $(this).prop('disabled', false);
                });
            }
        });

        $("#createAddressList [name=world_region]").change(function () {
            var world_region = $(this).val();

            $(".spinner-world_region").removeClass("d-none");
            $("#createAddressList [name=world_region_selection]").prop('disabled', true);

            if (world_region == 'area') {
                $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
                $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

                $('#createAddressList [name=exclude_continents]').prop('disabled', false);
                $('#createAddressList [name=exclude_countries]').prop('disabled', false);
            } else if (world_region == 'region') {
                $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
                $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

                $('#createAddressList [name=exclude_continents]').prop('disabled', true);
                $('#createAddressList [name=exclude_countries]').prop('disabled', false);
            } else {
                $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
                $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

                $('#createAddressList [name=exclude_continents]').prop('disabled', true);
                $('#createAddressList [name=exclude_countries]').prop('disabled', true);
            }

            $.ajax({
                type   : 'POST',
                url    : "{{ route('api.getWorldRegionData') }}",
                data   : {
                    value: world_region,
                },
                success: function (data) {
                    if (data.success) {
                        $('[name=world_region_selection]').empty();
                        $('[name=world_region_selection]').append('<option value="0">All</option>');
                        $.each(data.cmbData, function (key, value) {
                            $('[name=world_region_selection]').append('<option value="' + key + '">' + value + '</option>');
                        });
                        $(".spinner-world_region").addClass("d-none");
                        $("#createAddressList [name=world_region_selection]").prop('disabled', false);
                    }
                }
            });
        });

        $('#createAddressList').on('submit', function (event) {
            event.preventDefault();

            var language_option = $('#createAddressList [name=language_option]:checked').val();
            var level = $('#createAddressList [name=select_institution_level]').val();
            var world_region = $('#createAddressList [name=world_region]:checked').val();
            var world_region_selection = $('#createAddressList [name=world_region_selection]').val();
            var institution_type_selection_all = $('#createAddressList [name=institution_type_selection_all]').is(':checked');

            var institution_types = [];
            $('#createAddressList [name=institution_type_selection]:checked').each(function () {
                institution_types.push($(this).val());
            });

            var exclude_associations = [];
            $('#createAddressList [name=exclude_associations]:checked').each( function() {
                exclude_associations.push($(this).val());
            });

            var exclude_continents = $('#createAddressList [name=exclude_continents]').val();

            var exclude_countries = $('#createAddressList [name=exclude_countries]').val();

            if (!institution_type_selection_all && institution_types.length == 0)
                alert("You must select at least one institution type option.");
            else {
                if (institution_type_selection_all)
                    var url = "{{route('organisations.createOrganisationAddressList')}}?language=" + language_option +
                        "&exclude_associations=" + exclude_associations +
                        "&level=" + level +
                        "&world_region=" + world_region +
                        "&world_region_selection=" + world_region_selection +
                        "&exclude_continents=" + exclude_continents +
                        "&exclude_countries=" + exclude_countries;
                else
                    var url = "{{route('organisations.createOrganisationAddressList')}}?itypes=" + institution_types +
                        "&exclude_associations=" + exclude_associations +
                        "&language=" + language_option +
                        "&level=" + level +
                        "&world_region=" + world_region +
                        "&world_region_selection=" + world_region_selection +
                        "&exclude_continents=" + exclude_continents +
                        "&exclude_countries=" + exclude_countries;
                /*alert(url);*/


                window.location = url;
                $('#createAddressList').modal('hide');
            }
        });

        $("#createAddressList #resetBtn").click(function () {
            $("#createAddressList").find('form').trigger('reset');

            $("#createAddressList [name=institution_type_selection_all]").prop('checked', false);
            $("#createAddressList [name=institution_type_selection_all]").trigger('change');

            $("#createAddressList [name=exclude_associations]").prop('checked', false);
            $("#createAddressList [name=exclude_associations]").trigger('change');

            $("#createAddressList [name=world_region]").val('area');
            $("#createAddressList [name=world_region]").trigger('change');
        });

        $("#search-box").keyup(function () {
            $("#search-box").removeClass("is-invalid");
            $("#suggesstion-box").addClass("d-none");
            $("#search-box").removeClass("search-box-correct");
            validateCanonical($(this).val());
            $.ajax({
                type       : "get",
                url        : "{{ route('api.canonical-name-select2') }}",
                data       : {
                    search: $(this).val(),
                    email : $("[name=email]").val()
                },
                beforeSend : function () {
                    $("#search-box").css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
                },
                success    : function (data) {
                    if (typeof data.items !== "undefined" && data.items.length > 0) {
                        var $container = $(
                            "<div class='select2-result-repository clearfix'>" +
                            "<div class='select2-result-repository__meta'>" +
                            "<div class='select2-result-repository__name'></div>" +
                            "<div class='select2-result-repository__canonical_name'></div>" +
                            "</div>" +
                            "</div>"
                        );
                        var html = "";
                        $.each(data.items, function (key, value) {
                            if (typeof value.name !== "undefined" && value.name !== "") {
                                $container.find(".select2-result-repository__name").text("Institution: " + value.name);
                            }
                            if (typeof value.domain_name !== "undefined" && value.domain_name !== "") {
                                $container.find(".select2-result-repository__name").text("Domain name: " + value.domain_name);
                            }
                            $container.find(".select2-result-repository__canonical_name").text("Canonical Name: " + value.canonical_name);
                            var canonical_name = value.canonical_name.replace("'", "´");
                            $container.find(".select2-result-repository__meta").attr("onclick", "set_value('" + canonical_name + "')");
                            html += $container.html();
                        });
                        $("#suggesstion-box").removeClass("d-none");
                        $("#suggesstion-box").html(html);
                    } else {
                        $("#suggesstion-box").addClass("d-none");
                    }
                }, complete: function () {
                    $("#search-box").css("background", "#FFF");
                },
            });
        });

        function set_value(value) {
            value = value.replace("´", "'");
            validateCanonical(value);
            $("#search-box").val(value);
            $("#search-box").attr("onchange", "test('" + value + "')");
            $("#suggesstion-box").addClass("d-none");
        }

        function validateCanonical(value = null) {
            $.ajax({
                type: "get",
                url : "{{ route('organisations.validateCanonical') }}",
                data: {
                    search: value,
                    email: $("[name=email]").val()
                },
                beforeSend: function() {
                    $("#search-box").css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
                },
                success: function(data) {
                    if(data.error){
                        $("#search-box").addClass("is-invalid");
                        $("#search-box").removeClass("is-valid");
                        $("#invalid-canonical_name").removeClass("d-none");
                        $("#invalid-canonical_name").html(data.message);
                        $("#search-box").removeClass("search-box-correct");
                        $(".invalid-feedback-tooltips").removeClass("d-none");
                        $("#search-box").attr("data-validate", "false");
                    }else{
                        $("#search-box").removeClass("is-invalid");
                        $("#search-box").addClass("is-valid");
                        $("#invalid-canonical_name").html("");
                        $("#search-box").addClass("search-box-correct");
                        $(".invalid-feedback-tooltips").addClass("d-none");
                        $("#search-box").attr("data-validate", "true")
                    }
                },complete: function(r) {
                    $("#search-box").css("background", "#FFF");
                },
            });
        }

        $("html").click(function() {
            $("#suggesstion-box").addClass("d-none");
        });

        $("#saveInstitution").on("click", function(e) {
            if($("#search-box").attr("data-validate") === "false"){
                e.preventDefault();
            }
        });
        
        // hide Institution type when clicking Contact option
        $('#modelC').on('click', function () {
           $('.hidecontact').hide();
           $('.only-contacts').show();
           $('.extraline').show();
        });
        
        // hide Institution type when clicking Contact option
        $('#modelI, #modelB').on('click', function () {
           $('.only-contacts').hide();
           $('.hidecontact').show();
           $('.extraline').hide();
        });

    </script>

@endsection
