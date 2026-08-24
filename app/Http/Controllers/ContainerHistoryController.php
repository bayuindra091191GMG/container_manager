<?php

namespace App\Http\Controllers;

use App\Transformer\ContainerHistoryTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\DataTables\DataTables;

class ContainerHistoryController extends Controller
{
    public function index(Request $request)
    {
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

        $data = [
            'destinations' => $destinations,
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'filterDestination' => $filterDestination,
            'admin' => $admin,
        ];

        return view('container.index_history')->with($data);
    }

    public function datatable(Request $request)
    {
        $admin = Auth::user();

        $data = DB::table('container_histories');

        if (! $admin->isSuperAdmin()) {
            $data = $data->where('container_histories.si_destination_id', '=', $admin->branch_id);
        }

        if ($request->get('start_date') && $request->get('end_date')) {
            // Filter date
            $start = Carbon::createFromFormat('!d-m-Y', $request->get('start_date'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('!d-m-Y', $request->get('end_date'), 'Asia/Jakarta');
            $end->addDays(1);

            $data = $data
                ->whereNotNull('container_histories.returned_at')
                ->whereBetween('container_histories.returned_at', [$start->toDateTimeString(), $end->toDateTimeString()]);
        }

        if (! $admin->isSuperAdmin()) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            if ($request->get('destination') && $request->get('destination') > 0) {
                if (! in_array($request->get('destination'), $branchIdArr)) {
                    $data = $data->whereIn('container_histories.si_destination_id', $branchIdArr);
                } else {
                    $data = $data->where('container_histories.si_destination_id', '=', $request->get('destination'));
                }
            } else {
                $data = $data->whereIn('container_histories.si_destination_id', $branchIdArr);
            }
        } else {
            if ($request->get('destination') && $request->get('destination') > 0) {
                $data = $data->where('container_histories.si_destination_id', '=', $request->get('destination'));
            }
        }

        return DataTables::of($data)
            ->setTransformer(new ContainerHistoryTransformer)
            ->make(true);
    }

    public function exportList(Request $request)
    {
        try {
            $data = DB::table('container_histories');

            if ($request->get('start_date') && $request->get('end_date')) {
                // Filter date
                $start = Carbon::createFromFormat('!d-m-Y', $request->get('start_date'), 'Asia/Jakarta');
                $end = Carbon::createFromFormat('!d-m-Y', $request->get('end_date'), 'Asia/Jakarta');
                $end->addDays(1);

                $data = $data->whereBetween('container_histories.returned_at', [$start->toDateTimeString(), $end->toDateTimeString()]);
            }

            if ($request->get('destination')) {
                if ($request->get('destination') > 0) {
                    $data = $data->where('container_histories.si_destination_id', '=', $request->get('destination'));
                }
            }

            $data = $data->get();

            $no = 1;
            $containerArr = [];
            foreach ($data as $container) {
                $containerArr[] = [
                    'NO' => $no,
                    'CONTAINER NO' => $container->code,
                    'DO / WH TRANSFER NO' => $container->do_code,
                    'ITEM NAME' => $container->item_name,
                    'VESSEL NAME' => $container->vessel_name,
                    'DESTINATION' => $container->si_destination_name,
                    'ETD' => ! empty($container->si_etd_date) ? Carbon::parse($container->si_etd_date)->format('d-m-Y') : '',
                    'ETA' => ! empty($container->si_eta_date) ? Carbon::parse($container->si_eta_date)->format('d-m-Y') : '',
                    'BERTHING' => ! empty($container->berthing_at) ? Carbon::parse($container->berthing_at)->format('d-m-Y') : '',
                    'DOORING' => ! empty($container->dooring_at) ? Carbon::parse($container->dooring_at)->format('d-m-Y') : '',
                    'RECEIVED AT WH' => ! empty($container->received_at) ? Carbon::parse($container->received_at)->format('d-m-Y') : '',
                    'SEMEN OUT' => ! empty($container->semen_out_at) ? Carbon::parse($container->semen_out_at)->format('d-m-Y') : '',
                    'CERAMIC RECEIVED' => ! empty($container->ceramic_received_at) ? Carbon::parse($container->ceramic_received_at)->format('d-m-Y') : '',
                    'RETURN TO DEPO' => ! empty($container->returned_at) ? Carbon::parse($container->returned_at)->format('d-m-Y') : '',
                    'DAYS SINCE BERTHING' => $container->days_since_berthing,
                ];

                $no++;
            }

            $now = Carbon::now('Asia/Jakarta');
            $pattern = $now->year.$now->month.$now->day.$now->hour.$now->minute;

            return (new FastExcel($containerArr))->download('CONTAINER HISTORY EXPORT '.$pattern.'.xlsx');
        } catch (\Exception $ex) {
            dd($ex);
            Log::channel('container')->error('ContainerHistoryController exportList error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }
}
