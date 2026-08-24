@extends('layouts.master')

@section('title', 'CMS - Show Shipping Instruction')

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
    <h3>Show Shipping Instruction</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item active">Show Shipping Instruction</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row justify-content-end mb-2">
                        <div class="col-sm-4 col-12">
                            <a class="btn btn-dark btn-sm" href="{{ route('shipping_instruction') }}">Back</a>
                        </div>
                        <div class="col-sm-8 col-12 text-end">
                            @if ($siHeader->status_id === 7 && (auth()->user()->can('si.edit-si') || auth()->user()->isSuperAdmin()))
                                <button type="button" class="btn btn-primary btn-sm" onclick="updateStatus(8);">Set to Sailing</button>
                                &nbsp;
                            @endif
                            @if ($siHeader->status_id === 8 && (auth()->user()->can('si.edit-cabang') || auth()->user()->isSuperAdmin()))
                                <button type="button" class="btn btn-primary btn-sm" onclick="updateStatus(9);">Set to Berthing</button>
                                &nbsp;
                            @endif
                            @if (($siHeader->status_id === 9 ||
                                    $siHeader->status_id === 13 ||
                                    $siHeader->status_id === 18) &&
                                    (auth()->user()->can('si.edit-cabang') ||
                                    auth()->user()->isSuperAdmin()))
                                <button type="button" class="btn btn-primary btn-sm" onclick="editBerthingDate();">Edit Berthing Date</button>
                                &nbsp;
                            @endif
                            @if (($siHeader->status_id === 8) &&
                                    (auth()->user()->can('si.edit-si') ||
                                    auth()->user()->isSuperAdmin()))
                                <button type="button" class="btn btn-primary btn-sm" onclick="editSailingDate();">Edit Sailing Date</button>
                                &nbsp;
                            @endif
                            @if (($siHeader->status_id === 7 || $siHeader->status_id === 8 ) &&
                                    (auth()->user()->can('si.edit-si') || auth()->user()->isSuperAdmin()))
                                <a class="btn btn-secondary btn-sm" href="{{ route('shipping_instruction.edit', ['id' => $siHeader->id]) }}">Edit</a>
                                &nbsp;
                            @endif
                            @if ($siHeader->status_id === 9 || $siHeader->status_id === 13 || $siHeader->status_id === 18)
                                <a class="btn btn-primary btn-sm" href="{{ route('shipping_instruction.operation', ['id' => $siHeader->id, 'status' => 'all']) }}" target="_blank">Monitoring</a>
                                &nbsp;
                            @endif
                            @if (($siHeader->status_id === 18) &&
                                    (auth()->user()->can('si.document-download') ||
                                    auth()->user()->isSuperAdmin()))
                                <a class="btn btn-primary btn-sm" href="{{ route('shipping_instruction.upload.view', ['id' => $siHeader->id]) }}" target="_blank">Documents</a>
                                &nbsp;
                            @endif
                            @if (($siHeader->status_id === 7 || $siHeader->status_id === 8) &&
                                    (auth()->user()->can('si.delete') || auth()->user()->isSuperAdmin()))
                                <button type="button" class="btn btn-danger btn-sm" onclick="voidDocument(9);">Void</button>
                                &nbsp;
                            @endif
                        </div>
                    </div>
                    <div class="row justify-content-end mb-4">
                        <div class="col-12 text-end">
                            @if((auth()->user()->can('si.print') || auth()->user()->isSuperAdmin()))
                                <a class="btn btn-success btn-sm" href="{{ route('shipping_instruction.final.export', ['id' => $siHeader->id]) }}" target="_blank">Export SI Final</a>
                                &nbsp;
                                <a class="btn btn-success btn-sm" href="{{ route('shipping_instruction.export', ['id' => $siHeader->id]) }}" target="_blank">Export Cement Report</a>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">STATUS</label>
                        </div>
                        <div class="col-3">
                            @if($siHeader->status_id == 22)
                                <input type="text" class="form-control fw-bold text-danger"  value="{{ $siHeader->status->display }}" readonly>
                            @else
                                <input type="text" class="form-control fw-bold"  value="{{ $siHeader->status->display }}" readonly>
                            @endif

                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DOCUMENT NO.</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="code" id="code"
                                placeholder="Document Number" value="{{ $siHeader->code }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SHIPPER</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="shipper" id="shipper"
                                value="{{ $siHeader->shipper }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">VESSEL</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="vessel" id="vessel"
                                value="{{ $siHeader->vessel_name }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DESTINATION</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="vessel" id="vessel"
                                value="{{ $siHeader->destination }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">TO</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control"
                                name="port_receiver" id="port_receiver"
                                value="{{ $siHeader->port_receiver }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">ATTN</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control"
                                name="pic_receiver" id="pic_receiver"
                                value="{{ $siHeader->pic_receiver }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">ETD</label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="etd_date" id="etd_date"
                                value="{{ $etdDate }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">ETA</label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="eta_date" id="eta_date"
                                value="{{ $etaDate }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">SAILING DATE</label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="sailing_date" id="sailing_date"
                                value="{{ $sailingDate }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">BERTHING DATE</label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="docking_date" id="docking_date"
                                value="{{ $dockingDate }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DOORING DATE</label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="dooring_date" id="dooring_date"
                                value="{{ $dooringDate }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">RECEIVED AT WH DATE</label>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" name="received_date" id="received_date"
                                value="{{ $receivedDate }}" readonly>
                        </div>
                    </div>

                    @if($siHeader->status_id == 21)
                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label">VOID DATE</label>
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control" name="void_date" id="void_date"
                                    value="{{ $voidDate }}" readonly>
                            </div>
                        </div>
                    @endif

                    <hr/>

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
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <div class="table-responsive w-100">
                                                <table class="table table-bordered text-nowrap">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>DO NO</th>
                                                            <th>DELIVERY TYPE</th>
                                                            <th>CONSIGNEE</th>
                                                            <th>SO NO</th>
                                                            <th>SHIP NAME</th>
                                                            <th>CONTAINER1</th>
                                                            <th>CONTAINER2</th>
                                                            <th>ITEM</th>
                                                            <th>UNIT</th>
                                                            <th>QTY</th>
                                                            <th>REMARK</th>
                                                            @if(($siHeader->status_id == 7 || $siHeader->status_id == 8) &&
                                                                (auth()->user()->can('si.delete') || auth()->user()->isSuperAdmin()))
                                                                <th>VOID?</th>
                                                            @endif
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
                                                                <span>@{{ detail.so_code }}</span>
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
                                                            <td>
                                                                <span>@{{ detail.item }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span>@{{ detail.unit }}</span>
                                                            </td>
                                                            <td class="text-end">
                                                                <span>@{{ detail.qty }}</span>
                                                            </td>
                                                            <td>
                                                                <span>@{{ detail.remark }}</span>
                                                            </td>

                                                            @if(($siHeader->status_id == 7 || $siHeader->status_id == 8) &&
                                                                (auth()->user()->can('si.delete') ||
                                                                    auth()->user()->can('si.edit-si') ||
                                                                    auth()->user()->isSuperAdmin()))
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-danger btn-xs" v-on:click="voidEach(detail.do_id)">
                                                                        <i class="fa fa-ban"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel"
                                    aria-labelledby="profile-tabs">
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <div class="table-responsive w-100">
                                                <table class="table table-bordered text-nowrap">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>NO</th>
                                                            <th>TRANSFER NO</th>
                                                            <th>DELIVERY TYPE</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(detail, index) in detailListWt" :key="detail.key_id">
                                                            <td class="text-center">@{{ index + 1 }}</td>
                                                            <td>
                                                                <span>@{{ detail.do_code }}</span>
                                                            </td>
                                                            <td>
                                                                <span>@{{ detail.delivery_type }}</span>
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
                                                                                <span>@{{ detail.trucking_1 }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.so_no }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.shipto_name }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.container1 }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.real_qty_1 }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.unit }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.remark_1 }}</span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <span>@{{ detail.trucking_2 }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.so_no }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.shipto_name }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.container2 }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.real_qty_2 }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.unit }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span>@{{ detail.remark_2 }}</span>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr v-if="detailListWt.length < 1">
                                                            <td colspan="4" class="text-center">Tidak Ada Data</td>
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

    <div class="modal fade" id="modal_status_update" tabindex="-1" role="dialog"
        aria-labelledby="modal_status_update" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <form method="POST" id="form_status_update" action="{{ route('shipping_instruction.satus.update', ['id' => $siHeader->id]) }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="status_update_id" name="status_update_id" />
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="text-center pb-2" id="status_update_title"></h5>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <input type="text" class="form-control js-flatpickr"
                                    name="update_date"
                                    value="{{ $todayDate }}" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-success btn-sm" id="btn_status_update_submit" type="submit">SUBMIT</button>
                                    <button type="button" id="btn_status_update_loading" class="btn btn-success btn-sm" style="display: none;">
                                        <i class="spinner-border spinner-border-sm text-green" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </i>
                                    </button>
                                    &nbsp;
                                    <button class="btn btn-warning btn-sm" type="button"
                                        data-bs-dismiss="modal">CANCEL</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_edit_sailing_date" tabindex="-1" role="dialog"
        aria-labelledby="modal_edit_sailing_date" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <form method="POST" id="form_edit_sailing_date" action="{{ route('shipping_instruction.sailing_date.update', ['id' => $siHeader->id]) }}">
                            {{ csrf_field() }}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="text-center pb-2">Edit Sailing Date</h5>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <input type="text" class="form-control"
                                    id="update_sailing_date"
                                    name="update_sailing_date"
                                    value="{{ $sailingDate }}" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-success btn-sm" id="btn_edit_sailing_date_submit" type="submit">SUBMIT</button>
                                    <button type="button" id="btn_edit_sailing_date_loading" class="btn btn-success btn-sm" style="display: none;">
                                        <i class="spinner-border spinner-border-sm text-green" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </i>
                                    </button>
                                    &nbsp;
                                    <button class="btn btn-warning btn-sm" type="button"
                                    data-bs-dismiss="modal">CANCEL</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_edit_berthing_date" tabindex="-1" role="dialog"
        aria-labelledby="modal_edit_berthing_date" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <form method="POST" id="form_edit_berthing_date" action="{{ route('shipping_instruction.berthing_date.update', ['id' => $siHeader->id]) }}">
                            {{ csrf_field() }}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="text-center pb-2">Edit Berthing Date</h5>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <input type="text" class="form-control"
                                    id="update_berthing_date"
                                    name="update_date"
                                    value="{{ $dockingDate }}" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-success btn-sm" id="btn_edit_berthing_date_submit" type="submit">SUBMIT</button>
                                    <button type="button" id="btn_edit_berthing_date_loading" class="btn btn-success btn-sm" style="display: none;">
                                        <i class="spinner-border spinner-border-sm text-green" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </i>
                                    </button>
                                    &nbsp;
                                    <button class="btn btn-warning btn-sm" type="button"
                                    data-bs-dismiss="modal">CANCEL</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_void" tabindex="-1" role="dialog"
        aria-labelledby="modal_void" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <form method="POST" id="form_void" action="{{ route('shipping_instruction.void', ['id' => $siHeader->id]) }}">
                            {{ csrf_field() }}
                            <div class="row mb-1">
                                <div class="col-12">
                                    <h5 class="text-center pb-2">
                                        Apakah Anda Yakin ingin void dokumen SI ini?
                                    </h5>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <span class="pb-2 text-danger">
                                        Dokumen DO yang bersangkutan akan di-void juga!
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-danger btn-sm" id="btn_void_submit" type="submit">YES</button>
                                    <button type="button" id="btn_void_loading" class="btn btn-danger btn-sm" style="display: none;">
                                        <i class="spinner-border spinner-border-sm text-green" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </i>
                                    </button>
                                    &nbsp;
                                    <button class="btn btn-warning btn-sm" type="button"
                                        data-bs-dismiss="modal">NO</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_void_each" tabindex="-1" role="dialog"
        aria-labelledby="modal_void_each" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <form method="POST" id="form_void_each" action="{{ route('shipping_instruction.do.void', ['siId' => $siHeader->id]) }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="voided_do_id" name="voided_do_id" />
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="text-center pb-2">
                                        Apakah Anda Yakin ingin void Delivery Order ini?
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-danger btn-sm" id="btn_void_each_submit" type="submit">YES</button>
                                    <button type="button" id="btn_void_each_loading" class="btn btn-danger btn-sm" style="display: none;">
                                        <i class="spinner-border spinner-border-sm text-green" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </i>
                                    </button>
                                    &nbsp;
                                    <button class="btn btn-warning btn-sm" type="button"
                                        data-bs-dismiss="modal">NO</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                voidEach(id){
                    $('#voided_do_id').val(id);
                    voidEachModal.show();
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
        var statusUpdateModal = new bootstrap.Modal(document.getElementById('modal_status_update'), {
            keyboard: false
        })

        var berthingDateEditModal = new bootstrap.Modal(document.getElementById('modal_edit_berthing_date'), {
            keyboard: false
        })

        var sailingDateEditModal = new bootstrap.Modal(document.getElementById('modal_edit_sailing_date'), {
            keyboard: false
        })

        var voidModal = new bootstrap.Modal(document.getElementById('modal_void'), {
            keyboard: false
        })

        var voidEachModal = new bootstrap.Modal(document.getElementById('modal_void_each'), {
            keyboard: false
        })

        @if($siHeader->status_id == 8)
            $(".js-flatpickr").flatpickr({
                'dateFormat': 'd-m-Y',
                'minDate': '{{ $sailingDate }}',
                'maxDate': '{{ $berthMaxDate }}'
            });
        @elseif($siHeader->status_id == 7)
            $(".js-flatpickr").flatpickr({
                'dateFormat': 'd-m-Y',
                'minDate': '{{ $etdDate }}',
                'maxDate': '{{ $sailingMaxDate }}'
            });
        @else
            $(".js-flatpickr").flatpickr({
                'dateFormat': 'd-m-Y'
            });
        @endif

        $("#update_sailing_date").flatpickr({
            'dateFormat': 'd-m-Y',
            'minDate': '{{ $etdDate }}',
            'maxDate': '{{ $sailingMaxDate }}'
        });

        $("#update_berthing_date").flatpickr({
            'dateFormat': 'd-m-Y',
            'minDate': '{{ $sailingDate }}',
            'maxDate': '{{ $berthMaxDate }}'
        });


        function updateStatus(statusId){
            $('#status_update_id').val(statusId);
            if(statusId == 8){
                $('#status_update_title').html('INPUT SAILING DATE');
            }
            else if(statusId == 9){
                $('#status_update_title').html('INPUT BERTHING DATE');
            }
            statusUpdateModal.show();
        }

        function editBerthingDate(){
            berthingDateEditModal.show();
        }

        function editSailingDate(){
            sailingDateEditModal.show();
        }

        $("#form_status_update").on("submit", function( event ) {
            $('#btn_status_update_submit').hide();
            $('#btn_status_update_loading').show();
        });

        $("#form_edit_sailing_date").on("submit", function( event ) {
            $('#btn_edit_sailing_date_submit').hide();
            $('#btn_edit_sailing_date_loading').show();
        });

        $("#form_edit_berthing_date").on("submit", function( event ) {
            $('#btn_edit_berthing_date_submit').hide();
            $('#btn_edit_berthing_date_loading').show();
        });

        $("#form_void").on("submit", function( event ) {
            $('#btn_void_submit').hide();
            $('#btn_void_loading').show();
        });

        $("#form_void_each").on("submit", function( event ) {
            $('#btn_void_each_submit').hide();
            $('#btn_void_each_loading').show();
        });

        function voidDocument(){
            voidModal.show();
        }
    </script>
@endsection
