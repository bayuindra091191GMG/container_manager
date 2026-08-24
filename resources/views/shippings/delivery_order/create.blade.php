@extends('layouts.master')

@section('title', 'CMS - Create Delivery Order')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
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
    <h3>Create Delivery Order</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item"><a href="{{ route('delivery_order') }}">Delivery Order</a></li>
    <li class="breadcrumb-item active">Create Delivery Order</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <form id="form_create_do">
                    {{ csrf_field() }}

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-danger btn-sm" href="{{ route('delivery_order') }}">Cancel</a>
                            <button type="button" id="btn_loading" class="btn btn-success" style="display: none;">
                                <i class="spinner-border spinner-border-sm text-green" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </i>
                            </button>
                            <button type="button" id="btn_submit" class="btn btn-success btn-sm"
                                v-on:click="submitForm">Submit</button>
                        </div>
                    </div>

                    <div id="section_validation_error" class="row mb-2" style="display: none;">
                        <div class="col-lg-8">
                            <div class="alert alert-danger" role="alert">
                                <ul id="validation_error_list"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DO / TRANSFER NO. <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="code" id="code" placeholder="Document Number"
                                v-model="code" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">TYPE <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <select class="form-select" name="type" id="type" v-model="type">
                                <option value="delivery_order">DELIVERY ORDER</option>
                                <option value="transfer">TRANSFER</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DATE <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control date" name="date" id="date"
                                v-model="date" placeholder="dd-mm-yyyy">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SO NO.</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="so_code" id="so_code" v-model="so_code">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DR NO.</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="dr_code" id="dr_code" v-model="dr_code">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CUSTOMER CODE</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="customer_code" id="customer_code" v-model="customer_code">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CUSTOMER NAME</label>
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="customer_name" id="customer_name" v-model="customer_name">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SHIPTO CODE</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="shipto_code" id="shipto_code" v-model="shipto_code">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SHIPTO NAME</label>
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="shipto_name" id="shipto_name" v-model="shipto_name">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SHIPPING COMPANY</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="shipping_company" id="shipping_company" v-model="shipping_company">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">TRANSPORTATION</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="transportation" id="transportation" v-model="transportation">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DESTINATION BRANCH <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-4">
                            <select class="form-select" name="branch_destination" id="branch_destination" v-model="branch_destination">
                                <option value=""> -- SELECT DESTINATION -- </option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CONTAINER <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-4">
                            <select2 :url="containerSelectUrl" :tags="true" v-model="container1_code"
                                :name="'container1_code'" id="container1_code"
                                class="form-select form-control-select2"
                                placeholder=" -- SELECT OR TYPE CONTAINER CODE -- ">
                            </select2>
                        </div>
                    </div>


                    <hr />

                    <div class="row mb-3">
                        <div class="col-12">
                            <h5>ITEM DETAILS</h5>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered text-nowrap">
                                    <thead>
                                        <tr class="text-center">
                                            <th>DELETE</th>
                                            <th>NO</th>
                                            <th>ITEM</th>
                                            <th>QTY</th>
                                            <th>UNIT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(detail, index) in detailList" :key="detail.key_id">
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-xs"
                                                    v-on:click="removeDetail(detail)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">@{{ index + 1 }}</td>
                                            <td>
                                                <select2 :url="itemSelectUrl" v-model="detail.item_id"
                                                    :name="'details[' + index + '][item_id]'"
                                                    :id="'item_select_' + index" :value="detail.item_id"
                                                    :data-index="index"
                                                    class="form-select form-control-select2 select-item"
                                                    placeholder=" -- SELECT ITEM -- "
                                                    v-on:selected_unit="onItemSelected(index, $event)">
                                                </select2>
                                            </td>
                                            <td style="width: 120px;">
                                                <input type="number" class="form-control text-end" step="any" min="0"
                                                    v-model="detail.qty"
                                                    :name="'details[' + index + '][qty]'">
                                            </td>
                                            <td style="width: 120px;">
                                                <input type="text" class="form-control"
                                                    v-model="detail.unit"
                                                    :name="'details[' + index + '][unit]'">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <button type="button" class="btn btn-success btn-sm"
                                                    v-on:click="addDetail()">
                                                    <i class="fa fa-plus"></i>
                                                    &nbsp;
                                                    Add Item
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
            <div class="toast hide toast fade" id="toast_success" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex justify-content-between alert-success">
                    <div class="toast-body" id="toast_success_body"></div>
                    <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
            <div class="toast hide toast fade" id="toast_danger" role="alert" aria-live="assertive" aria-atomic="true">
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
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/custom-flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>

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
                code: '',
                type: 'delivery_order',
                date: '',
                so_code: '',
                dr_code: '',
                customer_code: '',
                customer_name: '',
                shipto_code: '',
                shipto_name: '',
                shipping_company: '',
                transportation: '',
                branch_destination: '',
                container1_code: '',
                detailList: [],
                itemSelectUrl: '{{ route('delivery_order.item.select2') }}',
                containerSelectUrl: '{{ route('delivery_order.container.select2') }}'
            },
            methods: {
                addDetail(){
                    this.detailList.push({
                        key_id: makeid(15),
                        item_id: '',
                        qty: 0,
                        unit: ''
                    });
                },
                removeDetail(detail){
                    this.detailList = this.detailList.filter(function(x) {
                        return x !== detail;
                    });
                },
                onItemSelected(index, itemId){
                    let app = this;

                    if(! itemId){
                        return;
                    }

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '{{ route('delivery_order.item.get') }}',
                        type: 'POST',
                        data: {
                            item_id: itemId
                        }
                    }).then(function (result){
                        app.detailList[index].unit = result.unit;
                    });
                },
                submitForm() {
                    $('#btn_submit').hide();
                    $('#btn_loading').show(200);
                    $('#section_validation_error').hide();

                    let form = $('#form_create_do')[0];
                    let formData = new FormData(form);

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delivery_order.store') }}',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        enctype: 'multipart/form-data',
                        success: function(data) {
                            let error = data['error'];
                            if (error === 'none') {
                                $('#toast_success_body').html(data['message']);
                                const toastSuccessModal = document.getElementById("toast_success");
                                const toastSuccess = new bootstrap.Toast(toastSuccessModal);
                                toastSuccess.show();

                                window.setTimeout(function() {
                                    window.location.href = data['result']['show_url'];
                                }, 2000);
                            } else if (error === 'validation_failed') {
                                $('#btn_submit').show();
                                $('#btn_loading').hide();
                                $('#section_validation_error').show();
                                $('#validation_error_list').empty();

                                for (let i = 0; i < data['result'].length; i++) {
                                    $('#validation_error_list').append('<li>' + data['result'][i] + '</li>');
                                }
                            } else {
                                $('#btn_submit').show();
                                $('#btn_loading').hide();
                                $('#toast_danger_body').html(data['message']);
                                const toastDangerModal = document.getElementById("toast_danger");
                                const toastDanger = new bootstrap.Toast(toastDangerModal);
                                toastDanger.show();
                            }
                        },
                        error: function() {
                            $('#btn_submit').show();
                            $('#btn_loading').hide();
                            $('#toast_danger_body').html('Internal Server Error!');
                            const toastDangerModal = document.getElementById("toast_danger");
                            const toastDanger = new bootstrap.Toast(toastDangerModal);
                            toastDanger.show();
                        }
                    });
                }
            },
            mounted() {
                $(".date").flatpickr({ dateFormat: 'd-m-Y' });
            }
        });
    </script>
@endsection
