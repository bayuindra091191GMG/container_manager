
@extends('layouts.master')

@section('title', 'CMS - Akses Menu')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
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
    <h3>Menu Access</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Menu Access</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        @include('partials._success')
        @include('partials._error')

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

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('access.permission.set') }}">
                    @csrf

                    <div class="row mb-3 align-items-center">
                        <div class="col-3">
                            <label class="form-label">Level</label>
                            <select2 url="{{ route('level.select2') }}" v-model="hierarchy" :value="hierarchy" class="form-select"
                                name="hierarchy_id" placeholder="Pick Level" required :disabled="loading"></select2>
                        </div>
                        <div class="col-auto">
                            <h5><i class="fa fa-fw fa-spin fa-circle-o-notch" v-if="loading"></i></h5>
                        </div>
                    </div>

                    <fieldset :disabled="loading || hierarchy == ''">
                        <div class="d-flex flex-row justify-content-between align-items-center mb-2">
                            <p class="m-0">Menu</p>
                            <div class="d-flex flex-row">
                                <button type="button" class="btn btn-sm btn-secondary" @click="checkAll"><i class="icon-check-box"></i> Check All</button>
                                <button type="button" class="btn btn-sm btn-primary ms-2" @click="uncheckAll"><i class="icon-close"></i> Uncheck All</button>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-12 h-auto mb-2" v-for="(menu, index) in menus" :key="menu.code">
                                <div class="border rounded p-2 h-100">
                                    <div class="mb-3 d-flex flex-row justify-content-between align-items-center">
                                        <h6 class="fw-bold">@{{ menu.name }}</h6>
                                        <button type="button" class="btn btn-sm btn-secondary" @click="checkPerMenu(index)"><i class="icon-check-box"></i></button>
                                    </div>

                                    <div class="row">
                                        <div class="col-6" v-for="action in menu.action">
                                            <div class="form-check">
                                                <input class="form-check-input" name="permission[]" type="checkbox" :value="action.code" :checked="action.checked">
                                                <label class="form-check-label" for="flexCheckDefault">@{{ action.name }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </fieldset>

                </form>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                menus: @json($menus),
                hierarchy: '',
                loading: false,
            },
            methods: {
                checkAll(){
                    for(var i = 0; i < this.menus.length; i++){
                        for(var j = 0; j < this.menus[i].action.length; j++){
                            this.menus[i].action[j].checked = true;
                        }
                    }
                },
                uncheckAll(){
                    for(var i = 0; i < this.menus.length; i++){
                        for(var j = 0; j < this.menus[i].action.length; j++){
                            this.menus[i].action[j].checked = false;
                        }
                    }
                },

                checkPerMenu(index){
                    var check = false;
                    for(var i = 0; i < this.menus[index].action.length; i++){
                        if(!this.menus[index].action[i].checked){
                            check = true;
                            break;
                        }
                    }

                    for(var i = 0; i < this.menus[index].action.length; i++){
                        this.menus[index].action[i].checked = check;
                    }
                }
            },
            computed: {

            },
            watch: {
                hierarchy(){
                    if(this.hierarchy != ''){
                        this.loading = true;

                        let app = this;
                        axios({
                            method: 'get',
                            url: '{!! route('access.permission') !!}',
                            params: {
                                id: app.hierarchy
                            },
                        }).then(function (response){
                            if(response.data.success){
                                app.menus = [];
                                setTimeout(function (){
                                    app.menus = response.data.menus
                                }, 10);
                            }
                            else {
                                app.hierarchy = '';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Internal Error!',
                                });
                            }
                            app.loading = false;
                        });
                    }
                }
            },
            mounted() {

            }
        });


    </script>
@endsection
