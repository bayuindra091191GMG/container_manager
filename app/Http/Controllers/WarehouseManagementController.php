<?php

namespace App\Http\Controllers;

use App\Libs\MiscUtility;
use App\Models\Branch;
use App\Models\ContainerBrokenImage;
use App\Models\ContainerData;
use App\Models\ShippingInstructionMonitor;
use App\Models\Status;
use App\Transformer\ContainerTransformer;
use App\Transformer\WarehouseManagementTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\DataTables;
use ZipArchive;

class WarehouseManagementController extends Controller
{
    public function indexContainer(Request $request)
    {
        $filterContainerStatusId = 1;
        $filterContainerStatusData = [];
        $filterContainerStatusData[] = (object) ['id' => 1, 'text' => 'ALL'];
        $filterContainerStatusData[] = (object) ['id' => 7, 'text' => 'PENDING'];
        $filterContainerStatusData[] = (object) ['id' => 9, 'text' => 'BERTHING'];
        $filterContainerStatusData[] = (object) ['id' => 13, 'text' => 'DOORING'];
        $filterContainerStatusData[] = (object) ['id' => 18, 'text' => 'RECEIVED AT WH'];

        $filterStatusId = 1;
        $filterStatusData = [];
        $filterStatusData[] = (object) ['id' => 1, 'text' => 'ALL'];
        $filterStatusData[] = (object) ['id' => 3, 'text' => 'FULL'];
        $filterStatusData[] = (object) ['id' => 4, 'text' => 'EMPTY_GOOD'];
        $filterStatusData[] = (object) ['id' => 5, 'text' => 'BROKEN'];

        $filterLocationId = 1;
        $filterLocationData = [];
        $filterLocationData[] = (object) ['id' => 1, 'text' => 'ALL'];
        $filterLocationData[] = (object) ['id' => 18, 'text' => 'RECEIVED AT WH'];
        $filterLocationData[] = (object) ['id' => 14, 'text' => 'AT FULL STORAGE'];
        $filterLocationData[] = (object) ['id' => 15, 'text' => 'STAGING TO UNLOAD'];
        $filterLocationData[] = (object) ['id' => 16, 'text' => 'AT EMPTY STORAGE'];
        $filterLocationData[] = (object) ['id' => 17, 'text' => 'AT BROKEN STORAGE'];

        $filterArea = 'SEMUA';

        if ($request->container_status_id != null && $request->container_status_id != null) {
            $filterContainerStatusId = rawurldecode($request->container_status_id);
        }

        $filterStartDate = '';
        $filterEndDate = '';

        if ($request->start_date != null && $request->end_date != null) {
            $startDateDecoded = rawurldecode($request->start_date);
            $endDateDecoded = rawurldecode($request->end_date);
            $start = Carbon::createFromFormat('!d-m-Y', $startDateDecoded, 'Asia/Jakarta');
            $end = Carbon::createFromFormat('!d-m-Y', $endDateDecoded, 'Asia/Jakarta');

            if ($end->greaterThanOrEqualTo($start)) {
                $filterStartDate = $startDateDecoded;
                $filterEndDate = $endDateDecoded;
            }
        }

        $filterDestination = 0;
        if ($request->destination != null) {
            $filterDestination = intval($request->destination);
        }

        if ($request->warehouse_status_id != null && $request->warehouse_location_id != null) {
            $filterStatusId = rawurldecode($request->warehouse_status_id);
        }

        if ($request->warehouse_location_id != null) {
            $filterLocationId = rawurldecode($request->warehouse_location_id);
        }

        if ($request->warehouse_area != null) {
            $filterArea = rawurldecode($request->warehouse_area);
        }

        $user = Auth::user();
        // ->where('branch_id', 1);

        // $areaList = ['A', 'B', 'C', 'D', 'E'];

        // $summary = [];
        // foreach($areaList as $area){
        //     $summaryData = [
        //         'area'  => $area,
        //         'full_qty'  => ContainerData::where('status_id', '!=',  2)->where('warehouse_area', $area)
        //                         ->where('warehouse_status_id', 3)->count(),
        //         'empty_qty'  => ContainerData::where('status_id', '!=',  2)->where('warehouse_area', $area)
        //                         ->where('warehouse_status_id', 4)->count(),
        //         'broken_qty'  => ContainerData::where('status_id', '!=',  2)->where('warehouse_area', $area)
        //                         ->where('warehouse_status_id', 5)->count(),
        //     ];

        //     $summary[] = (object)$summaryData;
        // }

        if ($user->isSuperAdmin()) {
            $destinations = DB::table('branches')
                ->orderBy('name')
                ->get();
        } else {
            if ($user->branches->count() > 0) {
                $otherBranchIds = $user->branches->pluck('id')->toArray();

                $destinations = DB::table('branches')
                    ->whereIn('id', $otherBranchIds)
                    ->orWhere('id', '=', $user->branch_id)
                    ->orderBy('name')
                    ->get();
            } else {
                $destinations = DB::table('branches')
                    ->where('id', $user->branch_id)
                    ->orderBy('name')
                    ->get();
            }
        }

        $summary = [];
        foreach ($destinations as $area) {
            $summaryData = [
                'area' => $area->name,
                // 'berthing'  => ContainerData::where('status_id', 9)->where('branch_id', $area->id)->count(),
                'berthing' => DB::table('shipping_instruction_monitors')
                    ->join('shipping_instruction_headers', 'shipping_instruction_monitors.header_id', '=', 'shipping_instruction_headers.id')
                    ->select(DB::raw('SUM(shipping_instruction_monitors.status_id = 9) as total_berthing'))
                    // ->where('shipping_instruction_headers.status_id', '=', 9)
                    ->where('shipping_instruction_headers.destination_branch_id', $area->id)
                    ->first(),

                // 'dooring'  => ContainerData::where('status_id', 13)->where('branch_id', $area->id)->count(),
                'dooring' => DB::table('shipping_instruction_monitors')
                    ->join('shipping_instruction_headers', 'shipping_instruction_monitors.header_id', '=', 'shipping_instruction_headers.id')
                    ->select(DB::raw('SUM(shipping_instruction_monitors.status_id = 13) as total_dooring'))
                    // ->where('shipping_instruction_headers.status_id', '=', 9)
                    ->where('shipping_instruction_headers.destination_branch_id', $area->id)
                    ->first(),

                // 'received'  => ContainerData::where('status_id', 18)->where('branch_id', $area->id)->count(),
                'received' => DB::table('shipping_instruction_monitors')
                    ->join('shipping_instruction_headers', 'shipping_instruction_monitors.header_id', '=', 'shipping_instruction_headers.id')
                    ->select(DB::raw('SUM(shipping_instruction_monitors.status_id = 18) as total_received'))
                    // ->where('shipping_instruction_headers.status_id', '=', 9)
                    ->where('shipping_instruction_headers.destination_branch_id', $area->id)
                    ->first(),
            ];

            // dd($summaryData);
            $summary[] = (object) $summaryData;
        }

        // Check warning filter
        $filterWarning = 'none';
        if ($request->exists('warning')) {
            $filterWarning = $request->query('warning');
        }

        $data = [
            'filterContainerStatusId' => $filterContainerStatusId,
            'filterContainerStatusData' => $filterContainerStatusData,
            'filterStatusId' => $filterStatusId,
            'filterStatusData' => $filterStatusData,
            'filterLocationId' => $filterLocationId,
            'filterLocationData' => $filterLocationData,
            'filterArea' => $filterArea,
            'destinations' => $destinations,
            'summary' => $summary,
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'filterDestination' => $filterDestination,
            'filterWarning' => $filterWarning,
            'user' => $user,
        ];

        return view('trx_warehouse.index-container')->with($data);
    }

