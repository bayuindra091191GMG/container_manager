
@extends('layouts.master')

@section('title', 'CMS - Distributor')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dist/datatable/datatable.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('style')
    <style>
        #date {
            background-color: white !important;
        }

        .btn-xs {
            padding: 0.05rem 0.4rem !important;
            font-size: 11px !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Distributor</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Distributor</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto">
                        @if(auth()->user()->is_superadmin || auth()->user()->hierarchy->hasPermissionTo('distributor.add'))
                            <a class="btn btn-primary btn-sm " href="{{ route('distributor.create') }}"><i class="fa fa-fw fa-plus"></i></a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <table class="table table-bordered compact nowrap" id="datatable">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 10% !important;">OPTION</th>
                                <th class="text-center">BRANCH</th>
                                <th class="text-center">DISTRIBUTOR CODE</th>
                                <th class="text-center">DISTRIBUTOR NAME</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            responsive: false,
            ajax: {
                url: '{!! route('distributor.datatable') !!}'
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
                { data: 'branch_name', name: 'branches.name', class: 'text-center'},
                { data: 'code', name: 'code', class: 'text-center'},
                { data: 'name', name: 'name', class: 'text-center'}
            ]
        });

        $('#datatable').on('click', '.delete-btn', function (){
            let el = $(this);

            Swal.fire({
                icon: 'info',
                title: 'Delete Distributor',
                text: 'Are you sure you want to delete this Distributor?',
                showDenyButton: true,
                confirmButtonText: 'Yes',
                denyButtonText: 'No',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return $.ajax({
                        url: '{!! route('distributor.delete') !!}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: el.data('id')
                        }
                    }).done(function (data){
                        console.log(data);
                        return data;
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(function (result){
                if(result.isConfirmed){
                    console.log(result);
                    if(result.value.success){
                        datatable.ajax.reload();
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Successfully deleted Distributor data',
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                        });
                    }
                    else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete data!',
                        });
                    }
                }
            });
        });
    </script>
@endsection
