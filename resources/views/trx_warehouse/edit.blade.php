
@extends('layouts.master')

@section('title', 'CMS - Edit Container Detail')

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
    <h3>Edit Container Detail</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Warehouse Management</li>
    <li class="breadcrumb-item active">Edit Container Detail</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('trx_warehouse.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ url()->previous() }}">Cancel</a>
                            <button type="button" id="btn_loading" class="btn btn-sm btn-success" style="display: none;">
                                <i class="spinner-border spinner-border-sm text-green" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </i>
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                        </div>
                    </div>
                    <div class="row mb-3 mt-3">
                        <div class="col-12">
                            @if(\Illuminate\Support\Facades\Session::has('error'))
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <p class="mb-0">
                                        {{ \Illuminate\Support\Facades\Session::get('error') }}
                                    </p>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{ $data->id }}">

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CONTAINER NO.</label>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control"
                                name="code" id="code" value="{{ $data->code }}" readonly>
                        </div>
                    </div>

                    {{-- <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DO / TRANSFER CODE</label>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control"
                                value="{{ $data->do_code }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">TIMBANGAN CODE</label>
                        </div>
                        <div class="col-6">
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
                            <label class="form-label">CONTAINER STATUS</label>
                        </div>
                        <div class="col-6">
                            <select class="form-control" name="container_status">
                                @foreach ($container_statuses as $container_status)
                                    <option value="{{ $container_status->id }}" @if ($container_status->id == $data->status_id) selected @endif>
                                        {{ $container_status->display }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">WAREHOUSE LOCATION</label>
                        </div>
                        <div class="col-6">
                            <select class="form-control" name="location">
                                @foreach ($locations as $location)
                                    <option value={{ $location->id }} @if($location->id == $data->warehouse_location_status) selected @endif>{{ $location->text }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">WAREHOUSE AREA</label>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <select class="form-control" name="area">
                                    <option value="A" @if("A" == $data->warehouse_area) selected @endif>A</option>
                                    <option value="B" @if("B" == $data->warehouse_area) selected @endif>B</option>
                                    <option value="C" @if("C" == $data->warehouse_area) selected @endif>C</option>
                                    <option value="D" @if("D" == $data->warehouse_area) selected @endif>D</option>
                                    <option value="E" @if("E" == $data->warehouse_area) selected @endif>E</option>
                                </select>

                                {{-- <button class="btn btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Area</button>
                                <ul class="dropdown-menu dropdown-block" style="">
                                  <li><a class="dropdown-item" href="javascript:void(0)">A</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0)">B</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0)">C</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0)">D</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0)">E</a></li>
                                  <li><a class="dropdown-item" href="javascript:void(0)">F</a></li>
                                </ul> --}}
                                <input class="form-control" type="text" name="area_row" value="{{  $data->warehouse_area_row }}" placeholder="Area Row Number">
                              </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CONTAINER IN WAREHOUSE STATUS</label>
                        </div>
                        <div class="col-6">
                            <select id="status" class="form-control" name="status" v-model="warehouse_status" v-on:change="changeStatus">
                                @foreach ($statuses as $status)
                                    <option value={{ $status->id }} @if($status->id == $data->warehouse_status) selected @endif>{{ $status->text }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div v-if="broken_status == 1">
                        <div class="row mb-3" >
                            <div class="col-3">
                                <label class="form-label">BROKEN REASON <br>*Fill only if Container Status Broken*</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" name="broken_reason"
                                    value="{{ $data->warehouse_status_reason }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label">BROKEN CONTAINER NUMBER <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-6">
                                @if (!empty($brokenImageContainerNo))
                                    <a href="{{ asset("storage/broken_images/".$brokenImageContainerNo) }}">
                                        <img src="{{ asset("storage/broken_images/".$brokenImageContainerNo) }}" width="200" height="200">
                                    </a>
                                    <br>
                                @endif
                                <input type="file" class="form-control" id="image_container_no" name="image_container_no" accept="image/png, image/jpeg"/>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label">BROKEN IMAGE <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-6">
                                @if (count($brokenImages) > 0)
                                    @foreach ($brokenImages as $brokenImage)
                                    <a href="{{ asset("storage/broken_images/".$brokenImage->filename) }}">
                                        <img src="{{ asset("storage/broken_images/".$brokenImage->filename) }}" width="200" height="200">
                                    </a>
                                    @endforeach
                                @endif
                                <input type="file" class="form-control" id="image" name="image[]" accept="image/png, image/jpeg" multiple/>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-else>
                        <input type="hidden" class="form-control" value="{{ $data->warehouse_status_reason }}">
                    </div>

                    {{-- <div class="row mb-3">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>DO NO</th>
                                        <th>DELIVERY TYPE</th>
                                        <th>CONSIGNEE</th>
                                        <th>SHIP NAME</th>
                                        <th>CONTAINER1</th>
                                        <th>CONTAINER2</th>
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
                                        <td>
                                            <span>@{{ detail.container2 }}</span>
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
                ...@json($vueData),...{
                    warehouse_status: {{ $data->warehouse_status_id }},
                    broken_status: {{ $data->warehouse_status_id == 5 ? 1: 0}},
                }
            },
            methods: {
                changeStatus(){
                    var status = $('#status').val();
                    console.log(status);
                    this.broken_status = status == 5 ? 1 : 0;

                    console.log(this.broken_status);
                }
            },
            computed: {

            },
            watch: {

            },
            mounted() {

            }
        });

        $("#image").fileinput({
            allowedFileExtensions: ["jpg", "jpeg", "png", "webp"],
            overwriteInitial: false,
            maxFileSize: 2048,
            showUpload: false,
        });

        $("#image").fileinput({
            allowedFileExtensions: ["jpg", "jpeg", "png", "webp"],
            overwriteInitial: false,
            maxFileSize: 2048,
            showUpload: false,
        });

    </script>
@endsection