    public function datatableContainer(Request $request)
    {
        $admin = Auth::user();

        $data = DB::table('container_datas')
            ->leftJoin('statuses', 'container_datas.status_id', '=', 'statuses.id')
            ->select(
                'container_datas.id as id',
                'container_datas.branch_id as branch_id',
                'container_datas.do_id as do_id',
                'container_datas.timbangan_id as timbangan_id',
                'container_datas.code as code',
                'container_datas.do_code as do_code',
                'container_datas.timbangan_code as timbangan_code',
                'container_datas.si_code as si_code',
                'container_datas.warehouse_location_status as warehouse_location_status',
                'container_datas.warehouse_status as warehouse_status',
                'container_datas.warehouse_area as warehouse_area',
                'container_datas.warehouse_area_row as warehouse_area_row',
                'container_datas.status_id as status_id',
                'container_datas.si_destination_id as si_destination_id',
                'container_datas.si_destination_name as si_destination_name',
                'container_datas.si_etd_date as si_etd_date',
                'container_datas.si_eta_date as si_eta_date',
                'container_datas.item_name as item_name',
                'container_datas.vessel_name as vessel_name',
                'statuses.display as status',
                'container_datas.created_at as created_at',
                'container_datas.berthing_at as berthing_at',
                'container_datas.dooring_at as dooring_at',
                'container_datas.received_at as received_at',
                'container_datas.semen_out_at as semen_out_at',
                'container_datas.ceramic_received_at as ceramic_received_at',
                'container_datas.returned_at as returned_at',
                'container_datas.free_storage_start_at as free_storage_start_at',
                'container_datas.free_storage_end_at as free_storage_end_at',
                'container_datas.free_demurrage_end_at as free_demurrage_end_at',
                'container_datas.is_storage_monitor_start as is_storage_monitor_start',
                'container_datas.is_storage_monitor_end as is_storage_monitor_end',
                'container_datas.is_demurrage_monitor_start as is_demurrage_monitor_start',
                'container_datas.is_demurrage_monitor_end as is_demurrage_monitor_end',
                'container_datas.is_over_storage as is_over_storage',
                'container_datas.is_over_demurrage as is_over_demurrage',
                DB::raw('TIMESTAMPDIFF(SECOND, container_datas.berthing_at, container_datas.returned_at) AS days_since_berthing_to_returned'),
                DB::raw('TIMESTAMPDIFF(SECOND, container_datas.berthing_at, NOW()) AS days_since_berthing'),
                DB::raw('TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_storage_end_at) AS free_storage_time_limit'),
                DB::raw('TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_demurrage_end_at) AS free_demurrage_time_limit')
            )
            ->where('container_datas.status_id', '!=', 2);

        if ($request->filled('warning')) {
            $filterWarning = $request->input('warning');
            if ($filterWarning == 'h-1s') {
                $data = $data->where('container_datas.is_storage_monitor_start', '=', 1)
                    ->where('container_datas.is_storage_monitor_end', '=', 0)
                    ->whereRaw('CEIL((TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_storage_end_at) / 3600) / 24) = 1');
            } elseif ($filterWarning == 'h-30d') {
                $data = $data->where('container_datas.is_demurrage_monitor_start', '=', 1)
                    ->where('container_datas.is_demurrage_monitor_end', '=', 0)
                    ->whereRaw('CEIL((TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_demurrage_end_at) / 3600) / 24) <= 30')
                    ->whereRaw('CEIL((TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_demurrage_end_at) / 3600) / 24) > 7');
            } elseif ($filterWarning == 'h-7d') {
                $data = $data->where('container_datas.is_demurrage_monitor_start', '=', 1)
                    ->where('container_datas.is_demurrage_monitor_end', '=', 0)
                    ->whereRaw('CEIL((TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_demurrage_end_at) / 3600) / 24) <= 7')
                    ->whereRaw('CEIL((TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_demurrage_end_at) / 3600) / 24) > 3');
            } elseif ($filterWarning == 'h-3d') {
                $data = $data->where('container_datas.is_demurrage_monitor_start', '=', 1)
                    ->where('container_datas.is_demurrage_monitor_end', '=', 0)
                    ->whereRaw('CEIL((TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_demurrage_end_at) / 3600) / 24) <= 3')
                    ->whereRaw('CEIL((TIMESTAMPDIFF(SECOND, NOW(), container_datas.free_demurrage_end_at) / 3600) / 24) > 0');
            } elseif ($filterWarning == 'over-d') {
                $data = $data->where('container_datas.is_over_demurrage', '=', 1);
            } elseif ($filterWarning == 'over-s') {
                $data = $data->where('container_datas.is_over_storage', '=', 1);
            }
        }

        if ($request->filled('container_status_id')) {
            if ($request->input('container_status_id') != 1) {
                // Filter date
                $data = $data->where('container_datas.status_id', $request->input('container_status_id'));
            }
        }

        if ($request->get('start_date') && $request->get('end_date')) {
            // Filter date
            $start = Carbon::createFromFormat('!d-m-Y', $request->get('start_date'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('!d-m-Y', $request->get('end_date'), 'Asia/Jakarta');
            $end->addDays(1);

            $data = $data
                ->whereNotNull('container_datas.si_etd_date')
                ->whereBetween('container_datas.si_etd_date', [$start->toDateTimeString(), $end->toDateTimeString()]);
        }

        $branchIds = [];
        if (! $admin->isSuperAdmin()) {
            $branchIds[] = (int) $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIds[] = (int) $adminBranch->id;
            }
            $branchIds = array_values(array_unique(array_filter($branchIds)));
        }

        $applyBranchScope = function ($query, array $scopeBranchIds): void {
            if ($scopeBranchIds === []) {
                $query->whereRaw('1 = 0');

                return;
            }

            $query->where(function ($scope) use ($scopeBranchIds): void {
                foreach (array_values($scopeBranchIds) as $index => $branchId) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $scope->{$method}(function ($branch) use ($branchId): void {
                        $branch->where('container_datas.si_destination_id', $branchId)
                            ->orWhere(function ($unassigned) use ($branchId): void {
                                $unassigned->whereNull('container_datas.si_destination_id')
                                    ->where('container_datas.branch_id', $branchId);
                            });
                    });
                }
            });
        };

        $requestedDestination = (int) $request->get('destination');
        if ($admin->isSuperAdmin()) {
            if ($requestedDestination > 0) {
                $applyBranchScope($data, [$requestedDestination]);
            }
        } else {
            if ($requestedDestination > 0 && in_array($requestedDestination, $branchIds, true)) {
                $applyBranchScope($data, [$requestedDestination]);
            } else {
                $applyBranchScope($data, $branchIds);
            }
        }

        if ($request->filled('warehouse_status_id')) {
            if ($request->input('warehouse_status_id') != 1) {
                // Filter date
                $data = $data->where('container_datas.warehouse_status_id', $request->input('warehouse_status_id'));
            }
        }

        if ($request->filled('warehouse_location_id')) {
            if ($request->input('warehouse_location_id') != 1) {
                // Filter date
                $data = $data->where('container_datas.warehouse_location_id', $request->input('warehouse_location_id'));
            }
        }

        if ($request->filled('warehouse_area')) {
            if ($request->input('warehouse_area') != 'SEMUA') {
                // Filter date
                $data = $data->where('container_datas.warehouse_area', 'like', $request->input('warehouse_area').'%');
            }
        }

        return DataTables::of($data)
            ->setTransformer(new ContainerTransformer)
            ->make(true);
    }

