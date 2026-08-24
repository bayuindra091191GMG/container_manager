@extends('layouts.master')

@section('title', 'CMS - Return to Depo (RECEIVED AT WH)')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('style')
    <style>
        .date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>RETURN CONTAINER TO DEPO</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Container List</li>
    <li class="breadcrumb-item active">Return to Depo (RECEIVED AT WH)</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end mb-4">
                    <div class="col-sm-4 col-12">
                        <a class="btn btn-dark btn-sm" href="{{ route('container') }}">Back</a>
                    </div>
                    <div class="col-sm-8 col-12 text-end">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">DESTINATION</label>
                    </div>
                    <div class="col-4">
                        <select class="form-select" id="destination">
                            <option value="0" @if($destinationId == 0) selected @endif>ALL WH</option>
                            @foreach ($destinations as $destination)
                                <option value="{{ $destination->id }}"
                                    @if($destinationId == $destination->id) selected @endif>{{ $destination->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-success btn-sm" onclick="filter()">
                            GET DATA
                        </button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label" for="operation-search">SEARCH</label>
                    </div>
                    <div class="col-4">
                        <input type="search" class="form-control" id="operation-search"
                            value="{{ $search }}" placeholder="Container / DO / SI code"
                            onkeydown="if(event.key === 'Enter') filter()">
                    </div>
                </div>

                <hr/>

                <div class="row">
                    <div class="row mb-3">
                        <ul class="nav nav-tabs" id="icon-tab" role="tablist">
                            <li class="nav-item"><a href="{{ route('container.operation', ['destination_id' => $destinationId, 'status' => 'all', 'search' => $search]) }}" class="nav-link txt-secondary"> <i class="icofont icofont-list"></i>ALL</a></li>
                            <li class="nav-item"><a href="{{ route('container.operation', ['destination_id' => $destinationId, 'status' => 'received', 'search' => $search]) }}" class="nav-link active txt-secondary"> <i class="icofont icofont-package"></i>RECEIVED AT WH</a></li>
                            <li class="nav-item"><a href="{{ route('container.operation', ['destination_id' => $destinationId, 'status' => 'returned', 'search' => $search]) }}" class="nav-link txt-secondary"> <i class="icofont icofont-ship"></i>RETURN TO DEPO</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <form method="POST" id="form_operation" action="{{ route('container.operation.update', ['status' => 'returned']) }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="destination_id" value="{{ $destinationId }}" />
                                    <div class="row my-3">
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-success btn-sm" onclick="popConfirmModal()">RETURN TO DEPO</button>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h5>CONTAINER DETAILS</h5>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <table class="table table-bordered table-sm text-nowrap">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th></th>
                                                        <th>CONTAINER</th>
                                                        <th>DO NO</th>
                                                        <th>DESTINATION</th>
                                                        <th>STATUS</th>
                                                        <th>FROM SI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(container, index) in containerList" :key="container.id">
                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-checkbox" name="checkboxes[]" :value="container.id" />
                                                        <td>
                                                            <span>@{{ container.code }}</span>
                                                        </td>
                                                        <td>
                                                            <span>@{{ container.do_code }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span>@{{ container.destination_name }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span>@{{ container.status }}</span>
                                                        </td>
                                                        <td>
                                                            <span>@{{ container.si_code }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="containerList.length == 0">
                                                        <td colspan="6" class="text-center">No Data...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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

        <div class="modal fade" id="modal_confirm" tabindex="-1" role="dialog"
            aria-labelledby="modal_confirm" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-toggle-wrapper">
                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <span>Are you sure you want to return selected containers to depo?</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button class="btn btn-success" type="button" v-on:click="submitForm">YES</button>
                                    &nbsp;
                                    <button class="btn btn-warning" type="button"
                                        data-bs-dismiss="modal">NO</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>

    <script>
        let vueComponent = new Vue({
            el: "#vue-container",
            data: {
                ...@json($vueData),
            },
            methods: {
                submitForm(){
                    let form = $('#form_operation');
                    form.submit();
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
        var confirmModal = new bootstrap.Modal(document.getElementById('modal_confirm'), {
            keyboard: false
        })

        function popConfirmModal(statusId){
            confirmModal.show();
        }

        function filter(){
            let destinationId = $('#destination').val();
            let search = $('#operation-search').val();
            let status = '{{ $status }}';

            window.location = '{{ route('container.operation') }}?destination_id=' + destinationId + '&status=' + status + '&search=' + encodeURIComponent(search);
        }
    </script>
@endsection
