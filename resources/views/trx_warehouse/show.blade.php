@extends('layouts.master')

@section('title', 'CMS - Show Container Detail')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
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
    <h3>Show Container Detail</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Warehouse Management</li>
    <li class="breadcrumb-item active">Show Container Detail</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('trx_warehouse.edit', ['id' => $data->id]) }}">Edit</a>
                            <a class="btn btn-sm btn-dark" href="{{ url()->previous() }}">Back</a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CONTAINER NO.</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control"
                                name="code" id="code" value="{{ $data->code }}" readonly>
                        </div>
                    </div>

                    {{-- <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DO / TRANSFER CODE</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control"
                                value="{{ $data->do_code }}" readonly>
                        </div>
                    </div> --}}

                    {{-- <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">TIMBANGAN CODE</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control"
                                value="{{ $data->timbangan_code }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SAILING DATE</label>
                        </div>
                        <div class="col-2">
                            <input type="text"
                                class="form-control"
                                name="sailing_date"
                                id="sailing_date"
                                readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">BERTHING DATE</label>
                        </div>
                        <div class="col-2">
                            <input type="text"
                                class="form-control"
                                name="docking_date"
                                id="docking_date"
                                readonly>
                        </div>
                    </div> --}}

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">WAREHOUSE LOCATION</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control"
                                value="{{ $data->warehouse_location_status }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">WAREHOUSE AREA</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control"
                                value="{{ $data->warehouse_area }} / {{ $data->warehouse_area_row }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">WAREHOUSE STATUS</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control"
                                value="{{ $data->warehouse_status }}" readonly>
                        </div>
                    </div>

                    @if($data->warehouse_status_id == 5)
                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label">CONTAINER BROKEN REASON</label>
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control"
                                    value="{{ $data->warehouse_status_reason }}" readonly>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('trx_warehouse.download', ['id' => $data->id]) }}" class="btn btn-sm btn-success"><i class="fa fa-fw fa-download me-2"></i>Download Broken Images</a>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label">BROKEN CONTAINER NUMBER <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-4">
                                <a href="{{ asset("storage/broken_images/".$brokenImageContainerNo) }}">
                                    <img src="{{ asset("storage/broken_images/".$brokenImageContainerNo) }}" width="200" height="200">
                                </a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label">BROKEN IMAGE <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-9">
                                @foreach ($brokenImages as $brokenImage)
                                <a href="{{ asset("storage/broken_images/".$brokenImage->filename) }}">
                                    <img src="{{ asset("storage/broken_images/".$brokenImage->filename) }}" width="200" height="200">
                                </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- <div class="row mb-3">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>DO NO</th>
                                        <th>DELIVERY TYPE</th>
                                        <th>CONSIGNEE</th>
                                        <th>SHIP NAME</th>
                                        <th>CONTAINER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(detail, index) in detailList" :key="detail.id">
                                        <td>
                                            <span>@{{ detail.do_code }}</span>
                                        </td>
                                        <td>
                                            <span>@{{ detail.delivery_type }}</span>
                                        </td>
                                        <td>
                                            <span>@{{ detail.trucking }}</span>
                                        </td>
                                        <td>
                                            <span>@{{ detail.shipto_name }}</span>
                                        </td>
                                        <td>
                                            <span>@{{ detail.container1 }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
                </form>
            </div>
        </div>
        <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
            <div class="toast hide toast fade" id="toast_success" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex justify-content-between alert-success">
                    <div class="toast-body" id="toast_success_body"></div>
                    <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
            <div class="toast hide toast fade" id="toast_danger" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex justify-content-between alert-danger">
                    <div class="toast-body" id="toast_danger_body"></div>
                    <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast"
                        aria-label="Close"></button>
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
                ...@json($vueData),
            },
            methods: {
            },
            computed: {

            },
            watch: {

            },
            mounted() {

            }
        });

    </script>
@endsection