    public function index(Request $request)
    {
        $filterContainerStatusId = 1;
        $filterContainerStatusData = [];
        $filterContainerStatusData[] = (object) ['id' => 1, 'text' => 'ALL'];
        $filterContainerStatusData[] = (object) ['id' => 7, 'text' => 'PENDING'];
        $filterContainerStatusData[] = (object) ['id' => 9, 'text' => 'BERTHING'];
        $filterContainerStatusData[] = (object) ['id' => 13, 'text' => 'DOORING'];
        $filterContainerStatusData[] = (object) ['id' => 18, 'text' => 'RECEIVED AT WH'];

        $filterStatusId = 1;
        $filterStatusData = [];
        $filterStatusData[] = (object) ['id' => 1, 'text' => 'ALL'];
        $filterStatusData[] = (object) ['id' => 3, 'text' => 'FULL'];
        $filterStatusData[] = (object) ['id' => 4, 'text' => 'EMPTY_GOOD'];
        $filterStatusData[] = (object) ['id' => 5, 'text' => 'BROKEN'];

        $filterLocationId = 1;
        $filterLocationData = [];
        $filterLocationData[] = (object) ['id' => 1, 'text' => 'ALL'];
        $filterLocationData[] = (object) ['id' => 18, 'text' => 'RECEIVED AT WH'];
        $filterLocationData[] = (object) ['id' => 14, 'text' => 'AT FULL STORAGE'];
        $filterLocationData[] = (object) ['id' => 15, 'text' => 'STAGING TO UNLOAD'];
        $filterLocationData[] = (object) ['id' => 16, 'text' => 'AT EMPTY STORAGE'];
        $filterLocationData[] = (object) ['id' => 17, 'text' => 'AT BROKEN STORAGE'];

        $filterArea = 'SEMUA';

        if ($request->container_status_id != null && $request->container_status_id != null) {
            $filterContainerStatusId = rawurldecode($request->container_status_id);
        }

        if ($request->warehouse_status_id != null && $request->warehouse_location_id != null) {
            $filterStatusId = rawurldecode($request->warehouse_status_id);
        }

        if ($request->warehouse_location_id != null) {
            $filterLocationId = rawurldecode($request->warehouse_location_id);
        }

        if ($request->warehouse_area != null) {
            $filterArea = rawurldecode($request->warehouse_area);
        }

        $filterWarning = 'none';
        if ($request->warning != null) {
            $filterWarning = $request->warning;
        }

        $user = Auth::user();
        // ->where('branch_id', 1);
        $areaList = ['A', 'B', 'C', 'D', 'E'];

        $summary = [];
        foreach ($areaList as $area) {
            $summaryData = [
                'area' => $area,
                'full_qty' => ContainerData::whereIn('status_id', [18])
                    ->where('warehouse_area', $area)
                    ->where('warehouse_status_id', 3)
                    ->count(),
                'empty_qty' => ContainerData::whereIn('status_id', [18])
                    ->where('warehouse_area', $area)
                    ->where('warehouse_status_id', 4)
                    ->count(),
                'broken_qty' => ContainerData::whereIn('status_id', [18])
                    ->where('warehouse_area', $area)
                    ->where('warehouse_status_id', 5)
                    ->count(),
            ];

            $summary[] = (object) $summaryData;
        }
        $data = [
            'filterContainerStatusId' => $filterContainerStatusId,
            'filterContainerStatusData' => $filterContainerStatusData,
            'filterStatusId' => $filterStatusId,
            'filterStatusData' => $filterStatusData,
            'filterLocationId' => $filterLocationId,
            'filterLocationData' => $filterLocationData,
            'filterArea' => $filterArea,
            'summary' => $summary,
            'filterWarning' => $filterWarning,
        ];

        return view('trx_warehouse.index')->with($data);
    }

