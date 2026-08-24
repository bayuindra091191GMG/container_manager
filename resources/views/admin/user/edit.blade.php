
@extends('layouts.master')

@section('title', 'CMS - Edit User')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('style')
    <style>
        .date {
            background-color: white !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Edit User</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Users</li>
    <li class="breadcrumb-item active">Edit User</li>
@endsection

@section('content')
    <div class="container-fluid" id="vue-container">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('user.update') }}">
                    @csrf

                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-sm btn-danger" href="{{ route('user') }}">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
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

                    <div class="row">
                        <div class="col-sm-8 col-12">
                            @include('partials._success')
                            @include('partials._error')
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{ $user->id }}">

                    <div class="row my-3">
                        <div class="col-2">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username') ?? $user->username }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <select2 url="{{ route('level.select2') }}" v-model="level" :value="level" class="form-select"
                                name="hierarchy_id" placeholder="Pick Level" required></select2>
                        </div>
                    </div>

                    @if($user->is_superadmin == 0)
                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label">Main Branch Access</label>
                            </div>
                            <div class="col-3">
                                <select id="main_branch" name="main_branch"
                                    class="form-select">
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            @if($branch->id == $user->branch_id) selected @endif>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label">Other Branch Access</label>
                            </div>
                            <div class="col-3">
                                <select id="other_branches" name="other_branches[]" multiple="multiple">
                                    @foreach ($otherBranches as $branch)
                                        <option value="{{ $branch['id'] }}" @if($branch['selected']) selected @endif>{{ $branch['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif


                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Change Password <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name') ?? $user->name }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-3">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ $user->email }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="form-label">Status</label>
                        </div>
                        <div class="col-3">
                            <select id="status" name="status"
                                class="form-select">
                                <option value="1" @if($user->status_id == 1) selected @endif>ACTIVE</option>
                                <option value="2" @if($user->status_id == 2) selected @endif>NON-ACTIVE</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>
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
                level: '{{ old('hierarchy_id') ?? $user->hierarchy_id }}',
            },
            methods: {

            },
            computed: {

            },
            watch: {

            },
            mounted() {

            }
        });

        $('#main_branch').select2();
        $('#other_branches').select2();
    </script>
@endsection
