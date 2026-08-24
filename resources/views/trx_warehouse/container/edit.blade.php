@extends('layouts.master')

@section('title', 'CMS - Edit Container')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Edit Container</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item"><a href="{{ route('container') }}">Container List</a></li>
    <li class="breadcrumb-item active">Edit Container</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @include('partials._success')
                @include('partials._error')

                <form method="post" action="{{ route('container.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $container->id }}">

                    <div class="row justify-content-end mb-3">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('container.show', $container->id) }}">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-2 col-12">
                            <label class="form-label" for="code">Container Code <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4 col-12">
                            <input type="text" class="form-control text-uppercase" name="code" id="code"
                                value="{{ old('code', $container->code) }}" maxlength="255" required autofocus>
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
                                    <option value="{{ $branch->id }}" @selected(old('branch_id', $container->branch_id) == $branch->id)>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        Only unlinked pending containers can be edited.
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