    public function datatable(Request $request)
    {
        $data = ContainerData::select(
            'container_datas.id as id',
            'container_datas.code as code',
            'container_datas.do_code as do_code',
            'container_datas.timbangan_code as timbangan_code',
            'container_datas.warehouse_location_status as warehouse_location_status',
            'container_datas.warehouse_status as warehouse_status',
            'container_datas.warehouse_area as warehouse_area',
            'container_datas.warehouse_area_row as warehouse_area_row',
            'container_datas.status_id as status_id',
            'statuses.display as status',
        )
            ->leftJoin('statuses', 'container_datas.status_id', '=', 'statuses.id')
            ->where('status_id', '=', 18)
            ->whereNotNull('container_datas.si_destination_id');

        if ($request->filled('container_status_id')) {
            if ($request->input('container_status_id') != 1) {
                // Filter date
                $data = $data->where('status_id', $request->input('container_status_id'));
            }
        }

        if ($request->filled('warehouse_status_id')) {
            if ($request->input('warehouse_status_id') != 1) {
                // Filter date
                $data = $data->where('warehouse_status_id', $request->input('warehouse_status_id'));
            }
        }

        if ($request->filled('warehouse_location_id')) {
            if ($request->input('warehouse_location_id') != 1) {
                // Filter date
                $data = $data->where('warehouse_location_id', $request->input('warehouse_location_id'));
            }
        }

        if ($request->filled('warehouse_area')) {
            if ($request->input('warehouse_area') != 'SEMUA') {
                // Filter date
                $data = $data->where('warehouse_area', 'like', $request->input('warehouse_area').'%');
            }
        }

        return DataTables::of($data)
            ->setTransformer(new WarehouseManagementTransformer)
            ->make(true);
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->is_super == 1) {
            $branches = Branch::all();
        } else {
            $branches = Branch::find($user->branch_id);
        }
        $dataDB = ContainerData::where('id', $id)->first();
        if ($dataDB == null) {
            Session::flash('error', 'Warehouse tidak ditemukan!');

            return back();
        }

