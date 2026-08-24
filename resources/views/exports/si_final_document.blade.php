<table>
    <thead>
        <tr>
            <th colspan="12">Shipping Instruction</th>
        </tr>
        <tr>
            <th colspan="12">( FOR B/L NEEDED )</th>
        </tr>
        <tr>
            <th>To</th>
            <th colspan="3">: {{ $siHeader->port_receiver }}</th>
            <th>Vessel name</th>
            <th colspan="3">: {{ $siHeader->vessel_name }}</th>
            <th>Manifest</th>
            <th colspan="3">: {{ $manifestNumber }}</th>
        </tr>
        <tr>
            <th>Attn</th>
            <th colspan="3">: {{ $siHeader->pic_receiver }}</th>
            <th>Shipper</th>
            <th colspan="3">: {{ $siHeader->shipper }}</th>
            <th>ETD</th>
            <th colspan="3">: {{ $etdDate }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Destination</th>
            <th colspan="3">: {{ $siHeader->destination }}</th>
            <th>ETA</th>
            <th colspan="3">: {{ $etaDate }}</th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr>
            <th>NO</th>
            <th>Consignee</th>
            <th>Delivery Type</th>
            <th>Tgl Surat Jalan</th>
            <th>No Transfer WH / No SO</th>
            <th>No Surat Jalan / Delivery Order (DO)</th>
            <th>Ship Name</th>
            <th>Item</th>
            <th>Qty</th>
            <th>UoM</th>
            <th>Container #</th>
            <th>Tgl Terima</th>
            <th>SJ QAS/INV QAS No</th>
            <th>Qty Received</th>
            <th>Ket</th>
        </tr>
    </thead>
    <tbody>
        @php($idx = 1)
        @foreach ($siHeader->shipping_instruction_monitors as $detail)
            <tr>
                <td>{{ $idx }}</td>
                <td>{{ $detail->trucking }}</td>
                <td>{{ $detail->delivery_type }}</td>
                <td>{{ Carbon\Carbon::parse($detail->do_date)->format('d-M-y') }}</td>
                @if($detail->do_type == 'transfer')
                    <td>{{ $detail->do_code }}</td>
                @else
                    <td>{{ $detail->so_code }}</td>
                @endif

                @if($detail->do_type == 'delivery_order')
                    <td>{{ $detail->do_code }}</td>
                @else
                    <td></td>
                @endif

                <td>{{ $detail->shipto_name }}</td>
                <td>{{ $detail->item_name }}</td>
                <td>{{ $detail->real_qty }}</td>
                <td>{{ $detail->unit }}</td>
                <td>{{ $detail->container_code }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $detail->remark ?? '' }}</td>
            </tr>
            @php($idx++)
        @endforeach
        <tr>
            <td colspan="15"></td>
        </tr>
        <tr>
            <td colspan="5">Disiapkan Oleh</td>
            <td colspan="5">Dikirim Oleh</td>
            <td colspan="5">Diterima Oleh</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="5"></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="5"></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="5"></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5">Logistik Karawang</td>
            <td colspan="5">_______________</td>
            <td colspan="5">Gudang_______________</td>
        </tr>
        <tr>
            <td colspan="5">PT JUI SHIN INDONESIA</td>
            <td colspan="5"></td>
            <td colspan="5">PT JUI SHIN INDONESIA</td>
        </tr>
    </tbody>
</table>
