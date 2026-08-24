
@extends('layouts.master')

@section('title', 'CMS - Detail Job Position')

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
    <h3>Detail Job Position</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Job Position</li>
    <li class="breadcrumb-item active">Detail Job Position</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-auto">
                        <a class="btn btn-sm btn-danger" href="{{ route('position') }}">Cancel</a>
                        <a class="btn btn-sm btn-primary" href="{{ route('position.edit', ['id' => $position->id]) }}">Edit</a>
                    </div>
                </div>

{{--                @if(auth()->user()->is_superadmin)--}}
{{--                    <div class="row mb-3">--}}
{{--                        <div class="col-2">--}}
{{--                            <label class="form-label">Cabang</label>--}}
{{--                        </div>--}}
{{--                        <div class="col-3">--}}
{{--                            <input type="text" class="form-control" value="{{ $position->branch->name }}" readonly>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Code</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="code" id="code" placeholder="Code" value="{{ old('code') ?? $position->code }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Name</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name') ?? $position->name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Description</label>
                    </div>
                    <div class="col-5">
                        <textarea class="form-control" rows="3" name="description" id="description" placeholder="Description" readonly>{{ old('description') ?? $position->description }}</textarea>
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
