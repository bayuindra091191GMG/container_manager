@extends('layouts.master')

@section('title', 'CMS - SI Monitoring (RECEIVED)')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('style')
    <style>
        .date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>SI Monitoring (RECEIVED)</h3>
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
                <form>
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
                                <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'berthing']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-hour-glass"></i>BERTHING</a></li>
                                <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'dooring']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-delivery-time"></i>DOORING</a></li>
                                <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'received']) }}" class="nav-link active txt-secondary"> <i class="icofont icofont-check"></i>RECEIVED</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active">
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <h5>CONTAINER DETAILS</h5>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <div class="table-responsive w-100">
                                                <table class="table table-bordered table-sm text-nowrap">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>NO</th>
                                                            <th>CONTAINER</th>
                                                            <th>STATUS</th>
                                                            <th>RECEIVED AT WH DATE</th>
                                                            <th>DO NO</th>
                                                            <th>DELIVERY TYPE</th>
                                                            <th>CONSIGNEE</th>
                                                            <th>SHIP NAME</th>
                                                            <th>ITEM</th>
                                                            <th>UNIT</th>
                                                            <th>QTY</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(detail, index) in detailList" :key="detail.id">
                                                            <td class="text-center">
                                                                @{{ index + 1 }}
                                                            </td>
                                                            <td>
                                                                <span>@{{ detail.container_code }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.status }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.received_date }}</span>
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
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-xs btn-warning" v-on:click="popRevertModal(detail.id)">Revert</button>
                                                            </td>
                                                        </tr>
                                                        <tr v-if="detailList.length == 0">
                                                            <td colspan="11" class="text-center">No Data...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

        <div class="modal fade" id="modal_revert" tabindex="-1" role="dialog"
            aria-labelledby="modal_revert" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-toggle-wrapper">
                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <span>Are you sure you want to revert this container status back to DOORING?</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-success" type="button" v-on:click="submitRevertForm">YES</button>
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

        <form id="form_revert" method="POST" action="{{ route('shipping_instruction.operation.revert') }}">
            {{ csrf_field() }}
            <input type="hidden" id="revert_to" name="revert_to" value="DOORING"/>
            <input type="hidden" id="reverted_monitor_id" name="reverted_monitor_id" />
        </form>
    </div>

@endsection

@section('script')
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>

    <script>
        let vueComponent = new Vue({
            el: "#vue-container",
            data: {
                ...@json($vueData),
            },
            methods: {
                popRevertModal(id){
                    $('#reverted_monitor_id').val(id);
                    var revertModal = new bootstrap.Modal(document.getElementById('modal_revert'), {
                        keyboard: false
                    })
                    revertModal.show();
                },
                submitRevertForm(){
                    let form = $('#form_revert');
                    form.submit();
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
