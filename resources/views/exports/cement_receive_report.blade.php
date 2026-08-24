<table>
    <thead>
        <tr>
            <th colspan="18">LAPORAN PENERIMAAN SEMEN</th>
        </tr>
        <tr>
            <th colspan="4">NAMA KAPAL</th>
            <th colspan="14">{{ $siHeader->vessel_name }}</th>
        </tr>
        <tr>
            <th colspan="4">CABANG/TUJUAN</th>
            <th colspan="14">{{ $siHeader->destination }}</th>
        </tr>
        <tr>
            <th colspan="18"></th>
        </tr>
        <tr>
            <th colspan="2">Kolom dengan tanda * wajib diiisi</th>
            <th colspan="2">isi oleh cabang</th>
            <th colspan="9"></th>
            <th colspan="5">isi oleh cabang</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Delivery Type</th>
            <th>Kode</th>
            <th>Confirm Delivery Type</th>
            <th>No Surat Jalan</th>
            <th>Date of SJ</th>
            <th>SO No</th>
            <th>Ship Name</th>
            <th>Item Product</th>
            <th>Qty</th>
            <th>UoM</th>
            <th>Container#</th>
            <th>Angkutan</th>
            <th>Tgl Terima*</th>
            <th>No SJ QAS/ No STOCK IN*</th>
            <th>Qty (Terima)*</th>
            <th>Qty (Selisih)*</th>
            <th>Remarks*</th>
        </tr>
    </thead>
    <tbody>
        @php($idx = 1)
        @foreach ($siHeader->shipping_instruction_monitors as $detail)
            <tr>
                <td>{{ $idx }}</td>
                <td>{{ $detail->delivery_type }}</td>
                <td></td>
                <td></td>
                <td>{{ $detail->do_code }}</td>
                <td>{{ Carbon\Carbon::parse($detail->do_date)->format('d-M-y') }}</td>
                <td>{{ $detail->so_code }}</td>
                <td>{{ $detail->shipto_name }}</td>
                <td>{{ $detail->item_name }}</td>
                <td>{{ $detail->real_qty }}</td>
                <td>{{ $detail->unit }}</td>
                <td>{{ $detail->container_code }}</td>
                <td>{{ $detail->transportation }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $detail->remark }}</td>
            </tr>
            @php($idx++)

        @endforeach
        <tr>
            <td colspan="17"></td>
        </tr>
        <tr>
            <td>KODE</td>
            <td colspan="3">DELIVERY TYPE</td>
            <td>DOC TYPE</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>A</td>
            <td colspan="3">FRANCO GUDANG</td>
            <td>NO STOCK IN</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>B</td>
            <td colspan="3">FRANCO PELABUHAN TO CUSTOMER</td>
            <td>NO SJ</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>C</td>
            <td colspan="3">SELFPICK PELABUHAN</td>
            <td>NO SJ</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="8">
                **** Jika terdapat semen rusak/hilang/retur, dsb, harus melampirkan Berita Acara (via scan) yang terdapat tanda tangan supir dan customer.
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="18"></td>
        </tr>
        <tr>
            <td colspan="3">Dibuat Oleh</td>
            <td colspan="3">Dicek Oleh</td>
            <td colspan="4">Dikonfirmasi Oleh</td>
            <td colspan="4">Diketahui Oleh</td>
            <td colspan="4">Diketahui Oleh</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="3"></td>
            <td colspan="4"></td>
            <td colspan="4"></td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="3"></td>
            <td colspan="4"></td>
            <td colspan="4"></td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="3"></td>
            <td colspan="4"></td>
            <td colspan="4"></td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="3">PIC</td>
            <td colspan="3">Kepala Gudang</td>
            <td colspan="4">BM</td>
            <td colspan="4">Logistik Karawang</td>
            <td colspan="4">HQ</td>
        </tr>
        <tr>
            <td colspan="3">(date)</td>
            <td colspan="3">(date)</td>
            <td colspan="4">(date)</td>
            <td colspan="4">(date)</td>
            <td colspan="4">(date)</td>
        </tr>
    </tbody>
</table>
