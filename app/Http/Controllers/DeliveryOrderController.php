<?php

namespace App\Http\Controllers;

use App\Models\ContainerData;
use App\Models\DoDetail;
use App\Models\DoHeader;
use App\Models\Item;
use App\Transformer\DeliveryOrderTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DeliveryOrderController extends Controller
{
    public function index(Request $request)
    {
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

        $filterBranchDestination = 0;
        if ($request->branch_destination != null) {
            $filterBranchDestination = intval($request->branch_destination);
        }

        $filterStatus = -1;
        if ($request->status !== null) {
            $filterStatus = intval($request->status);
        }

        $filterType = 'all';
        if ($request->type != null) {
            $filterType = $request->type;
        }

        $admin = Auth::user();
        if ($admin->isSuperAdmin()) {
            $branches = DB::table('branches')
                ->orderBy('name')
                ->get();
        } else {
            if ($admin->branches->count() > 0) {
                $otherBranchIds = $admin->branches->pluck('id')->toArray();

                $branches = DB::table('branches')
                    ->whereIn('id', $otherBranchIds)
                    ->orWhere('id', '=', $admin->branch_id)
                    ->orderBy('name')
                    ->get();
            } else {
                $branches = DB::table('branches')
                    ->where('id', $admin->branch_id)
                    ->orderBy('name')
                    ->get();
            }
        }

        $data = [
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'filterBranchDestination' => $filterBranchDestination,
            'filterStatus' => $filterStatus,
            'filterType' => $filterType,
            'branches' => $branches,
        ];

        return view('shippings.delivery_order.index')->with($data);
    }

    public function create()
    {
        $branches = DB::table('branches')
            ->where('status_id', 1)
            ->orderBy('name')
            ->get();

        $data = [
            'branches' => $branches,
        ];

        return view('shippings.delivery_order.create')->with($data);
    }

    public function edit($id)
    {
        $doHeader = DoHeader::with('do_details')->find($id);
        if (empty($doHeader) || $doHeader->status_id == 21) {
            Session::flash('error', 'Delivery Order not found!');

            return redirect()->route('delivery_order');
        }

        if ($doHeader->is_close) {
            Session::flash('error', 'Closed Delivery Order cannot be edited!');

            return redirect()->route('delivery_order');
        }

        $branches = DB::table('branches')
            ->where('status_id', 1)
            ->orderBy('name')
            ->get();

        $detailList = [];

        if (old('details')) {
            $oldDetails = old('details');
            foreach ($oldDetails as $index => $oldDetail) {
                $item = Item::find(intval($oldDetail['item_id']));
                $detailList[] = [
                    'key_id' => 'old-'.$index,
                    'item_id' => $oldDetail['item_id'] ?? '',
                    'item_code' => $item->code ?? '',
                    'item_name' => $item->name ?? '',
                    'qty' => $oldDetail['qty'] ?? 0,
                    'unit' => $oldDetail['unit'] ?? '',
                    'item_select_extra' => ['id' => $oldDetail['item_id'] ?? ''],
                ];
            }
        } else {
            foreach ($doHeader->do_details as $detail) {
                $detailList[] = [
                    'key_id' => 'detail-'.$detail->id,
                    'item_id' => $detail->item_id,
                    'item_code' => $detail->item_code,
                    'item_name' => $detail->item_name,
                    'qty' => $detail->qty,
                    'unit' => $detail->unit,
                    'item_select_extra' => ['id' => $detail->item_id],
                ];
            }
        }

        $data = [
            'branches' => $branches,
            'doHeader' => $doHeader,
            'detailList' => $detailList,
        ];

        return view('shippings.delivery_order.edit')->with($data);
    }

    public function update(Request $request)
    {
        try {
            $doHeader = DoHeader::find(intval($request->input('id')));
            if (empty($doHeader) || $doHeader->status_id == 21) {
                Session::flash('error', 'Delivery Order not found!');

                return redirect()->route('delivery_order');
            }

            if ($doHeader->is_close) {
                Session::flash('error', 'Closed Delivery Order cannot be edited!');

                return redirect()->back();
            }

            $validator = Validator::make($request->all(), [
                'code' => 'required|max:255|unique:do_headers,code,'.$doHeader->id,
                'type' => 'required|in:delivery_order,transfer',
                'date' => 'required',
                'branch_destination' => 'required|integer|exists:branches,id',
                'details' => 'required|array|min:1',
                'details.*.item_id' => 'required|integer|exists:items,id',
                'details.*.qty' => 'required|numeric|min:0',
                'details.*.unit' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            DB::beginTransaction();

            $doHeader->code = strtoupper(trim($request->input('code')));
            $doHeader->type = $request->input('type');
            $doHeader->date = Carbon::createFromFormat('!d-m-Y', $request->input('date'), 'Asia/Jakarta')->toDateTimeString();
            $doHeader->so_code = $request->input('so_code');
            $doHeader->dr_code = $request->input('dr_code');
            $doHeader->customer_code = $request->input('customer_code');
            $doHeader->customer_name = $request->input('customer_name');
            $doHeader->branch_destination_id = intval($request->input('branch_destination'));
            $doHeader->shipto_code = $request->input('shipto_code');
            $doHeader->shipto_name = $request->input('shipto_name');
            $doHeader->shipping_company = $request->input('shipping_company');
            $doHeader->transportation = $request->input('transportation');
            $doHeader->updated_by = Auth::id();
            $doHeader->save();

            DoDetail::where('header_id', '=', $doHeader->id)->delete();

            foreach ($request->input('details') as $detail) {
                $item = Item::find(intval($detail['item_id']));

                $newDoDetail = new DoDetail;
                $newDoDetail->header_id = $doHeader->id;
                $newDoDetail->transfer_no = $doHeader->code;
                $newDoDetail->item_id = $item->id;
                $newDoDetail->item_code = $item->code;
                $newDoDetail->item_name = $item->name;
                $newDoDetail->qty = floatval($detail['qty']);
                $newDoDetail->unit = $detail['unit'];
                $newDoDetail->save();
            }

            DB::commit();

            Session::flash('success', 'Delivery Order updated successfully!');

            return redirect()->route('delivery_order.show', ['id' => $doHeader->id]);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::channel('shipping')->error('DeliveryOrderController update error EX: '.$ex);
            Session::flash('error', 'Internal Server Error!');

            return redirect()->back()->withInput($request->all());
        }
    }

    public function show($id)
    {
        $doHeader = DoHeader::with('do_details')->find($id);
        if (empty($doHeader) || $doHeader->status_id == 21) {
            Session::flash('error', 'Delivery Order not found!');

            return redirect()->route('delivery_order');
        }

        $branchName = '';
        $branch = DB::table('branches')->where('id', '=', $doHeader->branch_destination_id)->first();
        if (! empty($branch)) {
            $branchName = $branch->name;
        }

        $data = [
            'doHeader' => $doHeader,
            'branchName' => $branchName,
        ];

        return view('shippings.delivery_order.show')->with($data);
    }

    public function void(Request $request)
    {
        try {
            $doHeader = DoHeader::find(intval($request->input('id')));
            if (empty($doHeader) || $doHeader->status_id == 21) {
                Session::flash('error', 'Delivery Order not found!');

                return redirect()->route('delivery_order');
            }

            if ($doHeader->is_close) {
                Session::flash('error', 'Closed Delivery Order cannot be voided!');

                return redirect()->back();
            }

            $doHeader->status_id = 21;
            $doHeader->voided_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $doHeader->updated_by = Auth::id();
            $doHeader->save();

            Session::flash('success', 'Delivery Order voided!');

            return redirect()->route('delivery_order');
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('DeliveryOrderController void error EX: '.$ex);
            Session::flash('error', 'Internal Server Error!');

            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|max:255|unique:do_headers,code',
                'type' => 'required|in:delivery_order,transfer',
                'date' => 'required',
                'branch_destination' => 'required|integer|exists:branches,id',
                'details' => 'required|array|min:1',
                'details.*.item_id' => 'required|integer|exists:items,id',
                'details.*.qty' => 'required|numeric|min:0',
                'details.*.unit' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'error' => 'validation_failed',
                    'result' => $validator->errors()->all(),
                ], 200);
            }

            $container1Code = strtoupper(trim((string) $request->input('container1_code', '')));
            $container2Code = strtoupper(trim((string) $request->input('container2_code', '')));
            if ($container1Code !== '' && $container1Code === $container2Code) {
                return response()->json([
                    'code' => 400,
                    'error' => 'validation_failed',
                    'result' => ['Container 1 and Container 2 must be different!'],
                ], 200);
            }

            DB::beginTransaction();

            $newDoHeader = new DoHeader;
            $newDoHeader->code = strtoupper(trim($request->input('code')));
            $newDoHeader->type = $request->input('type');
            $newDoHeader->date = Carbon::createFromFormat('!d-m-Y', $request->input('date'), 'Asia/Jakarta')->toDateTimeString();
            $newDoHeader->so_code = $request->input('so_code');
            $newDoHeader->dr_code = $request->input('dr_code');
            $newDoHeader->customer_code = $request->input('customer_code');
            $newDoHeader->customer_name = $request->input('customer_name');
            $newDoHeader->branch_destination_id = intval($request->input('branch_destination'));
            $newDoHeader->shipto_code = $request->input('shipto_code');
            $newDoHeader->shipto_name = $request->input('shipto_name');
            $newDoHeader->shipping_company = $request->input('shipping_company');
            $newDoHeader->transportation = $request->input('transportation');
            $newDoHeader->status_id = 1;
            $newDoHeader->is_close = false;
            $newDoHeader->created_by = Auth::id();
            $newDoHeader->updated_by = Auth::id();
            $newDoHeader->save();

            $this->linkContainerToDo($newDoHeader, 1, $container1Code);
            $this->linkContainerToDo($newDoHeader, 2, $container2Code);

            foreach ($request->input('details') as $detail) {
                $item = Item::find(intval($detail['item_id']));

                $newDoDetail = new DoDetail;
                $newDoDetail->header_id = $newDoHeader->id;
                $newDoDetail->transfer_no = $newDoHeader->code;
                $newDoDetail->item_id = $item->id;
                $newDoDetail->item_code = $item->code;
                $newDoDetail->item_name = $item->name;
                $newDoDetail->qty = floatval($detail['qty']);
                $newDoDetail->unit = $detail['unit'];
                $newDoDetail->save();
            }

            $newDoHeader->save();
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'A new Delivery Order has been successfully created!',
                'error' => 'none',
                'result' => [
                    'show_url' => route('delivery_order.show', ['id' => $newDoHeader->id]),
                ],
            ], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::channel('shipping')->error('DeliveryOrderController store error EX: '.$ex);

            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error!',
                'error' => 'server',
                'result' => [],
            ], 200);
        }
    }

    private function linkContainerToDo(DoHeader $doHeader, int $position, string $containerCode): void
    {
        $idField = 'container'.$position.'_id';
        $codeField = 'container'.$position.'_code';

        if ($containerCode === '') {
            $doHeader->{$idField} = null;
            $doHeader->{$codeField} = null;

            return;
        }

        $existingContainer = ContainerData::where('code', '=', $containerCode)->first();

        if (! empty($existingContainer)) {
            $existingContainer->do_id = $doHeader->id;
            $existingContainer->do_code = $doHeader->code;
            $existingContainer->do_type = $doHeader->type;
            $existingContainer->timbangan_id = null;
            $existingContainer->timbangan_code = null;
            $existingContainer->status_id = 7;
            $existingContainer->warehouse_location_id = 13;
            $existingContainer->warehouse_location_status = 'DOORING';
            $existingContainer->warehouse_area = 'B';
            $existingContainer->warehouse_area_row = 'B1';
            $existingContainer->warehouse_status_id = 3;
            $existingContainer->warehouse_status = 'FULL';
            $existingContainer->warehouse_status_reason = null;
            $existingContainer->si_code = null;
            $existingContainer->si_destination_id = null;
            $existingContainer->si_destination_name = null;
            $existingContainer->si_etd_date = null;
            $existingContainer->si_eta_date = null;
            $existingContainer->free_storage_start_at = null;
            $existingContainer->free_storage_end_at = null;
            $existingContainer->free_demurrage_start_at = null;
            $existingContainer->free_demurrage_end_at = null;
            $existingContainer->last_status_updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $existingContainer->berthing_at = null;
            $existingContainer->dooring_at = null;
            $existingContainer->received_at = null;
            $existingContainer->is_over_storage = false;
            $existingContainer->returned_at = null;
            $existingContainer->is_over_demurrage = false;
            $existingContainer->free_storage_duration = 0;
            $existingContainer->free_demurrage_duration = 0;
            $existingContainer->is_storage_monitor_start = false;
            $existingContainer->is_storage_monitor_end = false;
            $existingContainer->is_demurrage_monitor_start = false;
            $existingContainer->is_demurrage_monitor_end = false;
            $existingContainer->is_sync = false;
            $existingContainer->item_name = null;
            $existingContainer->save();

            $doHeader->{$idField} = $existingContainer->id;
            $doHeader->{$codeField} = $existingContainer->code;

            return;
        }

        $newContainer = new ContainerData;
        $newContainer->code = $containerCode;
        $newContainer->do_id = $doHeader->id;
        $newContainer->do_code = $doHeader->code;
        $newContainer->do_type = $doHeader->type;
        $newContainer->status_id = 7;
        $newContainer->warehouse_location_status = 'DOORING';
        $newContainer->save();

        $doHeader->{$idField} = $newContainer->id;
        $doHeader->{$codeField} = $newContainer->code;
    }

    public function select2Item(Request $request)
    {
        $search = trim($request->get('search', ''));

        if ($request->filled('id')) {
            $item = Item::find(intval($request->input('id')));
            $results = [];
            if (! empty($item)) {
                $results[] = [
                    'id' => $item->id,
                    'text' => $item->code.' ('.$item->name.')',
                ];
            }

            return response($results);
        }

        $items = Item::where('code', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->orderBy('code')
            ->take(20)
            ->get();

        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->code.' ('.$item->name.')',
            ];
        }

        return response($results);
    }

    public function select2Container(Request $request)
    {
        $search = trim($request->get('search', ''));

        $containers = DB::table('container_datas')
            ->where('code', 'like', '%'.$search.'%')
            ->orderBy('code')
            ->take(20)
            ->get();

        $results = [];
        foreach ($containers as $container) {
            $results[] = [
                'id' => $container->code,
                'text' => $container->code,
            ];
        }

        return response($results);
    }

    public function getItemData(Request $request)
    {
        try {
            $item = Item::find(intval($request->input('item_id')));
            if (empty($item)) {
                return Response::json(['error' => 'Item not found'], 404);
            }

            $unit = '';
            $firstItemUnit = DB::table('item_units')
                ->where('item_id', '=', $item->id)
                ->first();
            if (! empty($firstItemUnit)) {
                $unitMaterial = DB::table('unit_materials')
                    ->where('id', '=', $firstItemUnit->unit_id)
                    ->first();
                if (! empty($unitMaterial)) {
                    $unit = $unitMaterial->name;
                }
            }

            return Response::json([
                'item_id' => $item->id,
                'item_code' => $item->code,
                'item_name' => $item->name,
                'unit' => $unit,
            ], 200);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('DeliveryOrderController getItemData error EX: '.$ex);

            return Response::json(['error' => $ex->getMessage()], 500);
        }
    }

    public function datatable(Request $request)
    {
        $admin = Auth::user();
        $data = DB::table('do_headers')
            ->leftJoin('branches', 'do_headers.branch_destination_id', '=', 'branches.id')
            ->select('do_headers.*', 'branches.name as branch_name')
            ->where('do_headers.status_id', '!=', 21);

        if (! $admin->isSuperAdmin()) {
            $branchIdArr[] = $admin->branch_id;
            foreach ($admin->branches as $adminBranch) {
                $branchIdArr[] = $adminBranch->id;
            }

            if ($request->get('branch_destination') && $request->get('branch_destination') > 0) {
                if (! in_array($request->get('branch_destination'), $branchIdArr)) {
                    $data = $data->whereIn('do_headers.branch_destination_id', $branchIdArr);
                } else {
                    $data = $data->where('do_headers.branch_destination_id', '=', $request->get('branch_destination'));
                }
            } else {
                $data = $data->whereIn('do_headers.branch_destination_id', $branchIdArr);
            }
        } else {
            if ($request->get('branch_destination') && $request->get('branch_destination') > 0) {
                $data = $data->where('do_headers.branch_destination_id', '=', $request->get('branch_destination'));
            }
        }

        if ($request->exists('status')) {
            $statusFilter = intval($request->input('status'));
            if ($statusFilter >= 0) {
                $data = $data->where('do_headers.is_close', '=', $statusFilter);
            }
        }

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $data = $data->where('do_headers.type', '=', $request->input('type'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::createFromFormat('!d-m-Y', $request->input('start_date'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('!d-m-Y', $request->input('end_date'), 'Asia/Jakarta');
            $end->addDays(1);

            $data = $data->whereBetween('do_headers.date', [$start->toDateTimeString(), $end->toDateTimeString()]);
        }

        return DataTables::of($data)
            ->setTransformer(new DeliveryOrderTransformer)
            ->make(true);
    }

    public function select2(Request $request)
    {
        $search = trim($request->search);

        $isEdit = 0;
        if ($request->exists('is_edit')) {
            $isEdit = intval($request->is_edit);
        }

        $branchId = 0;
        $recipientCodeArr = [];
        if ($request->exists('branch_id')) {
            $branchId = intval($request->branch_id);

            $recipients = DB::table('branch_recipients')
                ->where('branch_id', '=', $branchId)
                ->get();

            foreach ($recipients as $recipient) {
                $recipientCodeArr[] = $recipient->code;
            }
        }

        $type = 'all';
        if ($request->exists('type')) {
            $type = $request->type;
        }

        $doHeaders = DB::table('do_headers')
            ->where('status_id', '!=', 21);

        // SI consumption assumes a single detail row per DO; only offer
        // single-line DOs until the SI side carries multiple item lines.
        $doHeaders = $doHeaders->whereRaw(
            '(SELECT COUNT(*) FROM do_details WHERE do_details.header_id = do_headers.id) = 1'
        );

        if ($type !== 'all') {
            $doHeaders = $doHeaders->where('type', '=', $type);
        }

        if ($request->get('etd') != null) {
            $etd = Carbon::createFromFormat('!d-m-Y', $request->get('etd'));
            $etd->addMinutes(5);
            $doHeaders = $doHeaders->where('date', '<', $etd->toDateTimeString());
        }

        if ($request->get('exclude_ids') != null) {
            $arr = explode(',', $request->get('exclude_ids'));
            $doHeaders = $doHeaders->whereNotIn('id', $arr);
        }

        if ($isEdit == 1) {
            $siId = intval($request->si_id);

            $doHeaders = $doHeaders->where(function ($query) use ($siId) {
                $query->where('is_close', '=', 0)
                    ->orWhere('closed_by_si_id', '=', $siId);
            });
        } else {
            $doHeaders = $doHeaders->where('is_close', '=', 0);
        }

        if ($branchId > 0) {
            $doHeaders = $doHeaders->whereIn('shipto_code', $recipientCodeArr);
        }

        $doHeaders = $doHeaders->where('code', 'LIKE', '%'.$search.'%')
            ->orderBy('date', 'desc')
            ->get();

        $results = [];
        foreach ($doHeaders as $doHeader) {
            $dateStr = Carbon::parse($doHeader->date)->format('d-m-Y');
            $results[] = ['id' => $doHeader->id, 'text' => $doHeader->code.' date: '.$dateStr];
        }

        return Response::json($results);
    }

    public function getContainerData(Request $request)
    {
        try {
            $doId = $request->input('do_id');
            $doHeader = DB::table('do_headers')
                ->where('id', '=', $doId)
                ->where('status_id', '!=', 21)
                ->first();

            if (empty($doHeader)) {
                return Response::json([
                    'error' => 'Delivery Order not found',
                ], 404);
            }

            $doDetail = DB::table('do_details')
                ->where('header_id', '=', $doId)
                ->first();

            if (empty($doDetail) || DB::table('do_details')->where('header_id', '=', $doId)->count() > 1) {
                return Response::json([
                    'error' => 'Delivery Order must have exactly one item line to be used in a Shipping Instruction',
                ], 400);
            }

            return Response::json([
                'do_id' => $doHeader->id,
                'do_code' => $doHeader->code,
                'container1' => $doHeader->container1_code ?? '',
                'container2' => $doHeader->container2_code ?? '',
                'shipto_name' => $doHeader->shipto_name,
                'so_no' => $doHeader->so_code,
                'do_type' => $doHeader->type,
                'qty' => $doDetail->qty,
                'unit' => $doDetail->unit,
            ], 200);
        } catch (\Exception $ex) {
            Log::channel('shipping')->error('DeliveryOrderController getContainerData error EX: '.$ex);

            return Response::json([
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
}
