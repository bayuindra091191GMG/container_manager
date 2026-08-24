<?php

namespace App\Http\Controllers;

use App\Models\ContainerData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Rap2hpoutre\FastExcel\FastExcel;

class ContainerManagementController extends Controller
{
    public function exportList(Request $request)
    {
        try {
            $data = DB::table('container_datas')
                ->leftJoin('statuses', 'container_datas.status_id', '=', 'statuses.id')
                ->select(
                    'container_datas.id as id',
                    'container_datas.code as code',
                    'container_datas.do_code as do_code',
                    'container_datas.timbangan_code as timbangan_code',
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
                    'container_datas.free_storage_start_at as free_storage_start_at',
                    'container_datas.free_storage_end_at as free_storage_end_at',
                    'container_datas.free_demurrage_end_at as free_demurrage_end_at',
                    'container_datas.is_storage_monitor_start as is_storage_monitor_start',
                    'container_datas.is_storage_monitor_end as is_storage_monitor_end',
                    'container_datas.is_demurrage_monitor_start as is_demurrage_monitor_start',
                    'container_datas.is_demurrage_monitor_end as is_demurrage_monitor_end',
                    'container_datas.is_over_storage as is_over_storage',
                    'container_datas.is_over_demurrage as is_over_demurrage',
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

            if ($request->get('destination')) {
                if ($request->get('destination') > 0) {
                    $data = $data->where('container_datas.si_destination_id', '=', $request->get('destination'));
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

            $data = $data->get();

            $no = 1;
            $containerArr = [];
            foreach ($data as $container) {
                $warning = '';
                if ($container->is_over_storage == 1 && $container->is_over_demurrage == 1) {
                    $warning = 'OVER STORAGE AND DEMURRAGE';
                } elseif ($container->is_over_storage != 1 && $container->is_over_demurrage == 1) {
                    $warning = 'OVER DEMURRAGE';
                } elseif ($container->is_over_storage == 1 && $container->is_over_demurrage != 1) {
                    $warning = 'OVER STORAGE';
                } else {
                    if ($container->free_storage_time_limit > 0) {
                        $storageDays = ceil(($container->free_storage_time_limit / 3600) / 24);
                        if ($container->is_storage_monitor_start == 1 &&
                            $container->is_storage_monitor_end == 0 &&
                            $storageDays == 1) {
                            $warning = 'H-1 STORAGE';
                        }
                    } else {
                        if ($container->free_demurrage_time_limit > 0) {
                            $demurrageDays = ceil(($container->free_demurrage_time_limit / 3600) / 24);
                            if ($container->is_demurrage_monitor_start == 1 &&
                                $container->is_demurrage_monitor_end == 0) {
                                if ($demurrageDays <= 30 && $demurrageDays > 7) {
                                    $warning = 'H-30 STORAGE';
                                } elseif ($demurrageDays <= 7 && $demurrageDays > 3) {
                                    $warning = 'H-7 STORAGE';
                                } elseif ($demurrageDays <= 3 && $demurrageDays > 0) {
                                    $warning = 'H-3 STORAGE';
                                }
                            }
                        }
                    }
                }

                $containerArr[] = [
                    'NO' => $no,
                    'CONTAINER NO' => $container->code,
                    'DO / WH TRANSFER NO' => $container->do_code,
                    'ITEM NAME' => $container->item_name,
                    'VESSEL NAME' => $container->vessel_name,
                    'DESTINATION' => $container->si_destination_name,
                    'CONTAINER STATUS' => $container->status,
                    'ETD' => ! empty($container->si_etd_date) ? Carbon::parse($container->si_etd_date)->format('d-m-Y') : '',
                    'ETA' => ! empty($container->si_eta_date) ? Carbon::parse($container->si_eta_date)->format('d-m-Y') : '',
                    'BERTHING' => ! empty($container->berthing_at) ? Carbon::parse($container->berthing_at)->format('d-m-Y') : '',
                    'DOORING' => ! empty($container->dooring_at) ? Carbon::parse($container->dooring_at)->format('d-m-Y') : '',
                    'RECEIVED AT WH' => ! empty($container->received_at) ? Carbon::parse($container->received_at)->format('d-m-Y') : '',
                    'SEMEN OUT' => ! empty($container->semen_out_at) ? Carbon::parse($container->semen_out_at)->format('d-m-Y') : '',
                    'CERAMIC RECEIVED' => ! empty($container->ceramic_received_at) ? Carbon::parse($container->ceramic_received_at)->format('d-m-Y') : '',
                    'RETURN TO DEPO' => ! empty($container->returned_at) ? Carbon::parse($container->returned_at)->format('d-m-Y') : '',
                    'WARNING' => $warning,
                    'DAYS SINCE BERTHING' => ! empty($container->days_since_berthing) ? floor(($container->days_since_berthing / 3600) / 24) : '0',
                ];

                $no++;
            }

            $now = Carbon::now('Asia/Jakarta');
            $pattern = $now->year.$now->month.$now->day.$now->hour.$now->minute;

            return (new FastExcel($containerArr))->download('CONTAINER LIST EXPORT '.$pattern.'.xlsx');
        } catch (\Exception $ex) {
            dd($ex);
            Log::channel('container')->error('ContainerManagementController exportList error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function operation(Request $request)
    {
        try {
            $containers = DB::table('container_datas')
                ->join('statuses', 'container_datas.status_id', '=', 'statuses.id')
                ->select('container_datas.id as id',
                    'container_datas.code as code',
                    'container_datas.do_code as do_code',
                    'container_datas.si_code as si_code',
                    'container_datas.si_destination_name as si_destination_name',
                    'statuses.display as status');

            $destinationId = 0;
            if ($request->exists('destination_id')) {
                $destinationId = intval($request->query('destination_id'));
            }

            if ($destinationId > 0) {
                $containers = $containers->where('container_datas.si_destination_id', '=', $destinationId);
            }

            $status = $request->query('status');
            if ($status == 'received') {
                $containers = $containers->where('container_datas.status_id', '=', 18);
            } elseif ($status == 'returned') {
                $containers = $containers->where('container_datas.status_id', '=', 12);
            } else {
                $containers = $containers->whereIn('container_datas.status_id', [12, 13]);
            }

            $search = trim((string) $request->query('search', ''));
            if ($search !== '') {
                $searchTerm = '%'.$search.'%';
                $containers = $containers->where(function ($query) use ($searchTerm) {
                    $query->where('container_datas.code', 'like', $searchTerm)
                        ->orWhere('container_datas.do_code', 'like', $searchTerm)
                        ->orWhere('container_datas.si_code', 'like', $searchTerm);
                });
            }

            $containers = $containers->get();

            $containerArr = [];
            foreach ($containers as $container) {
                $containerArr[] = [
                    'id' => $container->id,
                    'code' => $container->code,
                    'do_code' => $container->do_code,
                    'destination_name' => $container->si_destination_name,
                    'status' => $container->status,
                    'si_code' => $container->si_code,
                ];
            }

            $vueData = [
                'containerList' => $containerArr,
            ];

            $user = Auth::user();
            if ($user->isSuperAdmin()) {
                $destinations = DB::table('branches')
                    ->orderBy('name')
                    ->get();
            } else {
                $destinations = DB::table('branches')
                    ->where('id', $user->branch_id)
                    ->orderBy('name')
                    ->get();
            }

            $data = [
                'vueData' => $vueData,
                'destinationId' => $destinationId,
                'destinations' => $destinations,
                'status' => $status,
                'search' => $search,
            ];

            if ($status === 'all') {
                return view('trx_warehouse.operation_all')->with($data);
            } elseif ($status === 'received') {
                return view('trx_warehouse.operation_received')->with($data);
            } elseif ($status === 'returned') {
                return view('trx_warehouse.operation_returned')->with($data);
            } else {
                return redirect()->route('container');
            }
        } catch (\Exception $ex) {
            dd($ex);
            Log::channel('container')->error('ContainerManagementController operation error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function updateOperation(Request $request)
    {
        try {
            if (! $request->filled('checkboxes')) {
                Session::flash('error', 'Invalid input!');

                return redirect()->back();
            }

            $ids = $request->input('checkboxes');
            foreach ($ids as $containerId) {
                $containerMaster = ContainerData::find($containerId);
                $containerMaster->status_id = 12;
                $containerMaster->is_demurrage_monitor_end = true;
                $containerMaster->returned_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
                $containerMaster->save();
            }

            Session::flash('success', 'Update container status success!');

            return redirect()->route('container.operation', ['destination_id' => $request->input('destination_id'), 'status' => 'returned']);
        } catch (\Exception $ex) {
            dd($ex);
            Log::channel('container')->error('ContainerManagementController updateOperation error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }
}
