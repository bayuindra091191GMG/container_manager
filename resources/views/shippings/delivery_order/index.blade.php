@extends('layouts.master')

@section('title', 'CMS - Delivery Order')

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
    <h3>Delivery Order</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item active">Delivery Order</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto">
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->can('do.add'))
                            <a class="btn btn-sm btn-primary" href="{{ route('delivery_order.create') }}"><i class="fa fa-fw fa-plus"></i>&nbsp;Create</a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2 col-12">
                        <label class="form-label" for="start_date">Start Date</label>
                        <input type="text" id="start_date" class="form-control js-flatpickr" data-date-format="d-m-Y" value="{{ $filterStartDate }}"/>
                    </div>
                    <div class="col-sm-2 col-12">
                        <label class="form-label" for="end_date">End Date</label>
                        <input type="text" id="end_date" class="form-control js-flatpickr" data-date-format="d-m-Y" value="{{ $filterEndDate }}"/>
                    </div>
                    <div class="col-sm-3 col-12">
                        <label class="form-label" for="branch_destination">Branch Destination</label>
                        <select class="form-select" id="branch_destination">
                            <option value="0"> -- Select Destination -- </option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @if($filterBranchDestination == $branch->id) selected @endif>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 col-12">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select" id="status">
                            <option value="-1" @if($filterStatus == -1) selected @endif>ALL</option>
                            <option value="0" @if($filterStatus == 0) selected @endif>OPEN</option>
                            <option value="1" @if($filterStatus == 1) selected @endif>CLOSED</option>
                        </select>
                    </div>
                    <div class="col-sm-2 col-12">
                        <label class="form-label" for="type">Type</label>
                        <select class="form-select" id="type">
                            <option value="all" @if($filterType == 'all') selected @endif>ALL</option>
                            <option value="delivery_order" @if($filterType == 'delivery_order') selected @endif>DELIVERY ORDER</option>
                            <option value="transfer" @if($filterType == 'transfer') selected @endif>TRANSFER</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-9 col-12">
                        <button type="button" class="btn btn-success btn-sm" onclick="filterData();">Filter Data</button>
                        &nbsp;
                        <a href="{{ route('delivery_order') }}" class="btn btn-secondary btn-sm">Clear Filter</a>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <table class="table table-bordered nowrap compact" id="datatable">
                            <thead>
                            <tr>
                                <th class="text-center">OPTION</th>
                                <th class="text-center"></th>
                                <th class="text-center">DO NO</th>
                                <th class="text-center">TYPE</th>
                                <th class="text-center">DR NO</th>
                                <th class="text-center">SO NO</th>
                                <th class="text-center">DATE</th>
                                <th class="text-center">BRANCH DESTINATION</th>
                                <th class="text-center">SHIPPING COMPANY</th>
                                <th class="text-center">SHIP TO NAME</th>
                                <th class="text-center">CONT 1</th>
                                <th class="text-center">ANGKUTAN</th>
                                <th class="text-center">STATUS</th>
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
    <script src="{{ asset('js\axios.min.js') }}"></script>

    <script>
        $(".js-flatpickr").flatpickr();

        function escapeHtml(value) {
            if (value === null || value === undefined) {
                return '';
            }
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDetails(details) {
            if (!details || details.length === 0) {
                return 'No item lines';
            }

            let html = '<table class="table table-sm table-bordered mb-0" style="width: 100%">';
            html += '<thead><tr class="text-center"><th>ITEM CODE</th><th>ITEM NAME</th><th>QTY</th><th>UNIT</th></tr></thead><tbody>';
            for (let i = 0; i < details.length; i++) {
                let qty = details[i].qty ? details[i].qty.toLocaleString('de-DE') : '0';
                html += '<tr class="text-center"><td>' + escapeHtml(details[i].item_code) + '</td><td>' + escapeHtml(details[i].item_name) + '</td><td class="text-end">' + qty + '</td><td>' + escapeHtml(details[i].unit) + '</td></tr>';
            }
            html += '</tbody></table>';

            return html;
        }

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
                url: '{!! route('delivery_order.datatable') !!}',
                data: {
                    start_date: '{{ $filterStartDate }}',
                    end_date: '{{ $filterEndDate }}',
                    branch_destination: '{{ $filterBranchDestination }}',
                    status: '{{ $filterStatus }}',
                    type: '{{ $filterType }}'
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
            order: [[6, 'desc']],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                { data: null, orderable: false, searchable: false, class: 'text-center',
                    defaultContent: '<button class="btn btn-xs btn-light js-toggle-details"><i class="fa fa-fw fa-list"></i></button>' },
                { data: 'code', name: 'do_headers.code', class: 'text-center' },
                { data: 'type', name: 'do_headers.type', class: 'text-center' },
                { data: 'dr_code', name: 'do_headers.dr_code', class: 'text-center' },
                { data: 'so_code', name: 'do_headers.so_code', class: 'text-center' },
                { data: 'date', name: 'do_headers.date', class: 'text-center', orderable: true, searchable: false,
                    render: function ( data, type, row ){
                        if ( type === 'display' || type === 'filter' ){
                            if(data){
                                return moment(data).format('DD-MM-YYYY');
                            }
                            return data;
                        }
                        return data;
                    }
                },
                { data: 'branch_name', name: 'branches.name', class: 'text-center' },
                { data: 'shipping_company', name: 'do_headers.shipping_company', class: 'text-center' },
                { data: 'shipto_name', name: 'do_headers.shipto_name', class: 'text-center' },
                { data: 'container1_code', name: 'do_headers.container1_code', class: 'text-center' },
                { data: 'transportation', name: 'do_headers.transportation', class: 'text-center' },
                { data: 'is_close', name: 'do_headers.is_close', class: 'text-center', searchable: false,
                    render: function ( data, type, row ){
                        if ( type === 'display' || type === 'filter' ){
                            if(data == 1){
                                return '<span class="badge badge-danger">CLOSED</span>';
                            }
                            return '<span class="badge badge-success">OPEN</span>';
                        }
                        return data;
                    }
                }
            ]
        });

        $('#datatable tbody').on('click', 'button.js-toggle-details', function () {
            let tr = $(this).closest('tr');
            let row = $('#datatable').DataTable().row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(formatDetails(row.data().details)).show();
                tr.addClass('shown');
            }
        });

        $('#datatable tbody').on('click', 'button.js-void-do', function () {
            let doId = $(this).data('id');
            if (!confirm('Void this Delivery Order?')) {
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.post('{{ route('delivery_order.void') }}', { id: doId })
                .done(function () {
                    window.location.reload();
                })
                .fail(function () {
                    alert('Failed to void Delivery Order!');
                });
        });

        function filterData(){
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let branchDestination = $('#branch_destination').val();
            let status = $('#status').val();
            let type = $('#type').val();
            let url = '{{ route('delivery_order') }}';

            window.location = url + '?start_date=' + startDate + '&end_date=' + endDate + '&branch_destination=' + branchDestination + '&status=' + status + '&type=' + type;
        }
    </script>
@endsection
