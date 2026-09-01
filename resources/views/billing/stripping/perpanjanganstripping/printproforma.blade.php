<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Proforma {{ $data->no_request }}</title>
    <style>
        html {
            margin: 10px
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            /* border: 1px solid black; */
            /* padding-top: 3px;
            padding-bottom: 3px; */
        }

        th {
            /* background-color: #f2f2f2; */
        }

        .title {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .info {
            font-size: 7px;
            margin-bottom: 5px;
        }

        .thanks {
            font-size: 7px;
            margin-top: 10px;
            text-align: center;
        }

        .barcode {
            text-align: center;
            margin-bottom: 10px;
        }

        .barcode img {
            width: 250px;
            /* Atur lebar gambar */
            height: auto;
            /* Biarkan tinggi gambar disesuaikan secara proporsional */
        }


        .page-break {
            page-break-after:always;
        }

        .border-box {
            border: 1px solid #000;
            padding: 10px;
        }

        .total {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="barcode">
        <img src="data:image/png;base64,'.{{ base64_encode($barcode) }}.'" alt="barcode" style="height: 50px;">
        <span style="letter-spacing: 13px;">{{ $data->no_nota_mti }}</span><br>
    </div>
    <table style='font-weight:bold; font-size:9pt'>
        <tr>
            <td>{{ $data->no_nota_mti }}</td>
            <td style="text-align: right;">{{ $date }}</td>
        </tr>
        <tr>
            <td>{{ $data->no_request }}</td>
            <td style="text-align: right; font-size: 7pt !important"><span style="font-weight: normal;">No. Nota : </span>{{ $data->no_nota }}</td>
        </tr>
    </table>
    {{-- <b><span style="font-size: 9pt">{{ $data->no_request }}</span></b><br> --}}
    <span>POD : | </span><br>
    <b style="font-size: 6pt">PERP. STRIPPING</b><br />
    <b><span>{{ $data->nama }}</span></b><br>
    <b><span>{{ $data->npwp }}</span></b><br>
    <b><span>{{ $data->alamat }}</span></b><br>
    {{-- <b><span>{{ $data->vessel }} / {{$data->voyage}} </span></b><br> --}}

    <table>
        <tr>
            <td colspan="3"><b>PENUMPUKAN DARI :</b> </td>
            <td colspan="5" style="text-align: center"><b>{{ isset($data->tgl_stack) ? $data->tgl_stack : ' - ' }}
                    s/d {{ isset($data->tgl_muat) ? $data->tgl_muat : ' - ' }}</b></td>
        </tr>
        <tr>
            <th colspan="2"><b>KETERANGAN</b></th>
            <th colspan="2"><b>BX</b></th>

            <th><b>CONTENT</b></th>
            <th><b>HZ</b></th>

            <th><b>TARIF</b></th>
            <th><b>JUMLAH</b></th>
        </tr>

        <tr>
            <td colspan="10">
                <hr color="#000000" size="1" style="border: 0.5px solid #000000">
            </td>
        </tr>

        @foreach ($row2 as $rows)

            @if ($rows->keterangan != 'MONITORING DAN LISTRIK')
                @php
                    $tgl_start_stack = isset($rows->start_stack) ? $rows->start_stack : ' - ';
                    $tgl_end_stack = isset($rows->end_stack) ? $rows->end_stack : ' - ';
                    $jumlah_hari = isset($rows->jml_hari) ? $rows->jml_hari : ' 0 ';
                    $den = '(' . $tgl_start_stack . ' s/d ' . $tgl_end_stack . ')' . $jumlah_hari . 'hari';
                @endphp
            @else
                @php
                    $den = $rows->jml_hari . ' Shift';
                @endphp
            @endif

            @if (isset($rows->start_stack) && $rows->start_stack != '')
                <tr>
                    <td colspan="2" width="100">
                        <b>{{ $rows->keterangan }}</b>
                    </td>
                    <td colspan="2" width="10" align="left">
                        <b>{{ $rows->jml_cont }}</b>
                    </td>
                    <td width="50" align="left">
                        <b>{{ $rows->size_ . ' ' . $rows->type_ . ' ' . $rows->status }}</b>
                    </td>
                    <td width="10" align="left">
                        <b>{{ $rows->hz }}</b>
                    </td>
                    <td width="43" align="right">
                        <b>{{ $rows->tarif }}</b>
                    </td>
                    <td width="35" align="right">
                        <b>{{ $rows->biaya }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="8">
                        <i><b>{{ $den }}</b></i>
                    </td>
                </tr>
            @else
                <tr style="height: 10px">
                    <td colspan="2">
                        <b>{{ $rows->keterangan }}</b>
                    </td>
                    <td colspan="2">
                        <b>{{ $rows->jml_cont }}</b>
                    </td>
                    <td>
                        <b>{{ $rows->size_ . ' ' . $rows->type_ . ' ' . $rows->status }}</b>
                    </td>
                    <td>
                        <b>{{ $rows->hz }}</b>
                    </td>
                    <td>
                        <b>{{ $rows->tarif }}</b>
                    </td>
                    <td>
                        <b>{{ $rows->biaya }}</b>
                    </td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td colspan="10">
                <hr color="#000000" size="1" style="border: 0.5px solid #000000">
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="8" align="right"><b>Discount :</b></td>
            <td colspan="2" align="right"><b>0.00</b></td>
        </tr>
        <tr>

            <td colspan="8" align="right"><b>Administrasi :</b></td>
            <td colspan="2" align="right"><b>{{ $data->adm_nota }}</b></td>
        </tr>
        <tr>

            <td colspan="8" align="right"><b>Dasar Peng. Pajak :</b></td>
            <td colspan="2" align="right"><b>{{ $data->tagihan }}</b></td>
        </tr>
        <tr>

            <td colspan="8" align="right"><b>Jumlah PPN :</b></td>
            <td colspan="2" align="right"><b>{{ $data->ppn }}</b></td>
        </tr>
        <tr>

            <td colspan="8" align="right"><b>Jumlah PPN Subsidi :</b></td>
            <td colspan="2" align="right"><b>0.00</b></td>
        </tr>

        @if (intval($data_mtr = $data_mtr->biaya ?? 0) > 0)
            <tr>
                <td colspan="8" align="right"><b>Bea Materai :</b></td>
                <td colspan="2" align="right"><b>{{ $bea_materai }}</b></td>
            </tr>
        @endif
        <tr>
            <td colspan="8" align="right">
                <b style="font-size: 8pt">Jumlah Dibayar :</b>
            </td>
            <td colspan="2" align="right">
                <b style="font-size: 8pt">{{ $data->total_tagihan }}</b>
            </td>
        </tr>
        <tr>
            <td colspan="12">&nbsp;</td>
        </tr>
    </table>
    printed by {!! $nama_lengkap !!}
    @if (intval($data_mtr = $data_mtr->biaya ?? 0) > 0)
        <table>
            <tr>
                <td colspan="8">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="left">
                    Bea Materai Lunas Dengan Sistem Nomor Ijin : {{ $no_mat }}
                </td>
                <td></td>

                <td width="80" height="20" colspan="4" align="center" border="1"
                    style="border: 1px solid #000000">Termasuk Bea
                    Materai<br>
                    Rp. {{ $bea_materai }}
                </td>
                <td></td>
            </tr>
        </table>
    @endif

    <div class="page-break"></div>
    <h2>PT Multi Terminal Indonesia</h2>
    <table>
        <tr>
            <td colspan="9">
                <hr style="border: solid 1px #000000">
            </td>
        </tr>
        <tr>
            <td colspan="9">
                <i>form untuk Bank</i>
            </td>
        </tr>
        <tr>
            <td colspan="9">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td colspan="4" align="right">
                <b style="font-size: 8pt">Nomor Invoice :</b>
            </td>
            <td colspan="5" align="left">
                 <b style="font-size: 8pt">{{$data->no_nota_mti}}</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="right">
                <b style="font-size: 8pt">Customer :</b>
            </td>
            <td colspan="5" align="left">
                 <b style="font-size: 8pt">{{$data->nama}}</b>
            </td>
        </tr>
        <tr>

            <td colspan="4" align="right">
                <b style="font-size: 8pt">Jumlah Dibayar :</b>
            </td>
            <td colspan="5" align="left" style="font-size: 8pt">
                 Rp. <b>{{$data->total_tagihan}}</b>
            </td>
        </tr>
    </table>
        {!! $listcont !!}
</body>

</html>
