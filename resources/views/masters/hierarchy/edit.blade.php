
@extends('layouts.master')

@section('title', 'CMS - Ubah Hirearki')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('style')
    <style>
        #date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Ubah Hirearki</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Hirearki</li>
    <li class="breadcrumb-item active">Ubah Hirearki</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('hierarchy.update') }}">
                    @csrf

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('hierarchy') }}">Batal</a>
                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                        </div>
                    </div>

                    @if(count($errors))
                        <div class="row">
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

                    <input type="hidden" name="id" value="{{ $hierarchy->id }}">

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
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="code" id="code" placeholder="Kode" value="{{ old('code') ?? $hierarchy->code }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Nama" value="{{ old('name') ?? $hierarchy->name }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Deskripsi</label>
                        </div>
                        <div class="col-5">
                            <textarea class="form-control" rows="3" name="description" id="description" placeholder="Deskripsi">{{ old('description') ?? $hierarchy->description }}</textarea>
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
                branch: '{{ old('branch_id') ?? (auth()->user()->is_superadmin ? $hierarchy->branch_id : '') }}',
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
