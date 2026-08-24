
@extends('layouts.master')

@section('title', 'CMS - Users')

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
    <h3>Users</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto">
                        <a class="btn btn-sm btn-primary" href="{{ route('user.create') }}"><i class="fa fa-fw fa-plus"></i></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable">
                                <thead>
                                <tr>
                                    <th style="width: 10%">OPTION</th>
                                    <th class="text-center">USERNAME</th>
                                    <th class="text-center">BRANCH</th>
                                    <th class="text-center">EMAIL</th>
                                    <th class="text-center">LEVEL</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">CREATED AT</th>
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
    </div>
@endsection

@section('script')
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>
    <script src="{{ asset('dist/datatable/datatable.min.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/custom-flatpickr.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>

    <script>
        let vueComponent = new Vue({
            el: "#vue-container",
            data: {

            },
            methods: {

            },
            computed: {

            },
            mounted() {

            }
        });

        $("#date").flatpickr({
            mode: "range"
        });


        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            responsive: false,
            ajax: {
                url: '{!! route('user.datatable') !!}'
            },
            sWrapper: "dataTables_wrapper dt-bootstrap5",
            sFilterInput: "form-control form-control-sm",
            sLengthSelect: "form-select form-select-sm",
            pagingType: 'full_numbers',
            language: {
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: "Search.. ",
                info: "Page <strong>_PAGE_</strong> dari <strong>_PAGES_</strong>",
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>'
                }
            },
            order: [1, 'desc' ],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'},
                { data: 'username', name: 'users.username', class: 'text-center'},
                { data: 'branch', name: 'branches.name', class: 'text-center'},
                { data: 'email', name: 'users.email', class: 'text-center'},
                { data: 'level', name: 'hierarchies.name', class: 'text-center'},
                { data: 'status', name: 'statuses.display', class: 'text-center'},
                { data: 'created_at', name: 'users.created_at', class: 'text-center', orderable: true, searchable: false,
                    render: function ( data, type, row ){
                        if ( type === 'display' || type === 'filter' ){
                            return moment(data).format('DD MMM YYYY');
                        }
                        return data;
                    }
                },
            ]
        });
    </script>
@endsection
