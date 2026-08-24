@extends('layouts.master')

@section('title', 'CMS - Warehouse Operation')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endsection

@section('style')
<style>
</style>
@endsection

@section('breadcrumb-title')
<h3>Warehouse Operation</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Dashboard</li>
<li class="breadcrumb-item active">Warehouse Operation</li>
@endsection

@section('content')
<div class="container-fluid" id="vue-container">
    @include('partials._success')
    @include('partials._error')

    <div class="card">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-sm-9 col-12">

                </div>
                <div class="col-sm-3 col-12">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" class="form-control" v-model="keyword" v-on:input="searchContainer" />
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <table class="table table-bordered text-nowrap table-sm" id="datatable">
                        <thead>
                            <tr>
                                <th class="text-center">CONTAINER NO</th>
                                <th class="text-center">LOCATION</th>
                                <th class="text-center">WH STATUS</th>
                                <th class="text-center">AREA</th>
                                <th class="text-center">AREA ROW</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="containerList.length == 0">
                                <td colspan="6" class="text-center">
                                    There is no containers...
                                </td>
                            </tr>
                            <tr v-for="(container, index) in containerList" :key="container.key_id">
                                <td class="text-center">@{{ container.code }}</td>
                                <td class="text-center" v-if="container.warehouse_location_id != 17">
                                    <input type="hidden" v-model="container.warehouse_location_id_old" />
                                    <select class="form-select" v-model="container.warehouse_location_id"
                                        @change="updateWarehouseData($event, container, 'warehouse_location_id')">
                                        <option v-for="(option, index) in warehouseLocationOpts"
                                            :value="option.id">@{{ option.text }}</option>
                                    </select>
                                </td>
                                <td class="text-center" v-else>
                                    <input type="hidden" v-model="container.warehouse_location_id_old" />
                                    <span class="text-danger fw-bold">BROKEN</span>
                                </td>
                                <td class="text-center" v-if="container.warehouse_status_id != 5">
                                    <input type="hidden" v-model="container.warehouse_status_id_old" />
                                    <select class="form-select" v-model="container.warehouse_status_id"
                                        @change="updateWarehouseData($event, container, 'warehouse_status_id')">
                                        <option v-for="(option, index) in warehouseStatusOpts"
                                            :value="option.id">@{{ option.text }}</option>
                                    </select>
                                </td>
                                <td class="text-center" v-else>
                                    <input type="hidden" v-model="container.warehouse_status_id_old" />
                                    <span class="text-danger fw-bold">AT BROKEN STORAGE</span>
                                </td>
                                <td class="text-center">
                                    <input type="hidden" v-model="container.warehouse_area" />
                                    <select class="form-select" v-model="container.warehouse_area"
                                        @change="updateWarehouseData($event, container, 'warehouse_area')">
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                        <option value="F">F</option>
                                        <option value="G">G</option>
                                        <option value="H">H</option>
                                        <option value="I">I</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" v-model="container.warehouse_area_row_old" />
                                    <input type="text" class="form-control" v-on:keyup.enter="$event.target.blur()"
                                        @blur="updateWhAreaRow(container)" v-model="container.warehouse_area_row" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast hide toast fade" id="toast_success" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex justify-content-between alert-success">
                <div class="toast-body" id="toast_success_body"></div>
                <button class="btn-close btn-close-white me-2 m-auto btn-sm" type="button" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast hide toast fade" id="toast_danger" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex justify-content-between alert-danger">
                <div class="toast-body" id="toast_danger_body"></div>
                <button class="btn-close btn-close-white me-2 m-auto btn-sm" type="button" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js\axios.min.js') }}"></script>
<script src="{{ asset('js\vue.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/custom-flatpickr.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>

<script>
    let app = new Vue({
        el: "#vue-container",
            data: {
                ...@json($vueData),...{
                    keyword: ''
                }
            },
        methods: {
            updateWarehouseData(event, container, flag){
                let value = event.target.value;
                if(flag == 'warehouse_location_id'){
                    if(value == container.warehouse_location_id_old) return false;
                }
                else if(flag == 'warehouse_status_id'){
                    if(value == container.warehouse_status_id_old) return false;
                }
                else if(flag == 'warehouse_area'){
                    if(value == container.warehouse_area_old) return false;
                }
                else{
                    return false;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('container.management.update') }}',
                    type: 'POST',
                    data: {
                        id: container.id,
                        field: flag,
                        value: value
                    },
                    success: function (data){
                        // Update default value
                        if(flag == 'warehouse_location_id'){
                            container.warehouse_location_id_old = value;
                        }
                        else if(flag == 'warehouse_status_id'){
                            container.warehouse_status_id_old = value;
                        }
                        else if(flag == 'warehouse_area'){
                            container.warehouse_area_old = value;
                        }


                        $('#toast_success_body').html(data['message']);
                        const toastSuccessModal = document.getElementById("toast_success");
                        const toastSuccess = new bootstrap.Toast(toastSuccessModal);
                        toastSuccess.show();
                    },
                    error: function (error){
                        // Revert back if failed
                        if(flag == 'warehouse_location_id'){
                            container.warehouse_location_id = container.warehouse_location_id_old;
                        }
                        else if(flag == 'warehouse_status_id'){
                            container.warehouse_status_id = container.warehouse_status_id_old;
                        }
                        else if(flag == 'warehouse_area'){
                            container.warehouse_area = container.warehouse_area_old;
                        }

                        $('#toast_danger_body').html('No Internet Connection or Internal Server Error!');
                        const toastDangerModal = document.getElementById("toast_danger");
                        const toastDanger = new bootstrap.Toast(toastDangerModal);
                        toastDanger.show();
                    }
                })
            },
            searchContainer(){
                if(app.keyword != ''){
                    let result = [];
                    for (let i = 0; i < app.containerList.length; i++) {
                        if(app.containerList[i].code == app.keyword ||
                            app.containerList[i].code.includes(app.keyword)){
                            result.push(app.containerList[i]);
                        }
                    }

                    app.containerList = result;
                }
                else{
                    app.containerList = app.containerListDefault;
                }
            },
            updateWhAreaRow(container){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('container.management.update') }}',
                    type: 'POST',
                    data: {
                        id: container.id,
                        field: 'warehouse_area_row',
                        value: container.warehouse_area_row
                    },
                    success: function (data){
                        // Update default value
                        container.warehouse_area_row_old = container.warehouse_area_row;

                        $('#toast_success_body').html(data['message']);
                        const toastSuccessModal = document.getElementById("toast_success");
                        const toastSuccess = new bootstrap.Toast(toastSuccessModal);
                        toastSuccess.show();
                    },
                    error: function (error){
                        // Revert back if failed
                        container.warehouse_area_row = container.warehouse_area_row_old;

                        $('#toast_danger_body').html('No Internet Connection or Internal Server Error!');
                        const toastDangerModal = document.getElementById("toast_danger");
                        const toastDanger = new bootstrap.Toast(toastDangerModal);
                        toastDanger.show();
                    }
                })
            }
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
