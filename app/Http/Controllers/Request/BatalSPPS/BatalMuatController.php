<?php

namespace App\Http\Controllers\Request\BatalSPPS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Request\BatalMuat\BatalMuatService;
use Exception;

class BatalMuatController extends Controller
{
    private $batalMuat;
    public function __construct(BatalMuatService $batalMuat)
    {
        $this->batalMuat = $batalMuat;
    }


    function index()
    {
        return view('request.batal-muat.index');
    }

    function add()
    {
        return view('request.batal-muat.add');
    }

    function view(Request $request)
    {
        return view('request.batal-muat.view', $this->batalMuat->GetDataByNoReq($request->input('no_req')));
    }

    public function datatable(Request $request): JsonResponse
    {
        $listBatalMuat = $this->batalMuat->getData($request);
        return Datatables::of($listBatalMuat)
            ->addColumn('action', function ($listBatalMuat) {
                $link = route('uster.koreksi.batal_muat.view');
                if ($listBatalMuat->nota == 'Y') {
                    return "<i class='fas fa-print'></i>&nbsp; LUNAS <br>
                        <a href='$link?no_req=$listBatalMuat->no_request&no_req2=$listBatalMuat->no_req_itc' class='btn btn-primary'><img src='images/ico_approval.gif' class='img-fluid'>&nbsp; preview data </a>";
                } else {
                    return "<a href='$link?no_req=$listBatalMuat->no_request' class='btn btn-success'><i class='fas fa-print'></i>&nbsp; Lihat Data</a>";
                }
            })
            ->make(true);
    }

    public function viewContainerByRequest(Request $request): JsonResponse
    {
        $listBatalMuat = $this->batalMuat->GetContainerByNoReq($request->input('no_req'));
        return Datatables::of($listBatalMuat)->make(true);
    }

    public function getPMB(Request $request)
    {
        return $this->batalMuat->getPMB($request->input('q'));
    }

    public function getContainer(Request $request)
    {
        return $this->batalMuat->getContainer($request->input('jns'), $request->input('term'));
    }

    public function prayaGetContainer(Request $request)
    {
        $no_cont = $request->no_cont;
        $jenis_bm = $request->jenis_bm;
        return getDisableContainer($no_cont, $jenis_bm);
    }

    public function getContainerHistory(Request $request)
    {
        return $this->batalMuat->getContainerHistory($request->input('no_cont'));
    }

    public function masterVesselPalapa(Request $request)
    {
        return $this->batalMuat->masterVesselPalapa($request->input('term'));
    }


    public function masterPelabuhanPalapa(Request $request)
    {
        return $this->batalMuat->masterPelabuhanPalapa($request->input('term'));
    }

    public function validateContainer(Request $request)
    {
        return $this->batalMuat->validateContainer($request);
    }

    public function save_bm_praya(Request $request)
    {
        return $this->batalMuat->save_bm_praya($request);
    }

    public function save_payment_uster_batal_muat(Request $request)
        $query_cek  = "select NVL(LPAD(MAX(TO_NUMBER(SUBSTR(NO_REQUEST,8,13)))+1,6,0),'000001') AS JUM,
        TO_CHAR(SYSDATE, 'MM') AS MONTH,
        TO_CHAR(SYSDATE, 'YY') AS YEAR
        FROM REQUEST_BATAL_MUAT
        WHERE TGL_REQUEST BETWEEN TRUNC(SYSDATE,'MONTH') AND LAST_DAY(SYSDATE) ";

        $jum_  = DB::connection('uster')->selectOne($query_cek);
        if (is_null($jum_)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil nomor urut request (query DB uster mengembalikan null). Cek koneksi DB uster.'
            ], 500);
        }

        $jum        = $jum_->jum;
        $month      = $jum_->month;
        $year       = $jum_->year;

        $no_req_bm    = "BMU" . $month . $year . $jum;
        $new_id_request   = $no_req_bm;

        $payload_batal_muat = $request->payload_batal_muat;
        if (!is_array($payload_batal_muat)) {
            return response()->json([
                'status' => 'error',
                'message' => 'payload_batal_muat harus berupa array/object JSON yang valid.'
            ], 422);
        }

       

        if (!isset($payload_batal_muat['voyageOut']) || empty($payload_batal_muat['voyageOut'])) {
            $payload_batal_muat['voyageOut'] = $payload_batal_muat['voyageIn'] ?? '';
        }
        if (!isset($payload_batal_muat['tanggal_rencana_out']) || empty($payload_batal_muat['tanggal_rencana_out'])) {
            $payload_batal_muat['tanggal_rencana_out'] = $payload_batal_muat['tanggal_rencana_in'] ?? '';
        }

        try {
            $result = $this->batalMuat->save_payment_praya($payload_batal_muat, $new_id_request);
            if (isset($result['status']) && $result['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to save payment (Praya).'
                ], 400);
            }

            return response()->json([
                'code' => 1,
                'status' => 'success',
                'message' => 'Request Saved successfully.',
                'no_request' => $new_id_request
            ], 200);
        } catch (Exception $e) {
            // Lempar error ke Global Exception Handler (agar log dibuat dinamis sesuai nama menu)
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStartStack(Request $request)
    {
        return $this->batalMuat->getStartStack($request);
    }
}
