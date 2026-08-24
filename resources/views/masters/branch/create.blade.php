
@extends('layouts.master')

@section('title', 'CMS - Add Branch')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('style')
    <style>
        #date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Add Branch</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Branch</li>
    <li class="breadcrumb-item active">Add Branch</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('branch.store') }}">
                    @csrf

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('branch') }}">Cancel</a>
                            &nbsp;
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div>

                    @if(count($errors))
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif


                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control"
                                name="code" id="code" placeholder="Branch unique code"
                                value="{{ old('code') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control"
                                name="name" id="name" placeholder="Branch name"
                                value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Address</label>
                        </div>
                        <div class="col-5">
                            <textarea class="form-control" rows="3"
                                name="address" id="address"
                                placeholder="Optional branch address">{{ old('address') }}</textarea>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
