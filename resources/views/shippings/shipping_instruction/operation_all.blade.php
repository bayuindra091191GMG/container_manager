@extends('layouts.master')

@section('title', 'CMS - SI Monitoring (ALL)')

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
    <h3>SI Monitoring (ALL)</h3>
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
                                <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'all']) }}" class="nav-link active txt-secondary"> <i class="icofont icofont-list"></i>ALL</a></li>
                                <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'berthing']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-hour-glass"></i>BERTHING</a></li>
                                <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'dooring']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-delivery-time"></i>DOORING</a></li>
                                <li class="nav-item"><a href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'received']) }}" class="nav-link txt-secondary"> <i class="icofont icofont-check"></i>RECEIVED</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active">
                                    <div class="row my-3">
                                        <div class="col-sm-8 col-12">
                                            <h5>CONTAINER DETAILS</h5>
                                        </div>
                                        <div class="col-sm-4 col-12 text-end">
                                            <a class="btn btn-success btn-sm" href="{{ route('shipping_instruction.operation.export', ['si_id' => $siHeader->id]) }}">
                                                <i class="icofont icofont-file-excel"></i> Export
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="table-responsive w-100">
                                                <table class="table table-bordered table-sm text-nowrap">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>NO</th>
                                                            <th>CONTAINER</th>
                                                            <th>STATUS</th>
                                                            <th>DO NO</th>
                                                            <th>BERTHING DATE</th>
                                                            <th>DOORING DATE</th>
                                                            <th>RECEIVED AT WH DATE</th>
                                                            <th>DELIVERY TYPE</th>
                                                            <th>CONSIGNEE</th>
                                                            <th>SHIP NAME</th>
                                                            <th>VESSEL NAME</th>
                                                            <th>ITEM</th>
                                                            <th>UNIT</th>
                                                            <th>QTY</th>
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
                                                            <td>
                                                                <span>@{{ detail.status }}</span>
                                                            </td>
                                                            <td>
                                                                <span>@{{ detail.do_code }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.berthing_date }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.dooring_date }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.received_date }}</span>
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
                                                                <span>@{{ detail.vessel_name }}</span>
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
            methods: {},
            computed: {

            },
            watch: {

            },
            mounted() {

            }
        });
    </script>
@endsection
