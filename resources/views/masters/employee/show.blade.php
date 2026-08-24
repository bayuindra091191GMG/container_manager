
@extends('layouts.master')

@section('title', 'CMS - Detail Employee')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('style')
    <style>
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Detail Employee</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Employee</li>
    <li class="breadcrumb-item active">Detail Employee</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-auto">
                        <a class="btn btn-sm btn-danger" href="{{ route('employee') }}">Cancel</a>
                        <a class="btn btn-sm btn-primary" href="{{ route('employee.edit', ['id' => $employee->id]) }}">Edit</a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Code</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="code" id="code" placeholder="Code" value="{{ $employee->code }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Name</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ $employee->name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Email</label>
                    </div>
                    <div class="col-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ $employee->email }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Phone</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" value="{{ $employee->phone }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Address</label>
                    </div>
                    <div class="col-5">
                        <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address" readonly>{{ $employee->address }}</textarea>
                    </div>
                </div>

                {{-- <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Divisi</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" value="{{ $employee->division->name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Hierarki</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" value="{{ $employee->hierarchy->name }}" readonly>
                    </div>
                </div> --}}

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Job Position</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" value="{{ $employee->employee_position->name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Join Date</label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control date" name="join_date" id="join_date" placeholder="d-m-Y" value="{{ $employee->join_date->format('d-m-Y') }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">ID Number</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="id_card_no" id="id_card_no" placeholder="ID Number" value="{{ $employee->id_card_no }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">NPWP Number</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="npwp" id="npwp" placeholder="NPWP Number" value="{{ $employee->npwp }}" readonly>
                    </div>
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
    <script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-autonumeric@1.2.6/dist/vue-autonumeric.min.js"></script>

    <script type="text/x-template" id="select2-template">
        <select>
            <slot></slot>
        </select>
    </script>
    <script src="{{ asset('js/vueComponent-select2.js') }}"></script>

    <script>
        let vueComponent = new Vue({
            el: "#vue-container",
            data: {

                autonumericFormat: {
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    decimalPlaces: 10,
                    modifyValueOnWheel: false,
                    allowDecimalPadding: false,
                    unformatOnSubmit: true,
                },
            },
            methods: {

            },
            computed: {

            },
            mounted() {

            }
        });


    </script>
@endsection
