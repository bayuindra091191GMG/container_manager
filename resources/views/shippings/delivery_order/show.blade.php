@extends('layouts.master')

@section('title', 'CMS - Show Delivery Order')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Show Delivery Order</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item"><a href="{{ route('delivery_order') }}">Delivery Order</a></li>
    <li class="breadcrumb-item active">Show Delivery Order</li>
@endsection

@section('content')
    <div class="container-fluid">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end mb-3">
                    <div class="col-auto">
                        <a class="btn btn-sm btn-dark" href="{{ route('delivery_order') }}">Back</a>
                        @if(!$doHeader->is_close && $doHeader->status_id != 21 && (auth()->user()->is_superadmin || auth()->user()->hierarchy->hasPermissionTo('do.edit')))
                            &nbsp;<a class="btn btn-sm btn-secondary" href="{{ route('delivery_order.edit', ['id' => $doHeader->id]) }}">Edit</a>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">DO / TRANSFER NO.</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->code }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">TYPE</label>
                    </div>
                    <div class="col-9">
                        <span>{{ strtoupper($doHeader->type) }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">DATE</label>
                    </div>
                    <div class="col-9">
                        <span>{{ \Carbon\Carbon::parse($doHeader->date)->format('d-m-Y') }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">SO NO.</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->so_code }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">DR NO.</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->dr_code }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">CUSTOMER</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->customer_code }} - {{ $doHeader->customer_name }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">SHIP TO</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->shipto_code }} - {{ $doHeader->shipto_name }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">SHIPPING COMPANY</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->shipping_company }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">TRANSPORTATION</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->transportation }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">DESTINATION BRANCH</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $branchName }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">CONTAINERS</label>
                    </div>
                    <div class="col-9">
                        <span>{{ $doHeader->container1_code ?: '-' }}</span>
                        &nbsp;/&nbsp;
                        <span>{{ $doHeader->container2_code ?: '-' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label fw-bold">STATUS</label>
                    </div>
                    <div class="col-9">
                        @if($doHeader->is_close)
                            <span class="badge badge-danger">CLOSED</span>
                        @else
                            <span class="badge badge-success">OPEN</span>
                        @endif
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
                                        <th>NO</th>
                                        <th>ITEM CODE</th>
                                        <th>ITEM NAME</th>
                                        <th>QTY</th>
                                        <th>UNIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($doHeader->do_details as $detail)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detail->item_code }}</td>
                                            <td>{{ $detail->item_name }}</td>
                                            <td class="text-end">{{ number_format($detail->qty, 2, ',', '.') }}</td>
                                            <td>{{ $detail->unit }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No item lines</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
