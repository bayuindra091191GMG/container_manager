
@extends('layouts.master')

@section('title', 'CMS - Division')

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
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Division</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Division</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto">
                        @if(auth()->user()->is_superadmin || auth()->user()->hierarchy->hasPermissionTo('division.add'))
                            <a class="btn btn-sm btn-primary" href="{{ route('division.create') }}"><i class="fa fa-fw fa-plus"></i></a>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="datatable">
                        <thead>
                        <tr>
                            <th style="width: 20%">Option</th>
                            <th class="text-center">Code</th>
                            @if(auth()->user()->is_superadmin)
                                <th class="text-center">Branch</th>
                            @endif
                            <th class="text-center">Name</th>
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
                url: '{!! route('division.datatable') !!}'
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
            order: [ ],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center'},
                { data: 'code', name: 'code', class: 'text-center'},
                @if(auth()->user()->is_superadmin)
                { data: 'branch', name: 'branches.name', class: 'text-center'},
                @endif
                { data: 'name', name: 'employee_positions.name', class: 'text-center'},
                // { data: 'created_at', name: 'created_at', class: 'text-center', orderable: true, searchable: false,
                //     render: function ( data, type, row ){
                //         if ( type === 'display' || type === 'filter' ){
                //             return moment(data).format('DD MMM YYYY');
                //         }
                //         return data;
                //     }
                // },
            ]
        });


    </script>
@endsection
