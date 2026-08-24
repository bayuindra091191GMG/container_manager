<!DOCTYPE html>
<html lang="en">
<head>
    <title>SI FINAL</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" crossorigin="anonymous">

    <style>
        @media print {
            .print_show{
                display: block;
            }

            .print_hide {
                display: none;
            }
        }

        td{
            padding: 3px !important;
        }

        th{
            padding: 3px !important;
        }


    </style>
</head>
<body>
<div class="container" style="width: 900px !important;">
    <div id="print_button_section" class="row my-3">
        <div class="col-12">
            <button type="button" class="btn btn-primary" id="btn_print">CETAK</button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h5>Shipping Instruction</h5>
            <span style="font-size: 12px !important;">( FOR B/L NEEDED )</span>
        </div>
    </div>
    <div class="row fw-bold" style="font-size: 10px !important;">
        <div class="col-3">
            <div class="row">
                <div class="col-3">
                    <span>To</span>
                </div>
                <div class="col-9">
                    <span>: {{ $siHeader->port_receiver }}</span>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="row">
                <div class="col-4 text-end">
                    <span>Vessel Name :</span>
                </div>
                <div class="col-8">
                    <span>{{ $siHeader->vessel_name }}</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="row">
                <div class="col-6 text-end">
                    <span>Manifest :</span>
                </div>
                <div class="col-6">
                    <span>{{ $manifestNumber }}</span>
                </div>
            </div>
        </div>

    </div>
    <div class="row fw-bold" style="font-size: 10px !important;">
        <div class="col-3">
            <div class="row">
                <div class="col-3">
                    <span>Attn</span>
                </div>
                <div class="col-9">
                    <span>: {{ $siHeader->pic_receiver }}</span>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="row">
                <div class="col-4 text-end">
                    <span>Shipper :</span>
                </div>
                <div class="col-8">
                    <span>{{ $siHeader->shipper }}</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="row">
                <div class="col-6 text-end">
                    <span>ETD :</span>
                </div>
                <div class="col-6">
                    <span>{{ $etdDate }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2 fw-bold" style="font-size: 10px !important;">
        <div class="col-3">
        </div>
        <div class="col-5">
            <div class="row">
                <div class="col-4 text-end">
                    <span>Destination :</span>
                </div>
                <div class="col-8">
                    <span>{{ $siHeader->destination }}</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="row">
                <div class="col-6 text-end">
                    <span>ETA :</span>
                </div>
                <div class="col-6">
                    <span>{{ $etaDate }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4" style="font-size: 9px !important;">
        <div class="col-12">
            <table class="w-100 table table-bordered">
                <thead>
                <tr class="text-center">
                    <th class="text-center">NO</th>
                    <th class="text-center">Delivery Type</th>
                    <th class="text-center">Tgl Surat Jalan</th>
                    <th class="text-center">No Surat Jalan</th>
                    <th class="text-center">Consignee</th>
                    <th class="text-center">SO No.</th>
                    <th class="text-center">Ship Name</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">UoM</th>
                    <th class="text-center">Container #</th>
                    <th class="text-center">Tgl Terima</th>
                </tr>
                </thead>
                <tbody>
                    @php($idx = 1)
                    @foreach ($siHeader->shipping_instruction_monitors as $detail)
                        <tr>
                            <td class="text-center">{{ $idx }}</td>
                            <td class="text-center">{{ $detail->delivery_type }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($detail->do_date)->format('d-M-y') }}</td>
                            <td class="text-center">{{ $detail->do_code }}</td>
                            <td class="text-center">{{ $detail->trucking }}</td>
                            <td class="text-center">{{ $detail->so_code }}</td>
                            <td class="text-center">{{ $detail->shipto_name }}</td>
                            <td class="text-center">{{ $detail->item_name }}</td>
                            <td class="text-end">{{ $detail->qty }}</td>
                            <td class="text-center">{{ $detail->unit }}</td>
                            <td class="text-center">{{ $detail->container_code }}</td>
                            <td></td>
                        </tr>
                        @php($idx++)
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mb-2" style="font-size: 11px !important;">
        <div class="col-5">
            <span>Disiapkan Oleh</span>
            <br/>
            <br/>
            <br/>
            <br/>
            <span>Logistik Karawang</span><br/>
            <span>PT JUI SHIN INDONESIA</span>
        </div>
        <div class="col-4">
            <span>Dikirim Oleh</span>
            <br/>
            <br/>
            <br/>
            <br/>
            _____________________
        </div>
        <div class="col-3">
            <span>Diterima Oleh</span>
            <br/>
            <br/>
            <br/>
            <br/>
            Gudang_______________<br/>
            <span>PT JUI SHIN INDONESIA</span>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script>
    $(document).on('click', '#btn_print', function(){
        // $('#print_doc').printThis({
        //     header: "<h3>Surat Jalan</h3>",
        // });
        $('#print_button_section').hide();
        window.print();
    });
    // window.onafterprint = function(){
    //     $('#print_button_section').hide();
    // }
</script>

</body>
</html>
