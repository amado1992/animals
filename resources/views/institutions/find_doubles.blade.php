@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('organisations.index') }}">Institutions</a></li>
    <li class="breadcrumb-item active">Find duplicated institutions</li>
</ol>
@endsection

@section('header-content')
<h1 class="h1 text-white"><i class="fas fa-fw fa-building mr-2"></i> {{ __('Find duplicated institutions') }}</h1>
<p class="text-white">Find institutions that are doubles.</p>
@endsection

@section('main-content')
<div class="card shadow mb-2">
    <div class="card-body">
        {!! Form::open(['route' => 'organisations.filterOrganizationsDoubles', 'method' => 'GET', 'class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::label('filter_organisation_type', 'Type:') !!}
                {!! Form::select('filter_organisation_type', $organization_types, null, ['class' => 'form-control ml-1', 'style' => 'width: 200px', 'placeholder' => '- select -']) !!}
            </div>
            <div class="form-group ml-3">
                {!! Form::label('filter_country', 'Country:') !!}
                {!! Form::select('filter_country', $countries, null, ['class' => 'form-control ml-1', 'style' => 'width: 200px', 'placeholder' => '- select -']) !!}
            </div>
            <div class="form-group ml-3">
                {!! Form::label('filter_doubles_by', 'Doubles by:', ['class' => 'mr-2']) !!}

                {!! Form::radio('filter_doubles_by', 'name', true, ['id' => 'by_name']) !!}
                {!! Form::label('by_name', 'Name', ['class' => 'mr-2  ml-1']) !!}
                {!! Form::radio('filter_doubles_by', 'email', null, ['id' => 'by_email']) !!}
                {!! Form::label('by_email', 'Email', ['class' => 'mr-2  ml-1']) !!}
                {!! Form::radio('filter_doubles_by', 'domain_name', null, ['id' => 'by_domain_name']) !!}
                {!! Form::label('by_domain_name', 'Domain name', ['class' => 'ml-1']) !!}
            </div>
            <div class="form-group ml-3">
                <button type="submit" class="btn btn-primary mr-2">Search</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>

        {!! Form::close() !!}
    </div>
</div>

@if (isset($organisations))
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between">
            <h3>{{ __('Duplicated institutions') }}</h3>
            <div>
                <button id="mergeOption" class="btn btn-primary">Compare & Merge</button>
                <button id="deleteOptions" class="btn btn-danger">Delete</button>
            </div>
        </div>
        <div class="card-body">
            @unless($organisations->isEmpty())
                <div class="table-responsive mb-2" style="overflow-x: auto; overflow-y: hidden;">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 15px;"></th>
                                <th @if ($criteria == 'name'))
                                    style="text-decoration: underline;"
                                @endif>Name</th>
                                <th>Type</th>
                                <th>Country</th>
                                <th>City</th>
                                <th @if ($criteria == 'email'))
                                    style="text-decoration: underline;"
                                @endif>Email address</th>
                                <th @if ($criteria == 'domain_name'))
                                    style="text-decoration: underline;"
                                @endif>Domain name</th>
                                <th>Phone number</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($organisations as $organisation)
                            <tr>
                                <td>
                                    <input type="checkbox" class="selector mr-2" value="{{ $organisation->id }}" />
                                </td>
                                <td>{{ $organisation->name }}</td>
                                <td>@if($organisation->type) {{ $organisation->type->label }} @else - @endif</td>
                                <td>{{ ($organisation->country != null) ? $organisation->country->name : '' }}</td>
                                <td>{{ $organisation->city }}</td>
                                <td>{{ $organisation->email }}</td>
                                <td>{{ $organisation->domain_name }}</td>
                                <td>{{ $organisation->phone }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{$organisations->links()}}
            @else
                <p> No institutions doubles were found. </p>
            @endunless
        </div>
    </div>
@endif

@endsection

@section('page-scripts')

<script type="text/javascript">
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

        return await Swal.fire({
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
        document.getElementById('deleteOptions').addEventListener('click', async () => {
            let ids = [];

            document.querySelectorAll('input.selector:checked').forEach(element => {
                ids.push(element.value);
            });

            if (ids.length === 0) {
                InfoModal("You must select at least 1 item to delete");
                return;
            }

            const {isConfirmed} = await ConfirmChoice("Are you sure you want to delete these items?");

            if (isConfirmed) {
                if (ids.length !== 0) {
                    let url = "{{ route('organisations.destroy', ['organisation' => 'replace_by_id']) }}";

                    for (const id of ids) {
                        try {
                            const res = await axios.delete(url.replace("replace_by_id", id))

                            if (!res.data.success) {
                                await provideReplacementForOrganisation(id, false);
                            } else {
                                SuccessModal("Deleted organisation");
                            }
                        } catch (error) {
                            console.error(`Error deleting organisation with id = ${id}`, error);
                        }
                    }
                }
            }
        });
    })

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#mergeOption').on('click', function() {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length < 2 || ids.length > 2)
            alert("You must select 2 institutions to merge.");
        else {
            var organization_id1 = ids[0];
            var organization_id2 = ids[1];

            var url = '{{ route("organisations.compare", ["id1", "id2", "organization_doubles", 0]) }}';
            url = url.replace('id1', organization_id1);
            url = url.replace('id2', organization_id2);
            window.location = url;
        }
    });

</script>

@endsection
