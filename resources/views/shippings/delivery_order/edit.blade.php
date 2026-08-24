@extends('layouts.master')

@section('title', 'CMS - Edit Delivery Order')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2/select2.min.css') }}">
@endsection

@section('style')
    <style>
        .date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Edit Delivery Order</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item"><a href="{{ route('delivery_order') }}">Delivery Order</a></li>
    <li class="breadcrumb-item active">Edit Delivery Order</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <form id="form_edit_do" method="post" action="{{ route('delivery_order.update') }}">
                    @csrf

                    <input type="hidden" name="id" value="{{ $doHeader->id }}">

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-danger btn-sm" href="{{ route('delivery_order.show', ['id' => $doHeader->id]) }}">Cancel</a>
                            <button type="submit" class="btn btn-success btn-sm">Submit</button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DO / TRANSFER NO. <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="code" id="code" placeholder="Document Number"
                                value="{{ old('code', $doHeader->code) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">TYPE <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <select class="form-select" name="type" id="type">
                                <option value="delivery_order" @if(old('type', $doHeader->type) == 'delivery_order') selected @endif>DELIVERY ORDER</option>
                                <option value="transfer" @if(old('type', $doHeader->type) == 'transfer') selected @endif>TRANSFER</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DATE <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control date" name="date" id="date"
                                value="{{ old('date', \Carbon\Carbon::parse($doHeader->date)->format('d-m-Y')) }}" placeholder="dd-mm-yyyy">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SO NO.</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="so_code" id="so_code" value="{{ old('so_code', $doHeader->so_code) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DR NO.</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="dr_code" id="dr_code" value="{{ old('dr_code', $doHeader->dr_code) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CUSTOMER CODE</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="customer_code" id="customer_code" value="{{ old('customer_code', $doHeader->customer_code) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CUSTOMER NAME</label>
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="customer_name" id="customer_name" value="{{ old('customer_name', $doHeader->customer_name) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SHIPTO CODE</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="shipto_code" id="shipto_code" value="{{ old('shipto_code', $doHeader->shipto_code) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SHIPTO NAME</label>
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="shipto_name" id="shipto_name" value="{{ old('shipto_name', $doHeader->shipto_name) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SHIPPING COMPANY</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="shipping_company" id="shipping_company" value="{{ old('shipping_company', $doHeader->shipping_company) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">TRANSPORTATION</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="transportation" id="transportation" value="{{ old('transportation', $doHeader->transportation) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DESTINATION BRANCH <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-4">
                            <select class="form-select" name="branch_destination" id="branch_destination">
                                <option value=""> -- SELECT DESTINATION -- </option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if(old('branch_destination', $doHeader->branch_destination_id) == $branch->id) selected @endif>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CONTAINER 1</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" value="{{ $doHeader->container1_code }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">CONTAINER 2</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" value="{{ $doHeader->container2_code }}" readonly>
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
                                                    :extra="detail.item_select_extra"
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
                detailList: @json($detailList),
                itemSelectUrl: '{{ route('delivery_order.item.select2') }}'
            },
            methods: {
                addDetail(){
                    this.detailList.push({
                        key_id: makeid(15),
                        item_id: '',
                        qty: 0,
                        unit: '',
                        item_select_extra: { }
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
                }
            },
            mounted() {
                $(".date").flatpickr({ dateFormat: 'd-m-Y' });
            }
        });
    </script>
@endsection
