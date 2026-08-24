@extends('layouts.master')

@section('title', 'CMS - SI Monitoring (ALL)')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.css">
@endsection

@section('style')
    <style>
        .date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>SI Monitoring (BERTHING)</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item">Show Shipping Instruction</li>
    <li class="breadcrumb-item active">SI Monitoring</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end mb-4">
                    <div class="col-sm-4 col-12">
                        <a class="btn btn-dark btn-sm" href="{{ route('shipping_instruction.show', ['id' => $siHeader->id]) }}">Back</a>
                    </div>
                    <div class="col-sm-8 col-12 text-end">
                    </div>
                </div>

                <div class="row">
                    <div class="row mb-3">
                        <ul class="nav nav-tabs" id="icon-tab" role="tablist">
                            <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'all']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-list"></i>ALL</a></li>
                            <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'berthing']) }}" class="nav-link active txt-secondary"> <i class="icofont icofont-hour-glass"></i>BERTHING</a></li>
                            <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'dooring']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-delivery-time"></i>DOORING</a></li>
                            <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'received']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-check"></i>RECEIVED</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <form method="POST" id="form_operation" action="{{ route('shipping_instruction.operation.update', ['id' => $siHeader->id, 'status' => 'dooring']) }}">
                                    {{ csrf_field() }}
                                    <div class="row my-3">
                                        <div class="col-12 text-end">
                                            @if(auth()->user()->can('si.edit-cabang')
                                                || auth()->user()->isSuperAdmin())
                                                <button type="button"
                                                    class="btn btn-success btn-sm"
                                                    onclick="popConfirmModal()"
                                                    :disabled="validate()">SET TO DOORING</button>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h5>CONTAINER DETAILS</h5>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="table-responsive w-100">
                                                <table class="table table-bordered table-sm text-nowrap">
                                                    <thead>
                                                        <tr class="text-center">
                                                            @if(auth()->user()->can('si.edit-cabang')
                                                                || auth()->user()->isSuperAdmin())
                                                                <th>
                                                                    <input type="checkbox" class="form-checkbox"
                                                                        v-on:change="checkAll($event)" />
                                                                </th>
                                                            @else
                                                                <th>NO</th>
                                                            @endif

                                                            <th>TANGGAL DOORING</th>

                                                            <th>CONTAINER</th>
                                                            <th>STATUS</th>
                                                            <th>DO NO</th>
                                                            <th>DELIVERY TYPE</th>
                                                            <th>CONSIGNEE</th>
                                                            <th>SHIP NAME</th>
                                                            <th>ITEM</th>
                                                            <th>UNIT</th>
                                                            <th>QTY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(detail, index) in detailList" :key="detail.id">
                                                            <td class="text-center">
                                                                @if(auth()->user()->can('si.edit-cabang')
                                                                    || auth()->user()->isSuperAdmin())
                                                                    <input type="checkbox" class="form-checkbox"
                                                                        name="checkboxes[]"
                                                                        :checked="detail.checked"
                                                                        :value="detail.id"
                                                                        v-on:change="toogleDateInput($event,detail)"/>
                                                                @else
                                                                    @{{ index + 1 }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <flat-pickr class="form-control text-center"
                                                                    :config="flatpickrConfig"
                                                                    :name="'input_dates[]'"
                                                                    v-model="detail.input_date"
                                                                    :value="detail.input_date"
                                                                    :disabled="!detail.checked">
                                                                </flat-pickr>
                                                            </td>
                                                            <td>
                                                                <span>@{{ detail.container_code }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.status }}</span>
                                                            </td>
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
                                                                <span>@{{ detail.item }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.unit }}</span>
                                                            </td>
                                                            <td class="text-end">
                                                                <span>@{{ detail.qty }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr v-if="detailList.length == 0">
                                                            <td colspan="10" class="text-center">No Data...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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

        <div class="modal fade" id="modal_confirm" tabindex="-1" role="dialog"
            aria-labelledby="modal_confirm" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-toggle-wrapper">
                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <span>Are you sure you want to update selected containers to DOORING?</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-success" type="button" v-on:click="submitForm">YES</button>
                                    &nbsp;
                                    <button class="btn btn-warning" type="button"
                                        data-bs-dismiss="modal">NO</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.js"></script>
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-flatpickr-component@8"></script>

    <script>
        Vue.component('flat-pickr', VueFlatpickr);

        let vueComponent = new Vue({
            el: "#vue-container",
            data: {
                ...@json($vueData),
                flatpickrConfig:{
                    dateFormat: 'd-m-Y',
                    minDate: '{{ $inputDooringMinDate }}',
                    maxDate: '{{ $inputDooringMaxDate }}'
                },
            },
            methods: {
                submitForm(){
                    let form = $('#form_operation');
                    form.submit();
                },
                checkAll(event){
                    if (event.target.checked){
                        for(let i = 0; i < this.detailList.length; i++){
                            this.detailList[i].checked = true;
                        }
                    }
                    else{
                        for(let i = 0; i < this.detailList.length; i++){
                            this.detailList[i].checked = false;
                        }
                    }
                },
                toogleDateInput(event,detail){
                    if (event.target.checked){
                        detail.checked = true;
                    }
                    else{
                        detail.checked = false;
                    }
                },
                validate(){
                    $isCheckedFound = false;
                    for(let i = 0; i < this.detailList.length; i++){
                        if(this.detailList[i].checked){
                            $isCheckedFound = true;
                        }
                    }

                    if(!$isCheckedFound) return true;

                    for(let i = 0; i < this.detailList.length; i++){
                        if(this.detailList[i].checked && !this.detailList[i].input_date){
                            return true;
                        }
                    }

                    return false;
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
    <script>
        var confirmModal = new bootstrap.Modal(document.getElementById('modal_confirm'), {
            keyboard: false
        })

        function popConfirmModal(statusId){
            confirmModal.show();
        }
    </script>
@endsection