        $filterStatusId = 1;
        $filterStatusData = [];
        $filterStatusData[] = (object) ['id' => 3, 'text' => 'FULL'];
        $filterStatusData[] = (object) ['id' => 4, 'text' => 'EMPTY_GOOD'];
        $filterStatusData[] = (object) ['id' => 5, 'text' => 'BROKEN'];

        $filterLocationId = 1;
        $filterLocationData = [];
        $filterLocationData[] = (object) ['id' => 18, 'text' => 'RECEIVED AT WH'];
        $filterLocationData[] = (object) ['id' => 14, 'text' => 'AT FULL STORAGE'];
        $filterLocationData[] = (object) ['id' => 15, 'text' => 'STAGING TO UNLOAD'];
        $filterLocationData[] = (object) ['id' => 16, 'text' => 'AT EMPTY STORAGE'];
        $filterLocationData[] = (object) ['id' => 17, 'text' => 'AT BROKEN STORAGE'];

        $brokenImageContainerNo = '';
        $brokenImages = [];
        $brokenImageDbs = ContainerBrokenImage::where('container_id', $id)->get();

        if (count($brokenImageDbs) > 0) {
            foreach ($brokenImageDbs as $brokenImageDb) {
                if (str_contains($brokenImageDb->filename, '_containerno_')) {
                    $brokenImageContainerNo = $brokenImageDb->filename;
                } else {
                    $data = [
                        'filename' => $brokenImageDb->filename,
                    ];
                    $brokenImages[] = (object) $data;
                }
            }
        }

