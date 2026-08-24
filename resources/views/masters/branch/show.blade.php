
@extends('layouts.master')

@section('title', 'CMS - Detail Branch')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('style')
@endsection

@section('breadcrumb-title')
    <h3>Detail Branch</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Branch</li>
    <li class="breadcrumb-item active">Detail Branch</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        @include('partials._success')
                        @include('partials._error')
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Branch Code</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control"
                            name="code" id="code" value="{{ $branch->code }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Branch Name</label>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control"
                            name="name" id="name"
                            value="{{ $branch->name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Address</label>
                    </div>
                    <div class="col-5">
                        <textarea class="form-control" rows="3"
                            name="address" id="address" readonly>{{ $branch->address ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
