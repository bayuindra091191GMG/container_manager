<?php

namespace App\Http\Controllers;

use App\Exports\CementReceiveReportExport;
use App\Exports\ShippingInstructionFinalExport;
use App\Libs\AutoNumberUtility;
use App\Libs\MiscUtility;
use App\Models\Branch;
use App\Models\ContainerData;
use App\Models\ContainerLog;
use App\Models\DoDetail;
use App\Models\DoHeader;
use App\Models\ShippingInstructionDetail;
use App\Models\ShippingInstructionHeader;
use App\Models\ShippingInstructionMonitor;
use App\Models\ShippingInstructionUpload;
use App\Transformer\ShippingInstructionTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\DataTables\Facades\DataTables;

class ShippingInstructionController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::today()->subDays(30);
        $end = Carbon::today();

        $filterStartDate = Carbon::today()->subDays(30)->format('d-m-Y');
        $filterEndDate = Carbon::today()->format('d-m-Y');

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

        $filterStatus = 0;
        if ($request->status_id != null) {
            $filterStatus = intval($request->status_id);
        }

        $keyword = '';
        if ($request->keyword != null) {
            $keyword = $request->keyword;
        }

        $admin = Auth::user();
        if ($admin->is_superadmin == 1) {
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

        $statuses = DB::table('statuses')
            ->whereIn('id', [7, 8, 9, 13, 18, 21])
            ->get();

        // Get status summary
        $statusSummary = DB::table('shipping_instruction_headers')
            ->select(DB::raw('SUM(status_id = 7) as total_pending'),
                DB::raw('SUM(status_id = 8) as total_sailing'),
                DB::raw('SUM(status_id = 9) as total_berthing'),
                DB::raw('SUM(status_id = 13) as total_dooring'),
                DB::raw('SUM(status_id = 18) as total_received'));
        // ->where('created_at', '>', Carbon::today('Asia/Jakarta')->subMonths(6));

        if ($admin->is_superadmin != 1) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            $statusSummary = $statusSummary->whereIn('destination_branch_id', $branchIdArr);
        }

        // Filter status summary by ETD
        $statusSummary = $statusSummary->whereBetween('etd_date', [$start->toDateTimeString(), $end->toDateTimeString()]);

        // Filter status summary by status
        // if($filterStatus > 0){
        //     $statusSummary = $statusSummary->where('status_id', '=', $filterStatus);
        // }

        $statusSummary = $statusSummary->first();

        $containerSummary = DB::table('shipping_instruction_monitors')
            ->join('shipping_instruction_headers', 'shipping_instruction_monitors.header_id', '=', 'shipping_instruction_headers.id')
            ->select(DB::raw('SUM(shipping_instruction_monitors.status_id = 7) as total_pending'),
                DB::raw('SUM(shipping_instruction_monitors.status_id = 9) as total_berthing'),
                DB::raw('SUM(shipping_instruction_monitors.status_id = 13) as total_dooring'),
                DB::raw('SUM(shipping_instruction_monitors.status_id = 18) as total_received'));
        // ->where('shipping_instruction_headers.created_at', '>', Carbon::today('Asia/Jakarta')->subMonths(6));

        if ($admin->is_superadmin != 1) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            $containerSummary = $containerSummary->whereIn('shipping_instruction_headers.destination_branch_id', $branchIdArr);
        }

        // Filter container summary by ETD
        $containerSummary = $containerSummary->whereBetween('shipping_instruction_headers.etd_date', [$start->toDateTimeString(), $end->toDateTimeString()]);

        // Filter status summary by status
        // if($filterStatus > 0){
        //     $containerSummary = $containerSummary->where('shipping_instruction_headers.status_id', '=', $filterStatus);
        // }

        $containerSummary = $containerSummary->first();

        $data = [
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'filterDestination' => $filterDestination,
            'filterStatus' => $filterStatus,
            'destinations' => $destinations,
            'statuses' => $statuses,
            'statusSummary' => $statusSummary,
            'containerSummary' => $containerSummary,
            'keyword' => $keyword,
        ];

        return view('shippings.shipping_instruction.index')->with($data);
    }

    public function datatable(Request $request)
    {
        $admin = Auth::user();
        $data = DB::table('shipping_instruction_headers')
            ->join('statuses', 'shipping_instruction_headers.status_id', '=', 'statuses.id')
            ->select('shipping_instruction_headers.id as id',
                'shipping_instruction_headers.code as code',
                'shipping_instruction_headers.shipper as shipper',
                'shipping_instruction_headers.vessel_name as vessel_name',
                'shipping_instruction_headers.sailing_date as sailing_date',
                'shipping_instruction_headers.docking_date as docking_date',
                'shipping_instruction_headers.dooring_at as dooring_at',
                'shipping_instruction_headers.received_at as received_at',
                'shipping_instruction_headers.created_at as created_at',
                'shipping_instruction_headers.etd_date as etd_date',
                'shipping_instruction_headers.eta_date as eta_date',
                'shipping_instruction_headers.destination as destination',
                'shipping_instruction_headers.is_sync as is_sync',
                'shipping_instruction_headers.total_berthing_cont as total_berthing_cont',
                'shipping_instruction_headers.total_dooring_cont as total_dooring_cont',
                'shipping_instruction_headers.total_received_cont as total_received_cont',
                'statuses.display as status_display');

        if ($admin->is_superadmin != 1) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            if ($request->get('destination') && $request->get('destination') > 0) {
                if (! in_array($request->get('destination'), $branchIdArr)) {
                    $data = $data->whereIn('shipping_instruction_headers.destination_branch_id', $branchIdArr);
                } else {
                    $data = $data->where('shipping_instruction_headers.destination_branch_id', '=', $request->get('destination'));
                }
            } else {
                $data = $data->whereIn('shipping_instruction_headers.destination_branch_id', $branchIdArr);
            }
        } else {
            if ($request->get('destination') && $request->get('destination') > 0) {
                $data = $data->where('shipping_instruction_headers.destination_branch_id', '=', $request->get('destination'));
            }
        }

        if ($request->get('start_date') || $request->get('end_date')) {
            // Filter date
            $start = Carbon::createFromFormat('!d-m-Y', $request->get('start_date'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('!d-m-Y', $request->get('end_date'), 'Asia/Jakarta');
            $end->addDays(1);

            $data = $data->whereBetween('shipping_instruction_headers.etd_date', [$start->toDateTimeString(), $end->toDateTimeString()]);
        }

        if ($request->get('status_id')) {
            if ($request->get('status_id') > 0) {
                $data = $data->where('status_id', '=', $request->get('status_id'));
            }
        }

        if ($request->get('keyword')) {
            $keyword = $request->get('keyword');
            if (! empty($keyword)) {
                $data = $data->whereExists(function ($query) use ($keyword) {
                    $query->select(DB::raw(1))
                        ->from('shipping_instruction_monitors')
                        ->whereRaw('shipping_instruction_headers.id = shipping_instruction_monitors.header_id')
                        ->where(function ($query1) use ($keyword) {
                            $query1->where('shipping_instruction_monitors.do_code', 'LIKE', '%'.$keyword.'%')
                                ->orWhere('shipping_instruction_monitors.container_code', 'LIKE', '%'.$keyword.'%');
                        });
                });
            }
        }

        return DataTables::of($data)
            ->setTransformer(new ShippingInstructionTransformer)
            ->make(true);
    }

    public function create()
    {
        return view('shippings.shipping_instruction.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'shipper' => 'required|max:200',
                'vessel' => 'required|max:200',
                'eta_date' => 'required',
                'etd_date' => 'required',
                'pic_receiver' => 'required|max:200',
                'port_receiver' => 'required|max:200',
            ], [
                'shipper.required' => 'SHIPPER must be filled!',
                'shipper.max' => 'SHIPPER must be below 200 characters!',
                'vessel.required' => 'VESSEL must be filled!',
                'vessel.max' => 'VESSEL must be below 200 characters!',
                'eta_date.required' => 'ETA must be filled!',
                'etd_date.required' => 'ETD must be filled!',
                'pic_receiver.required' => 'ATTN must be filled!',
                'pic_receiver.max' => 'ATTN must be below 200 characters!',
                'port_receiver.required' => 'TO must be filled!',
                'port_receiver.max' => 'TO must be below 200 characters!',
            ]);

            if ($validator->fails()) {
                return response()->json(collect([
                    'code' => 422,
                    'error' => 'validation_failed',
                    'result' => $validator->errors()->all(),
                ]), 200);
            }

            $errors = [];

            // Validate destination
            if (! $request->filled('destination')) {
                $errors[] = 'Please select destination!';
            }

            // Validate ETD ETA
            $etaDate = Carbon::createFromFormat('!d-m-Y', $request->input('eta_date'), 'Asia/Jakarta');
            $etdDate = Carbon::createFromFormat('!d-m-Y', $request->input('etd_date'), 'Asia/Jakarta');
            if ($etdDate->greaterThanOrEqualTo($etaDate)) {
                $errors[] = 'ETD date must be earlier than ETA date';
            }

            if (! $request->filled('details') && ! $request->filled('detailsWt')) {
                return response()->json(collect([
                    'code' => 500,
                    'error' => 'special_validation_failed',
                    'result' => 'DO or Warehouse Transfer details must be inputted!',
                ]), 200);
            }

            $details = $request->input('details');
            $detailsWt = $request->input('detailsWt');

            // Validate inputted normal DO details
            if (! empty($details)) {
                $tmpDoIds = [];
                foreach ($details as $detail) {
                    if (! empty($detail['do_id'])) {
                        $tmpDo = DB::table('do_headers')
                            ->where('id', '=', $detail['do_id'])
                            ->first();

                        if (in_array($detail['do_id'], $tmpDoIds)) {
                            $errors[] = 'DO '.$tmpDo->code.' is duplicated!';
                        }
                        $tmpDoIds[] = $detail['do_id'];

                        if (DB::table('do_details')->where('header_id', '=', $detail['do_id'])->count() > 1) {
                            $errors[] = 'DO '.$tmpDo->code.' has more than one item line and cannot be used in a Shipping Instruction!';
                        }

                        if ($tmpDo->is_close == 1) {
                            $errors[] = 'DO '.$tmpDo->code.' is already closed!';
                        }

                        if ($detail['delivery_type'] == 'default') {
                            $errors[] = 'DO '.$tmpDo->code.' must select delivery type!';
                        } else {
                            if ($detail['delivery_type'] == 'FRANCO GUDANG' && empty($detail['trucking'])) {
                                $errors[] = 'DO '.$tmpDo->code.' must select consignee!';
                            }
                        }
                    }
                }
            }

            // Validate warehouse transfer details
            if (! empty($detailsWt)) {
                foreach ($detailsWt as $detail) {
                    $tmpDo = DB::table('do_headers')
                        ->where('id', '=', $detail['do_id'])
                        ->first();
                    if (DB::table('do_details')->where('header_id', '=', $detail['do_id'])->count() > 1) {
                        $errors[] = 'Warehouse Transfer '.$tmpDo->code.' has more than one item line and cannot be used in a Shipping Instruction!';
                    }

                    if ($tmpDo->is_close == 1) {
                        $errors[] = 'Warehouse Transfer '.$tmpDo->code.' is already closed!';
                    }

                    if ($detail['delivery_type'] == 'default') {
                        $errors[] = 'Warehouse Transfer '.$tmpDo->code.' must select delivery type!';
                    }

                    if (empty($detail['trucking_1']) || empty($detail['trucking_2'])) {
                        $errors[] = 'Warehouse Transfer '.$tmpDo->code.' must input consignee for each container!';
                    }
                }
            }

            if (count($errors) > 0) {
                return response()->json(collect([
                    'code' => 400,
                    'error' => 'validation_failed',
                    'result' => $errors,
                ]), 200);
            }

            $newSiHeader = new ShippingInstructionHeader();
            $newSiHeader->code = AutoNumberUtility::generateAutonumber('SI');
            $newSiHeader->shipper = 'PT. JUI SHIN INDONESIA';
            $newSiHeader->vessel_name = strtoupper($request->input('vessel'));

            $branchId = intval($request->input('destination'));
            $branch = DB::table('branches')->where('id', '=', $branchId)->first();

            $newSiHeader->destination_branch_id = $branchId;
            $newSiHeader->destination = strtoupper($branch->name);
            $newSiHeader->pic_receiver = strtoupper($request->input('pic_receiver'));
            $newSiHeader->port_receiver = strtoupper($request->input('port_receiver'));
            $newSiHeader->eta_date = $etaDate;
            $newSiHeader->etd_date = $etdDate;
            $newSiHeader->status_id = 7;
            $newSiHeader->is_sync = true;
            $newSiHeader->created_by = Auth::id();
            $newSiHeader->updated_by = Auth::id();
            $newSiHeader->save();

            // Process normal DO
            if (! empty($details)) {
                foreach ($details as $detail) {
                    if (! empty($detail['do_id'])) {
                        $newSiDetail = new ShippingInstructionDetail();
                        $newSiDetail->header_id = $newSiHeader->id;
                        $newSiDetail->header_code = $newSiHeader->code;
                        $newSiDetail->delivery_type = $detail['delivery_type'];

                        if (! empty($detail['trucking'])) {
                            $consigneeId = intval($detail['trucking']);
                            $newSiDetail->consignee_id = $consigneeId;

                            $consignee = DB::table('consignees')->where('id', '=', $consigneeId)->first();
                            $newSiDetail->consignee_code = strtoupper($consignee->code);
                            $newSiDetail->trucking = strtoupper($consignee->name);
                        }

                        $doHeader = DoHeader::where('id', '=', $detail['do_id'])
                            ->first();

                        $newSiDetail->do_id = $detail['do_id'];
                        $newSiDetail->do_code = $doHeader->code;
                        $newSiDetail->do_type = $doHeader->type;
                        $newSiDetail->so_code = $doHeader->so_code;
                        $newSiDetail->dr_code = $doHeader->dr_code;
                        $newSiDetail->shipto_code = $doHeader->shipto_code;
                        $newSiDetail->shipto_name = $doHeader->shipto_name;

                        $doDetail = DoDetail::where('header_id', '=', $newSiDetail->do_id)
                            ->first();

                        $newSiDetail->item_id = $doDetail->item_id;
                        $newSiDetail->item_code = $doDetail->item_code;
                        $newSiDetail->item_name = $doDetail->item_name;
                        $newSiDetail->unit = $doDetail->unit;
                        $newSiDetail->qty = $doDetail->qty;
                        $newSiDetail->transportation = $doHeader->transportation;
                        $newSiDetail->container1_id = $doHeader->container1_id;
                        $newSiDetail->container1_code = $doHeader->container1_code;
                        $newSiDetail->container2_id = $doHeader->container2_id;
                        $newSiDetail->container2_code = $doHeader->container2_code;

                        $realQty = $doDetail->qty;
                        if (! empty($detail['real_qty']) && $doHeader->type == 'transfer') {
                            $realQty = MiscUtility::toFloat($detail['real_qty']);
                        }

                        $newSiDetail->real_qty = $realQty;

                        if (! empty($detail['remark'])) {
                            $newSiDetail->remark = $detail['remark'];
                        }

                        $newSiDetail->created_by = Auth::id();
                        $newSiDetail->updated_by = Auth::id();
                        $newSiDetail->save();

                        // Update relevant DOs to close
                        $doHeader->is_close = true;
                        $doHeader->closed_by_si_id = $newSiHeader->id;
                        $doHeader->save();

                        $divider = 1;
                        if (! empty($doHeader->container1_code) && ! empty($doHeader->container2_code)) {
                            $divider = 2;
                        }

                        // Create monitors
                        if (! empty($doHeader->container1_code)) {
                            $this->storeMonitor($newSiDetail, $doHeader->container1_code, $divider);
                        }

                        if (! empty($doHeader->container2_code)) {
                            $this->storeMonitor($newSiDetail, $doHeader->container2_code, $divider);
                        }
                    }
                }
            }

            // Process warehouse transfer DO
            if (! empty($detailsWt)) {
                foreach ($detailsWt as $detail) {
                    if (! empty($detail['do_id'])) {
                        $newSiDetail = new ShippingInstructionDetail();
                        $newSiDetail->header_id = $newSiHeader->id;
                        $newSiDetail->header_code = $newSiHeader->code;
                        $newSiDetail->delivery_type = $detail['delivery_type'];

                        $trucking1 = null;
                        $consigneeCode1 = null;
                        if (! empty($detail['trucking_1'])) {
                            $consigneeId = intval($detail['trucking_1']);
                            $newSiDetail->consignee_id = $consigneeId;

                            $consignee = DB::table('consignees')->where('id', '=', $consigneeId)->first();
                            $trucking1 = strtoupper($consignee->name);
                            $consigneeCode1 = strtoupper($consignee->code);
                        }

                        $trucking2 = null;
                        $consigneeCode2 = null;
                        if (! empty($detail['trucking_2'])) {
                            $consigneeId = intval($detail['trucking_2']);
                            $newSiDetail->consignee_id = $consigneeId;

                            $consignee = DB::table('consignees')->where('id', '=', $consigneeId)->first();
                            $trucking2 = strtoupper($consignee->name);
                            $consigneeCode2 = strtoupper($consignee->code);
                        }

                        $truckingInput = '';
                        $consigneeCodeInput = '';
                        if (! empty($trucking1) && ! empty($trucking2)) {
                            if ($trucking1 !== $trucking2) {
                                $truckingInput = $trucking1.', '.$trucking2;
                                $consigneeCodeInput = $consigneeCode1.', '.$consigneeCode2;
                            } else {
                                $truckingInput = $trucking1;
                                $consigneeCodeInput = $consigneeCode1;
                            }
                        } elseif (! empty($trucking1) && empty($trucking2)) {
                            $truckingInput = $trucking1;
                            $consigneeCodeInput = $consigneeCode1;
                        } elseif (empty($trucking1) && ! empty($trucking2)) {
                            $truckingInput = $trucking2;
                            $consigneeCodeInput = $consigneeCode2;
                        }

                        $newSiDetail->trucking = $truckingInput;
                        $newSiDetail->consignee_code = $consigneeCodeInput;

                        $doHeader = DoHeader::where('id', '=', $detail['do_id'])
                            ->first();

                        $newSiDetail->do_id = $detail['do_id'];
                        $newSiDetail->do_code = $doHeader->code;
                        $newSiDetail->do_type = $doHeader->type;
                        $newSiDetail->so_code = $doHeader->so_code;
                        $newSiDetail->dr_code = $doHeader->dr_code;
                        $newSiDetail->shipto_code = $doHeader->shipto_code;
                        $newSiDetail->shipto_name = $doHeader->shipto_name;

                        $doDetail = DoDetail::where('header_id', '=', $newSiDetail->do_id)
                            ->first();

                        $newSiDetail->item_id = $doDetail->item_id;
                        $newSiDetail->item_code = $doDetail->item_code;
                        $newSiDetail->item_name = $doDetail->item_name;
                        $newSiDetail->unit = $doDetail->unit;
                        $newSiDetail->qty = $doDetail->qty;
                        $newSiDetail->transportation = $doHeader->transportation;
                        $newSiDetail->container1_id = $doHeader->container1_id;
                        $newSiDetail->container1_code = $doHeader->container1_code;
                        $newSiDetail->container2_id = $doHeader->container2_id;
                        $newSiDetail->container2_code = $doHeader->container2_code;

                        $realQty1 = MiscUtility::toFloat($detail['real_qty_1']);
                        $realQty2 = MiscUtility::toFloat($detail['real_qty_1']);
                        $newSiDetail->real_qty = $realQty1 + $realQty2;

                        $newSiDetail->created_by = Auth::id();
                        $newSiDetail->updated_by = Auth::id();
                        $newSiDetail->save();

                        // Update relevant DOs to close
                        $doHeader->is_close = true;
                        $doHeader->closed_by_si_id = $newSiHeader->id;
                        $doHeader->save();

                        $divider = 1;
                        if (! empty($doHeader->container1_code) && ! empty($doHeader->container2_code)) {
                            $divider = 2;
                        }

                        // Create monitors
                        if (! empty($doHeader->container1_code)) {
                            $this->storeMonitorWt($newSiDetail, $doHeader->container1_code, $realQty1, $detail['trucking_1'], $detail['remark_1']);
                        }

                        if (! empty($doHeader->container2_code)) {
                            $this->storeMonitorWt($newSiDetail, $doHeader->container2_code, $realQty2, $detail['trucking_2'], $detail['remark_2']);
                        }
                    }
                }
            }

            return Response::json([
                'code' => 200,
                'message' => 'A new Shipping Instruction has been successfully created!',
                'error' => 'none',
                'result' => [
                    'show_url' => route('shipping_instruction.show', ['id' => $newSiHeader->id]),
                ],
            ], 200);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController store error EX: '.$ex);

            return Response::json([
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    public function storeMonitor(ShippingInstructionDetail $siDetail, $containerCode, $divider)
    {
        $newMonitor = new ShippingInstructionMonitor();
        $newMonitor->header_id = $siDetail->header_id;
        $newMonitor->header_code = $siDetail->header_code;
        $newMonitor->consignee_id = $siDetail->consignee_id;
        $newMonitor->consignee_code = $siDetail->consignee_code;
        $newMonitor->trucking = $siDetail->trucking;
        $newMonitor->delivery_type = $siDetail->delivery_type;
        $newMonitor->do_id = $siDetail->do_id;

        $doHeader = DB::table('do_headers')
            ->where('id', '=', $siDetail->do_id)
            ->first();

        $newMonitor->do_code = $siDetail->do_code;
        $newMonitor->do_date = $doHeader->date;
        $newMonitor->do_type = $siDetail->do_type;
        $newMonitor->so_code = $siDetail->so_code;
        $newMonitor->dr_code = $siDetail->dr_code;
        $newMonitor->shipto_code = $siDetail->shipto_code;
        $newMonitor->shipto_name = $siDetail->shipto_name;
        $newMonitor->item_id = $siDetail->item_id;
        $newMonitor->item_code = $siDetail->item_code;
        $newMonitor->item_name = $siDetail->item_name;
        $newMonitor->qty = $siDetail->qty / $divider;
        $newMonitor->real_qty = $siDetail->real_qty / $divider;
        $newMonitor->unit = $siDetail->unit;

        $container = DB::table('container_datas')
            ->where('code', '=', $containerCode)
            ->first();

        $newMonitor->container_id = $container->id;
        $newMonitor->container_code = $container->code;
        $newMonitor->transportation = $siDetail->transportation;
        $newMonitor->remark = $siDetail->remark;
        $newMonitor->status_id = 7;
        $newMonitor->is_sync = false;
        $newMonitor->save();

        $siHeader = $siDetail->shipping_instruction_header;
        $branch = Branch::find($siHeader->destination_branch_id);

        // Update container data
        DB::table('container_datas')
            ->where('code', '=', $containerCode)
            ->update([
                'si_code' => $siDetail->header_code,
                'si_destination_id' => $siHeader->destination_branch_id,
                'si_destination_name' => $siHeader->destination,
                'si_etd_date' => $siHeader->etd_date,
                'si_eta_date' => $siHeader->eta_date,
                'free_storage_duration' => $branch->free_storage_duration,
                'free_demurrage_duration' => $branch->free_demurrage_duration,
                'vessel_name' => $siHeader->vessel_name,
            ]);

        return true;
    }

    public function storeMonitorWt(ShippingInstructionDetail $siDetail, $containerCode, $qty, $trucking, $remark)
    {
        $newMonitor = new ShippingInstructionMonitor();
        $newMonitor->header_id = $siDetail->header_id;
        $newMonitor->header_code = $siDetail->header_code;

        $consignee = DB::table('consignees')
            ->where('id', '=', $trucking)
            ->first();

        $newMonitor->consignee_id = $consignee->id;
        $newMonitor->consignee_code = $consignee->code;
        $newMonitor->trucking = $consignee->name;
        $newMonitor->delivery_type = $siDetail->delivery_type;
        $newMonitor->do_id = $siDetail->do_id;

        $doHeader = DB::table('do_headers')
            ->where('id', '=', $siDetail->do_id)
            ->first();

        $newMonitor->do_code = $siDetail->do_code;
        $newMonitor->do_date = $doHeader->date;
        $newMonitor->do_type = $siDetail->do_type;
        $newMonitor->so_code = $siDetail->so_code;
        $newMonitor->dr_code = $siDetail->dr_code;
        $newMonitor->shipto_code = $siDetail->shipto_code;
        $newMonitor->shipto_name = $siDetail->shipto_name;
        $newMonitor->item_id = $siDetail->item_id;
        $newMonitor->item_code = $siDetail->item_code;
        $newMonitor->item_name = $siDetail->item_name;
        $newMonitor->qty = $qty;
        $newMonitor->real_qty = $qty;
        $newMonitor->unit = $siDetail->unit;

        $container = DB::table('container_datas')
            ->where('code', '=', $containerCode)
            ->first();

        $newMonitor->container_id = $container->id;
        $newMonitor->container_code = $container->code;
        $newMonitor->transportation = $siDetail->transportation;
        $newMonitor->remark = $remark;
        $newMonitor->status_id = 7;
        $newMonitor->is_sync = false;
        $newMonitor->save();

        $siHeader = $siDetail->shipping_instruction_header;
        $branch = Branch::find($siHeader->destination_branch_id);

        // Update container data
        DB::table('container_datas')
            ->where('code', '=', $containerCode)
            ->update([
                'si_code' => $siDetail->header_code,
                'si_destination_id' => $siHeader->destination_branch_id,
                'si_destination_name' => $siHeader->destination,
                'si_etd_date' => $siHeader->etd_date,
                'si_eta_date' => $siHeader->eta_date,
                'free_storage_duration' => $branch->free_storage_duration,
                'free_demurrage_duration' => $branch->free_demurrage_duration,
                'vessel_name' => $siHeader->vessel_name,
            ]);

        return true;
    }

    public function edit($id)
    {
        $siHeader = ShippingInstructionHeader::with(['shipping_instruction_details'])->find($id);
        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        $admin = Auth::user();

        if ($siHeader->status_id !== 7 && $siHeader->status_id !== 8) {
            return redirect()->route('shipping_instruction');
        }

        // Validate admin permission
        if ($admin->is_superadmin == 0) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            if (! in_array($siHeader->destination_branch_id, $branchIdArr)) {
                return redirect()->route('shipping_instruction');
            }
        }

        $detailListArr = [];
        foreach ($siHeader->shipping_instruction_details as $detail) {
            $doHeader = DB::table('do_headers')
                ->where('id', '=', $detail->do_id)
                ->first();

            if ($doHeader->type == 'transfer') {
                $tmpDetailWtArr[] = $detail;

                continue;
            }

            $detailListArr[] = [
                'key_id' => MiscUtility::generateRandomShortString(9),
                'id' => $detail->id,
                'do_id' => $detail->do_id,
                'do_code' => $detail->do_code,
                'delivery_type' => $detail->delivery_type,
                'trucking' => $detail->consignee_id,
                'consignee_name' => $detail->trucking,
                'so_no' => $detail->so_code,
                'shipto_name' => $detail->shipto_name,
                'container1' => $detail->container1_code ?? '',
                'container2' => $detail->container2_code ?? '',
                'consignee_select_url' => route('consignee.select2', ['branch_id' => $siHeader->destination_branch_id]),
                'do_select_url' => route('delivery_order.select2', ['branch_id' => $siHeader->destination_branch_id, 'is_edit' => 1, 'si_id' => $siHeader->id]),
                'is_initiated' => false,
                'is_real_qty_initiated' => false,
                'do_type' => $detail->do_type,
                'real_qty' => $detail->real_qty,
                'unit' => $detail->unit,
                'remark' => $detail->remark ?? '',
            ];
        }

        $detailListWtArr = [];
        if (! empty($tmpDetailWtArr)) {
            foreach ($tmpDetailWtArr as $detail) {
                $monitor1 = DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siHeader->id)
                    ->where('container_code', '=', $detail->container1_code)
                    ->first();

                $monitor2 = DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siHeader->id)
                    ->where('container_code', '=', $detail->container2_code)
                    ->first();

                $detailListWtArr[] = [
                    'key_id' => MiscUtility::generateRandomShortString(9),
                    'id' => $detail->id,
                    'do_id' => $detail->do_id,
                    'do_code' => $detail->do_code,
                    'delivery_type' => $detail->delivery_type,
                    'trucking_1' => $monitor1->consignee_id,
                    'consignee_name_1' => $monitor1->trucking,
                    'trucking_2' => $monitor2->consignee_id,
                    'consignee_name_2' => $monitor2->trucking,
                    'so_code' => $detail->so_code,
                    'shipto_name' => $detail->shipto_name,
                    'container1' => $detail->container1_code ?? '',
                    'container2' => $detail->container2_code ?? '',
                    'consignee_select_url' => route('consignee.select2', ['branch_id' => $siHeader->destination_branch_id]),
                    'item' => $detail->item_code,
                    'unit' => $detail->unit,
                    'real_qty_1' => $monitor1->real_qty,
                    'real_qty_2' => $monitor2->real_qty,
                    'remark_1' => $monitor1->remark,
                    'remark_2' => $monitor2->remark,
                ];
            }
        }

        $vueData = [
            'headerId' => $siHeader,
            'destination' => $siHeader->destination_branch_id,
            'detailList' => $detailListArr,
            'detailListWt' => $detailListWtArr,
        ];

        $etaDate = '-';
        if (! empty($siHeader->eta_date)) {
            $etaDate = Carbon::parse($siHeader->eta_date)->format('d-m-Y');
        }

        $etdDate = '-';
        if (! empty($siHeader->etd_date)) {
            $etdDate = Carbon::parse($siHeader->etd_date)->format('d-m-Y');
        }

        $data = [
            'vueData' => $vueData,
            'siHeader' => $siHeader,
            'etaDate' => $etaDate,
            'etdDate' => $etdDate,
        ];

        return view('shippings.shipping_instruction.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'shipper' => 'required|max:200',
                'vessel' => 'required|max:200',
                'eta_date' => 'required',
                'etd_date' => 'required',
                'pic_receiver' => 'required|max:200',
                'port_receiver' => 'required|max:200',
            ], [
                'shipper.required' => 'SHIPPER must be filled!',
                'shipper.max' => 'SHIPPER must be below 200 characters!',
                'vessel.required' => 'VESSEL must be filled!',
                'vessel.max' => 'VESSEL must be below 200 characters!',
                'eta_date.required' => 'ETA must be filled!',
                'etd_date.required' => 'ETD must be filled!',
                'pic_receiver.required' => 'ATTN must be filled!',
                'pic_receiver.max' => 'ATTN must be below 200 characters!',
                'port_receiver.required' => 'TO must be filled!',
                'port_receiver.max' => 'TO must be below 200 characters!',
            ]);

            if ($validator->fails()) {
                return response()->json(collect([
                    'code' => 422,
                    'error' => 'validation_failed',
                    'result' => $validator->errors()->all(),
                ]), 200);
            }

            $errors = [];

            // Validate ETD ETA
            $etaDate = Carbon::createFromFormat('!d-m-Y', $request->input('eta_date'), 'Asia/Jakarta');
            $etdDate = Carbon::createFromFormat('!d-m-Y', $request->input('etd_date'), 'Asia/Jakarta');
            if ($etdDate->greaterThanOrEqualTo($etaDate)) {
                $errors[] = 'ETD date must be earlier than ETA date';
            }

            if (! $request->filled('details') && ! $request->filled('detailsWt')) {
                return response()->json(collect([
                    'code' => 500,
                    'error' => 'special_validation_failed',
                    'result' => 'DO or Warehouse Transfer details must be inputted!',
                ]), 200);
            }

            $details = $request->input('details');
            $detailsWt = $request->input('detailsWt');

            // Validate inputted normal DO details
            if (! empty($details)) {
                $tmpDoIds = [];
                foreach ($details as $detail) {
                    if (! empty($detail['do_id'])) {
                        $tmpDo = DB::table('do_headers')
                            ->where('id', '=', $detail['do_id'])
                            ->first();

                        if (in_array($detail['do_id'], $tmpDoIds)) {
                            $errors[] = 'DO '.$tmpDo->code.' is duplicated!';
                        }
                        $tmpDoIds[] = $detail['do_id'];

                        if ($tmpDo->is_close == 1 && $tmpDo->closed_by_si_id != $id) {
                            $errors[] = 'DO '.$tmpDo->code.' is already registered in another SI!';
                        }

                        // if($tmpDo->is_close == 1){
                        //     $errors[] = 'DO '. $tmpDo->code. ' is already closed!';
                        // }

                        if ($detail['delivery_type'] == 'default') {
                            $errors[] = 'DO '.$tmpDo->code.' must select delivery type!';
                        }
                    }
                }
            }

            // Validate warehouse transfer details
            if (! empty($detailsWt)) {
                foreach ($detailsWt as $detail) {
                    $tmpDo = DB::table('do_headers')
                        ->where('id', '=', $detail['do_id'])
                        ->first();
                    // if($tmpDo->is_close == 1){
                    //     $errors[] = 'Warehouse Transfer '. $tmpDo->code. ' is already closed!';
                    // }

                    if ($tmpDo->is_close == 1 && $tmpDo->closed_by_si_id != $id) {
                        $errors[] = 'Warehouse Transfer '.$tmpDo->code.' is already registered in another SI!';
                    }

                    if ($detail['delivery_type'] == 'default') {
                        $errors[] = 'Warehouse Transfer '.$tmpDo->code.' must select delivery type!';
                    }

                    if (empty($detail['trucking_1']) || empty($detail['trucking_2'])) {
                        $errors[] = 'Warehouse Transfer '.$tmpDo->code.' must input consignee for each container!';
                    }
                }
            }

            if (count($errors) > 0) {
                return response()->json(collect([
                    'code' => 400,
                    'error' => 'validation_failed',
                    'result' => $errors,
                ]), 200);
            }

            $siHeader = ShippingInstructionHeader::find($id);
            $siHeader->vessel_name = strtoupper($request->input('vessel'));

            $branchId = intval($request->input('destination'));
            $branch = DB::table('branches')->where('id', '=', $branchId)->first();

            $siHeader->destination_branch_id = $branchId;
            $siHeader->destination = strtoupper($branch->name);
            $siHeader->pic_receiver = strtoupper($request->input('pic_receiver'));
            $siHeader->port_receiver = strtoupper($request->input('port_receiver'));
            $siHeader->eta_date = $etaDate;
            $siHeader->etd_date = $etdDate;
            $siHeader->updated_by = Auth::id();
            $siHeader->save();

            // Check for deleted SI details
            $existingIds[] = [];
            if (! empty($details)) {
                foreach ($details as $detail) {
                    if ($detail['id'] > 0) {
                        $existingIds[] = $detail['id'];
                    }
                }
            }

            foreach ($siHeader->shipping_instruction_details as $detail) {
                if (! in_array($detail->id, $existingIds)) {
                    // Revert is_close
                    DB::table('do_headers')
                        ->where('id', '=', $detail->do_id)
                        ->update([
                            'is_close' => 0,
                            'closed_by_si_id' => null,
                        ]);

                    $detail->delete();
                }
            }

            if (! empty($details)) {
                foreach ($details as $detail) {
                    if ($detail['id'] > 0) {
                        $siDetail = ShippingInstructionDetail::find($detail['id']);
                        $siDetail->delivery_type = $detail['delivery_type'];

                        if (! empty($detail['trucking'])) {
                            $consigneeId = intval($detail['trucking']);
                            $siDetail->consignee_id = $consigneeId;

                            $consignee = DB::table('consignees')->where('id', '=', $consigneeId)->first();
                            $siDetail->consignee_code = strtoupper($consignee->code);
                            $siDetail->trucking = strtoupper($consignee->name);
                        } else {
                            $siDetail->consignee_id = null;
                            $siDetail->consignee_code = null;
                            $siDetail->trucking = null;
                        }

                        $doHeader = DoHeader::where('id', '=', $detail['do_id'])
                            ->first();

                        $siDetail->do_id = $detail['do_id'];
                        $siDetail->do_code = $doHeader->code;
                        $siDetail->do_type = $doHeader->type;
                        $siDetail->so_code = $doHeader->so_code;
                        $siDetail->shipto_code = $doHeader->shipto_code;
                        $siDetail->shipto_name = $doHeader->shipto_name;

                        $doDetail = DoDetail::where('header_id', '=', $detail['do_id'])
                            ->first();

                        $siDetail->item_id = $doDetail->item_id;
                        $siDetail->item_code = $doDetail->item_code;
                        $siDetail->item_name = $doDetail->item_name;
                        $siDetail->unit = $doDetail->unit;
                        $siDetail->qty = $doDetail->qty;
                        $siDetail->transportation = $doHeader->transportation;
                        $siDetail->transportation = $doHeader->transportation;
                        $siDetail->container1_id = $doHeader->container1_id;
                        $siDetail->container1_code = $doHeader->container1_code;
                        $siDetail->container2_id = $doHeader->container2_id;
                        $siDetail->container2_code = $doHeader->container2_code;

                        $realQty = $doDetail->qty;
                        if (! empty($detail['real_qty']) && $doHeader->type == 'transfer') {
                            $realQty = MiscUtility::toFloat($detail['real_qty']);
                        }

                        $siDetail->real_qty = $realQty;

                        if (! empty($detail['remark'])) {
                            $siDetail->remark = $detail['remark'];
                        } else {
                            $siDetail->remark = '';
                        }

                        $siDetail->updated_by = Auth::id();
                        $siDetail->save();

                        // Update relevant DOs to close
                        $doHeader->is_close = true;
                        $doHeader->closed_by_si_id = $siHeader->id;
                        $doHeader->save();
                    } else {
                        if (! empty($detail['do_id'])) {
                            $newSiDetail = new ShippingInstructionDetail();
                            $newSiDetail->header_id = $id;
                            $newSiDetail->header_code = $siHeader->code;
                            $newSiDetail->delivery_type = $detail['delivery_type'];

                            if (! empty($detail['trucking'])) {
                                $consigneeId = intval($detail['trucking']);
                                $newSiDetail->consignee_id = $consigneeId;

                                $consignee = DB::table('consignees')->where('id', '=', $consigneeId)->first();
                                $newSiDetail->consignee_code = strtoupper($consignee->code);
                                $newSiDetail->trucking = strtoupper($consignee->name);
                            }

                            $doHeader = DoHeader::where('id', '=', $detail['do_id'])
                                ->first();

                            $newSiDetail->do_id = $detail['do_id'];
                            $newSiDetail->do_code = $doHeader->code;
                            $newSiDetail->so_code = $doHeader->so_code;
                            $newSiDetail->shipto_code = $doHeader->shipto_code;
                            $newSiDetail->shipto_name = $doHeader->shipto_name;

                            $doDetail = DoDetail::where('header_id', '=', $newSiDetail->do_id)
                                ->first();

                            $newSiDetail->item_id = $doDetail->item_id;
                            $newSiDetail->item_code = $doDetail->item_code;
                            $newSiDetail->item_name = $doDetail->item_name;
                            $newSiDetail->unit = $doDetail->unit;
                            $newSiDetail->qty = $doDetail->qty;
                            $newSiDetail->transportation = $doHeader->transportation;
                            $newSiDetail->transportation = $doHeader->transportation;
                            $newSiDetail->container1_id = $doHeader->container1_id;
                            $newSiDetail->container1_code = $doHeader->container1_code;
                            $newSiDetail->container2_id = $doHeader->container2_id;
                            $newSiDetail->container2_code = $doHeader->container2_code;

                            $realQty = $doDetail->qty;
                            if (! empty($detail['real_qty']) && $doHeader->type == 'transfer') {
                                $realQty = MiscUtility::toFloat($detail['real_qty']);
                            }

                            $newSiDetail->real_qty = $realQty;

                            if (! empty($detail['remark'])) {
                                $newSiDetail->remark = $detail['remark'];
                            }

                            $newSiDetail->created_by = Auth::id();
                            $newSiDetail->updated_by = Auth::id();
                            $newSiDetail->save();

                            // Update relevant DOs to close
                            $doHeader->is_close = true;
                            $doHeader->closed_by_si_id = $siHeader->id;
                            $doHeader->save();
                        }
                    }
                }
            }

            // Recreate shipping monitors
            DB::table('shipping_instruction_monitors')
                ->where('header_id', '=', $id)
                ->delete();

            $updatedSiDetails = DB::table('shipping_instruction_details')
                ->where('header_id', '=', $id)
                ->get();

            $tmpSiDetailsWt = [];
            foreach ($updatedSiDetails as $updatedSiDetail) {
                $tmpSiDetail = ShippingInstructionDetail::find($updatedSiDetail->id);
                $doHeader = DoHeader::find($tmpSiDetail->do_id);

                // Skip transfer type to process later
                if ($doHeader->type == 'transfer') {
                    $tmpSiDetailsWt[] = $updatedSiDetail;
                }

                $divider = 1;
                if (! empty($doHeader->container1_code) && ! empty($doHeader->container2_code)) {
                    $divider = 2;
                }

                if (! empty($doHeader->container1_code)) {
                    $this->storeMonitor($tmpSiDetail, $doHeader->container1_code, $divider);
                }

                if (! empty($doHeader->container2_code)) {
                    $this->storeMonitor($tmpSiDetail, $doHeader->container2_code, $divider);
                }
            }

            // Recreate DO transfer type shipping details
            if (! empty($tmpSiDetailsWt)) {
                foreach ($tmpSiDetailsWt as $tmpSiDetail) {
                    DB::table('shipping_instruction_details')
                        ->where('id', '=', $tmpSiDetail->id)
                        ->delete();
                }
            }

            // Process warehouse transfer DO
            if (! empty($detailsWt)) {
                foreach ($detailsWt as $detail) {
                    if (! empty($detail['do_id'])) {
                        $newSiDetail = new ShippingInstructionDetail();
                        $newSiDetail->header_id = $siHeader->id;
                        $newSiDetail->header_code = $siHeader->code;
                        $newSiDetail->delivery_type = $detail['delivery_type'];

                        $trucking1 = null;
                        $consigneeCode1 = null;
                        if (! empty($detail['trucking_1'])) {
                            $consigneeId = intval($detail['trucking_1']);
                            $newSiDetail->consignee_id = $consigneeId;

                            $consignee = DB::table('consignees')->where('id', '=', $consigneeId)->first();
                            $trucking1 = strtoupper($consignee->name);
                            $consigneeCode1 = strtoupper($consignee->code);
                        }

                        $trucking2 = null;
                        $consigneeCode2 = null;
                        if (! empty($detail['trucking_2'])) {
                            $consigneeId = intval($detail['trucking_2']);
                            $newSiDetail->consignee_id = $consigneeId;

                            $consignee = DB::table('consignees')->where('id', '=', $consigneeId)->first();
                            $trucking2 = strtoupper($consignee->name);
                            $consigneeCode2 = strtoupper($consignee->code);
                        }

                        $truckingInput = '';
                        $consigneeCodeInput = '';
                        if (! empty($trucking1) && ! empty($trucking2)) {
                            if ($trucking1 !== $trucking2) {
                                $truckingInput = $trucking1.', '.$trucking2;
                                $consigneeCodeInput = $consigneeCode1.', '.$consigneeCode2;
                            } else {
                                $truckingInput = $trucking1;
                                $consigneeCodeInput = $consigneeCode1;
                            }
                        } elseif (! empty($trucking1) && empty($trucking2)) {
                            $truckingInput = $trucking1;
                            $consigneeCodeInput = $consigneeCode1;
                        } elseif (empty($trucking1) && ! empty($trucking2)) {
                            $truckingInput = $trucking2;
                            $consigneeCodeInput = $consigneeCode2;
                        }

                        $newSiDetail->trucking = $truckingInput;
                        $newSiDetail->consignee_code = $consigneeCodeInput;

                        $doHeader = DoHeader::where('id', '=', $detail['do_id'])
                            ->first();

                        $newSiDetail->do_id = $detail['do_id'];
                        $newSiDetail->do_code = $doHeader->code;
                        $newSiDetail->do_type = $doHeader->type;
                        $newSiDetail->so_code = $doHeader->so_code;
                        $newSiDetail->dr_code = $doHeader->dr_code;
                        $newSiDetail->shipto_code = $doHeader->shipto_code;
                        $newSiDetail->shipto_name = $doHeader->shipto_name;

                        $doDetail = DoDetail::where('header_id', '=', $newSiDetail->do_id)
                            ->first();

                        $newSiDetail->item_id = $doDetail->item_id;
                        $newSiDetail->item_code = $doDetail->item_code;
                        $newSiDetail->item_name = $doDetail->item_name;
                        $newSiDetail->unit = $doDetail->unit;
                        $newSiDetail->qty = $doDetail->qty;
                        $newSiDetail->transportation = $doHeader->transportation;
                        $newSiDetail->container1_id = $doHeader->container1_id;
                        $newSiDetail->container1_code = $doHeader->container1_code;
                        $newSiDetail->container2_id = $doHeader->container2_id;
                        $newSiDetail->container2_code = $doHeader->container2_code;

                        $realQty1 = MiscUtility::toFloat($detail['real_qty_1']);
                        $realQty2 = MiscUtility::toFloat($detail['real_qty_1']);
                        $newSiDetail->real_qty = $realQty1 + $realQty2;

                        $newSiDetail->created_by = Auth::id();
                        $newSiDetail->updated_by = Auth::id();
                        $newSiDetail->save();

                        // Update relevant DOs to close
                        $doHeader->is_close = true;
                        $doHeader->closed_by_si_id = $siHeader->id;
                        $doHeader->save();

                        // Create monitors
                        if (! empty($doHeader->container1_code)) {
                            $this->storeMonitorWt($newSiDetail, $doHeader->container1_code, $realQty1, $detail['trucking_1'], $detail['remark_1']);
                        }

                        if (! empty($doHeader->container2_code)) {
                            $this->storeMonitorWt($newSiDetail, $doHeader->container2_code, $realQty2, $detail['trucking_2'], $detail['remark_2']);
                        }
                    }
                }
            }

            return Response::json([
                'code' => 200,
                'message' => 'Shipping Instruction has been successfully updated!',
                'error' => 'none',
                'result' => [
                    'show_url' => route('shipping_instruction.show', ['id' => $id]),
                ],
            ], 200);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController update error EX: '.$ex);

            return Response::json([
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $siHeader = ShippingInstructionHeader::with(['shipping_instruction_details'])->find($id);
        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        // Validate admin permission
        $admin = Auth::user();
        if ($admin->is_superadmin == 0) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            if (! in_array($siHeader->destination_branch_id, $branchIdArr)) {
                return redirect()->route('shipping_instruction');
            }
        }

        $detailListArr = [];
        $tmpDetailWtArr = [];
        foreach ($siHeader->shipping_instruction_details as $detail) {
            $doHeader = DB::table('do_headers')
                ->where('id', '=', $detail->do_id)
                ->first();

            if ($doHeader->type == 'transfer') {
                $tmpDetailWtArr[] = $detail;

                continue;
            }

            $detailListArr[] = [
                'id' => $detail->id,
                'do_id' => $detail->do_id,
                'do_code' => $detail->do_code,
                'delivery_type' => $detail->delivery_type,
                'trucking' => $detail->trucking,
                'so_code' => $detail->so_code,
                'shipto_name' => $detail->shipto_name,
                'container1' => $detail->container1_code ?? '',
                'container2' => $detail->container2_code ?? '',
                'item' => $detail->item_code,
                'unit' => $detail->unit,
                'qty' => $detail->real_qty,
                'remark' => $detail->remark,
            ];
        }

        $detailListWtArr = [];
        if (! empty($tmpDetailWtArr)) {
            foreach ($tmpDetailWtArr as $detail) {
                $monitor1 = DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siHeader->id)
                    ->where('container_code', '=', $detail->container1_code)
                    ->first();

                $monitor2 = DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siHeader->id)
                    ->where('container_code', '=', $detail->container2_code)
                    ->first();

                $detailListWtArr[] = [
                    'id' => $detail->id,
                    'do_id' => $detail->do_id,
                    'do_code' => $detail->do_code,
                    'delivery_type' => $detail->delivery_type,
                    'trucking_1' => $monitor1->trucking,
                    'trucking_2' => $monitor2->trucking,
                    'so_code' => $detail->so_code,
                    'shipto_name' => $detail->shipto_name,
                    'container1' => $detail->container1_code ?? '',
                    'container2' => $detail->container2_code ?? '',
                    'item' => $detail->item_code,
                    'unit' => $detail->unit,
                    'real_qty_1' => $monitor1->real_qty,
                    'real_qty_2' => $monitor2->real_qty,
                    'remark_1' => $monitor1->remark,
                    'remark_2' => $monitor2->remark,
                ];
            }
        }

        $vueData = [
            'detailList' => $detailListArr,
            'detailListWt' => $detailListWtArr,
        ];

        $etaDate = '-';
        if (! empty($siHeader->eta_date)) {
            $etaDate = Carbon::parse($siHeader->eta_date)->format('d-m-Y');
        }

        $etdDate = '-';
        $sailingMaxDate = '';
        if (! empty($siHeader->etd_date)) {
            $etdDate = Carbon::parse($siHeader->etd_date)->format('d-m-Y');
            $sailingMaxDate = Carbon::today('Asia/Jakarta')->addDays(90)->format('d-m-Y');
        }

        $sailingDate = '-';
        $berthMaxDate = '';
        if (! empty($siHeader->sailing_date)) {
            $sailingDate = Carbon::parse($siHeader->sailing_date)->format('d-m-Y');
            $berthMaxDate = Carbon::today('Asia/Jakarta')->addDays(14)->format('d-m-Y');
        }

        $dockingDate = '-';
        if (! empty($siHeader->docking_date)) {
            $dockingDate = Carbon::parse($siHeader->docking_date)->format('d-m-Y');
        }

        $dooringDate = '-';
        if (! empty($siHeader->dooring_at)) {
            $dooringDate = Carbon::parse($siHeader->dooring_at)->format('d-m-Y');
        }

        $receivedDate = '-';
        if (! empty($siHeader->received_at)) {
            $receivedDate = Carbon::parse($siHeader->received_at)->format('d-m-Y');
        }

        $voidDate = ! empty($siHeader->voided_at) ? Carbon::parse($siHeader->voided_at)->format('d-m-Y') : '';

        $data = [
            'vueData' => $vueData,
            'siHeader' => $siHeader,
            'sailingMaxDate' => $sailingMaxDate,
            'sailingDate' => $sailingDate,
            'berthMaxDate' => $berthMaxDate,
            'dockingDate' => $dockingDate,
            'dooringDate' => $dooringDate,
            'receivedDate' => $receivedDate,
            'etaDate' => $etaDate,
            'etdDate' => $etdDate,
            'todayDate' => Carbon::today('Asia/Jakarta')->format('d-m-Y'),
            'voidDate' => $voidDate,
        ];

        return view('shippings.shipping_instruction.show')->with($data);
    }

    public function operation($id, $status)
    {
        $siHeader = ShippingInstructionHeader::with(['shipping_instruction_monitors', 'shipping_instruction_monitors.status'])->find($id);
        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        $admin = Auth::user();

        // Validate admin permission
        if ($admin->is_superadmin == 0) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            if (! in_array($siHeader->destination_branch_id, $branchIdArr)) {
                return redirect()->route('shipping_instruction');
            }
        }

        $detailListArr = [];
        foreach ($siHeader->shipping_instruction_monitors as $detail) {
            if ($status === 'berthing' && $detail->status_id !== 9) {
                continue;
            }
            if ($status === 'dooring' && $detail->status_id !== 13) {
                continue;
            }
            if ($status === 'received' && $detail->status_id !== 18) {
                continue;
            }

            $dooringDate = '-';
            if (! empty($detail->dooring_at)) {
                $dooringDate = Carbon::parse($detail->dooring_at)->format('d-m-Y');
            }

            $receivedDate = '-';
            if (! empty($detail->received_at)) {
                $receivedDate = Carbon::parse($detail->received_at)->format('d-m-Y');
            }

            $berthingDate = '-';
            if (! empty($detail->berthing_at)) {
                $berthingDate = Carbon::parse($detail->berthing_at)->format('d-m-Y');
            }

            $inputBerthingMinDate = '';
            $inputBerthingMaxDate = '';
            if ($status === 'dooring') {
                $inputBerthingMinDate = Carbon::parse($detail->dooring_at)->format('d-m-Y');
                $inputBerthingMaxDate = Carbon::parse($detail->dooring_at)->addDays(90)->format('d-m-Y');
            }

            $detailListArr[] = [
                'checked' => false,
                'input_date' => '',
                'id' => $detail->id,
                'do_code' => $detail->do_code,
                'delivery_type' => $detail->delivery_type,
                'trucking' => $detail->trucking,
                'shipto_name' => $detail->shipto_name,
                'container_code' => $detail->container_code ?? '',
                'item' => $detail->item_code.' ('.$detail->item_name.')',
                'unit' => $detail->unit,
                'qty' => $detail->real_qty,
                'status' => $detail->status->display,
                'dooring_date' => $dooringDate,
                'received_date' => $receivedDate,
                'berthing_date' => $berthingDate,
                'input_min_date' => $inputBerthingMinDate,
                'input_max_date' => $inputBerthingMaxDate,
                'vessel_name' => $siHeader->vessel_name,
            ];
        }

        $vueData = [
            'detailList' => $detailListArr,
        ];

        $etaDate = '-';
        if (! empty($siHeader->eta_date)) {
            $etaDate = Carbon::parse($siHeader->eta_date)->format('d-m-Y');
        }

        $etdDate = '-';
        if (! empty($siHeader->etd_date)) {
            $etdDate = Carbon::parse($siHeader->etd_date)->format('d-m-Y');
        }

        $sailingDate = '-';
        if (! empty($siHeader->sailing_date)) {
            $sailingDate = Carbon::parse($siHeader->sailing_date)->format('d-m-Y');
        }

        $dockingDate = '-';
        if (! empty($siHeader->docking_date)) {
            $dockingDate = Carbon::parse($siHeader->docking_date)->format('d-m-Y');
        }

        $inputDooringMinDate = '';
        $inputDooringMaxDate = '';
        if ($status === 'berthing') {
            $inputDooringMinDate = Carbon::parse($siHeader->docking_date)->format('d-m-Y');
            $inputDooringMaxDate = Carbon::parse($siHeader->docking_date)->addDays(90)->format('d-m-Y');
        }

        $data = [
            'vueData' => $vueData,
            'siHeader' => $siHeader,
            'sailingDate' => $sailingDate,
            'dockingDate' => $dockingDate,
            'etaDate' => $etaDate,
            'etdDate' => $etdDate,
            'todayDate' => Carbon::today('Asia/Jakarta')->format('d-m-Y'),
            'inputDooringMinDate' => $inputDooringMinDate,
            'inputDooringMaxDate' => $inputDooringMaxDate,
        ];

        if ($status === 'all') {
            return view('shippings.shipping_instruction.operation_all')->with($data);
        } elseif ($status === 'berthing') {
            return view('shippings.shipping_instruction.operation_pending')->with($data);
        } elseif ($status === 'dooring') {
            return view('shippings.shipping_instruction.operation_dooring')->with($data);
        } elseif ($status === 'received') {
            return view('shippings.shipping_instruction.operation_received')->with($data);
        } else {
            return redirect()->route('shipping_instruction.show', ['id' => $id]);
        }
    }

    public function updateOperation(Request $request, $id, $status)
    {
        try {
            $siHeader = DB::table('shipping_instruction_headers')
                ->where('id', '=', $id)
                ->first();

            if (empty($siHeader)) {
                return redirect()->back();
            }

            if (! $request->filled('checkboxes')) {
                Session::flash('error', 'Invalid input!');

                return redirect()->back();
            }

            if ($status === 'dooring') {
                $updateStatusId = 13;
                $redirectStatus = 'berthing';
            } elseif ($status === 'received') {
                $updateStatusId = 18;
                $redirectStatus = 'dooring';
            } else {
                return redirect()->back();
            }

            $ids = $request->input('checkboxes');
            $inputDates = $request->input('input_dates');
            $firstDooringDate = '';
            $lastReceivedDate = '';

            if (! DB::table('shipping_instruction_monitors')
                ->where('header_id', '=', $siHeader->id)
                ->whereNotNull('dooring_at')
                ->exists()) {
                $firstDooringDate = Carbon::parse($inputDates[0])->toDateTimeString();
            } else {
                $tmpMonitor = DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siHeader->id)
                    ->whereNotNull('dooring_at')
                    ->orderBy('dooring_at')
                    ->first();
                $firstDooringDate = $tmpMonitor->dooring_at;
            }

            $lastArrNumber = count($inputDates) - 1;
            $lastReceivedDate = Carbon::parse($inputDates[$lastArrNumber])->toDateTimeString();

            $idx = 0;
            foreach ($ids as $monitorId) {
                $dooringAt = '';
                $receivedAt = '';
                $monitor = ShippingInstructionMonitor::find($monitorId);

                // Update monitor
                $monitor->status_id = $updateStatusId;
                $monitor->status_updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();

                if ($updateStatusId == 13) {
                    $dooringAt = Carbon::parse($inputDates[$idx])->toDateTimeString();

                    $monitor->dooring_at = $dooringAt;
                } elseif ($updateStatusId == 18) {
                    $receivedAt = Carbon::parse($inputDates[$idx])->toDateTimeString();

                    $monitor->received_at = $receivedAt;
                }

                $monitor->save();

                // Update master
                $containerMaster = ContainerData::where('code', '=', $monitor->container_code)->first();

                $containerMaster->status_id = $updateStatusId;
                if ($updateStatusId == 18) {
                    $containerMaster->warehouse_location_id = 18;
                    $containerMaster->warehouse_location_status = 'RECEIVED AT WH';

                    // End free storage time
                    $containerMaster->is_storage_monitor_end = true;

                    $containerMaster->received_at = $receivedAt;
                    $containerMaster->status_id = 12;
                    $containerMaster->returned_at = $receivedAt;
                    $containerMaster->is_demurrage_monitor_end = true;
                } elseif ($updateStatusId == 13) {
                    $containerMaster->warehouse_location_status = 'DOORING';
                    $containerMaster->dooring_at = $dooringAt;
                    $containerMaster->status_id = 13;
                }

                $containerMaster->save();

                // Save log
                $newLog = new ContainerLog();
                $newLog->container_id = $containerMaster->id;
                $newLog->reference_id = $id;
                $newLog->reference_code = $siHeader->code;
                $newLog->reference_type = 'SI';
                $newLog->created_by = Auth::id();
                $newLog->updated_by = Auth::id();
                $newLog->save();

                $idx++;
            }

            // Check condition to set SI status to DOORING
            if ($updateStatusId == 13 && $siHeader->status_id == 9) {
                DB::table('shipping_instruction_headers')
                    ->where('id', '=', $id)
                    ->update([
                        'status_id' => 13,
                        'dooring_at' => $firstDooringDate,
                        'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    ]);
            }

            // Check if all monitor status DOORING OR RECEIVED AT WH
            if ($updateStatusId == 18) {
                // Check SI status
                $updatedSiMonitors = DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $id)
                    ->get();

                $isAllUpdated = true;
                foreach ($updatedSiMonitors as $siMonitor) {
                    if ($siMonitor->status_id !== $updateStatusId) {
                        $isAllUpdated = false;
                        break;
                    }
                }

                if ($isAllUpdated) {
                    $tmpReceivedDate = $lastReceivedDate;

                    DB::table('shipping_instruction_headers')
                        ->where('id', '=', $id)
                        ->update([
                            'status_id' => $updateStatusId,
                            'received_at' => $tmpReceivedDate,
                            'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                        ]);
                }
            }

            // Recount total container
            ShippingInstructionHeader::recountContainerTotals($id);

            Session::flash('success', 'Update container status success!');

            return redirect()->route('shipping_instruction.operation', ['id' => $id, 'status' => $redirectStatus]);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController updateOperation error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $statusUpdateId = intval($request->input('status_update_id'));
            $updateDate = Carbon::createFromFormat('!d-m-Y', $request->input('update_date'), 'Asia/Jakarta');

            $siHeader = ShippingInstructionHeader::find($id);
            if ($siHeader->status_id == 7 && $statusUpdateId !== 8) {
                Session::flash('error', 'Invalid Status!');

                return redirect()->back();
            }

            if ($siHeader->status_id == 8 && $statusUpdateId !== 9) {
                Session::flash('error', 'Invalid Status!');

                return redirect()->back();
            }

            $siHeader->status_id = $statusUpdateId;

            if ($statusUpdateId == 8) {
                $siHeader->sailing_date = $updateDate->toDateTimeString();

                // Update monitors
                $siMonitors = ShippingInstructionMonitor::where('header_id', '=', $id)->get();
                foreach ($siMonitors as $siMonitor) {
                    // Update master
                    $containerMaster = ContainerData::where('code', '=', $siMonitor->container_code)->first();
                    $containerMaster->status_id = 8;
                    $containerMaster->last_status_updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
                    $containerMaster->save();
                }
            } elseif ($statusUpdateId == 9) {
                $siHeader->docking_date = $updateDate->toDateTimeString();
                $siHeader->is_sync = true;

                // Update monitors
                $berthingCount = 0;
                $siMonitors = ShippingInstructionMonitor::where('header_id', '=', $id)->get();
                foreach ($siMonitors as $siMonitor) {
                    $berthingCount++;

                    $siMonitor->status_id = 9;
                    $siMonitor->berthing_at = $updateDate->toDateTimeString();
                    $siMonitor->status_updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
                    $siMonitor->save();

                    // Update master
                    $containerMaster = ContainerData::where('code', '=', $siMonitor->container_code)->first();
                    $containerMaster->berthing_at = $updateDate->toDateTimeString();

                    // Start free storage time
                    $storageStartDate = $updateDate->copy();
                    $containerMaster->is_storage_monitor_start = true;
                    $containerMaster->free_storage_start_at = $storageStartDate->addDay()->startOfDay()->toDateTimeString();
                    $containerMaster->free_storage_end_at = $storageStartDate->copy()
                        ->addDays($containerMaster->free_storage_duration)
                        ->toDateTimeString();

                    // Start free demurrage time
                    $containerMaster->is_demurrage_monitor_start = true;
                    $containerMaster->free_demurrage_start_at = $storageStartDate->addDay()->startOfDay()->toDateTimeString();
                    $containerMaster->free_demurrage_end_at = $storageStartDate->copy()
                        ->addDays($containerMaster->free_demurrage_duration)
                        ->toDateTimeString();

                    $containerMaster->status_id = 9;
                    $containerMaster->last_status_updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();

                    $containerMaster->is_sync = true;
                    $containerMaster->save();
                }

                $siHeader->total_berthing_cont = $berthingCount;
            }

            $siHeader->save();

            Session::flash('success', 'Status Updated!');

            return redirect()->route('shipping_instruction.show', ['id' => $id]);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController updateStatus error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function updateBerthingDate(Request $request, $id)
    {
        try {
            $editedDate = Carbon::createFromFormat('!d-m-Y', $request->input('update_date'), 'Asia/Jakarta');

            $siHeader = ShippingInstructionHeader::find($id);

            if ($siHeader->status_id != 9) {
                Session::flash('error', 'Invalid Status!');

                return redirect()->back();
            }

            if ($siHeader->docking_date == $editedDate->toDateTimeString()) {
                Session::flash('success', 'Date not edited!');

                return redirect()->route('shipping_instruction.show', ['id' => $id]);
            }

            $siHeader->docking_date = $editedDate->toDateTimeString();

            // Update monitors
            $siMonitors = ShippingInstructionMonitor::where('header_id', '=', $id)->get();
            foreach ($siMonitors as $siMonitor) {
                $siMonitor->berthing_at = $editedDate->toDateTimeString();
                $siMonitor->save();

                // Update master
                $containerMaster = ContainerData::where('code', '=', $siMonitor->container_code)->first();
                $containerMaster->berthing_at = $editedDate->toDateTimeString();

                // Start free storage time
                $storageStartDate = $editedDate->copy();
                $containerMaster->free_storage_start_at = $storageStartDate->addDay()->startOfDay()->toDateTimeString();
                $containerMaster->free_storage_end_at = $storageStartDate->copy()->addDays($containerMaster->free_storage_duration)->toDateTimeString();
                $containerMaster->save();
            }

            $siHeader->save();

            Session::flash('success', 'Berthing date successfully edited!');

            return redirect()->route('shipping_instruction.show', ['id' => $id]);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController updateBerthingDate error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function updateSailingDate(Request $request, $id)
    {
        try {
            $editedDate = Carbon::createFromFormat('!d-m-Y', $request->input('update_sailing_date'), 'Asia/Jakarta');

            $siHeader = ShippingInstructionHeader::find($id);
            if ($siHeader->status_id != 8) {
                Session::flash('error', 'Invalid Status!');

                return redirect()->back();
            }

            if ($siHeader->sailing_date == $editedDate->toDateTimeString()) {
                Session::flash('success', 'Date not edited!');

                return redirect()->route('shipping_instruction.show', ['id' => $id]);
            }

            $siHeader->sailing_date = $editedDate->toDateTimeString();
            $siHeader->save();

            Session::flash('success', 'Sailing date successfully edited!');

            return redirect()->route('shipping_instruction.show', ['id' => $id]);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController updateSailingDate error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function select2Company(Request $request)
    {
        $search = trim($request->search);

        $companies = DB::table('do_headers')
            ->select('shipping_company')
            ->where('shipping_company', 'LIKE', '%'.$search.'%')
            ->distinct()
            ->get();

        $results = [];
        foreach ($companies as $company) {
            $results[] = ['id' => $company->shipping_company, 'text' => $company->shipping_company];
        }

        return Response::json($results);
    }

    public function printPreview($id)
    {
        $siHeader = ShippingInstructionHeader::with(['shipping_instruction_monitors'])->find($id);
        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        $etdDate = Carbon::parse($siHeader->etd_date)->format('d-M-Y');
        $etaDate = Carbon::parse($siHeader->eta_date)->format('d-M-Y');

        $manifestNumber = DB::table('shipping_instruction_monitors')
            ->where('header_id', '=', $id)
            ->count();

        $data = [
            'siHeader' => $siHeader,
            'etdDate' => $etdDate,
            'etaDate' => $etaDate,
            'manifestNumber' => $manifestNumber.' x 20"',
        ];

        return view('shippings.shipping_instruction.print')->with($data);
    }

    public function exportFinalDocument($id)
    {
        $siHeader = DB::table('shipping_instruction_headers')
            ->where('id', '=', $id)
            ->first();

        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        return Excel::download(new ShippingInstructionFinalExport($id), 'SI FINAL '.$siHeader->code.'.xlsx');
    }

    public function exportReport($id)
    {
        $siHeader = DB::table('shipping_instruction_headers')
            ->where('id', '=', $id)
            ->first();

        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        return Excel::download(new CementReceiveReportExport($id), 'LAPORAN PENERIMAAN SEMEN SI '.$siHeader->code.'.xlsx');
    }

    public function exportList(Request $request)
    {
        try {
            $data = DB::table('shipping_instruction_headers')
                ->join('statuses', 'shipping_instruction_headers.status_id', '=', 'statuses.id')
                ->select('shipping_instruction_headers.code as code',
                    'shipping_instruction_headers.destination as destination',
                    'shipping_instruction_headers.etd_date as etd_date',
                    'shipping_instruction_headers.eta_date as eta_date',
                    'statuses.display as status_display',
                    'shipping_instruction_headers.shipper as shipper',
                    'shipping_instruction_headers.vessel_name as vessel_name',
                    'shipping_instruction_headers.sailing_date as sailing_date',
                    'shipping_instruction_headers.docking_date as docking_date',
                    'shipping_instruction_headers.dooring_at as dooring_at',
                    'shipping_instruction_headers.received_at as received_at');

            if ($request->get('start_date') || $request->get('end_date')) {
                // Filter date
                $start = Carbon::createFromFormat('!d-m-Y', $request->get('start_date'), 'Asia/Jakarta');
                $end = Carbon::createFromFormat('!d-m-Y', $request->get('end_date'), 'Asia/Jakarta');
                $end->addDays(1);

                $data = $data->whereBetween('shipping_instruction_headers.etd_date', [$start->toDateTimeString(), $end->toDateTimeString()]);
            }

            if ($request->get('destination')) {
                if ($request->get('destination') > 0) {
                    $data = $data->where('destination_branch_id', '=', $request->get('destination'));
                }
            }

            if ($request->get('status_id')) {
                if ($request->get('status_id') > 0) {
                    $data = $data->where('status_id', '=', $request->get('status_id'));
                }
            }

            if ($request->get('keyword')) {
                $keyword = $request->get('keyword');
                if (! empty($keyword)) {
                    $data = $data->whereExists(function ($query) use ($keyword) {
                        $query->select(DB::raw(1))
                            ->from('shipping_instruction_monitors')
                            ->whereRaw('shipping_instruction_headers.id = shipping_instruction_monitors.header_id')
                            ->where(function ($query1) use ($keyword) {
                                $query1->where('shipping_instruction_monitors.do_code', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('shipping_instruction_monitors.container_code', 'LIKE', '%'.$keyword.'%');
                            });
                    });
                }
            }

            $data = $data->get();

            $excelArr = [];
            foreach ($data as $item) {
                $excelArr[] = [
                    'DOC NO' => $item->code,
                    'VESSEL NAME' => $item->vessel_name,
                    'DESTINATION' => $item->destination,
                    'STATUS' => $item->status_display,
                    'ETD' => $item->etd_date,
                    'ETA' => $item->eta_date,
                    'SAILING' => $item->sailing_date,
                    'BERTHING' => $item->docking_date,
                    'DOORING' => $item->dooring_at,
                    'RECEIVED' => $item->received_at,
                ];
            }

            $now = Carbon::now('Asia/Jakarta');
            $pattern = $now->year.$now->month.$now->day.$now->hour.$now->minute;

            return (new FastExcel($excelArr))->download('SHIPPING INSTRUCTION LIST EXPORT '.$pattern.'.xlsx');
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController exportList error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function exportOperation(Request $request)
    {
        try {
            $id = $request->get('si_id');
            $siHeader = ShippingInstructionHeader::with(['shipping_instruction_monitors', 'shipping_instruction_monitors.status'])->find($id);
            if (empty($siHeader)) {
                return redirect()->route('shipping_instruction');
            }

            $admin = Auth::user();

            // Validate admin permission
            if ($admin->is_superadmin == 0) {
                $branchIdArr[] = $admin->branch_id;
                foreach ($admin->branches as $adminBranch) {
                    $branchIdArr[] = $adminBranch->id;
                }

                if (! in_array($siHeader->destination_branch_id, $branchIdArr)) {
                    return redirect()->route('shipping_instruction');
                }
            }

            $excelArr = [];
            foreach ($siHeader->shipping_instruction_monitors as $detail) {
                $dooringDate = '-';
                if (! empty($detail->dooring_at)) {
                    $dooringDate = Carbon::parse($detail->dooring_at)->format('d-m-Y');
                }

                $receivedDate = '-';
                if (! empty($detail->received_at)) {
                    $receivedDate = Carbon::parse($detail->received_at)->format('d-m-Y');
                }

                $berthingDate = '-';
                if (! empty($detail->berthing_at)) {
                    $berthingDate = Carbon::parse($detail->berthing_at)->format('d-m-Y');
                }

                $excelArr[] = [
                    'CONTAINER' => $detail->container_code ?? '',
                    'STATUS' => $detail->status->display,
                    'DO NO' => $detail->do_code,
                    'DOORING' => $dooringDate,
                    'RECEIVED' => $receivedDate,
                    'BERTHING' => $berthingDate,
                    'DELIVERY TYPE' => $detail->delivery_type,
                    'CONSIGNEE' => $detail->trucking,
                    'SHIPTO NAME' => $detail->shipto_name,
                    'VESSEL NAME' => $siHeader->vessel_name,
                    'ITEM' => $detail->item_code.' ('.$detail->item_name.')',
                    'UNIT' => $detail->unit,
                    'QTY' => $detail->real_qty,
                ];
            }

            $now = Carbon::now('Asia/Jakarta');
            $pattern = $now->year.$now->month.$now->day.$now->hour.$now->minute;

            return (new FastExcel($excelArr))->download($siHeader->code.' OPERATION EXPORT '.$pattern.'.xlsx');
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController exportOperation error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function void(Request $request, $id)
    {
        try {
            $siHeader = ShippingInstructionHeader::find($id);
            if ($siHeader->status_id != 7 && $siHeader->status_id != 8) {
                Session::flash('error', 'Invalid Status!');

                return redirect()->back();
            }

            $siHeader->status_id = 21;
            $siHeader->voided_at = Carbon::now('Asia/Jakarta')->toDateTimeString();

            // Void all related DOs
            foreach ($siHeader->shipping_instruction_details as $siDetail) {
                DB::table('delivery_order_headers')
                    ->where('id', '=', $siDetail->do_id)
                    ->update([
                        'status_id' => 21,
                        'voided_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    ]);
            }

            Session::flash('success', 'Void Shipping Instruction Success!');

            return redirect()->route('shipping_instruction.show', ['id' => $id]);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController void error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function voidDo(Request $request, $siId)
    {
        try {
            $voidedDoId = $request->input('voided_do_id');
            $doHeader = DoHeader::find($voidedDoId);

            //dd($voidedDoId);

            // Remove from SI details and monitors
            if (! empty($doHeader->container1_code)) {
                DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siId)
                    ->where('container_code', '=', $doHeader->container1_code)
                    ->delete();
            }

            if (! empty($doHeader->container2_code)) {
                DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siId)
                    ->where('container_code', '=', $doHeader->container2_code)
                    ->delete();
            }

            DB::table('shipping_instruction_details')
                ->where('header_id', '=', $siId)
                ->where('do_id', '=', $voidedDoId)
                ->delete();

            // Mark DO status to VOID
            $doHeader->status_id = 21;
            $doHeader->voided_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $doHeader->save();

            Session::flash('success', 'Void Delivery Order Success!');

            return redirect()->route('shipping_instruction.show', ['id' => $siId]);
        } catch (\Exception $ex) {
            dd($ex);
            Log::channel('shipping')->error('ShippingInstructionController voidDo error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function viewUploads($id)
    {
        $siHeader = ShippingInstructionHeader::with(['shipping_instruction_details'])->find($id);
        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        $uploads = ShippingInstructionUpload::where('header_id', '=', $id)->get();

        $data = [
            'siHeader' => $siHeader,
            'uploads' => $uploads,
        ];

        return view('shippings.shipping_instruction.view_upload')->with($data);
    }

    public function formUpload($id)
    {
        $siHeader = ShippingInstructionHeader::with(['shipping_instruction_details'])->find($id);
        if (empty($siHeader)) {
            return redirect()->route('shipping_instruction');
        }

        return view('shippings.shipping_instruction.form_upload', compact('siHeader'));
    }

    public function submitUploads(Request $request)
    {
        try {
            if (! $request->hasFile('files')) {
                Session::flash('error', 'Please upload documents');

                return redirect()->back();
            }

            $siHeader = ShippingInstructionHeader::with(['shipping_instruction_details'])->find($request->input('si_id'));
            if (empty($siHeader)) {
                return redirect()->route('shipping_instruction');
            }

            //check folder exists
            $tmpPath = Storage::disk('si_documents')->path($siHeader->code);
            if (! Storage::exists($tmpPath)) {
                //create the folder
                Storage::disk('si_documents')->makeDirectory($siHeader->code);
            }

            $files = $request->file('files');
            foreach ($files as $file) {
                $ext = $file->getClientOriginalExtension();
                $fileName = $file->getClientOriginalName();

                $newUpload = new ShippingInstructionUpload();
                $newUpload->header_id = $siHeader->id;
                $newUpload->filename = $fileName;
                $newUpload->ext = $ext;
                $newUpload->save();

                $file->storeAs($siHeader->code, $fileName, 'si_documents');
            }

            Session::flash('success', 'Upload SI Documents success!');

            return redirect()->route('shipping_instruction.upload.view', ['id' => $siHeader->id]);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController submitUploads error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function downloadDocument($id)
    {
        try {
            $file = ShippingInstructionUpload::find($id);
            if (empty($file)) {
                return redirect()->back()->withErrors('BAD REQUEST!', 'default');
            }

            $downloadPath = public_path().'/storage/si_documents/'.$file->shipping_instruction_header->code.'/'.$file->filename;

            return response()->download($downloadPath, $file->file_path);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController - downloadDocument error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function deleteDocument(Request $request)
    {
        try {
            $uploadId = $request->input('deleted_upload_id');

            $upload = ShippingInstructionUpload::find($uploadId);
            if (empty($upload)) {
                Session::flash('error', 'BAD REQUEST!');

                return redirect()->back();
            }

            $headerId = $upload->header_id;

            $deletePath = public_path().'/storage/si_documents/'.$upload->shipping_instruction_header->code.'/'.$upload->filename;
            if (file_exists($deletePath)) {
                unlink($deletePath);
            }
            $upload->delete();

            Session::flash('success', 'Delete SI Document success!');

            return redirect()->route('shipping_instruction.upload.view', ['id' => $headerId]);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('ShippingInstructionController - deleteDocument error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    public function revertMonitorStatus(Request $request)
    {
        try {
            $revertTo = $request->input('revert_to');
            $monitorId = $request->input('reverted_monitor_id');

            $siMonitor = ShippingInstructionMonitor::find($monitorId);
            if (empty($siMonitor)) {
                return redirect()->back();
            }

            if ($revertTo == 'BERTHING') {
                $siMonitor->status_id = 9;
                $siMonitor->dooring_at = null;
                $siMonitor->save();

                // Revert master
                $containerMaster = ContainerData::where('code', '=', $siMonitor->container_code)->first();
                $containerMaster->warehouse_location_status = 'DOORING';
                $containerMaster->dooring_at = null;
                $containerMaster->status_id = 9;
                $containerMaster->save();

                if (! DB::table('shipping_instruction_monitors')
                    ->where('header_id', '=', $siMonitor->header_id)
                    ->where('status_id', '=', 13)
                    ->exists()) {
                    // Revert SI status if all monitors reverted
                    DB::table('shipping_instruction_headers')
                        ->where('id', '=', $siMonitor->header_id)
                        ->update([
                            'status_id' => 9,
                            'dooring_at' => null,
                            'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                        ]);
                }

                // Recount total container
                ShippingInstructionHeader::recountContainerTotals($siMonitor->header_id);

                Session::flash('success', 'Revert container status success!');

                return redirect()->route('shipping_instruction.operation', ['id' => $siMonitor->header_id, 'status' => 'dooring']);
            } elseif ($revertTo == 'DOORING') {
                $siMonitor->status_id = 13;
                $siMonitor->received_at = null;
                $siMonitor->save();

                // Revert master
                $containerMaster = ContainerData::where('code', '=', $siMonitor->container_code)->first();
                $containerMaster->warehouse_location_status = 'DOORING';
                $containerMaster->received_at = null;
                $containerMaster->status_id = 13;
                $containerMaster->is_storage_monitor_end = false;
                $containerMaster->returned_at = null;
                $containerMaster->is_demurrage_monitor_end = false;
                $containerMaster->save();

                // Revert SI status to DOORING
                DB::table('shipping_instruction_headers')
                    ->where('id', '=', $siMonitor->header_id)
                    ->update([
                        'status_id' => 13,
                        'received_at' => null,
                        'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    ]);

                // Recount total container
                ShippingInstructionHeader::recountContainerTotals($siMonitor->header_id);

                Session::flash('success', 'Revert container status success!');

                return redirect()->route('shipping_instruction.operation', ['id' => $siMonitor->header_id, 'status' => 'received']);
            }
        } catch (\Exception $ex) {
            dd($ex);
            Log::channel('shipping')->error('ShippingInstructionController - revertMonitorStatus error EX: '.$ex);
            Session::flash('error', 'Internal Server Error');

            return redirect()->back();
        }
    }

    // TEMPORARY - recount container totals for an SI header (debug endpoint)
    public function recountTotals($id)
    {
        $header = DB::table('shipping_instruction_headers')
            ->where('id', '=', $id)
            ->first();

        if (empty($header)) {
            return response()->json([
                'success' => false,
                'message' => 'SI header not found',
            ], 404);
        }

        $before = [
            'total_berthing_cont' => $header->total_berthing_cont,
            'total_dooring_cont' => $header->total_dooring_cont,
            'total_received_cont' => $header->total_received_cont,
        ];

        $statusCounts = DB::table('shipping_instruction_monitors')
            ->where('header_id', '=', $id)
            ->select('status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('status_id')
            ->pluck('total', 'status_id');

        ShippingInstructionHeader::recountContainerTotals($id);

        $after = DB::table('shipping_instruction_headers')
            ->where('id', '=', $id)
            ->first();

        return response()->json([
            'success' => true,
            'header' => [
                'id' => $header->id,
                'code' => $header->code,
            ],
            'before' => $before,
            'after' => [
                'total_berthing_cont' => $after->total_berthing_cont,
                'total_dooring_cont' => $after->total_dooring_cont,
                'total_received_cont' => $after->total_received_cont,
            ],
            'monitor_status_counts' => $statusCounts,
        ]);
    }
}