        $detailListArr = [];

        $vueData = [
            'detailList' => $detailListArr,
        ];

        $data = [
            'data' => $dataDB,
            'branches' => $branches,
            'statuses' => $filterStatusData,
            'locations' => $filterLocationData,
            'vueData' => $vueData,
            'brokenImageContainerNo' => $brokenImageContainerNo,
            'brokenImages' => $brokenImages,
            'container_statuses' => Status::whereIn('id', [9, 13, 18])->get(),
        ];
        // dd($data);

        return view('trx_warehouse.edit')->with($data);
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required',
                'location' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->all());
            }
            // dd($request);

            $user = Auth::user();
            $data = ContainerData::where('id', $request->input('id'))->first();
            if ($data == null) {
                Session::flash('error', 'Container Not Found!');

                return back()->withInput($request->all());
            }
            if ($request->input('status') == 5) {
                $brokenImageDbs = ContainerBrokenImage::where('container_id', $request->input('id'))->get();
                if (count($brokenImageDbs) == 0) {
                    if (! $request->file('image_container_no')) {
                        Session::flash('error', 'For Broken Status Need upload Container No Photo!');

                        return back()->withInput($request->all());
                    }

                    if (! $request->file('image')) {
                        Session::flash('error', 'For Broken Status, Need upload Min 1 Broken Image!');

                        return back()->withInput($request->all());
                    }
                }
            }

            $data->warehouse_location_id = $request->input('location');
            $data->warehouse_location_status = Status::find($request->input('location'))->display;
            $data->warehouse_status_id = $request->input('status');
            $data->warehouse_status = Status::find($request->input('status'))->display;
            $data->warehouse_status_reason = $request->input('broken_reason') ?? '';
            $data->warehouse_area = $request->input('area') ?? 'B';
            $data->warehouse_area_row = $request->input('area_row') ?? '1';
            $data->updated_by = $user->id;
            $data->save();

            if ($request->filled('container_status')) {
                $data->status_id = $request->input('container_status');
                $data->save();

                $siContainer = ShippingInstructionMonitor::where('container_id', $request->input('id'))->first();
                $siContainer->status_id = $request->input('container_status');
                $siContainer->save();
            }

            $filenameContainerNo = '';
            $ct = 0;
            if ($request->file('image_container_no')) {
                $brokenImageDbs = ContainerBrokenImage::where('container_id', $request->input('id'))->where('filename', 'like', '%_containerno_%')->first();
                if (! empty($brokenImageDbs)) {
                    Storage::disk('broken_images')->delete($brokenImageDbs->filename);
                    $brokenImageDbs->delete();
                }

                $file = $request->file('image_container_no');
                $filenameContainerNo = $data->code.'_broken-photo'.'_'.$ct.'_containerno_'.date('YmdHi').'.'.$file->extension();

                $uploadedPath = Storage::disk('broken_images')->path('');

                $tmpImg = Image::make($file->getRealPath());
                $tmpImg->orientate();
                if ($tmpImg->width() > $tmpImg->height()) {
                    if ($tmpImg->width() > 800) {
                        // Resize uploaded photo width
                        $tmpImg->resize(800, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                } else {
                    if ($tmpImg->height() > 800) {
                        // Resize uploaded photo height
                        $tmpImg->resize(null, 800, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                }
                $tmpImg->save($uploadedPath.'/'.$filenameContainerNo, 70);

                // $file->storeAs('', $filenameContainerNo, 'broken_images');
                ContainerBrokenImage::create([
                    'container_id' => $data->id,
                    'filename' => $filenameContainerNo,
                ]);
            }

            $ct = 1;
            $filename = '';
            if ($request->file('image')) {
                $brokenImageDbs = ContainerBrokenImage::where('container_id', $request->input('id'))->whereNot('filename', 'like', '%_containerno_%')->get();
                if (count($brokenImageDbs) > 0) {
                    foreach ($brokenImageDbs as $brokenImageDb) {
                        Storage::disk('broken_images')->delete($brokenImageDb->filename);
                        $brokenImageDb->delete();
                    }
                }

                foreach ($request->file('image') as $file) {
                    $filename = $data->code.'_broken-photo_'.$ct.'_'.date('YmdHi').'.'.$file->extension();

                    $uploadedPath = Storage::disk('broken_images')->path('');

                    $tmpImg = Image::make($file->getRealPath());
                    $tmpImg->orientate();
                    if ($tmpImg->width() > $tmpImg->height()) {
                        if ($tmpImg->width() > 800) {
                            // Resize uploaded photo width
                            $tmpImg->resize(800, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                    } else {
                        if ($tmpImg->height() > 800) {
                            // Resize uploaded photo height
                            $tmpImg->resize(null, 800, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                    }
                    $tmpImg->save($uploadedPath.'/'.$filename, 70);

                    // $file->storeAs('', $filename, 'broken_images');
                    ContainerBrokenImage::create([
                        'container_id' => $data->id,
                        'filename' => $filename,
                    ]);
                    $ct++;
                }
            }

            Session::flash('success', 'Sukses mengubah data gudang!');

            return redirect()->route('container');
        } catch (\Exception $ex) {
            dd($ex);
            Session::flash('error', 'Maaf kesalahan terjadi!'.$ex);

            return back()->withInput($request->all());
        }
    }

    public function show($id)
    {
        $dataDB = ContainerData::find($id);

        $detailListArr = [];
        // foreach($siHeader->shipping_instruction_details as $detail){
        //     $detailListArr[] = [
        //         'id' => $detail->id,
        //         'do_code' => $detail->do_code,
        //         'delivery_type' => $detail->delivery_type,
        //         'trucking' => $detail->trucking,
        //         'shipto_name' => $detail->shipto_name,
        //         'container1' => $detail->do_header->container1_code ?? '',
        //     ];
        // }
        $brokenImageContainerNo = '';
        $brokenImages = [];
        $brokenImageDbs = ContainerBrokenImage::where('container_id', $id)->get();

        if (count($brokenImageDbs) > 0) {
            foreach ($brokenImageDbs as $brokenImageDb) {
                if (str_contains($brokenImageDb->filename, '_containerno_')) {
                    $brokenImageContainerNo = $brokenImageDb->filename;
                } else {
                    $data = [
                        'filename' => $brokenImageDb->filename,
                    ];
                    $brokenImages[] = (object) $data;
                }
            }
        }

        $vueData = [
            'detailList' => $detailListArr,
        ];

        $data = [
            'data' => $dataDB,
            'vueData' => $vueData,
            'brokenImageContainerNo' => $brokenImageContainerNo,
            'brokenImages' => $brokenImages,
        ];

        return view('trx_warehouse.show')->with($data);
    }

    public function delete(Request $request)
    {
        try {
            $data = ContainerData::where('id', $request->input('id'))->first();
            if ($data == null) {
                Session::flash('error', 'Gudang tidak ditemukan!');

                return back();
            }
            $data->status_id = 2;
            $data->save();

            return response([
                'success' => true,
            ]);
        } catch (\Exception $ex) {
            return response([
                'success' => false,
            ]);
        }
    }

    public function downloadAll(Request $request)
    {
        try {
            $dataDB = ContainerBrokenImage::where('container_id', $request->get('id'))->get();
            $containerDB = ContainerData::find($request->get('id'));

            $zip = new ZipArchive;
            $zipname = 'Broken Container_'.$containerDB->code.'_'.date('YmdHi').'.zip';

            if (file_exists($zipname)) {
                unlink($zipname);
            }

            $zip->open($zipname, ZipArchive::CREATE);
            foreach ($dataDB as $detail) {
                $path = public_path();
                $path = str_replace('\\', '/', $path);
                $zip->addFile($path.'/storage/broken_images/'.$detail->filename, $detail->filename);
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename='.$zipname);
            header('Content-Length: '.filesize($zipname));
            readfile($zipname);
            unlink($zipname);
        } catch (\Exception $ex) {
            dd($ex);
        }
    }

    public function management()
    {
        $containers = DB::table('container_datas')
            ->select(
                'container_datas.id as id',
                'container_datas.code as code',
                'container_datas.do_code as do_code',
                'container_datas.timbangan_code as timbangan_code',
                'container_datas.warehouse_location_id as warehouse_location_id',
                'container_datas.warehouse_location_status as warehouse_location_status',
                'container_datas.warehouse_status_id as warehouse_status_id',
                'container_datas.warehouse_status as warehouse_status',
                'container_datas.warehouse_area as warehouse_area',
                'container_datas.warehouse_area_row as warehouse_area_row',
                'container_datas.status_id as status_id',
                'statuses.display as status',
            )
            ->leftJoin('statuses', 'container_datas.status_id', '=', 'statuses.id')
            ->where('container_datas.status_id', '=', 18)
            ->whereNotNull('container_datas.si_destination_id')
            ->get();

        $containerArr = [];
        foreach ($containers as $container) {
            $containerArr[] = [
                'key_id' => MiscUtility::generateRandomShortString(10),
                'id' => $container->id,
                'code' => $container->code,
                'warehouse_location_id_old' => $container->warehouse_location_id,
                'warehouse_location_id' => $container->warehouse_location_id,
                'warehouse_status_id_old' => $container->warehouse_status_id,
                'warehouse_status_id' => $container->warehouse_status_id,
                'warehouse_area_old' => $container->warehouse_area,
                'warehouse_area' => $container->warehouse_area,
                'warehouse_area_row_old' => $container->warehouse_area_row,
                'warehouse_area_row' => $container->warehouse_area_row,
            ];
        }

        $warehouseStatusOpts = [];
        $warehouseStatusOpts[] = (object) ['id' => 3, 'text' => 'FULL'];
        $warehouseStatusOpts[] = (object) ['id' => 4, 'text' => 'EMPTY_GOOD'];

        $warehouseLocationOpts = [];
        $warehouseLocationOpts[] = (object) ['id' => 18, 'text' => 'RECEIVED AT WH'];
        $warehouseLocationOpts[] = (object) ['id' => 14, 'text' => 'AT FULL STORAGE'];
        $warehouseLocationOpts[] = (object) ['id' => 15, 'text' => 'STAGING TO UNLOAD'];
        $warehouseLocationOpts[] = (object) ['id' => 16, 'text' => 'AT EMPTY STORAGE'];

        $vueData = [
            'containerList' => $containerArr,
            'containerListDefault' => $containerArr,
            'warehouseStatusOpts' => $warehouseStatusOpts,
            'warehouseLocationOpts' => $warehouseLocationOpts,
        ];

        $data = [
            'vueData' => $vueData,
        ];

        return view('trx_warehouse.management')->with($data);
    }

    public function updateWarehouseData(Request $request)
    {
        try {
            $id = $request->input('id');
            $field = $request->input('field');
            $value = $request->input('value');

            if ($field == 'warehouse_location_id') {
                $feedback = 'LOCATION UPDATED!';
            } elseif ($field == 'warehouse_status_id') {
                $feedback = 'WH STATUS UPDATED!';
            } elseif ($field == 'warehouse_area') {
                $feedback = 'AREA UPDATED!';
            } elseif ($field == 'warehouse_area_row') {
                $feedback = 'AREA ROW UPDATED!';
            } else {
                // Guard suspicious parameter
                return Response::json([
                    'code' => 200,
                    'message' => '',
                    'error' => 'none',
                    'result' => [
                        'field' => $field,
                        'value' => $value,
                    ],
                ], 200);
            }

            DB::table('container_datas')
                ->where('id', '=', $id)
                ->update([
                    $field => $value,
                ]);

            if ($field == 'warehouse_location_id') {
                $whLocationStatus = Status::find($value)->display;

                DB::table('container_datas')
                    ->where('id', '=', $id)
                    ->update([
                        'warehouse_location_status' => $whLocationStatus,
                    ]);
            } elseif ($field == 'warehouse_status_id') {
                $whStatus = Status::find($value)->display;

                DB::table('container_datas')
                    ->where('id', '=', $id)
                    ->update([
                        'warehouse_status' => $whStatus,
                    ]);
            }

            return Response::json([
                'code' => 200,
                'message' => $feedback,
                'error' => 'none',
                'result' => [
                    'field' => $field,
                    'value' => $value,
                ],
            ], 200);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController WarehouseManagementController error EX: '.$ex);

            return Response::json([
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
}
