@extends('layouts.master')

@section('title', 'CMS - Shipping Instruction')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dist/datatable/datatable.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endsection

@section('style')
    <style>
        #date {
            background-color: white !important;
        }
        .dataTables_wrapper .dataTables_filter input[type="search"]{
            border: 1px solid red;
            height: 30px;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Shipping Instruction</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Shipping Instruction</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="accordion accordion-flush dark-accordion" id="accordionFlushExample">
                    <div class="accordion-item ">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button txt-success accordion-light-success active collapsed"
                                type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                aria-expanded="false" aria-controls="flush-collapseOne">
                                SUMMARY
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down svg-color">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                        </h2>
                        <div class="accordion-collapse collapse" id="flush-collapseOne" aria-labelledby="flush-headingOne"
                            data-bs-parent="#accordionFlushExample" style="">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <h5>SI STATUS RECAP</h5>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>PENDING</td>
                                                        <td class="text-end">{{ $statusSummary->total_pending ?? 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>SAILING</td>
                                                        <td class="text-end">{{ $statusSummary->total_sailing ?? 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>BERTHING</td>
                                                        <td class="text-end">{{ $statusSummary->total_berthing ?? 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>DOORING</td>
                                                        <td class="text-end">{{ $statusSummary->total_dooring ?? 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>RECEIVED AT WH</td>
                                                        <td class="text-end">{{ $statusSummary->total_received ?? 0 }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <h5>SI CONTAINER STATUS RECAP</h5>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>PENDING</td>
                                                        <td class="text-end">{{ $containerSummary->total_pending ?? 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>BERTHING</td>
                                                        <td class="text-end">{{ $containerSummary->total_berthing ?? 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>DOORING</td>
                                                        <td class="text-end">{{ $containerSummary->total_dooring ?? 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>RECEIVED</td>
                                                        <td class="text-end">{{ $containerSummary->total_received ?? 0 }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row mb-2 justify-content-end">
                    <div class="col-auto">
                        @if(auth()->user()->hierarchy->hasPermissionTo('si.add'))
                            <a class="btn btn-primary" href="{{ route('shipping_instruction.create') }}"><i
                                class="fa fa-fw fa-plus"></i></a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="start_date">ETD Start (for summary data too)</label>
                        <input type="text" id="start_date" class="form-control js-flatpickr" data-date-format="d-m-Y"
                            value="{{ $filterStartDate }}" />
                    </div>
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="end_date">ETD End (for summary data too)</label>
                        <input type="text" id="end_date" class="form-control js-flatpickr" data-date-format="d-m-Y"
                            value="{{ $filterEndDate }}" />
                    </div>
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="end_date">Destination</label>
                        <select class="form-select" id="destination">
                            <option value="0" @if($filterDestination == 0) selected @endif>-- Filter by Destination --</option>
                            @foreach ($destinations as $destination)
                                <option value="{{ $destination->id }}"
                                    @if($filterDestination == $destination->id) selected @endif>{{ $destination->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="end_date">Status</label>
                        <select class="form-select" id="status_id">
                            <option value="0" @if($filterStatus == 0) selected @endif>-- Filter by Status --</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}"
                                    @if($filterStatus == $status->id) selected @endif>{{ $status->display }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6 col-12">
                        <label class="form-label" for="keyword">Search DO or Container Number</label>
                        <input type="text" id="keyword" class="form-control" value="{{ $keyword }}" />
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-9 col-12">
                        <button type="button" class="btn btn-info btn-sm" onclick="filterData();">Filter Data</button>
                        &nbsp;
                        <a href="{{ route('shipping_instruction') }}" class="btn btn-primary btn-sm">Clear Filter</a>
                        &nbsp;
                        <button type="button" class="btn btn-success btn-sm" onclick="exportList();">
                            <i class="icofont icofont-file-excel"></i> Export
                        </button>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <table class="table table-bordered nowrap compact" id="datatable">
                            <thead>
                                <tr>
                                    <th class="text-center">OPTION</th>
                                    <th class="text-center">DOC NO</th>
                                    <th class="text-center">VESSEL NAME</th>
                                    <th class="text-center">DESTINATION</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">ETD</th>
                                    <th class="text-center">ETA</th>
                                    <th class="text-center">SAILING</th>
                                    <th class="text-center">BERTHING</th>
                                    <th class="text-center">T.BERTHING</th>
                                    <th class="text-center">DOORING</th>
                                    <th class="text-center">T.DOORING</th>
                                    <th class="text-center">RECEIVED</th>
                                    <th class="text-center">T.RECEIVED</th>
                                    <th class="text-center">SYNC STATUS</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('dist/datatable/datatable.min.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/custom-flatpickr.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>

    <script>
        $(".js-flatpickr").flatpickr();

        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            responsive: false,
            scrollCollapse: true,
            scrollY: '300px',
            scrollX: true,
            fixedHeader: true,
            ajax: {
                url: '{!! route('shipping_instruction.datatable') !!}',
                data: @json(request()->all())
            },
            sWrapper: "dataTables_wrapper dt-bootstrap5",
            sFilterInput: "form-control form-control-sm",
            sLengthSelect: "form-select form-select-sm",
            pagingType: 'full_numbers',
            language: {
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: "Cari Data.. ",
                info: "Halaman <strong>_PAGE_</strong> dari <strong>_PAGES_</strong>",
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>'
                }
            },
            order: [
                [1, 'desc']
            ],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                {
                    data: 'code',
                    name: 'code',
                    class: 'text-center'
                },
                {
                    data: 'vessel_name',
                    name: 'vessel_name',
                    class: 'text-center'
                },
                {
                    data: 'destination',
                    name: 'destination',
                    class: 'text-center'
                },
                {
                    data: 'status_display',
                    name: 'statuses.display',
                    class: 'text-center'
                },
                {
                    data: 'etd_date',
                    name: 'etd_date',
                    class: 'text-center',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return moment(data).format('DD-MM-YYYY');
                        }
                        return data;
                    }
                },
                {
                    data: 'eta_date',
                    name: 'eta_date',
                    class: 'text-center',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return moment(data).format('DD-MM-YYYY');
                        }
                        return data;
                    }
                },
                {
                    data: 'sailing_date',
                    name: 'sailing_date',
                    class: 'text-center',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if (data) {
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                {
                    data: 'docking_date',
                    name: 'docking_date',
                    class: 'text-center',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if (data) {
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                {
                    data: 'total_berthing_cont',
                    name: 'total_berthing_cont',
                    class: 'text-center'
                },
                {
                    data: 'dooring_at',
                    name: 'dooring_at',
                    class: 'text-center',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if (data) {
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                {
                    data: 'total_dooring_cont',
                    name: 'total_dooring_cont',
                    class: 'text-center'
                },
                {
                    data: 'received_at',
                    name: 'received_at',
                    class: 'text-center',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if (data) {
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                {
                    data: 'total_received_cont',
                    name: 'statuses.display',
                    class: 'text-center'
                },
                {
                    data: 'is_sync',
                    name: 'is_sync',
                    class: 'text-center',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if (data == 1) {
                                return '<i class="fa fa-check text-success"></>';
                            }
                            else{
                                return '<i class="fa fa-times text-danger"></>';
                            }
                            return data;
                        }
                        return data;
                    }
                }
            ]
        });

        function filterData() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let destination = $('#destination').val();
            let status = $('#status_id').val();
            let url = '{{ route('shipping_instruction') }}';
            let keyword = $('#keyword').val();

            window.location = url + '?start_date=' + startDate + '&end_date=' + endDate + '&destination=' + destination  + '&status_id=' + status + '&keyword=' + keyword;
        }

        function exportList() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let destination = $('#destination').val();
            let status = $('#status_id').val();
            let url = '{{ route('shipping_instruction.list.export') }}';
            let keyword = $('#keyword').val();

            window.location = url + '?start_date=' + startDate + '&end_date=' + endDate + '&destination=' + destination  + '&status_id=' + status + '&keyword=' + keyword;
        }
    </script>
@endsection
