<?php

namespace App\Http\Controllers\Billing\NotaStripping;

use App\Http\Controllers\Controller;
use App\Services\Billing\NotaStripping\NotaPerpStrippingServices;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Yajra\DataTables\DataTables;

class PerpanjanganStrippingController extends Controller
{
    protected $service;
    public function __construct(NotaPerpStrippingServices $notaService)
    {
        $this->service = $notaService;
    }

    public function index()
    {
        return view('billing.stripping.perpanjanganstripping.index');
    }

    public function data(Request $request)
    {
        $data = $this->service->getData($request);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('perp_dari', function ($data) {
                $noReq = '<p><b class="font-bold">No Req : </b>' . $data->no_request . '</p>';
                $perpDari = '<p><b class="font-bold">Perp Dari : </b>' . $data->perp_dari . '</p>';
                return $noReq . $perpDari;
            })
            ->editColumn('tgl_request', function ($data) {
                return '<span class="badge badge-pill badge-info p-2"><i class="mdi mdi-calendar"></i> ' . Carbon::parse($data->tgl_request)->translatedFormat('d M Y') . '</span>';
            })
            ->editColumn('action', function ($data) {
                $btn = self::cekNota($data->no_request, $data->nota, $data->koreksi);
                return $btn;
            })
            ->editColumn('do_bl', function ($data) {
                return $data->no_do . ' | ' . $data->no_bl;
            })
            ->rawColumns(['tgl_request', 'action', 'do_bl', 'perp_dari'])
            ->make(true);
    }

    private function previewProformaBtn($koreksi, $no_req)
    {
        $btn = '<a href="' . route('uster.billing.nota_ext_stripping.print.preview_proforma', ['no_req' => $no_req, 'n' => '999', 'koreksi' => $koreksi]) . '" target="_blank" class="link font-14" data-toggle="tooltip" data-placement="top" title="Preview Proforma Stripping"> <b><i> Preview Proforma Stripping</i></b></a> ';
        return $btn;
    }

    public function cekNota($no_req, $nota, $koreksi)
    {
        $no_req = base64_encode($no_req);

        if (($nota <> 'Y') and ($koreksi <> 'Y')) {
            return $this->previewProformaBtn('N', $no_req);
        } else if (($nota == NULL) and ($koreksi == NULL)) {
            return $this->previewProformaBtn('N', $no_req);
        } else if (($nota == 'Y') and ($koreksi <> 'Y' || $koreksi == 'Y')) {
            // return $this->previewProformaBtn('N', $no_req);
            $btn = '<a href="' . route('uster.billing.nota_ext_stripping.print.print_proforma', ['no_req' => $no_req]) . '" target="_blank" class="link font-14" data-toggle="tooltip" data-placement="top" title="Cetak Ulang Nota Stripping"> <b class="font-bold">Cetak Ulang</b></a> ';
            return $btn;
        } else if (($nota <> 'Y') and ($koreksi == 'Y')) {
            return $this->previewProformaBtn('Y', $no_req);
        }
    }

    public function printProformaStrip(Request $request)
    {
        $no_req = base64_decode($request->no_req);
        $allData = $this->service->fetchData($no_req);
        $date = date("d M Y H:i:s");
        $generator = new BarcodeGeneratorPNG();
        $nota = $allData['data']->no_nota_mti ?? 'NOT_FOUND';
        $barcode = $generator->getBarcode($nota, $generator::TYPE_CODE_128);
        $data = $allData;
        $data['barcode'] = $barcode;
        $data['date'] = $date;

        $pdf = Pdf::loadView('billing.stripping.perpanjanganstripping.printproforma', $data);
        $pdf->setPaper('a7');
        $pdf->setOption('margin-top', 1);
        $pdf->setOption('margin-right', 1);
        $pdf->setOption('margin-bottom', 1);
        $pdf->setOption('margin-left', 1);
        return $pdf->stream('invoice.pdf');
    }

    public function previewProforma(Request $request)
    {
        $no_req = base64_decode($request->no_req);
        $koreksi = $request->koreksi;
        $data = $this->service->previewNota($no_req, $koreksi);
        $data = $data->getData(true);
        $data['row_nota'] = json_decode(json_encode($data['row_nota']));
        
        return view('billing.stripping.perpanjanganstripping.previewproforma', $data);
    }

    public function insertProformaStripping(Request $request, $no_req)
    {
        $no_req = base64_decode($no_req);
        $query_cek_nota 	= "SELECT NO_NOTA, STATUS FROM NOTA_STRIPPING WHERE NO_REQUEST = '$no_req'";
        $nota				= DB::connection('uster')->selectOne($query_cek_nota);
        $no_nota_cek		= $nota->no_nota ?? null;
        $nota_status		= $nota->status ?? null;

        try {
            if (($no_nota_cek != NULL && $nota_status == 'BATAL') || ($no_nota_cek == NULL && $nota_status == NULL)){
                $saveProforma = $this->service->insertProforma($no_req, $request->koreksi);
                if($saveProforma->getStatusCode() != 200){
                    throw new Exception('Gagal Menyimpan Proforma Nota', 500);
                }
            } else {
                return response()->redirectToRoute('uster.billing.nota_ext_stripping.print.print_proforma', ['no_req' => base64_encode($no_req)]);
            }

            return response()->redirectToRoute('uster.billing.nota_ext_stripping.print.print_proforma', ['no_req' => base64_encode($no_req), 'first=1']);
        } catch (Exception $th) {
            return redirect()->back()->with(['error' => 'Gagal Menyimpan Proforma Nota Ini, Harap Coba Lagi Nanti']);
        }
    }
}
