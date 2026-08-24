
@extends('layouts.master')

@section('title', 'CMS - Edit Employee')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('style')
    <style>
        .date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Edit Employee</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Employee</li>
    <li class="breadcrumb-item active">Edit Employee</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('employee.update') }}">
                    @csrf

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('employee') }}">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                        </div>
                    </div>

                    @if(count($errors))
                        <div class="row mb-3">
                            <div class="col-lg-8">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <input type="hidden" name="id" value="{{ $employee->id }}">

{{--                    @if(auth()->user()->is_superadmin)--}}
{{--                        <div class="row mb-3">--}}
{{--                            <div class="col-2">--}}
{{--                                <label class="form-label">Cabang <span class="text-danger">*</span></label>--}}
{{--                            </div>--}}
{{--                            <div class="col-3">--}}
{{--                                <select2 url="{{ route('branch.select2') }}" v-model="branch" :value="branch" class="form-select"--}}
{{--                                         name="branch_id" placeholder="Pilih Cabang" disabled></select2>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="code" id="code" placeholder="Code" value="{{ old('code') ?? $employee->code }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name') ?? $employee->name }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') ?? $employee->email }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" value="{{ old('phone') ?? $employee->phone }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Address</label>
                        </div>
                        <div class="col-5">
                            <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address">{{ old('address') ?? $employee->address }}</textarea>
                        </div>
                    </div>

                    {{-- <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Divisi <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <select2 url="{{ route('division.select2', ['branch' => $employee->branch_id]) }}" v-model="division" :value="division" class="form-select"
                                     name="division_id" placeholder="Pilih Divisi" required></select2>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Hierarki <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <select2 url="{{ route('hierarchy.select2', ['branch' => auth()->user()->branch_id]) }}" v-model="hierarchy" :value="hierarchy" class="form-select"
                                     name="hierarchy_id" placeholder="Pilih Hierarki" required></select2>
                        </div>
                    </div> --}}

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Job Position <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <select2 url="{{ route('position.select2', ['branch' => $employee->branch_id]) }}" v-model="position" :value="position" class="form-select"
                                     name="position_id" placeholder="Choose Job Position" required></select2>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Join Date <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control date" name="join_date" id="join_date" placeholder="d-m-Y" value="{{ old('join_date') ?? $employee->join_date->format('d-m-Y') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">ID Number</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="id_card_no" id="id_card_no" placeholder="ID Number" value="{{ old('id_card_no') ?? $employee->id_card_no }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">NPWP Number</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="npwp" id="npwp" placeholder="NPWP Number" value="{{ old('npwp') ?? $employee->npwp }}">
                        </div>
                    </div>

                </form>
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
                branch: '{{ old('branch_id') ?? $employee->branch_id }}',
                hierarchy: '{{ old('hierarchy_id') ?? $employee->hierarchy_id }}',
                division: '{{ old('division_id') ?? $employee->division_id }}',
                position: '{{ old('position_id') ?? $employee->position_id }}',

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

        $(".date").flatpickr({
            dateFormat: 'd-m-Y'
        });
        $(".date:visible").on('focus', function (){
            $(this).blur();
        });
        $(".date:visible").prop('readonly', false);


    </script>
@endsection
