@extends('layouts.master')

@section('title', 'CMS - Branch')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dist/datatable/datatable.min.css') }}">
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
    <h3>Branch</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Branch</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto">
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->can('branch.add'))
                            <a class="btn btn-sm btn-primary" href="{{ route('branch.create') }}"><i class="fa fa-fw fa-plus"></i></a>
                        @endif
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <table class="table table-bordered nowrap compact" id="datatable">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 10%">OPTION</th>
                                <th class="text-center">BRANCH CODE</th>
                                <th class="text-center">BRANCH NAME</th>
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
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>
    <script src="{{ asset('dist/datatable/datatable.min.js') }}"></script>

    <script>
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            responsive: false,
            ajax: {
                url: '{!! route('branch.datatable') !!}'
            },
            sWrapper: "dataTables_wrapper dt-bootstrap5",
            sFilterInput: "form-control form-control-sm",
            sLengthSelect: "form-select form-select-sm",
            pagingType: 'full_numbers',
            language: {
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: "Find keyword..",
                emptyTable: "No data to display yet",
                infoEmpty: "Page 0 of 0",
                info: "Page <strong>_PAGE_</strong> of <strong>_PAGES_</strong>",
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>'
                }
            },
            order: [1, 'asc' ],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'},
                { data: 'code', name: 'code', class: 'text-center'},
                { data: 'name', name: 'branches.name', class: 'text-center'},
                { data: 'status', name: 'status.display', class: 'text-center'}
            ]
        });


    </script>
@endsection
