
@extends('layouts.master')

@section('title', 'CMS - Upload Documents')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
@endsection

@section('style')
    <style>
        #date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Upload Documents</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item">Show Shipping Instruction</li>
    <li class="breadcrumb-item">View Documents</li>
    <li class="breadcrumb-item active">Upload Documents</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('shipping_instruction.upload.submit') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('shipping_instruction.upload.view', ['id' => $siHeader->id]) }}">Cancel</a>
                            &nbsp;
                            <button type="submit" class="btn btn-sm btn-success">Upload</button>
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
                            <label class="form-label">DOCUMENT NO.</label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control"
                                name="code" id="code"
                                value="{{ $siHeader->code }}" readonly>
                            <input type="hidden" name="si_id" value="{{ $siHeader->id }}" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Upload Documents <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-5">
                            <input type="file" id="file" name="files[]" multiple/>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/fileinput/fileinput.min.js') }}"></script>
    <script src="{{ asset('https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/themes/fa5/theme.min.js') }}"></script>

    <script>
        $('#file').fileinput({
            'showUpload': false,
            'overwriteInitial': false
        });
    </script>
@endsection
