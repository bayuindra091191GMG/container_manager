
@extends('layouts.master')

@section('title', 'CMS - Division')

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
    <h3>Division</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Shipping Instruction</li>
    <li class="breadcrumb-item">Show Shipping Instruction</li>
    <li class="breadcrumb-item">View Documents</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto">
                        @if (auth()->user()->hierarchy->hasPermissionTo('si.document-upload') ||
                                auth()->user()->is_superadmin)
                            <a class="btn btn-sm btn-success" href="{{ route('shipping_instruction.upload.form', ['id' => $siHeader->id]) }}"><i class="fa fa-fw fa-plus"></i></a>
                        @endif

                    </div>
                </div>

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
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped" id="datatable">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 20%">Option</th>
                                    <th class="text-center">Document</th>
                                    <th class="text-center">Download</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if($uploads->count() > 0)
                                        @foreach ($uploads as $upload)
                                        <tr>
                                            <td class="text-center">
                                                @if (auth()->user()->hierarchy->hasPermissionTo('si.document-delete') ||
                                                    auth()->user()->is_superadmin)
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="popModalDelete({{ $upload->id }})">
                                                        <i class="fa fa-fw fa-trash"></i>&nbsp;Delete
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span>{{ $upload->filename }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->user()->hierarchy->hasPermissionTo('si.document-download') ||
                                                    auth()->user()->is_superadmin)
                                                    <a class="btn btn-sm btn-success" href="{{ route('shipping_instruction.document.download', ['id' => $upload->id]) }}">
                                                        <i class="fa fa-fw fa-download"></i>&nbsp;Download
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <span class="fw-bold">There are no uploads yet</span>
                                            </td>
                                        </tr>
                                    @endif


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_delete" tabindex="-1" role="dialog"
        aria-labelledby="modal_delete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <form method="POST" id="form_delete" action="{{ route('shipping_instruction.document.delete') }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="deleted_upload_id" name="deleted_upload_id" />
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="text-center pb-2">
                                        Apakah Anda Yakin ingin hapus dokumen ini?
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-danger btn-sm" id="btn_delete_submit" type="submit">YES</button>
                                    <button type="button" id="btn_delete_loading" class="btn btn-danger btn-sm" style="display: none;">
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

    <script>
        var deleteModal = new bootstrap.Modal(document.getElementById('modal_delete'), {
            keyboard: false
        })

        function popModalDelete($id){
            $('#deleted_upload_id').val($id);
            deleteModal.show();
        }
    </script>
@endsection
