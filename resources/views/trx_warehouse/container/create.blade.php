@extends('layouts.master')

@section('title', 'CMS - Add Container')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Add Container</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item"><a href="{{ route('container') }}">Container List</a></li>
    <li class="breadcrumb-item active">Add Container</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @include('partials._success')
                @include('partials._error')

                <form method="post" action="{{ route('container.store') }}">
                    @csrf

                    <div class="row justify-content-end mb-3">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('container') }}">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-2 col-12">
                            <label class="form-label" for="code">Container Code <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4 col-12">
                            <input type="text" class="form-control text-uppercase" name="code" id="code"
                                value="{{ old('code') }}" maxlength="255" required autofocus>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-2 col-12">
                            <label class="form-label" for="branch_id">Owning Branch <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4 col-12">
                            <select class="form-select" name="branch_id" id="branch_id" required>
                                <option value="">-- SELECT BRANCH --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        New containers start in <strong>PENDING</strong> status and can be linked to a Delivery Order later.
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
