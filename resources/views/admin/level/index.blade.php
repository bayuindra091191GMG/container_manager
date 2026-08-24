
@extends('layouts.master')

@section('title', 'CMS - Levels')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
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
    <h3>Levels</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Levels</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto">
                        @if(auth()->user()->is_superadmin || auth()->user()->hierarchy->hasPermissionTo('level.add'))
                            <a class="btn btn-sm btn-primary" href="{{ route('level.create') }}"><i class="fa fa-fw fa-plus"></i></a>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="datatable">
                        <thead>
                        <tr>
                            <th style="width: 20%"></th>
                            <th class="text-center">Code</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Created At</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
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
                url: '{!! route('level.datatable') !!}'
            },
            sWrapper: "dataTables_wrapper dt-bootstrap5",
            sFilterInput: "form-control form-control-sm",
            sLengthSelect: "form-select form-select-sm",
            pagingType: 'full_numbers',
            language: {
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: "Cari Data.. ",
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>'
                }
            },
            order: [ ],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'},
                { data: 'code', name: 'hirearchies.code', class: 'text-center'},
                { data: 'name', name: 'hirearchies.name', class: 'text-center'},
                { data: 'description', name: 'hirearchies.description', class: 'text-center'},
                { data: 'created_at', name: 'created_at', class: 'text-center', orderable: true, searchable: false,
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
