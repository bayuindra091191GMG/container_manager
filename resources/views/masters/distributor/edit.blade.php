
@extends('layouts.master')

@section('title', 'CMS - Edit Distributor')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('style')
    <style>
        #date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Edit Distributor</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Distributor</li>
    <li class="breadcrumb-item active">Edit Distributor</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('distributor.update', $distributor->id) }}">
                    @csrf

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('distributor') }}">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
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
                        <div class="col-3">
                            <label class="form-label">BRANCH <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-4">
                            <select class="form-select form-control-select2" id="branch" name="branch">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if($distributor->branch_id == $branch->id) selected @endif>{{ strtoupper($branch->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DISTRIBUTOR CODE <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" style="text-transform: uppercase;"
                                name="code" id="code"
                                placeholder="INPUT CODE HERE"
                                value="{{ $distributor->code }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-3">
                            <label class="form-label">DISTRIBUTOR NAME <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" style="text-transform: uppercase;"
                                name="name" id="name"
                                placeholder="INPUT NAME HERE"
                                value="{{ $distributor->name }}" required>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>

    <script>
        $('#branch').select2();
    </script>
@endsection
