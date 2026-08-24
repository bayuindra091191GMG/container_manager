@extends('layouts.master')

@section('title', 'CMS - Warehouse Management')

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
<h3>Warehouse Management</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Dashboard</li>
<li class="breadcrumb-item active">Warehouse Management</li>
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
                                            <th class="f-light">FULL</th>
                                            <th class="f-light">EMPTY_GOOD</th>
                                            <th class="f-light">BROKEN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $totalFull = 0;
                                        $totalEmpty = 0;
                                        $totalBroken = 0;
                                        @endphp
                                        @foreach ($summary as $summaryData)
                                        @php
                                        $totalFull += $summaryData->full_qty;
                                        $totalEmpty += $summaryData->empty_qty;
                                        $totalBroken += $summaryData->broken_qty;
                                        @endphp
                                        <tr>
                                            <td> Area {{ $summaryData->area }} </td>
                                            <td>{{ $summaryData->full_qty }} Container</td>
                                            <td>{{ $summaryData->empty_qty }} Container</td>
                                            <td>{{ $summaryData->broken_qty }} Container</td>
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
                                            <th class="f-dark">{{ $totalFull }} Container</th>
                                            <th class="f-dark">{{ $totalEmpty }} Container</th>
                                            <th class="f-dark">{{ $totalBroken }} Container</th>
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
                    <a class="btn btn-primary" href="{{ route('trx_warehouse.create') }}"><i
                            class="fa fa-fw fa-plus"></i></a>
                </div>
            </div> --}}
            <div class="row mb-3">
                <div class="col-sm-3 col-12">
                    <label class="form-label" for="start_date">Container Status</label>
                    <select class="form-control" id="container_status_id">
                        @foreach ($filterContainerStatusData as $status)
                        <option value="{{ $status->id }}" @if($filterContainerStatusId==$status->id) selected @endif>{{
                            $status->text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3 col-12">
                    <label class="form-label" for="start_date">WH Status</label>
                    <select class="form-control" id="warehouse_status_id">
                        @foreach ($filterStatusData as $status)
                        <option value="{{ $status->id }}" @if($filterStatusId==$status->id) selected @endif>{{
                            $status->text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3 col-12">
                    <label class="form-label" for="end_date">Container Location</label>
                    <select class="form-control" id="warehouse_location_id">
                        @foreach ($filterLocationData as $location)
                        <option value="{{ $location->id }}" @if($filterLocationId==$location->id) selected @endif>{{
                            $location->text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3 col-12">
                    <label class="form-label" for="end_date">Container Area</label>
                    <select class="form-control" id="warehouse_area">
                        <option value="SEMUA" @if("SEMUA"==$filterArea) selected @endif>SEMUA</option>
                        <option value="A" @if("A"==$filterArea) selected @endif>A</option>
                        <option value="B" @if("B"==$filterArea) selected @endif>B</option>
                        <option value="C" @if("C"==$filterArea) selected @endif>C</option>
                        <option value="D" @if("D"==$filterArea) selected @endif>D</option>
                        <option value="E" @if("E"==$filterArea) selected @endif>E</option>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-9 col-12">
                    <button type="button" class="btn btn-success btn-sm" onclick="filterData();">Filter Data</button>
                    &nbsp;
                    <a href="{{ route('trx_warehouse') }}" class="btn btn-sm btn-primary">Clear Filter</a>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <table class="table table-bordered nowrap compact" id="datatable">
                        <thead>
                            <tr>
                                <th style="width: 5%"></th>
                                <th class="text-center">CONTAINER NO</th>
                                <th class="text-center">DO / TRANSFER CODE</th>
                                <th class="text-center">CONTAINER STATUS</th>
                                <th class="text-center">LOCATION</th>
                                <th class="text-center">WH STATUS</th>
                                <th class="text-center">AREA</th>
                                <th class="text-center">AREA ROW</th>
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
    $("#js-flatpickr").flatpickr();

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
                url: '{!! route('trx_warehouse.datatable') !!}',
                data: {
                    'container_status_id': '{{ $filterContainerStatusId }}',
                    'warehouse_status_id': '{{ $filterStatusId }}',
                    'warehouse_location_id': '{{ $filterLocationId }}',
                    'warehouse_area': '{{ $filterArea }}',
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
            order: [ [1, 'desc'] ],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'},
                // { data: 'created_at', name: 'created_at', class: 'text-center', orderable: true, searchable: false,
                //     render: function ( data, type, row ){
                //         if ( type === 'display' || type === 'filter' ){
                //             return moment(data).format('DD-MM-YYYY');
                //         }
                //         return data;
                //     }
                // },
                { data: 'code', name: 'code', class: 'text-center'},
                { data: 'do_code', name: 'do_code', class: 'text-center'},
                { data: 'status', name: 'statuses.display', class: 'text-center'},
                { data: 'warehouse_location_status', name: 'warehouse_location_status', class: 'text-center'},
                { data: 'warehouse_status', name: 'warehouse_status', class: 'text-center'},
                { data: 'warehouse_area', name: 'warehouse_area', class: 'text-center'},
                { data: 'warehouse_area_row', name: 'warehouse_area_row', class: 'text-center'},
            ]
        });

        function filterData(){
            let container_status_id = $('#container_status_id').val();
            let warehouse_status_id = $('#warehouse_status_id').val();
            let warehouse_location_id = $('#warehouse_location_id').val();
            let warehouse_area = $('#warehouse_area').val();
            let url = '{{ route('trx_warehouse') }}';

            window.location = url + '?container_status_id=' + container_status_id
                + '&warehouse_status_id=' + warehouse_status_id
                + '&warehouse_location_id=' + warehouse_location_id
                + '&warehouse_area=' + warehouse_area;
        }
</script>
@endsection
