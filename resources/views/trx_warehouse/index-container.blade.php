@extends('layouts.master')

@section('title', 'CMS - Container Management')

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
    <h3>Container Management</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Container Management</li>
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
                                CONTAINER SUMMARY PER AREA
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
                                <div class="recent-table table-responsive" style="font-size: 12px;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="f-light">AREA</th>
                                                <th class="f-light">BERTHING</th>
                                                <th class="f-light">DOORING</th>
                                                <th class="f-light">RECEIVED</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalBerthing = 0;
                                                $totalDooring = 0;
                                                $totalReceived = 0;
                                            @endphp
                                            @foreach ($summary as $summaryData)
                                                @php
                                                    $totalBerthing += $summaryData->berthing->total_berthing ?? 0;
                                                    $totalDooring += $summaryData->dooring->total_dooring ?? 0;
                                                    $totalReceived += $summaryData->received->total_received ?? 0;
                                                @endphp
                                                <tr>
                                                    <td> {{ $summaryData->area }} </td>
                                                    <td>{{ $summaryData->berthing->total_berthing ?? 0 }} Container</td>
                                                    <td>{{ $summaryData->dooring->total_dooring ?? 0 }} Container</td>
                                                    <td>{{ $summaryData->received->total_received ?? 0 }} Container</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                            </tr>
                                            {{-- <tr>
                                      <td>>45 Hari</td>
                                      <td>214 Container</td>
                                      <td>52 Container</td>
                                      <td>36 Container</td>
                                  </tr>
                                  <tr>
                                      <td> 15 Hari </td>
                                      <td>0 Container</td>
                                      <td>0 Container</td>
                                      <td>0 Container</td>
                                  </tr> --}}
                                        </tbody>
                                        <thead>
                                            <tr style="margin-top:10px;">
                                                <th class="f-dark">TOTAL</th>
                                                <th class="f-dark">{{ $totalBerthing }} Container</th>
                                                <th class="f-dark">{{ $totalDooring }} Container</th>
                                                <th class="f-dark">{{ $totalReceived }} Container</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="card">
            <div class="card-body">
                {{-- <div class="row mb-2 justify-content-end">
                    <div class="col-auto">
                        <a class="btn btn-primary" href="{{ route('trx_warehouse.create') }}"><i class="fa fa-fw fa-plus"></i></a>
                    </div>
                </div> --}}
                <div class="row mb-2">
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="start_date">ETD Start</label>
                        <input type="text" id="start_date" class="form-control js-flatpickr" data-date-format="d-m-Y"
                            value="{{ $filterStartDate ?? '' }}" />
                    </div>
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="end_date">ETD End</label>
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
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="start_date">Container Status</label>
                        <select class="form-control" id="container_status_id">
                            @foreach ($filterContainerStatusData as $status)
                                <option value="{{ $status->id }}" @if ($filterContainerStatusId == $status->id) selected @endif>
                                    {{ $status->text }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="warning">Warning</label>
                        <select class="form-control" id="warning">
                            <option value="none" @if($filterWarning == 'none') selected @endif>-- Select Warning --</option>
                            <option value="h-1s" @if($filterWarning == 'h-1s') selected @endif>H-1 Storage</option>
                            <option value="h-30d" @if($filterWarning == 'h-30d') selected @endif>H-30 Demurrage</option>
                            <option value="h-7d" @if($filterWarning == 'h-7d') selected @endif>H-7 Demurrage</option>
                            <option value="h-3d" @if($filterWarning == 'h-3d') selected @endif>H-3 Demurrage</option>
                            <option value="over-s" @if($filterWarning == 'over-s') selected @endif>Over Storage</option>
                            <option value="over-d" @if($filterWarning == 'over-d') selected @endif>Over Demurrage</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-8 col-12">
                        <button type="button" class="btn btn-info btn-sm" onclick="filterData();">Filter Data</button>
                        &nbsp;
                        <a href="{{ route('container') }}" class="btn btn-sm btn-primary">Clear Filter</a>
                        &nbsp;
                        <button type="button" class="btn btn-success btn-sm" onclick="exportList();">
                            <i class="icofont icofont-file-excel"></i> Export
                        </button>
                    </div>
                    <div class="col-sm-4 col-12 text-end">
                        @if($user->isSuperAdmin())
                            <a href="{{ route('container.operation', ['status' => 'received']) }}" class="btn btn-sm btn-secondary">Return to Depo</a>
                        @endif
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <table class="table table-bordered nowrap compact" id="datatable">
                            <thead>
                                <tr>
                                    <th class="text-center">CONTAINER NO</th>
                                    <th class="text-center">DO / WH TRANSFER NO.</th>
                                    <th class="text-center">ITEM NAME</th>
                                    <th class="text-center">VESSEL NAME</th>
                                    <th class="text-center">DESTINATION</th>
                                    <th class="text-center">CONTAINER STATUS</th>
                                    <th class="text-center">ETD</th>
                                    <th class="text-center">ETA</th>
                                    <th class="text-center">BERTHING</th>
                                    <th class="text-center">DOORING</th>
                                    <th class="text-center">RECEIVED AT WH</th>
                                    <th class="text-center">SEMEN OUT</th>
                                    <th class="text-center">CERAMIC IN</th>
                                    <th class="text-center">RETURN TO DEPO</th>
                                    <th class="text-center">WARNING</th>
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
                url: '{!! route('container.datatable') !!}',
                data: {
                    'container_status_id': '{{ $filterContainerStatusId }}',
                    'warehouse_status_id': '{{ $filterStatusId }}',
                    'warehouse_location_id': '{{ $filterLocationId }}',
                    'warehouse_area': '{{ $filterArea }}',
                    'start_date': '{{ $filterStartDate }}',
                    'end_date': '{{ $filterEndDate }}',
                    'destination': '{{ $filterDestination }}',
                    'warning': '{{ $filterWarning }}'
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
                [0, 'asc']
            ],
            columns: [
                { data: 'code', name: 'code', class: 'text-center' },
                { data: 'do_code', name: 'do_code', class: 'text-center' },
                { data: 'item_name', name: 'item_name' },
                { data: 'vessel_name', name: 'vessel_name' },
                { data: 'si_destination_name', name: 'si_destination_name', class: 'text-center' },
                { data: 'status', name: 'statuses.display', class: 'text-center' },
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
                { data: 'free_storage_time_limit', name: 'free_storage_time_limit', class: 'text-center', searchable: false, sortable: false,
                    render: function ( data, type, full, meta ){
                        if ( type === 'display' || type === 'filter' ){
                            if(full.is_over_storage == 1 && full.is_over_demurrage == 1){
                                return 'OVER STORAGE AND DEMURRAGE';
                            }
                            else if(full.is_over_storage != 1 && full.is_over_demurrage == 1){
                                return 'OVER DEMURRAGE';
                            }
                            else if(full.is_over_storage == 1 && full.is_over_demurrage != 1){
                                return 'OVER STORAGE';
                            }
                            else{
                                if(data > 0 && full.is_storage_monitor_start == 1 && full.is_storage_monitor_end == 0){
                                    let days = Math.ceil((data / 3600) / 24);
                                    if(days == 1) return 'H-1 STORAGE';
                                }
                                else{
                                    if(full.is_demurrage_monitor_start == 1 && full.free_demurrage_time_limit > 0){
                                        let days = Math.ceil((full.free_demurrage_time_limit / 3600) / 24);
                                        if(days <= 30 && days > 7){
                                            return 'H-30 DEMURRAGE';
                                        }
                                        else if(days <= 7 && days > 3){
                                            return 'H-7 DEMURRAGE';
                                        }
                                        else if(days <= 3 && days > 0){
                                            return 'H-3 DEMURRAGE';
                                        }
                                    }
                                }
                            }

                            return '-';
                        }
                        return data;
                    }
                },
                // { data: 'days_since_berthing', name: 'days_since_berthing', class: 'text-center', searchable: false, sortable: false,
                //     render: function ( data, type, row ){
                //         if ( type === 'display' || type === 'filter' ){
                //             if(data > 0){
                //                 let days = Math.floor((data / 3600) / 24);
                //                 return days + ' HARI';
                //             }

                //             return '-';
                //         }
                //         return data;
                //     }
                // },
                {
                    data: null,
                    render: function(data, type, full, meta) {
                        if (data.is_demurrage_monitor_end == 1) {
                            if(data.days_since_berthing_to_returned > 0){
                                let days = Math.floor((data.days_since_berthing_to_returned / 3600) / 24);
                                days++;
                                return days + ' HARI';
                            }

                            return '-';

                        } else {
                            if(data.days_since_berthing > 0){
                                let days = Math.floor((data.days_since_berthing / 3600) / 24);
                                days++;
                                return days + ' HARI';
                            }

                            return '-';
                        }
                    },
                    class: "text-center",
                    searchable: false,
                    orderable: false
                },
            ]
        });

        function filterData() {
            let container_status_id = $('#container_status_id').val();
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let destination = $('#destination').val();
            let warning = $('#warning').val();
            let url = '{{ route('container') }}';

            url = url + '?container_status_id=' + container_status_id  + '&destination=' + destination + '&warning=' + warning;

            if(startDate && endDate){
                url = url + '&start_date=' + startDate + '&end_date=' + endDate;
            }

            window.location = url;
        }

        function exportList() {
            let container_status_id = $('#container_status_id').val();
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let destination = $('#destination').val();
            let warning = $('#warning').val();
            let url = '{{ route('container.list.export') }}';

            url = url + '?container_status_id=' + container_status_id  + '&destination=' + destination + '&warning=' + warning;

            if(startDate && endDate){
                url = url + '&start_date=' + startDate + '&end_date=' + endDate;
            }

            window.location = url;
        }
    </script>
@endsection
