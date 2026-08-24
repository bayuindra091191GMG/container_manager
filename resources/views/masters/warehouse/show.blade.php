
@extends('layouts.master')

@section('title', 'CMS - Detail Warehouse')

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
    <h3>Detail Warehouse</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Warehouse</li>
    <li class="breadcrumb-item active">Detail Warehouse</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-auto">
                        <a class="btn btn-sm btn-danger" href="{{ route('warehouse') }}">Cancel</a>
                        <a class="btn btn-sm btn-primary" href="{{ route('warehouse.edit', ['id' => $warehouse->id]) }}">Edit</a>
                    </div>
                </div>



                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Code</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="code" id="code" placeholder="Code" value="{{ old('code') ?? $warehouse->code }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Name</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name') ?? $warehouse->name }}" readonly>
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

    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-autonumeric@1.2.6/dist/vue-autonumeric.min.js"></script>


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

        $("#date").flatpickr({
            mode: "range"
        });


    </script>
@endsection
