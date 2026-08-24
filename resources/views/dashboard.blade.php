@extends('layouts.master')

@section('title', 'CMS - Dashboard')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('style')
    <style>
        .card .card-header {
            padding: 20px !important;
        }
    </style>
@endsection

@section('breadcrumb-title')
    <h3>Welcome, {{ auth()->user()->name }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div id="app" class="container-fluid">
        <div class="row starter-main mb-3">
            <div class="col-md-4">
                <label class="form-label" for="end_date">Destination</label>
                <select class="form-select" id="h_destination_filter"
                    v-model="h_destination_filter" @change="onChangeHDestination($event)">
                    @if($admin->is_superadmin)
                        <option value="0" selected>-- Semua Cabang --</option>
                        @foreach ($destinations as $destination)
                            <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                        @endforeach
                    @else
                        @foreach ($destinations as $destination)
                            <option value="{{ $destination->id }}" selected>{{ $destination->name }}</option>
                        @endforeach
                    @endif

                </select>
            </div>
        </div>
        <div class="row starter-main">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Total H-3 Demurrage</h5>
                    </div>
                    <div class="card-body py-3">
                        <div class="spinner-border text-primary" role="status" v-if="loadingTotal3Demurrage">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mb-0" v-else>
                            <div>
                                <h5>@{{ total3Demurrage }} Container</h5>
                                <br/>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-light f-light"
                                    href="{{ route('container', ['warning' => 'h-3d']) }}"
                                    target="_blank">
                                    <i class="fa fa-list"></i>&nbsp;
                                    View Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Total H-7 Demurrage</h5>
                    </div>
                    <div class="card-body">
                        <div class="spinner-border text-primary" role="status" v-if="loadingTotal7Demurrage">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mb-0" v-else>
                            <div>
                                <h5>@{{ total7Demurrage }} Container</h5>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-light f-light"
                                    href="{{ route('container', ['warning' => 'h-7d']) }}"
                                    target="_blank">
                                    <i class="fa fa-list"></i>&nbsp;
                                    View Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Total H-30 Demurrage</h5>
                    </div>
                    <div class="card-body">
                        <div class="spinner-border text-primary" role="status" v-if="loadingTotal30Demurrage">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mb-0" v-else>
                            <div>
                                <h5>@{{ total30Demurrage }} Container</h5>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-light f-light"
                                    href="{{ route('container', ['warning' => 'h-30d']) }}"
                                    target="_blank">
                                    <i class="fa fa-list"></i>&nbsp;
                                    View Data
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Total H-1 Storage</h5>
                    </div>
                    <div class="card-body">
                        <div class="spinner-border text-primary" role="status" v-if="loadingTotal1Storage">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mb-0" v-else>
                            <div>
                                <h5>@{{ total1Storage }} Container</h5>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-light f-light"
                                    href="{{ route('container', ['warning' => 'h-1s']) }}"
                                    target="_blank">
                                    <i class="fa fa-list"></i>&nbsp;
                                    View Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Total Over Storage</h5>
                    </div>
                    <div class="card-body">
                        <div class="spinner-border text-primary" role="status" v-if="loadingTotalStoragePinalty">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mb-0" v-else>
                            <div>
                                <h5>@{{ totalStoragePinalty }} Container</h5>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-light f-light"
                                    href="{{ route('container', ['warning' => 'over-s']) }}"
                                    target="_blank">
                                    <i class="fa fa-list"></i>&nbsp;
                                    View Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Total Over Demurrage</h5>
                    </div>
                    <div class="card-body">
                        <div class="spinner-border text-primary" role="status" v-if="loadingTotalDemurragePinalty">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mb-0" v-else>
                            <div>
                                <h5>@{{ totalDemurragePinalty }} Container</h5>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-light f-light"
                                    href="{{ route('container', ['warning' => 'over-d']) }}"
                                    target="_blank">
                                    <i class="fa fa-list"></i>&nbsp;
                                    View Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xl-12">
              <div class="card">
                <div class="card-header card-no-border">
                  <div class="header-top">
                    <h5>Last 10 DO</h5>
                  </div>
                </div>
                <div class="card-body pt-0">
                    <div class="spinner-border text-primary" role="status" v-if="loadingDOData">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mb-0" v-else>
                        <div class="recent-table table-responsive currency-table" style="font-size: 12px;">
                          <table class="table">
                            <thead>
                              <tr>
                                <th class="f-light">Customer</th>
                                <th class="f-light">Ship To</th>
                                <th class="f-light">Tanggal</th>
                                <th class="f-light">Shipping Company</th>
                                <th class="f-light">No Delivery Order (DO)</th>
                                <th class="f-light">Item</th>
                                <th class="f-light">Qty / UOM</th>
                                <th class="f-light">Container No</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in DOData">
                                  <td>@{{ item.customer_name }}</td>
                                  <td>@{{ item.shipto_name }}</td>
                                  <td>@{{ item.date }}</td>
                                  <td>@{{ item.shipping_company }}</td>
                                  <td>@{{ item.code }}</td>
                                  <td>@{{ item.item_name }}</td>
                                  <td>@{{ item.qty }} / @{{ item.unit }}</td>
                                  <td>@{{ item.container1_code }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>
              </div>
            </div> --}}


            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5>SI RECAP</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-12 px-4 pb-3">
                            <label class="form-label" for="end_date">Destination</label>
                            <select class="form-select" id="destination" @change="onChangeDestination($event)"
                                v-model="destination">
                                <option value="0" @if ($filterDestination == 0) selected @endif>-- Semua Cabang --
                                </option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->id }}"
                                        @if ($filterDestination == $destination->id) selected @endif>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header card-no-border">
                                    <div class="header-top">
                                        <h5>SI STATUS RECAP</h5>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="spinner-border text-primary" role="status" v-if="loadingSIStatusRecap">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="mb-0" v-else>
                                        <div class="recent-table table-responsive" style="font-size: 12px;">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>PENDING</td>
                                                        <td class="text-end">
                                                            <a @click="getSIList(7)">
                                                                @{{ SIStatusRecap.total_pending == null ? 0 : SIStatusRecap.total_pending }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>SAILING</td>
                                                        <td class="text-end">
                                                            <a @click="getSIList(8)">
                                                                @{{ SIStatusRecap.total_sailing == null ? 0 : SIStatusRecap.total_sailing }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>BERTHING</td>
                                                        <td class="text-end">
                                                            <a @click="getSIList(9)">
                                                                @{{ SIStatusRecap.total_berthing == null ? 0 : SIStatusRecap.total_berthing }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>DOORING</td>
                                                        <td class="text-end">
                                                            <a @click="getSIList(13)">
                                                                @{{ SIStatusRecap.total_dooring == null ? 0 : SIStatusRecap.total_dooring }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>RECEIVED</td>
                                                        <td class="text-end">
                                                            <a @click="getSIList(18)">
                                                                @{{ SIStatusRecap.total_received == null ? 0 : SIStatusRecap.total_received }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-success btn-sm"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header card-no-border">
                                    <div class="header-top">
                                        <h5>CONTAINER STATUS RECAP</h5>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="spinner-border text-primary" role="status"
                                        v-if="loadingSIContainerStatusRecap">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="mb-0" v-else>
                                        <div class="recent-table table-responsive" style="font-size: 12px;">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>BERTHING</td>

                                                        <td class="text-end">
                                                            <a @click="getContainerList(7)">
                                                                @{{ SIContainerStatusRecap.total_berthing == null ? 0 : SIContainerStatusRecap.total_berthing }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>DOORING</td>
                                                        <td class="text-end">
                                                            <a @click="getContainerList(13)">
                                                                @{{ SIContainerStatusRecap.total_dooring == null ? 0 : SIContainerStatusRecap.total_dooring }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>RECEIVED</td>
                                                        <td class="text-end">
                                                            <a @click="getContainerList(18)">
                                                                @{{ SIContainerStatusRecap.total_received == null ? 0 : SIContainerStatusRecap.total_received }}
                                                                &nbsp;&nbsp;&nbsp;
                                                                <button class="btn btn-sm btn-success"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            {{-- <div class="col-xl-6">
                <div class="card">
                    <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Container By Type</h5>
                    </div>
                    </div>
                    <div class="card-body pt-0">
                    <div class="spinner-border text-primary" role="status" v-if="loadingContainerType">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mb-0" v-else>
                        <div class="recent-table table-responsive" style="font-size: 12px;">
                            <table class="table">
                            <thead>
                                <tr>
                                <th class="f-light">Container</th>
                                <th class="f-light">Product Type</th>
                                <th class="f-light">Full</th>
                                <th class="f-light">Empty</th>
                                <th class="f-light">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {-- <tr>
                                    <td>TEMAS</td>
                                    <td>Semen Sak</td>
                                    <td>94</td>
                                    <td>37</td>
                                    <td>131</td>
                                </tr>
                                <tr>
                                    <td>Meratus</td>
                                    <td>Semen Curah</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr> --}
                            </tbody>
                            <thead>
                                <tr>
                                <th class="f-dark" colspan="2">TOTAL</th>
                                <th class="f-dark">0</th>
                                <th class="f-dark">0</th>
                                <th class="f-dark">0</th>
                                </tr>
                            </thead>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Container By Overdue</h5>
                    </div>
                    </div>
                    <div class="card-body pt-0">
                    <div class="spinner-border text-primary" role="status" v-if="loadingContainerOverdue">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mb-0" v-else>
                        <div class="recent-table table-responsive" style="font-size: 12px;">
                            <table class="table">
                            <thead>
                                <tr>
                                <th class="f-light">Overdues</th>
                                <th class="f-light">Temas</th>
                                <th class="f-light">Meratus LBNI</th>
                                <th class="f-light">Meratus AST</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- <tr>
                                    <td>>45 Hari</td>
                                    <td>214 Container</td>
                                    <td>52 Container</td>
                                    <td>36 Container</td>
                                </tr>
                                <tr>
                                    <td> 15 Hari </td>
                                    <td>0 Container</td>
                                    <td>0 Container</td>
                                    <td>0 Container</td>
                                </tr> --}
                            </tbody>
                            <thead>
                                <tr>
                                <th class="f-dark">TOTAL</th>
                                <th class="f-dark">0 Container</th>
                                <th class="f-dark">0 Container</th>
                                <th class="f-dark">0 Container</th>
                                </tr>
                                <tr>
                                <th class="f-dark">Broken</th>
                                <th class="f-dark">0 Container</th>
                                <th class="f-dark">0 Container</th>
                                <th class="f-dark">0 Container</th>
                                </tr>
                            </thead>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>
            </div> --}}


        </div>
    </div>
    <script type="text/javascript">
        var session_layout = '{{ session()->get('layout') }}';
    </script>
@endsection

@section('script')
    <script src="{{ asset('js\axios.min.js') }}"></script>
    <script src="{{ asset('js\vue.js') }}"></script>
    <script src="https://unpkg.com/vue-toasted"></script>
    <script>
        let app = new Vue({
            el: '#app',
            data: {
                h_destination_filter: '{{ $admin->is_superadmin ? 0 : $admin->branch_id }}',
                loadingTotal3Demurrage: false,
                total3Demurrage: 0,
                loadingTotal7Demurrage: false,
                total7Demurrage: 0,
                loadingTotal30Demurrage: false,
                total30Demurrage: 0,
                loadingTotal1Storage: false,
                total1Storage: 0,
                loadingTotalStoragePinalty: false,
                totalStoragePinalty: 0,
                loadingTotalDemurragePinalty: false,
                totalDemurragePinalty: 0,

                loadingDOData: false,
                DOData: [],
                loadingContainerType: false,
                ContainerType: [],
                loadingContainerOverdue: false,
                ContainerOverdue: [],

                destination: {{ $filterDestination }},
                loadingSIStatusRecap: false,
                SIStatusRecap: [],
                loadingSIContainerStatusRecap: false,
                SIContainerStatusRecap: [],
            },
            methods: {
                async getTotalDemurrage() {
                    this.loadingTotal3Demurrage = true;
                    this.loadingTotal7Demurrage = true;
                    this.loadingTotal30Demurrage = true;
                    this.loadingTotal1Storage = true;
                    this.loadingTotalStoragePinalty = true;
                    this.loadingTotalDemurragePinalty = true;

                    await axios({
                        method: 'get',
                        url: '{{ route('admin.dashboard.get_total_demurrage') }}?destination_id=' + this.h_destination_filter,
                    }).then(function(response) {
                        if (response) {
                            if (response.status === 200) {
                                app.total30Demurrage = response.data.demurrage30;
                                app.total7Demurrage = response.data.demurrage7;
                                app.total3Demurrage = response.data.demurrage3;
                                app.total1Storage = response.data.storage1;
                                app.totalStoragePinalty = response.data.overStorage;
                                app.totalDemurragePinalty = response.data.overDemurrage;
                            } else {
                                console.log(response);
                            }
                        }
                    });
                    this.loadingTotal3Demurrage = false;
                    this.loadingTotal7Demurrage = false;
                    this.loadingTotal30Demurrage = false;
                    this.loadingTotal1Storage = false;
                    this.loadingTotalStoragePinalty = false;
                    this.loadingTotalDemurragePinalty = false;
                },
                onChangeHDestination(event) {
                    app.h_destination_filter = event.target.value;

                    this.getTotalDemurrage();
                },
                async getDOData() {
                    this.loadingDOData = true;
                    await axios({
                        method: 'get',
                        url: '{{ route('admin.dashboard.get_last_do') }}',
                    }).then(function(response) {
                        if (response) {
                            if (response.status === 200) {
                                console.log(response.data);
                                app.DOData = response.data;
                                //this.totalFocCustomer = response.data.totalFocCustomer;
                            } else {
                                console.log(response);
                            }
                        }
                    });
                    this.loadingDOData = false;
                },
                async getContainerType() {
                    this.loadingContainerType = true;
                    await axios({
                        method: 'get',
                        url: '{{ route('admin.dashboard.get_container_type') }}',
                    }).then(function(response) {
                        if (response) {
                            if (response.status === 200) {
                                app.ContainerType = response.data;
                                //this.totalFocCustomer = response.data.totalFocCustomer;
                            } else {
                                console.log(response);
                            }
                        }
                    });
                    this.loadingContainerType = false;
                },
                async getContainerOverdue() {
                    this.loadingContainerOverdue = true;
                    await axios({
                        method: 'get',
                        url: '{{ route('admin.dashboard.get_container_overdue') }}',
                    }).then(function(response) {
                        if (response) {
                            if (response.status === 200) {
                                app.ContainerOverdue = response.data;
                                //this.totalFocCustomer = response.data.totalFocCustomer;
                            } else {
                                console.log(response);
                            }
                        }
                    });
                    this.loadingContainerOverdue = false;
                },
                onChangeDestination(event) {
                    app.destination = event.target.value;
                    console.log(event.target.value);

                    this.getSIStatusRecap();
                },
                async getSIStatusRecap() {
                    this.loadingSIStatusRecap = true;
                    this.loadingSIContainerStatusRecap = true;

                    await axios({
                        method: 'get',
                        url: '{{ route('admin.dashboard.get_si_status') }}' + "?destination=" + this
                            .destination,
                    }).then(function(response) {
                        if (response) {
                            if (response.status === 200) {
                                app.SIStatusRecap = response.data;
                                console.log(response.data);
                            } else {
                                console.log(response);
                            }
                        }
                    });
                    this.loadingSIStatusRecap = false;


                    await axios({
                        method: 'get',
                        url: '{{ route('admin.dashboard.get_si_container_status') }}' +
                            "?destination=" + this.destination,
                    }).then(function(response) {
                        if (response) {
                            if (response.status === 200) {
                                app.SIContainerStatusRecap = response.data;
                                console.log(response.data);
                                //this.totalFocCustomer = response.data.totalFocCustomer;
                            } else {
                                console.log(response);
                            }
                        }
                    });
                    this.loadingSIContainerStatusRecap = false;
                },
                getSIList(status) {
                    let startDate = "{{ $startDate }}";
                    let endDate = "{{ $datenow }}";
                    let destination = app.destination;
                    let url = '{{ route('shipping_instruction') }}';

                    let targetUrl = url + '?start_date=' + startDate + '&end_date=' + endDate + '&destination=' +
                        destination + '&status_id=' + status;
                    // console.log(targetUrl);
                    window.location = targetUrl;
                },
                getContainerList(status) {
                    let url = '{{ route('container') }}';

                    let targetUrl = url + '?container_status_id=' + status;
                    // console.log(targetUrl);
                    window.location = targetUrl;
                },
            },
            async mounted() {
                this.getTotalDemurrage();
                // this.getDOData();
                this.getContainerType();
                this.getContainerOverdue();

                this.getSIStatusRecap();
            }

        });
    </script>

@endsection
