@extends('layouts.master')

@section('title', 'CMS - Container History')

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

        .dataTables_wrapper .dataTables_filter input[type="search"] {
            border: 1px solid red;
            height: 30px;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Container History</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Container History</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="start_date">Return Date Start</label>
                        <input type="text" id="start_date" class="form-control js-flatpickr" data-date-format="d-m-Y"
                            value="{{ $filterStartDate ?? '' }}" />
                    </div>
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="end_date">Return Date End</label>
                        <input type="text" id="end_date" class="form-control js-flatpickr" data-date-format="d-m-Y"
                            value="{{ $filterEndDate ?? '' }}" />
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
                </div>
                <div class="row mb-2">
                    <div class="col-sm-8 col-12">
                        <button type="button" class="btn btn-info btn-sm" onclick="filterData();">Filter Data</button>
                        &nbsp;
                        <a href="{{ route('container_history') }}" class="btn btn-sm btn-primary">Clear Filter</a>
                        &nbsp;
                        <button type="button" class="btn btn-success btn-sm" onclick="exportList();">
                            <i class="icofont icofont-file-excel"></i> Export
                        </button>
                    </div>
                    <div class="col-sm-4 col-12 text-end">

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <table class="table table-bordered nowrap compact" id="datatable">
                            <thead>
                                <tr>
                                    <th class="text-center">RETURN TO DEPO</th>
                                    <th class="text-center">CONTAINER NO</th>
                                    <th class="text-center">DO / WH TRANSFER NO.</th>
                                    <th class="text-center">ITEM NAME</th>
                                    <th class="text-center">VESSEL NAME</th>
                                    <th class="text-center">DESTINATION</th>
                                    <th class="text-center">ETD</th>
                                    <th class="text-center">ETA</th>
                                    <th class="text-center">BERTHING</th>
                                    <th class="text-center">DOORING</th>
                                    <th class="text-center">RECEIVED AT WH</th>
                                    <th class="text-center">SEMEN OUT</th>
                                    <th class="text-center">CERAMIC IN</th>
                                    <th class="text-center">DAYS SINCE BERTHING</th>
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

        var myTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            responsive: false,
            scrollCollapse: true,
            scrollY: '300px',
            scrollX: true,
            fixedHeader: true,
            ajax: {
                url: '{!! route('container_history.datatable') !!}',
                data: {
                    'start_date': '{{ $filterStartDate }}',
                    'end_date': '{{ $filterEndDate }}',
                    'destination': '{{ $filterDestination }}'
                }
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
                [0, 'desc']
            ],
            columns: [
                { data: 'returned_at', name: 'returned_at', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                { data: 'code', name: 'code', class: 'text-center' },
                { data: 'do_code', name: 'do_code', class: 'text-center' },
                { data: 'item_name', name: 'item_name' },
                { data: 'vessel_name', name: 'vessel_name' },
                { data: 'si_destination_name', name: 'si_destination_name', class: 'text-center' },
                { data: 'si_etd_date', name: 'si_etd_date', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                { data: 'si_eta_date', name: 'si_eta_date', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                {
                    data: 'berthing_at', name: 'berthing_at', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                {
                    data: 'dooring_at', name: 'dooring_at', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                { data: 'received_at', name: 'received_at', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                { data: 'semen_out_at', name: 'semen_out_at', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                { data: 'ceramic_received_at', name: 'ceramic_received_at', class: 'text-center', orderable: true, searchable: false,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                { data: 'days_since_berthing', name: 'days_since_berthing', class: 'text-center' }
            ]
        });

        function filterData() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let destination = $('#destination').val();
            let url = '{{ route('container_history') }}';

            url = url + '?destination=' + destination;

            if(startDate && endDate){
                url = url + '&start_date=' + startDate + '&end_date=' + endDate;
            }

            window.location = url;
        }

        function exportList() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let destination = $('#destination').val();
            let url = '{{ route('container_history.list.export') }}';

            url = url + '?destination=' + destination;

            if(startDate && endDate){
                url = url + '&start_date=' + startDate + '&end_date=' + endDate;
            }

            window.location = url;
        }
    </script>
@endsection
