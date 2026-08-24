<?php

namespace App\Http\Controllers;

use App\Models\DoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function index()
    {
        // $containers = DB::table('container_datas')
        //         ->select('container_datas.id as id',
        //             'container_datas.si_destination_id as si_destination_id',
        //             'container_datas.is_storage_monitor_start as is_storage_monitor_start',
        //             'container_datas.is_storage_monitor_end as is_storage_monitor_end',
        //             'container_datas.is_demurrage_monitor_start as is_demurrage_monitor_start',
        //             'container_datas.is_demurrage_monitor_end as is_demurrage_monitor_end',
        //             'container_datas.free_storage_duration as free_storage_duration',
        //             'container_datas.free_demurrage_duration as free_demurrage_duration',
        //             'container_datas.free_storage_start_at as free_storage_start_at',
        //             'container_datas.free_storage_end_at as free_storage_end_at',
        //             'container_datas.free_demurrage_start_at as free_demurrage_start_at',
        //             'container_datas.free_demurrage_end_at as free_demurrage_end_at',
        //             'container_datas.is_over_demurrage as is_over_demurrage',
        //             'container_datas.is_over_storage as is_over_storage',
        //             'container_datas.status_id as status_id',
        //             DB::raw('TIMESTAMPDIFF(SECOND, NOW(), free_storage_end_at) AS free_storage_time_limit'),
        //             DB::raw('TIMESTAMPDIFF(SECOND, NOW(), free_demurrage_end_at) AS free_demurrage_time_limit'))
        //         ->whereNotNull('si_destination_id')
        //         ->get();

        $admin = Auth::user();
        if ($admin->isSuperAdmin()) {
            $destinations = DB::table('branches')
                ->orderBy('name')
                ->get();
        } else {
            if ($admin->branches->count() > 0) {
                $otherBranchIds = $admin->branches->pluck('id')->toArray();

                $destinations = DB::table('branches')
                    ->whereIn('id', $otherBranchIds)
                    ->orWhere('id', '=', $admin->branch_id)
                    ->orderBy('name')
                    ->get();
            } else {
                $destinations = DB::table('branches')
                    ->where('id', $admin->branch_id)
                    ->orderBy('name')
                    ->get();
            }
        }

        $datenow = Carbon::today('Asia/Jakarta')->format('d-m-Y');
        $startDate = Carbon::today('Asia/Jakarta')->subMonths(6)->format('d-m-Y');

        $data = [
            'admin' => $admin,
            'destinations' => $destinations,
            'filterDestination' => 0,
            'datenow' => $datenow,
            'startDate' => $startDate,
        ];

        return view('dashboard')->with($data);
    }

    public function getTotalDemurrage(Request $request)
    {
        try {
            // check demurrage
            $containers = DB::table('container_datas')
                ->select('container_datas.id as id',
                    'container_datas.si_destination_id as si_destination_id',
                    'container_datas.is_storage_monitor_start as is_storage_monitor_start',
                    'container_datas.is_storage_monitor_end as is_storage_monitor_end',
                    'container_datas.is_demurrage_monitor_start as is_demurrage_monitor_start',
                    'container_datas.is_demurrage_monitor_end as is_demurrage_monitor_end',
                    'container_datas.free_storage_duration as free_storage_duration',
                    'container_datas.free_demurrage_duration as free_demurrage_duration',
                    'container_datas.free_storage_start_at as free_storage_start_at',
                    'container_datas.free_storage_end_at as free_storage_end_at',
                    'container_datas.free_demurrage_start_at as free_demurrage_start_at',
                    'container_datas.free_demurrage_end_at as free_demurrage_end_at',
                    'container_datas.is_over_demurrage as is_over_demurrage',
                    'container_datas.is_over_storage as is_over_storage',
                    'container_datas.status_id as status_id',
                    DB::raw('TIMESTAMPDIFF(SECOND, NOW(), free_storage_end_at) AS free_storage_time_limit'),
                    DB::raw('TIMESTAMPDIFF(SECOND, NOW(), free_demurrage_end_at) AS free_demurrage_time_limit'))
                ->whereNotNull('si_destination_id');

            $admin = Auth::user();
            if (! $admin->isSuperAdmin()) {
                $branchIdArr[] = $admin->branch_id;
                foreach ($admin->branches as $adminBranch) {
                    $branchIdArr[] = $adminBranch->id;
                }

                if ($request->get('destination_id') && $request->get('destination_id') > 0) {
                    if (! in_array($request->get('destination_id'), $branchIdArr)) {
                        $containers = $containers->whereIn('si_destination_id', $branchIdArr);
                    } else {
                        $containers = $containers->where('si_destination_id', '=', $request->get('destination_id'));
                    }
                } else {
                    $containers = $containers->whereIn('si_destination_id', $branchIdArr);
                }
            } else {
                if ($request->get('destination_id') && $request->get('destination_id') > 0) {
                    $containers = $containers->where('si_destination_id', '=', $request->get('destination_id'));
                }
            }

            $containers = $containers->get();

            $h30DemurrageTotal = 0; // For medan only
            $h7DemurrageTotal = 0;
            $h3DemurrageTotal = 0;
            $h1StorageTotal = 0;
            $overDemurrageTotal = 0;
            $overStorageTotal = 0;
            foreach ($containers as $container) {
                $demurrageDayLimit = 0;
                if (! empty($container->free_demurrage_time_limit) && $container->free_demurrage_time_limit > 0) {
                    $demurrageDayLimit = ceil(($container->free_demurrage_time_limit / 3600) / 24);
                }

                $storageDayLimit = 0;
                if ($container->free_storage_time_limit > 0) {
                    $storageDayLimit = ceil(($container->free_storage_time_limit / 3600) / 24);
                }

                // Check for demurrage H-30
                if ($container->is_demurrage_monitor_start == 1 &&
                    $demurrageDayLimit <= 30 &&
                    $demurrageDayLimit > 7) {
                    $h30DemurrageTotal++;
                }

                // Check for demurrage H-7
                if ($container->is_demurrage_monitor_start == 1 &&
                    $demurrageDayLimit <= 7 &&
                    $demurrageDayLimit > 3) {
                    $h7DemurrageTotal++;
                }

                // Check for demurrage H-3
                if ($container->is_demurrage_monitor_start == 1 &&
                    $demurrageDayLimit <= 3 &&
                    $demurrageDayLimit > 0) {
                    $h3DemurrageTotal++;
                }

                // Check for storage H-1
                if ($container->is_storage_monitor_start == 1 &&
                    $container->is_storage_monitor_end == 0 &&
                    $storageDayLimit == 1) {
                    $h1StorageTotal++;
                }

                // Check for demurrage pinalty
                if ($container->is_demurrage_monitor_start == 1 &&
                    $container->is_over_demurrage == 1) {
                    $overDemurrageTotal++;
                }

                // Check for storage pinalty
                if ($container->is_storage_monitor_start == 1 &&
                    $container->is_over_storage == 1) {
                    $overStorageTotal++;
                }
            }

            $data = [
                'demurrage30' => $h30DemurrageTotal,
                'demurrage7' => $h7DemurrageTotal,
                'demurrage3' => $h3DemurrageTotal,
                'storage1' => $h1StorageTotal,
                'overDemurrage' => $overDemurrageTotal,
                'overStorage' => $overStorageTotal,
            ];

            return Response::json($data, 200);
        } catch (\Exception $ex) {
            return Response::json($ex, 500);
        }
    }

    public function getLastDo()
    {
        try {
            $datas = DB::table('do_details')
                ->join('do_headers', 'do_details.header_id', '=', 'do_headers.id')
                ->select('do_details.id as detail_id',
                    'do_headers.id as header_id',
                    'do_headers.code as code',
                    'do_headers.type as type',
                    'do_headers.date as date',
                    // 'DATE_FORMAT(do_headers.date, "%d %m %Y")  as date',
                    'do_headers.shipping_company as shipping_company',
                    'do_headers.customer_code as customer_code',
                    'do_headers.customer_name as customer_name',
                    'do_headers.shipto_code as shipto_code',
                    'do_headers.shipto_name as shipto_name',
                    'do_headers.container1_code as container1_code',
                    'do_details.item_code as item_code',
                    'do_details.item_name as item_name',
                    'do_details.unit as unit',
                    'do_details.qty as qty')
                ->where('do_headers.is_close', 0)
                ->where('do_headers.type', 'delivery_order')
                ->orderby('header_id', 'DESC')
                ->skip(0)->take(10)
                ->get();

            $returnResult = [];
            foreach ((object) $datas as $data) {
                $detail = [
                    'customer_name' => $data->customer_name,
                    'shipto_name' => $data->shipto_name,
                    'date' => Carbon::parse($data->date)->locale('id_ID')->format('d-m-Y'),
                    'shipping_company' => $data->shipping_company,
                    'code' => $data->code,
                    'item_name' => $data->item_name,
                    'qty' => $data->qty,
                    'unit' => $data->unit,
                    'container1_code' => $data->container1_code,
                ];

                $returnResult[] = $detail;
            }

            return Response::json($returnResult, 200);
        } catch (\Exception $ex) {
            return Response::json($ex, 500);
        }
    }

    public function getContainerType()
    {
        try {
            $totalSo = DoHeader::count('id');

            return Response::json($totalSo, 200);
        } catch (\Exception $ex) {
            return Response::json($ex, 500);
        }
    }

    public function getContainerOverdue()
    {
        try {
            $totalSo = DoHeader::count('id');

            return Response::json($totalSo, 200);
        } catch (\Exception $ex) {
            return Response::json($ex, 500);
        }
    }

    public function getSIStatus(Request $request)
    {
        try {
            $statusSummary = DB::table('shipping_instruction_headers')
                ->select(DB::raw('SUM(status_id = 7) as total_pending'),
                    DB::raw('SUM(status_id = 8) as total_sailing'),
                    DB::raw('SUM(status_id = 9) as total_berthing'),
                    DB::raw('SUM(status_id = 13) as total_dooring'),
                    DB::raw('SUM(status_id = 18) as total_received'))
                ->where('created_at', '>', Carbon::today('Asia/Jakarta')->subMonths(3));

            $admin = Auth::user();
            if (! $admin->isSuperAdmin()) {
                $branchIdArr[] = $admin->branch_id;
                foreach ($admin->branches as $adminBranch) {
                    $branchIdArr[] = $adminBranch->id;
                }

                if ($request->get('destination') && $request->get('destination') > 0) {
                    if (! in_array($request->get('destination'), $branchIdArr)) {
                        $statusSummary = $statusSummary->whereIn('destination_branch_id', $branchIdArr);
                    } else {
                        $statusSummary = $statusSummary->where('destination_branch_id', '=', $request->get('destination'));
                    }
                } else {
                    $statusSummary = $statusSummary->whereIn('destination_branch_id', $branchIdArr);
                }
            } else {
                if ($request->get('destination') && $request->get('destination') > 0) {
                    $statusSummary = $statusSummary->where('destination_branch_id', '=', $request->get('destination'));
                }
            }

            $statusSummary = $statusSummary->first();

            return Response::json($statusSummary, 200);
        } catch (\Exception $ex) {
            return Response::json($ex, 500);
        }
    }

    public function getSIContainerStatus(Request $request)
    {
        try {
            $containerSummary = DB::table('shipping_instruction_monitors')
                ->join('shipping_instruction_headers', 'shipping_instruction_monitors.header_id', '=', 'shipping_instruction_headers.id')
                ->select(DB::raw('SUM(shipping_instruction_monitors.status_id = 9) as total_berthing'),
                    DB::raw('SUM(shipping_instruction_monitors.status_id = 13) as total_dooring'),
                    DB::raw('SUM(shipping_instruction_monitors.status_id = 18) as total_received'))
                ->where('shipping_instruction_headers.created_at', '>', Carbon::today('Asia/Jakarta')->subMonths(3));

            $admin = Auth::user();
            if (! $admin->isSuperAdmin()) {
                $branchIdArr[] = $admin->branch_id;
                foreach ($admin->branches as $adminBranch) {
                    $branchIdArr[] = $adminBranch->id;
                }

                if ($request->get('destination') && $request->get('destination') > 0) {
                    if (! in_array($request->get('destination'), $branchIdArr)) {
                        $containerSummary = $containerSummary->whereIn('shipping_instruction_headers.destination_branch_id', $branchIdArr);
                    } else {
                        $containerSummary = $containerSummary->where('shipping_instruction_headers.destination_branch_id', '=', $request->get('destination'));
                    }
                } else {
                    $containerSummary = $containerSummary->whereIn('shipping_instruction_headers.destination_branch_id', $branchIdArr);
                }
            } else {
                if ($request->get('destination') && $request->get('destination') > 0) {
                    $containerSummary = $containerSummary->where('shipping_instruction_headers.destination_branch_id', '=', $request->get('destination'));
                }
            }

            $containerSummary = $containerSummary->first();

            return Response::json($containerSummary, 200);
        } catch (\Exception $ex) {
            return Response::json($ex, 500);
        }
    }
}
