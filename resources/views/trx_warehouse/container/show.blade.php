@extends('layouts.master')

@section('title', 'CMS - Container Detail')

@section('breadcrumb-title')
    <h3>Container Detail</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item"><a href="{{ route('container') }}">Container List</a></li>
    <li class="breadcrumb-item active">Container Detail</li>
@endsection

@section('content')
    <div class="container-fluid">
        @include('partials._success')
        @include('partials._error')

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2 mb-3">
                    <a class="btn btn-sm btn-secondary" href="{{ route('container') }}">Back</a>
                    @if ($canEdit && (auth()->user()->isSuperAdmin() || auth()->user()->can('container.edit')))
                        <a class="btn btn-sm btn-primary" href="{{ route('container.edit', $container->id) }}">Edit</a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <th class="w-25">Container Code</th>
                                <td>{{ $container->code }}</td>
                            </tr>
                            <tr>
                                <th>Owning Branch</th>
                                <td>{{ $branch->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Lifecycle Status</th>
                                <td>{{ $container->status_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>DO Number</th>
                                <td>{{ $container->do_code ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Shipping Instruction</th>
                                <td>{{ $container->si_code ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Warehouse Status</th>
                                <td>{{ $container->warehouse_status ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Warehouse Location</th>
                                <td>{{ $container->warehouse_location_status ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Warehouse Area</th>
                                <td>{{ trim(($container->warehouse_area ?: '-').' '.($container->warehouse_area_row ?: '')) }}</td>
                            </tr>
                            <tr>
                                <th>Vessel</th>
                                <td>{{ $container->vessel_name ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ optional($container->created_at)->format('d-m-Y H:i') ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
