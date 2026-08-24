@extends('layouts.master')

@section('title', 'CMS - Create Shipping Instruction')

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
<h3>Create Shipping Instruction</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Dashboard</li>
<li class="breadcrumb-item">Shipping Instruction</li>
<li class="breadcrumb-item active">Create Shipping Instruction</li>
@endsection

@section('content')
<div class="container-fluid" id="vue-container">
    <div class="card">
        <div class="card-body">
            <form id="form_create_si">
                {{ csrf_field() }}

                <div class="row justify-content-end">
                    <div class="col-auto">
                        <a class="btn btn-danger btn-sm" href="{{ route('shipping_instruction') }}">Cancel</a>
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
                            <ul id="validation_error_list">

                            </ul>
                            {{-- <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button> --}}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">DOCUMENT NO.</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" name="code" id="code" placeholder="Document Number"
                            value="AUTO" readonly>
                    </div>
                </div>

                {{-- <div class="row mb-3">
                    <div class="col-2"></div>
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" id="switch_is_autonumber" type="checkbox" role="switch"
                                value="is_autonumber" name="is_autonumber" v-on:change="setAutonumber($event)" />
                            <label class="form-check-label" for="switch_is_autonumber">Use Autonumber?</label>
                        </div>
                    </div>
                </div> --}}

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">SHIPPER</label>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" name="shipper" id="shipper"
                            value="PT. JUI SHIN INDONESIA" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">VESSEL <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" name="vessel" id="vessel" placeholder="VESSEL NAME"
                            style="text-transform: uppercase;">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">DESTINATION <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-4">
                        <select2 url="{{ route('branch.select2') }}" v-model="destination" :name="'destination'"
                            class="form-select form-control-select2 update-consignee"
                            placeholder=" -- SELECT DESTINATION -- ">
                        </select2>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">TO <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" name="port_receiver" id="port_receiver"
                            placeholder="INPUT PORT RECEIVER NAME" style="text-transform: uppercase;">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">ATTN <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" name="pic_receiver" id="pic_receiver"
                            placeholder="INPUT PIC RECEIVER NAME" style="text-transform: uppercase;">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">ETD <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control date" name="etd_date" id="etd_date" v-model="etd_date"
                            placeholder="INPUT ETD DATE">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">ETA <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control date" name="eta_date" id="eta_date"
                            placeholder="INPUT ETA DATE">
                    </div>
                </div>

                <hr />

                <div class="row mb-3">
                    <div class="col-12">
                        <ul class="simple-wrapper nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item"><a class="nav-link active txt-primary" id="first-tab" data-bs-toggle="tab"
                                    href="#first" role="tab" aria-controls="first" aria-selected="true">DO Details</a></li>
                            <li class="nav-item"><a class="nav-link txt-primary" id="profile-tabs"
                                    data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile"
                                    aria-selected="false">Warehouse Transfer</a></li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="first" role="tabpanel" aria-labelledby="first-tab">
                                <div class="row my-3" v-if="this.destination && this.etd_date">
                                    <div class="col-12">
                                        <div class="table-responsive w-100">
                                            <table class="table table-bordered text-nowrap">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>DELETE</th>
                                                        <th>NO</th>
                                                        <th>DO NO</th>
                                                        <th>DELIVERY TYPE</th>
                                                        <th>CONSIGNEE</th>
                                                        <th>SO NO</th>
                                                        <th>SHIP NAME</th>
                                                        <th>CONTAINER</th>
                                                        <th>QTY</th>
                                                        <th>UNIT</th>
                                                        <th>REMARK</th>
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
                                                            <select2 :url="doSelectUrl" v-model="detail.do_id"
                                                                :name="'details[' + index + '][do_id]'" :id="'do_select_' + index"
                                                                :value="detail.do_id" :data-index="index"
                                                                class="form-select form-control-select2 select-do"
                                                                placeholder=" -- SELECT DO -- ">
                                                            </select2>
                                                        </td>
                                                        <td>
                                                            <select class="form-select" v-model="detail.delivery_type"
                                                                :name="'details[' + index + '][delivery_type]'">
                                                                <option value="default"> -- SELECT DELIVERY TYPE -- </option>
                                                                <option value="FRANCO GUDANG">FRANCO GUDANG</option>
                                                                <option value="FRANCO PELABUHAN TO CUSTOMER">FRANCO PELABUHAN TO
                                                                    CUSTOMER</option>
                                                                <option value="SELF PICK PELABUHAN">SELF PICK PELABUHAN</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select2 :url="detail.consignee_select_url" v-model="detail.trucking"
                                                                :name="'details[' + index + '][trucking]'"
                                                                :id="'consignee_select_'+index" :value="detail.trucking"
                                                                :data-index="index" class="form-select form-control-select2"
                                                                placeholder=" -- SELECT TRUCKING COMPANY -- ">
                                                            </select2>
                                                        </td>
                                                        <td>
                                                            <span>@{{ detail.so_no }}</span>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" v-model="detail.shipto_name"
                                                                :name="'details[' + index + '][shipto_name]'" />
                                                            <span>@{{ detail.shipto_name }}</span>
                                                        </td>
                                                        <td>
                                                            <span>@{{ detail.container1 }}</span>
                                                        </td>
                                                        <td v-if="detail.do_type == 'default'"></td>
                                                        <td v-if="detail.do_type == 'transfer'">
                                                            <vue-autonumeric :options="qtyAutonumericFormat"
                                                                :name="'details[' + index + '][real_qty]'" class="form-control text-end"
                                                                :value="detail.real_qty" style="width: 100px;"></vue-autonumeric>
                                                        </td>
                                                        <td style="width: 100px;" v-if="detail.do_type == 'delivery_order'">
                                                            <vue-autonumeric :options="qtyAutonumericFormat"
                                                                :name="'details[' + index + '][real_qty]'" class="form-control text-end"
                                                                :value="detail.real_qty" style="width: 100px;" readonly>
                                                            </vue-autonumeric>
                                                        </td>
                                                        <td>
                                                            <span>@{{ detail.unit }}</span>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" style="width: 250px;"
                                                                :name="'details[' + index + '][remark]'" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        {{-- <td colspan="8" class="text-center">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                v-on:click="addDetail()">
                                                                <i class="fa fa-plus"></i>
                                                                &nbsp;
                                                                Add New
                                                            </button>
                                                        </td> --}}
                                                        <td colspan="11" class="text-center">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                v-on:click="copyLastRow()">
                                                                <i class="fa fa-plus"></i>
                                                                &nbsp;
                                                                <span v-if="this.detailList.length > 0">Add DO</span>
                                                                <span v-else>Add New</span>

                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-3" v-else>
                                    <div class="col-12">
                                        <span class="text-danger fw-bold">Please select DESTINATION and ETD first!</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel"
                                aria-labelledby="profile-tabs">
                                <div class="row my-3" v-if="this.destination && this.etd_date">
                                    <div class="col-sm-3 col-12">
                                        <label class="form-label mt-2">Select Warehouse Transfer</label>
                                    </div>
                                    <div class="col-sm-4 col-12">
                                        <select2 :url="doSelectUrlWt" v-model="select_do_id_wt"
                                            id="select-do-wt" :value="select_do_id_wt"
                                            class="form-select form-control-select2"
                                            placeholder=" -- SELECT DO TRANSFER -- ">
                                        </select2>
                                    </div>
                                    <div class="col-sm-3 col-12">
                                        <button type="button" class="btn btn-success"
                                            v-on:click="addDetailWt()">Add New</button>
                                    </div>
                                </div>
                                <div class="row mb-3" v-if="this.destination && this.etd_date">
                                    <div class="col-12">
                                        <div class="table-responsive w-100">
                                            <table class="table table-bordered text-nowrap">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>DELETE</th>
                                                        <th>NO</th>
                                                        <th>TRANSFER NO</th>
                                                        <th>DELIVERY TYPE</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(detail, index) in detailListWt" :key="detail.key_id">
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-xs"
                                                                v-on:click="removeDetailWt(detail)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                        <td class="text-center">@{{ index + 1 }}</td>
                                                        <td>
                                                            <span>@{{ detail.do_code }}</span>
                                                            <input type="hidden" v-model="detail.do_id" :name="'detailsWt[' + index + '][do_id]'" />
                                                        </td>
                                                        <td>
                                                            <select class="form-select" v-model="detail.delivery_type"
                                                                :name="'detailsWt[' + index + '][delivery_type]'">
                                                                <option value="default"> -- SELECT DELIVERY TYPE -- </option>
                                                                <option value="FRANCO GUDANG">FRANCO GUDANG</option>
                                                                <option value="FRANCO PELABUHAN TO CUSTOMER">FRANCO PELABUHAN TO
                                                                    CUSTOMER</option>
                                                                <option value="SELF PICK PELABUHAN">SELF PICK PELABUHAN</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>CONSIGNEE</th>
                                                                        <th>SO NO</th>
                                                                        <th>SHIP NAME</th>
                                                                        <th>CONTAINER</th>
                                                                        <th>QTY</th>
                                                                        <th>UNIT</th>
                                                                        <th>REMARK</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <select2 :url="detail.consignee_select_url" v-model="detail.trucking"
                                                                                :name="'detailsWt[' + index + '][trucking]'"
                                                                                :id="'consignee_select_'+index" :value="detail.trucking"
                                                                                :data-index="index" class="form-select form-control-select2"
                                                                                placeholder=" -- SELECT TRUCKING COMPANY -- ">
                                                                            </select2>
                                                                        </td>
                                                                        <td>
                                                                            <span>@{{ detail.so_no }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" v-model="detail.shipto_name"
                                                                                :name="'detailsWt[' + index + '][shipto_name]'" />
                                                                            <span>@{{ detail.shipto_name }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <span>@{{ detail.container1 }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <vue-autonumeric :options="qtyAutonumericFormat"
                                                                                :name="'detailsWt[' + index + '][real_qty]'" class="form-control text-end"
                                                                                :value="detail.real_qty" style="width: 100px;"></vue-autonumeric>
                                                                        </td>
                                                                        <td>
                                                                            <span>@{{ detail.unit }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control" style="width: 250px;"
                                                                                :name="'detailsWt[' + index + '][remark]'" />
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="detailListWt.length < 1">
                                                        <td colspan="5" class="text-center">Tidak Ada Data</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-3" v-else>
                                    <div class="col-12">
                                        <span class="text-danger fw-bold">Please select DESTINATION and ETD first!</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="row mb-3" v-if="this.destination && this.etd_date">
                    <div class="col-12">
                        <h5>DO DETAILS</h5>
                    </div>
                </div> --}}


            </form>
        </div>
    </div>
    <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast hide toast fade" id="toast_success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex justify-content-between alert-success">
                <div class="toast-body" id="toast_success_body"></div>
                <button class="btn-close btn-close-white me-2 m-auto btn-sm" type="button" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast hide toast fade" id="toast_danger" role="alert" aria-live="assertive" aria-atomic="true">
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
                code: '',
                destination : '',
                etd_date: '',
                detailList: [],
                detailListWt: [],
                select_do_id_wt: '',

                autonumericFormat: {
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    decimalPlaces: 10,
                    modifyValueOnWheel: false,
                    allowDecimalPadding: false,
                    unformatOnSubmit: true,
                },
                qtyAutonumericFormat: {
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    decimalPlaces: 2,
                    maximumValue: 99999,
                    minimumValue: 0,
                    modifyValueOnWheel: false,
                    allowDecimalPadding: false,
                    unformatOnSubmit: true,
                },
            },
            computed: {
                doSelectUrl: function(){
                    let url = '{{ route('delivery_order.select2') }}';
                    if(this.destination){
                        url += '?branch_id=' + this.destination;
                    }
                    else{
                        url += '?branch_id=0';
                    }

                    url += '&type=delivery_order';

                    if(this.etd_date){
                        url += '&etd=' + this.etd_date;
                    }

                    url += '&exclude_ids=';
                    for(let i=0; i<this.detailList.length; i++){
                        url += this.detailList[i].do_id + ',';
                    }
                    url = url.substring(0, url.length - 1);

                    console.log(url);
                    return url;
                },
                doSelectUrlWt: function(){
                    let url = '{{ route('delivery_order.select2') }}';
                    if(this.destination){
                        url += '?branch_id=' + this.destination;
                    }
                    else{
                        url += '?branch_id=0';
                    }

                    url += '&type=transfer';

                    if(this.etd_date){
                        url += '&etd=' + this.etd_date;
                    }

                    url += '&exclude_ids=';
                    for(let i=0; i<this.detailListWt.length; i++){
                        url += this.detailListWt[i].do_id + ',';
                    }
                    url = url.substring(0, url.length - 1);

                    console.log(url);
                    return url;
                }
            },
            methods: {
                addDetail(){
                    let consigneeSelectUrl = '{{ route('consignee.select2') }}';
                    // let doSelectUrl = '{{ route('delivery_order.select2') }}';
                    if(this.destination){
                        consigneeSelectUrl += '?branch_id=' + this.destination;
                        // doSelectUrl += '?branch_id=' + this.destination;
                    }
                    else{
                        consigneeSelectUrl += '?branch_id=0';
                        // doSelectUrl += '?branch_id=0';
                    }

                    this.detailList.push({
                        key_id: makeid(15),
                        do_id: '',
                        delivery_type: 'default',
                        trucking: '',
                        so_no: '',
                        shipto_name: '',
                        container1: '',
                        consignee_select_url: consigneeSelectUrl,
                        // do_select_url: doSelectUrl,
                        do_type: 'default',
                        real_qty: 0,
                        unit: ''
                    })
                },
                addDetailWt(){
                    let app = this;
                    if(!app.select_do_id_wt){
                        return false;
                    }

                    $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            url: '{{ route('delivery_order.container.get') }}',
                            type: 'POST',
                            data: {
                                do_id: app.select_do_id_wt
                            }
                        }).then(function (result){
                            let realQty = result.qty;

                            let consigneeSelectUrl = '{{ route('consignee.select2') }}';
                            if(app.destination){
                                consigneeSelectUrl += '?branch_id=' + app.destination;
                            }
                            else{
                                consigneeSelectUrl += '?branch_id=0';
                            }

                            app.detailListWt.push({
                                key_id: makeid(15),
                                do_id: result.do_id,
                                do_code: result.do_code,
                                delivery_type: 'default',
                                trucking: '',
                                so_no: result.so_no,
                                shipto_name: result.shipto_name,
                                container1: result.container1,
                                consignee_select_url: consigneeSelectUrl,
                                do_type: 'transfer',
                                real_qty: realQty,
                                unit: result.unit
                            })

                            app.select_do_id_wt = '';
                            $('#select-do-wt').val(null).trigger("change");
                        });
                },
                removeDetail(detail){
                    this.detailList = this.detailList.filter(function(x) {
                        return x !== detail;
                    });
                },
                removeDetailWt(detail){
                    this.detailListWt = this.detailListWt.filter(function(x) {
                        return x !== detail;
                    });
                },
                copyLastRow(){
                    if(this.detailList.length > 0){
                        let lastRow = this.detailList[this.detailList.length - 1];

                        this.detailList.push({
                            key_id: makeid(15),
                            do_id: '',
                            delivery_type: lastRow.delivery_type,
                            trucking: lastRow.trucking,
                            so_no: '',
                            shipto_name: '',
                            container1: '',
                            consignee_select_url: lastRow.consignee_select_url,
                            // do_select_url: lastRow.do_select_url,
                            do_type: 'default',
                            real_qty: 0,
                            unit: ''
                        })
                    }
                    else{
                        this.addDetail();
                    }
                },
                getContainerData(index){
                    let app = this;

                    if(app.detailList[index].do_id){
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            url: '{{ route('delivery_order.container.get') }}',
                            type: 'POST',
                            data: {
                                do_id: app.detailList[index].do_id
                            }
                        }).then(function (result){
                            app.detailList[index].container1 = result.container1;
                            app.detailList[index].shipto_name = result.shipto_name;
                            app.detailList[index].so_no = result.so_no;
                            app.detailList[index].do_type = result.do_type;
                            app.detailList[index].real_qty = result.qty;
                            app.detailList[index].unit = result.unit;
                        });
                    }
                },
                updateSelectUrl(branchId){
                    let app = this;

                    for(let tmpIdx = 0; tmpIdx < app.detailList.length; tmpIdx++){
                        // app.detailList[tmpIdx].do_select_url = '{{ route('delivery_order.select2') }}?branch_id=' + branchId;
                        app.detailList[tmpIdx].consignee_select_url = '{{ route('consignee.select2') }}?branch_id=' + branchId;
                        app.detailList[tmpIdx].trucking = '';

                        $('#consignee_select_' + tmpIdx).val('').trigger('change');
                        $('#do_select_' + tmpIdx).val('').trigger('change');
                    }
                },
                submitForm() {
                    $('#btn_submit').hide();
                    $('#btn_loading').show(200);
                    $('#section_validation_error').hide();

                    let form = $('#form_create_si')[0];
                    let formData = new FormData(form);

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('shipping_instruction.store') }}',
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
                                $('#toast_danger_body').html('Validation Failed!');
                                const toastDangerModal = document.getElementById("toast_danger");
                                const toastDanger = new bootstrap.Toast(toastDangerModal);
                                toastDanger.show();

                                $('#validation_error_list').empty();

                                if (data['result']) {
                                    for (let errorIdx = 0; errorIdx < data['result']
                                        .length; errorIdx++) {
                                        $('#validation_error_list').append('<li>' + data['result'][
                                            errorIdx
                                        ] + '</li>');
                                    }

                                    $('#section_validation_error').show(100);

                                    // Scroll to validation feedback list
                                    $([document.documentElement, document.body]).animate({
                                        scrollTop: $("#validation_error_list").offset().top -
                                            100
                                    }, 100);
                                }

                                $('#btn_submit').show(200);
                                $('#btn_loading').hide(200);
                            } else if (error === 'special_validation_failed') {
                                $('#toast_danger_body').html(data['result']);
                                const toastDangerModal2 = document.getElementById("toast_danger");
                                const toastDanger2 = new bootstrap.Toast(toastDangerModal2);
                                toastDanger2.show();

                                $('#btn_submit').show(200);
                                $('#btn_loading').hide(200);
                            } else if (error === 'bad_request') {
                                $('#toast_danger_body').html('Bad Request!');
                                const toastDangerModal2 = document.getElementById("toast_danger");
                                const toastDanger2 = new bootstrap.Toast(toastDangerModal2);
                                toastDanger2.show();

                                $('#btn_submit').show(200);
                                $('#btn_loading').hide(200);
                            } else if (error === 'query_exception') {
                                $('#toast_danger_body').html('Internal Server Error!');
                                const toastDangerModal3 = document.getElementById("toast_danger");
                                const toastDanger3 = new bootstrap.Toast(toastDangerModal3);
                                toastDanger3.show();

                                $('#btn_submit').show(200);
                                $('#btn_loading').hide(200);
                            }
                        },
                        error: function(data) {
                            $('#toast_danger_body').html('Internal Server Error: ' + data['error']);
                                const toastDangerModal4 = document.getElementById("toast_danger");
                                const toastDanger4 = new bootstrap.Toast(toastDangerModal4);
                                toastDanger4.show();

                            $('#btn_submit').show(200);
                            $('#btn_loading').hide(200);
                        }
                    });
                }
            },
            watch: {

            },
            mounted() {
                let app = this;
                $('#form_create_si').on('change', '.select-do', function (){
                    app.getContainerData($(this).data('index'));
                });

                $('#form_create_si').on('change', '.update-consignee', function (){
                    if($(this).val() != null){
                        app.updateSelectUrl(app.destination);
                    }
                });
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
