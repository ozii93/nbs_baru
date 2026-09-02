<?php

namespace App\Services\Request;

use App\Services\Others\PrayaService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use PDO;

class DeliveryKeTpkRepoService
{

    protected $prayaServices;

    public function __construct(PrayaService $prayaServices)
    {
        $this->prayaServices = $prayaServices;
    }

    public function dataDeliveryLuar($request)
    {

        $from       = $request->has('from') ? $request->from : null;
        $to         = $request->has('to') ? $request->to : null;
        $no_req    = isset($request->search['value']) ? $request->search['value'] : null; //$request->no_req;
        // $id_yard    =    session()->get('IDYARD_STORAGE"];     ')
        // if (isset($from) || isset($to) || isset($no_req)) {
        if (($no_req == NULL) && (isset($from)) && (isset($to))) {
            $query_list = " SELECT NVL(NOTA_DELIVERY.LUNAS, 0) LUNAS, NVL(NOTA_DELIVERY.STATUS, 0) STATUS, REQUEST_DELIVERY.NO_REQUEST,  TO_CHAR( REQUEST_DELIVERY.TGL_REQUEST,'dd Mon yyyy') TGL_REQUEST,  TO_CHAR(REQUEST_DELIVERY.TGL_REQUEST_DELIVERY,'dd/mm/yyyy') TGL_REQUEST_DELIVERY, emkl.NM_PBM as NAMA_EMKL ,request_delivery.VESSEL as NAMA_VESSEL, request_delivery.VOYAGE, yard_area.NAMA_YARD, request_delivery.NO_REQ_ICT, REQUEST_DELIVERY.JN_REPO
            FROM REQUEST_DELIVERY, NOTA_DELIVERY, v_mst_pbm emkl, yard_area
            WHERE  REQUEST_DELIVERY.KD_EMKL = emkl.KD_PBM AND emkl.KD_CABANG = '05'
            AND REQUEST_DELIVERY.ID_YARD = YARD_AREA.ID
            AND NOTA_DELIVERY.NO_REQUEST(+) = REQUEST_DELIVERY.NO_REQUEST
            AND request_delivery.TGL_REQUEST BETWEEN TO_DATE('$from','yy-mm-dd') AND TO_DATE('$to','yy-mm-dd')
           AND request_delivery.PERALIHAN NOT IN ('RELOKASI','STUFFING','STRIPPING')
           AND request_delivery.DELIVERY_KE = 'TPK'
            ORDER BY REQUEST_DELIVERY.TGL_REQUEST DESC
                                ";
        } else {

            $query_list = "SELECT * FROM  (SELECT NVL (NOTA_DELIVERY.LUNAS, 0) LUNAS,
                                    NVL (NOTA_DELIVERY.STATUS, 0) STATUS,
                                    REQUEST_DELIVERY.NO_REQUEST,
                                    TO_CHAR (REQUEST_DELIVERY.TGL_REQUEST, 'dd Mon yyyy') TGL_REQUEST,
                                    TO_CHAR (REQUEST_DELIVERY.TGL_REQUEST_DELIVERY, 'dd/mm/yyyy')
                                    TGL_REQUEST_DELIVERY,
                                    emkl.NM_PBM AS NAMA_EMKL,
                                    request_delivery.VESSEL AS NAMA_VESSEL,
                                    request_delivery.VOYAGE,
                                    request_delivery.NO_REQ_ICT,
                                    REQUEST_DELIVERY.JN_REPO
                            FROM REQUEST_DELIVERY,
                                    NOTA_DELIVERY,
                                    v_mst_pbm emkl
                            WHERE     REQUEST_DELIVERY.KD_EMKL = emkl.KD_PBM
                                    AND emkl.KD_CABANG = '05'
                                    AND NOTA_DELIVERY.NO_REQUEST(+) = REQUEST_DELIVERY.NO_REQUEST
                                    AND request_delivery.DELIVERY_KE = 'TPK'
                                    AND request_delivery.REQUEST_BY IS NULL
                        ORDER BY REQUEST_DELIVERY.TGL_REQUEST DESC)
                        WHERE ROWNUM <= 100
                            ";
        }

        return DB::connection('uster')->select($query_list);
    }

    function view($noReq)
    {
        $query_request    = "SELECT REQUEST_DELIVERY.KETERANGAN, request_delivery.DELIVERY_KE, REQUEST_DELIVERY.NO_REQUEST, TO_CHAR(REQUEST_DELIVERY.TGL_REQUEST_DELIVERY,'dd/mm/yyyy') TGL_REQUEST_DELIVERY, emkl.NM_PBM AS NAMA_EMKL, REQUEST_DELIVERY.VESSEL, REQUEST_DELIVERY.VOYAGE, REQUEST_DELIVERY.PEB, REQUEST_DELIVERY.NPE
        FROM REQUEST_DELIVERY INNER JOIN V_MST_PBM emkl ON REQUEST_DELIVERY.KD_EMKL = emkl.KD_PBM
        WHERE REQUEST_DELIVERY.NO_REQUEST = '$noReq'";
        $row_request    = DB::connection('uster')->selectOne($query_request);

        $query_list = "SELECT MASTER_CONTAINER.*, CONTAINER_DELIVERY.*
						  FROM MASTER_CONTAINER
						       INNER JOIN
						          CONTAINER_DELIVERY
						       ON MASTER_CONTAINER.NO_CONTAINER = CONTAINER_DELIVERY.NO_CONTAINER
						       INNER JOIN
						            HISTORY_CONTAINER
						       ON HISTORY_CONTAINER.NO_CONTAINER = CONTAINER_DELIVERY.NO_CONTAINER
						       AND HISTORY_CONTAINER.NO_REQUEST = CONTAINER_DELIVERY.NO_REQUEST
						       AND HISTORY_CONTAINER.KEGIATAN = 'REQUEST DELIVERY'
						 WHERE CONTAINER_DELIVERY.NO_REQUEST = '$noReq'
						 ORDER BY TGL_UPDATE ASC";
        $result_table =  DB::connection('uster')->select($query_list);

        return  [
            "row_request" =>  $row_request,
            "result_table" =>  $result_table,
        ];
    }

    function edit($no_req, $no_req2)
    {
        $kd_cbg  = 5;
        $q_ceknota = "SELECT NO_REQUEST, STATUS, LUNAS FROM NOTA_DELIVERY WHERE NO_REQUEST = '$no_req' AND STATUS <> 'BATAL'";

        $rwc       = DB::connection('uster')->selectOne($q_ceknota);
        // dd($rwc);

        // if ($rwc->no_request != NULL && $rwc->lunas == 'YES') {

        // header('Location: '.HOME.APPID);
        // } else {

        $query_request    = "SELECT
                                request_delivery.NO_REQ_STUFFING,
                                request_delivery.PEB,
                                request_delivery.REQUEST_BY,
                                request_delivery.NPE,
                                request_delivery.TGL_MUAT,
                                request_delivery.TGL_STACKING,
                                request_delivery.KETERANGAN,
                                request_delivery.NO_REQUEST,
                                request_delivery.NO_RO,
                                request_delivery.JN_REPO,
                                TO_CHAR(request_delivery.TGL_REQUEST_DELIVERY,'yyyy/mm/dd') TGL_REQUEST_DELIVERY,
                                emkl.NM_PBM AS NAMA_PBM,
                                emkl.KD_PBM KD_PBM,
                                pmb.NM_PBM as NAMA_PBM2,
                                pmb.KD_PBM as KD_PBM2,
                                ves.NM_AGEN,
                                    ves.KD_AGEN,
                                    ves.KD_KAPAL,
                                    ves.NM_KAPAL,
                                    ves.VOYAGE_IN,
                                    ves.VOYAGE_OUT,
                                    ves.VOYAGE,
                                    ves.NO_UKK,
                                    ves.NO_BOOKING,
                                    TO_CHAR(ves.tgl_jam_berangkat,'dd/mm/rrrr') TGL_BERANGKAT,
                                    TO_CHAR(ves.tgl_jam_tiba,'dd/mm/rrrr') TGL_TIBA,
                                    ves.PELABUHAN_ASAL KD_PELABUHAN_ASAL,
                                    ves.PELABUHAN_TUJUAN KD_PELABUHAN_TUJUAN
                                    --pel_asal.KD_PELABUHAN as KD_PELABUHAN_ASAL,
                                    --pel_asal.NM_PELABUHAN as NM_PELABUHAN_ASAL,
                                    --pel_tujuan.KD_PELABUHAN as KD_PELABUHAN_TUJUAN,
                                    --pel_tujuan.NM_PELABUHAN as NM_PELABUHAN_TUJUAN
                                FROM request_delivery INNER JOIN v_mst_pbm emkl ON request_delivery.KD_EMKL = emkl.KD_PBM
                                    INNER JOIN v_mst_pbm pmb ON request_delivery.KD_EMKL = pmb.KD_PBM
                                    INNER JOIN v_pkk_cont ves ON request_delivery.NO_BOOKING = ves.NO_BOOKING
                                    --LEFT JOIN v_mst_pelabuhan pel_asal ON request_delivery.KD_PELABUHAN_ASAL = pel_asal.KD_PELABUHAN
                                    --LEFT JOIN v_mst_pelabuhan pel_tujuan ON request_delivery.KD_PELABUHAN_TUJUAN = pel_tujuan.KD_PELABUHAN
                                WHERE REQUEST_DELIVERY.NO_REQUEST = '$no_req'";
        $row_request    = DB::connection('uster')->selectOne($query_request);

        //debug($row_request);die;
        return [
            "kd_cbg" => $kd_cbg,
            "no_req" => $no_req,
            "no_req2" => $no_req2,
            "row_request" => $row_request
        ];
        // }
    }

    function cekEarly($request)
    {
        //$arrPost    = $request->
        //bug ($request->die        $NO_BOOKING = $request->NO_BOOKING;
        $KD_PBM     = $request->KD_PBM;
        $NO_UKK        = $request->NO_UKK;
        //$TYPE	    = $request->TYPE;
        $_HARI      = 4;

        echo "Y";
        die();
        // //echo $NO_BOOKING;echo $KD_PBM; exit;
        // // outputraw();
        // // require_lib('acl.php');
        // // $acl = new ACL();
        // // $acl->load();
        // // $aclist = $acl->getLogin()->info;
        // // $KD_CABANG = ($aclist['KD_CABANG'] == '') ? '00' : $aclist['KD_CABANG'];
        // //if($KD_CABANG == '03'){
        // //		if($TYPE == '07'){
        // //	$sql = "SELECT TO_CHAR(DOC_CLOSING_DATE_REEFER,'YYYY-MM-DD HH24:MI:SS') AS DOCDATE FROM TTD_VESSEL_SCHEDULE WHERE NO_UKK='".$NO_UKK."' AND VS_STATUS='P'";
        // //}else{
        // $sql = "SELECT TO_CHAR(DOC_CLOSING_DATE_DRY,'YYYY-MM-DD HH24:MI:SS') AS DOCDATE FROM TTD_VESSEL_SCHEDULE WHERE NO_UKK='" . $NO_UKK . "' ";
        // //}

        // $rsx = DB::connection('ora')->selectOne($sql);
        // // $rsx 	= DB::connection('uster')->select($query);

        // // $rsx = $rsx->RecordCount();

        // // if($rsx->RecordCount()>0){

        // // echo "ono isinya";die;

        // // }
        // // else
        // // {
        // // echo "ora ono";die;
        // // }

        // // debug($rsx);die;

        // if (count($rsx) > 0) {
        //     $datedoc = $rsx->docdate;
        //     $sql2 = "SELECT
        //                             CASE
        //                             WHEN TO_DATE('" . $datedoc . "','YYYY-MM-DD HH24:MI:SS') > SYSDATE  THEN
        //                                     'Y'
        //                                 ELSE
        //                                     'T'
        //                             END AS VALIDX
        //                             FROM
        //                             DUAL";
        //     $rows = DB::connection('ora')->selectOne($sql2);
        //     $result = $rows->validx;
        //     if ($result == 'T') {
        //         echo "T";
        //         die;
        //     } else {
        //         echo "Y";
        //         die;
        //     }
        // } else {
        //     $sql = "SELECT JENIS, TO_CHAR(TGL_JAM_TIBA,'YYYY-MM-DD') AS TGL_JAM_TIBA FROM TTH_CONT_BOOKING,V_PKK_CONT WHERE TTH_CONT_BOOKING.NO_UKK=V_PKK_CONT.NO_UKK AND TTH_CONT_BOOKING.NO_BOOKING='" . $NO_BOOKING . "'";
        //     $rsx = DB::connection('ora')->selectOne($sql);
        //     if (count($rsx) > 0) {
        //         if ($rsx->jenis == '1') {
        //             $sqljam = "SELECT TRUNC((((86400*(TO_DATE('" . $rsx->tgl_jam_tiba . "','YYYY-MM-DD HH24:MI:SS')-SYSDATE))/60)/60)/24) AS JMLHARI FROM DUAL";
        //             $rsj = DB::connection('ora')->selectOne($sqljam);
        //             if ($rsj) {
        //                 if ($rsj->jmlhari >  $_HARI) {
        //                     $sqlck = "SELECT * FROM TTD_BOOKING_PBM WHERE NO_BOOKING='" . $NO_BOOKING . "' AND KD_PBM='" . $KD_PBM . "'";
        //                     $rsck =  DB::connection('ora')->select($sqlck);
        //                     if (count($rsck) > 0) {
        //                         echo "Y";
        //                     } else {
        //                         echo "N";
        //                         die;
        //                     }
        //                 } else {
        //                     echo "Y";
        //                     die;
        //                 }
        //             }
        //         } else {
        //             echo "Y";
        //             die;
        //         }
        //     } else {
        //         echo "N";
        //         die;
        //     }
        // }
    }

    function editDo($request)
    {
        DB::beginTransaction();;
        try {
            $ID_USER                = session()->get("LOGGED_STORAGE");
            $NM_USER                = session()->get("NAME");
            $ID_YARD                = session()->get("IDYARD_STORAGE");
            $NM_USER                = session()->get("NAME");
            $KD_PELANGGAN          = $request->KD_PELANGGAN;
            $KD_PELANGGAN2          = $request->KD_PELANGGAN2;
            $NO_REQUEST2              = $request->NO_REQUEST2;
            $NO_REQUEST              = $request->NO_REQUEST;
            $NO_BOOKING            = $request->NO_BOOKING;
            $TGL_REQ                = $request->TGL_REQ;
            $PEB                    = $request->NO_PEB;
            $NPE                     = $request->NO_NPE;
            $NO_RO                 = $request->NO_RO;
            $KETERANGAN            = $request->KETERANGAN;
            $SHIFT_RFR                = $request->SHIFT_RFR;
            $TGL_MUAT                = $request->TGL_MUAT;
            $TGL_STACKING            = $request->TGL_STACKING;
            //kapal
            $TGL_BERANGKAT         = $request->TGL_BERANGKAT;
            $KD_PELABUHAN_ASAL     = $request->KD_PELABUHAN_ASAL;
            $KD_PELABUHAN_TUJUAN      = $request->KD_PELABUHAN_TUJUAN;
            $NM_KAPAL               = $request->NM_KAPAL;
            $KD_KAPAL                 = $request->KD_KAPAL;
            $VOYAGE_IN             = $request->VOYAGE_IN;
            $VOYAGE_OUT             = $request->VOYAGE_OUT;
            $NO_UKK                = $request->NO_UKK;
            $ETD                     = $request->ETD;
            $ETA                     = $request->ETA;
            $CALL_SIGN               = $request->CALL_SIGN;
            $NM_AGEN                 = $request->NM_AGEN;
            $KD_AGEN                 = $request->KD_AGEN;
            $VOYAGE                 = $request->VOYAGE;


            //==================update uster=========================
            $query_req     = DB::connection('uster')
                ->table('request_delivery')
                ->where('NO_REQUEST', $NO_REQUEST)
                ->update([
                    'KD_EMKL' => $KD_PELANGGAN2,
                    'KD_EMKL2' => $KD_PELANGGAN2,
                    'KETERANGAN' => $KETERANGAN,
                    'ID_USER' => $ID_USER,
                    'PEB' => $PEB,
                    'NPE' => $NPE,
                    'NO_RO' => $NO_RO
                ]);;



            $query_update_vessel = DB::connection('uster')
                ->table('V_PKK_CONT')
                ->where('NO_BOOKING', $NO_BOOKING)
                ->update([
                    'KD_KAPAL' => $KD_KAPAL,
                    'NM_KAPAL' => $NM_KAPAL,
                    'VOYAGE_IN' => $VOYAGE_IN,
                    'VOYAGE_OUT' => $VOYAGE_OUT,
                    'TGL_JAM_TIBA' => DB::raw("TO_DATE('$ETA', 'DD-MM-YYYY HH24:MI:SS')"),
                    'TGL_JAM_BERANGKAT' => DB::raw("TO_DATE('$ETD', 'DD-MM-YYYY HH24:MI:SS')"),
                    'NO_UKK' => $NO_UKK,
                    'NM_AGEN' => $NM_AGEN,
                    'KD_AGEN' => $KD_AGEN,
                    'PELABUHAN_ASAL' => $KD_PELABUHAN_ASAL,
                    'PELABUHAN_TUJUAN' => $KD_PELABUHAN_TUJUAN,
                    'VOYAGE' => $VOYAGE,
                    'CALL_SIGN' => $CALL_SIGN
                ]);;

            DB::commit();
            return [
                'status' => [
                    'code' => 200,
                    'msg' => 'Success Processing Data',
                ],
            ];
        } catch (Exception $th) {
            DB::rollBack();
            return [
                'status' => [
                    'msg' => $th->getMessage() != '' ? $th->getMessage() : 'Err',
                    'code' => $th->getCode() != '' ? $th->getCode() : 500,
                ],
                'data' => null,
                'err_detail' => $th,
                'message' => $th->getMessage() != '' ? $th->getMessage() : 'Terjadi Kesalahan Saat Input Data, Harap Coba lagi!'
            ];
        }
    }

    function contDelivery($request)
    {
        // dd($request);
        $no_cont        = strtoupper($request->term);
        $jn_repo        = strtoupper($request->jn_repo);

        if ($jn_repo == 'EMPTY') {
            $result = DB::connection('uster')->table('MASTER_CONTAINER as a')
                ->select(
                    'a.NO_CONTAINER',
                    'a.SIZE_',
                    'a.TYPE_',
                    DB::raw("CASE WHEN a.NO_BOOKING = 'VESSEL_NOTHING' THEN 'BS05I100001' ELSE a.NO_BOOKING END AS NO_BOOKING"),
                    'b.STATUS_CONT as STATUS',
                    DB::raw("TO_DATE('','dd/mm/rrrr') as TGL_STACK"),
                    DB::raw("'UST' as ASAL")
                )
                ->join('HISTORY_CONTAINER as b', 'a.NO_CONTAINER', '=', 'b.NO_CONTAINER')
                ->where('a.NO_CONTAINER', 'like', $no_cont . '%')
                ->where('a.LOCATION', 'IN_YARD')
                ->where('b.status_cont', 'MTY')
                ->whereRaw("b.TGL_UPDATE = (SELECT MAX(TGL_UPDATE) FROM HISTORY_CONTAINER WHERE NO_CONTAINER LIKE ? AND AKTIF IS NULL)", [$no_cont . '%'])
                ->whereIn('b.kegiatan', [
                    'REALISASI STRIPPING',
                    'GATE IN',
                    'REQUEST DELIVERY',
                    'PERP DELIVERY',
                    'BATAL STUFFING',
                    'BORDER GATE IN'
                ])
                ->get();
        } else if ($jn_repo == 'FULL') {
            $result = DB::connection('uster')->table('MASTER_CONTAINER as a')
                ->select(
                    'a.NO_CONTAINER',
                    'a.SIZE_',
                    'a.TYPE_',
                    DB::raw("CASE WHEN a.NO_BOOKING = 'VESSEL_NOTHING' THEN 'BS05I100001' ELSE a.NO_BOOKING END AS NO_BOOKING"),
                    'b.STATUS_CONT as STATUS',
                    DB::raw("TO_DATE('','dd/mm/rrrr') as TGL_STACK"),
                    DB::raw("'UST' as ASAL")
                )
                ->join('HISTORY_CONTAINER as b', 'a.NO_CONTAINER', '=', 'b.NO_CONTAINER')
                ->where('a.NO_CONTAINER', 'like', $no_cont . '%')
                ->where('a.LOCATION', 'IN_YARD')
                ->where('b.status_cont', 'FCL')
                ->whereRaw("b.TGL_UPDATE = (SELECT MAX(TGL_UPDATE) FROM HISTORY_CONTAINER WHERE NO_CONTAINER LIKE ? AND AKTIF IS NULL)", [$no_cont . '%'])
                ->whereIn('b.kegiatan', [
                    'REALISASI STUFFING',
                    'GATE IN',
                    'REQUEST DELIVERY',
                    'PERP DELIVERY',
                    'BATAL STRIPPING',
                    'BORDER GATE IN'
                ])
                ->get();
        } else {
            $historySub = DB::connection('uster')
                ->table('HISTORY_CONTAINER as h2')
                ->selectRaw("
                    h2.*,
                    ROW_NUMBER() OVER (
                        PARTITION BY h2.NO_CONTAINER, h2.NO_BOOKING
                        ORDER BY h2.TGL_UPDATE DESC
                    ) AS rn
                ")
                ->whereNull('h2.AKTIF')
                ->whereIn('h2.KEGIATAN', [
                    'REALISASI STUFFING',
                    'REQUEST BATALMUAT'
                ]);

            $stuffingSub = DB::connection('uster')
                ->table('CONTAINER_STUFFING as cs')
                ->selectRaw("
                    cs.*,
                    ROW_NUMBER() OVER (
                        PARTITION BY cs.NO_CONTAINER
                        ORDER BY cs.TGL_REALISASI DESC
                    ) AS rn
                ")
                ->where('cs.AKTIF', 'T')
                ->whereNotNull('cs.TGL_REALISASI');

            $result = DB::connection('uster')
                ->table('MASTER_CONTAINER as m')
                ->select(
                    'm.NO_CONTAINER',
                    'm.SIZE_',
                    'm.TYPE_',
                    'm.NO_BOOKING',
                    's.TGL_REALISASI as REALISASI_STUFFING',
                    'h.STATUS_CONT as STATUS',
                    'h.NO_REQUEST'
                )

                ->joinSub($historySub, 'h', function ($join) {
                    $join->on('m.NO_CONTAINER', '=', 'h.NO_CONTAINER')
                        ->on('m.NO_BOOKING', '=', 'h.NO_BOOKING')
                        ->where('h.rn', 1);
                })

                ->joinSub($stuffingSub, 's', function ($join) {
                    $join->on('s.NO_CONTAINER', '=', 'm.NO_CONTAINER')
                        ->where('s.rn', 1);
                })

                ->where('m.LOCATION', 'IN_YARD')
                ->where('m.NO_CONTAINER', 'like', '%' . $no_cont . '%')
                ->get();
        }

        return $result;
    }
    function carrierPraya($request)
    {
        // dd($request);
        $voyage     = $request->voyage;
        $term       = strtoupper($request->term);
        try {
            // $json = getDataFromUrlGuzzle(env('PRAYA_API_TOS') . "/api/getOperator?orgId=" . env('PRAYA_ITPK_PNK_ORG_ID') . "&terminalId=" . env('PRAYA_ITPK_PNK_TERMINAL_ID') . "&voyage=" . $voyage . "&search=" . $term);
            // $json = $this->prayaServices->getDatafromUrl(env('PRAYA_API_TOS') . "/api/getOperator?orgId=" . env('PRAYA_ITPK_PNK_ORG_ID') . "&terminalId=" . env('PRAYA_ITPK_PNK_TERMINAL_ID') . "&voyage=" . $voyage . "&search=" . $term);

            $json = getDataFromUrl(env('PRAYA_API_TOS') . "/api/getOperator?orgId=" . env('PRAYA_ITPK_PNK_ORG_ID') . "&terminalId=" . env('PRAYA_ITPK_PNK_TERMINAL_ID') . "&voyage=" . $voyage . "&search=" . $term);
            $json = json_decode($json, true);

            if ($json['code'] == 1) {
                return $json['data'];
            } else {
                return 'N';
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function cekNoCont($request)
    {
        return 'Y';
    }

    function addCont($request)
    {

        DB::beginTransaction();;
        try {
            $nm_user        = session()->get("NAME");
            $no_cont        = $request->NO_CONT;
            $no_req         = $request->NO_REQ;
            $no_req2        = $request->NO_REQ2;
            $status         = $request->STATUS;
            $hz             = $request->HZ;
            $keterangan     = $request->KETERANGAN;
            $no_seal        = $request->NO_SEAL;
            $berat          = $request->BERAT;
            $via            = $request->VIA;
            $komoditi       = $request->KOMODITI;
            $kd_komoditi    = $request->KD_KOMODITI;
            $size           = $request->SIZE;
            $tipe           = $request->TIPE;
            $status         = $request->STATUS;
            $no_booking     = $request->NO_BOOKING;
            $jn_repo        = $request->JN_REPO;
            $ex_pmb         = $request->EX_PMB;
            $no_ukk         = $request->NO_UKK;
            $tgl_delivery   = $request->tgl_delivery;
            $imoclass       = $request->IMO;
            $unnumber       = $request->UNNUMBER;
            $height         = $request->HEIGHT;
            $temperature    = $request->TEMP;
            $carrier        = $request->CARRIER;
            $oh_size        = $request->OH;
            $ow_size        = $request->OW;
            $ol_size        = $request->OL;
            $id_user        = session()->get("LOGGED_STORAGE");
            $id_yard_       = session()->get("IDYARD_STORAGE");
            $asal           = $request->asal;
            $tgl_stack      = $request->tgl_stack;
            $cont_limit     = $request->CONT_LIMIT;

            $bp_id = $ex_pmb;
            $returnMsg = '';

            $cek_gati = "SELECT AKTIF FROM CONTAINER_RECEIVING WHERE NO_CONTAINER = '$no_cont' order by AKTIF DESC";
            $rw_gati = DB::connection('uster')->selectOne($cek_gati);
            $aktif_rec = $rw_gati->aktif ?? null;
            if ($aktif_rec == 'Y') {
                $returnMsg = 'EXIST_REC';
            }

            $cek_req_satu_kapal = "select no_booking from container_delivery, request_delivery where container_delivery.no_request = request_delivery.no_request
					and no_container = '$no_cont' and no_booking = '$no_booking' and aktif = 'Y'";
            $rw_cekkpl = DB::connection('uster')->selectOne($cek_req_satu_kapal);
            $nobokk_lama =  $rw_cekkpl->no_booking ?? null;
            if ($nobokk_lama != NULL) {
                $returnMsg = 'EXIST_DEL_BY_BOOKING';
            }

            $cek_stuf = "SELECT AKTIF
                FROM CONTAINER_STUFFING
               WHERE NO_CONTAINER = '$no_cont' AND AKTIF = 'Y'";
            $r_stuf = DB::connection('uster')->selectOne($cek_stuf);
            $l_stuf = $r_stuf->aktif ?? null;
            if ($l_stuf == 'Y') {
                $returnMsg = "EXIST_STUF";
            }

            $cek_strip = "SELECT AKTIF
                FROM CONTAINER_STRIPPING
               WHERE NO_CONTAINER = '$no_cont' AND AKTIF = 'Y'";
            $r_strip = DB::connection('uster')->selectOne($cek_strip);
            $l_strip = $r_strip->aktif ?? null;
            if ($l_strip == 'Y') {
                $returnMsg = "EXIST_STRIP";
            }

            $query_cek_cont            = "SELECT NO_BOOKING, COUNTER
											FROM  MASTER_CONTAINER
											WHERE NO_CONTAINER ='$no_cont'
											";
            $row_cek_cont     = DB::connection('uster')->selectOne($query_cek_cont);
            $cek_book         = $row_cek_cont->no_booking ?? null;
            $cek_counter    = $row_cek_cont->counter ?? null;

            //if($cek_book == NULL){
            $q_update_book = "UPDATE MASTER_CONTAINER SET NO_BOOKING = '$no_booking' WHERE NO_CONTAINER = '$no_cont'";
            DB::connection('uster')->statement($q_update_book);
            //}
            $q_getcounter = "SELECT NO_BOOKING, COUNTER FROM MASTER_CONTAINER WHERE NO_CONTAINER = '$no_cont'";
            $rw_getcounter = DB::connection('uster')->selectOne($q_getcounter);
            $cur_counter = $rw_getcounter->counter ?? null;
            $cur_booking = $rw_getcounter->no_booking ?? null;


            if ($cur_booking == NULL) {
                $cur_booking == 'VESSEL_NOTHING';
            }

            if ($cur_counter == NULL) {
                $type = '';

                $q_insert_no_container = "INSERT INTO MASTER_CONTAINER(NO_CONTAINER, SIZE_, TYPE_, LOCATION, NO_BOOKING, COUNTER) VALUES('$no_cont','$size','$type','IN_YARD','$no_booking','1')";

                DB::connection('uster')->statement($q_insert_no_container);
            } else {
                $q_update_book2 = "UPDATE MASTER_CONTAINER SET NO_BOOKING = '$no_booking', COUNTER = '$cur_counter' WHERE NO_CONTAINER = '$no_cont'";
                DB::connection('uster')->statement($q_update_book2);
            }

            $query_cek        = "SELECT b.NO_CONTAINER, b.LOCATION --, NVL((), '') as STATUS
                                FROM MASTER_CONTAINER b
                                WHERE b.NO_CONTAINER = '$no_cont'";
            $query_cek2 = "SELECT NO_CONTAINER FROM CONTAINER_DELIVERY WHERE NO_CONTAINER = '$no_cont' AND AKTIF = 'Y'";

            $row_cek        = DB::connection('uster')->selectOne($query_cek);

            $row_cek2        = DB::connection('uster')->selectOne($query_cek2);

            $no_cont        = $row_cek->no_container;
            $location        = $row_cek->location;
            if ($asal == 'TPK') {
                $location = 'IN_YARD';
            }
            $req_dev        = $row_cek2->no_container ?? null;
            //ECHO $query_cek;
            if (($no_cont <> NULL) && ($location == 'IN_YARD') && ($req_dev <> NULL)) {
                $returnMsg = "SDH_REQUEST";
                throw new Exception('Container Masih aktif di Request Delivery', 400);
            } else if (($no_cont <> NULL) && ($location == 'GATI') && ($req_dev == NULL)) {
                $returnMsg = "BLM_PLACEMENT";
                throw new Exception('Container Belum Placement', 400);
            } else if (($no_cont <> NULL) && ($location == 'IN_YARD') && ($req_dev == NULL)) {



                // =========================================== NBS_OPUS ==============================================//
                $di = 'D';
                $pdo = DB::connection('uster')->getPdo();
                $param_detail = array(
                    'in_nocont' => $no_cont,
                    'in_noreq' => $no_req,
                    'in_reqnbs' => $no_req2,
                    'in_user' => $id_user,
                    'in_jnrepo' => $jn_repo,
                    'in_status' => $status,
                    'in_hz' => $hz,
                    'in_komoditi' => $komoditi,
                    'in_kdkomoditi' => $kd_komoditi,
                    'in_keterangan' => $keterangan,
                    'in_noseal' => $no_seal,
                    'in_berat' => $berat,
                    'in_via' => $via,
                    'in_idyard' => $id_yard ?? '',
                    'in_startstack' => $tgl_stack,
                    'in_tgldelivery' => $tgl_delivery,
                    'in_asalcont' => $asal_cont ?? '',
                    'in_bpid' => $bp_id,
                    'in_nobooking' => $no_booking,
                    'in_idvsb' => $no_ukk,
                    'in_counter' => $cur_counter,
                    'in_imo' => $imoclass,
                    'in_hg' => $height,
                    'in_ship' => $di,
                    'in_car' => $carrier,
                    'in_temp' => $temperature,
                    'in_oh' => $oh_size,
                    'in_ow' => $ow_size,
                    'in_ol' => $ol_size,
                    'in_un' => $unnumber,
                    'in_contlimit' => $cont_limit,
                    'out_msgdet' => ''
                );

                $msgout = "";
                $querydet = "declare begin USTER.PACK_CREATE_REQ_DELIVERY_REPO.CREATE_DETAIL_REPO_PRAYA(:in_nocont,:in_noreq,
                            :in_reqnbs,:in_user,:in_jnrepo,:in_status,:in_hz,:in_komoditi,:in_kdkomoditi,:in_keterangan,:in_noseal,:in_berat,:in_via,
                            :in_idyard,:in_startstack,:in_tgldelivery,:in_asalcont,:in_bpid,:in_nobooking,:in_idvsb,:in_counter,:in_imo,:in_hg,
                            :in_ship,:in_car,:in_temp,:in_oh,:in_ow,:in_ol,:in_un,:in_contlimit,:out_msgdet); end;";

                $stmt = $pdo->prepare($querydet);
                foreach ($param_detail as $key => &$value) {
                    $stmt->bindParam(":$key", $value, PDO::PARAM_STR);
                }
                $stmt->bindParam(":out_msgdet", $outMsg, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                $stmt->execute();

                // ===========================================  NBS_OPUS ==============================================//
                if ($msgout == 'OK1') {

                    $query_cek        = "SELECT a.ID_YARD_AREA FROM placement b, blocking_area a
                                    WHERE b.ID_BLOCKING_AREA = a.ID
                                    AND b.NO_CONTAINER = '$no_cont'";
                    $row_cek        = DB::connection('uster')->selectOne($query_cek);
                    $id_yard        = $row_cek->id_yard_area;

                    if ($jn_repo == 'EMPTY') {
                        $query_tgl_stack_depo = "SELECT TGL_UPDATE , NO_REQUEST, KEGIATAN
	                                            FROM HISTORY_CONTAINER
	                                            WHERE no_container = '$no_cont'
	                                            AND kegiatan IN ('GATE IN','REALISASI STRIPPING','PERPANJANGAN STUFFING','REQUEST STUFFING')
	                                            ORDER BY TGL_UPDATE DESC";

                        $row_tgl_stack_depo        = DB::connection('uster')->selectOne($query_tgl_stack_depo);
                        //$tgl_stack	= $row_tgl_stack_depo["TGL_STACK"];
                        $ex_keg    = $row_tgl_stack_depo->kegiatan;
                        $no_re_st    = $row_tgl_stack_depo->no_request;
                        if ($ex_keg == "REALISASI STRIPPING") {
                            $qtgl_r = "SELECT TGL_REALISASI FROM CONTAINER_STRIPPING WHERE NO_CONTAINER = '$no_cont' AND NO_REQUEST = '$no_re_st'";
                            $rtgl_r = DB::connection('uster')->selectOne($qtgl_r);
                            $start_stack = $rtgl_r->tgl_realisasi;
                            $asal_cont         = 'DEPO';
                        } else if ($ex_keg == "GATE IN") {
                            $qtgl_r = "SELECT TGL_IN FROM GATE_IN WHERE NO_CONTAINER = '$no_cont' AND NO_REQUEST = '$no_re_st'";
                            $rtgl_r = DB::connection('uster')->selectOne($qtgl_r);
                            $start_stack = $rtgl_r->tgl_in;
                            $asal_cont         = 'DEPO';
                        } else if ($ex_keg == "PERPANJANGAN STUFFING") {
                            $qtgl_r = "SELECT END_STACK_PNKN FROM CONTAINER_STUFFING WHERE NO_REQUEST = '$no_re_st' AND NO_CONTAINER = '$no_cont'";
                            $rtgl_r = DB::connection('uster')->selectOne($qtgl_r);
                            $start_stack = $rtgl_r->end_stack_pnkn;
                            $asal_cont         = 'DEPO';
                        } else if ($ex_keg == "REQUEST STUFFING") {
                            $qtgl_r = "SELECT START_PERP_PNKN FROM CONTAINER_STUFFING WHERE NO_REQUEST = '$no_re_st' AND NO_CONTAINER = '$no_cont'";
                            $rtgl_r = DB::connection('uster')->selectOne($qtgl_r);
                            $start_stack = $rtgl_r->start_perp_pnkn;
                            $asal_cont         = 'DEPO';
                        }
                    } else {

                        $query_cek1        = "SELECT tes.NO_REQUEST,
                                    CASE SUBSTR(KEGIATAN,9)
                                        WHEN 'RECEIVING' THEN (SELECT CONCAT('RECEIVING_',a.RECEIVING_DARI) FROM request_receiving a WHERE a.NO_REQUEST = tes.NO_REQUEST)
										WHEN 'NGAN STUFFING' THEN
										SUBSTR(KEGIATAN,14)
										WHEN 'NGAN STRIPPING' THEN
										SUBSTR(KEGIATAN,14)
										WHEN 'I STRIPPING' THEN
                                        SUBSTR(KEGIATAN,11)
										WHEN 'I STUFFING' THEN
                                        SUBSTR(KEGIATAN,11)
                                        WHEN 'IVERY' THEN
							            SUBSTR (KEGIATAN, 6)
                                        ELSE SUBSTR(KEGIATAN,9)
                                    END KEGIATAN
                                FROM (SELECT TGL_UPDATE, NO_REQUEST,KEGIATAN FROM history_container WHERE no_container = '$no_cont' and kegiatan IN ('REQUEST RECEIVING','REQUEST STRIPPING','REQUEST STUFFING','REQUEST RELOKASI','PERPANJANGAN STUFFING','PERPANJANGAN STRIPPING','REALISASI STRIPPING', 'REALISASI STUFFING', 'REQUEST DELIVERY','PERP DELIVERY') AND AKTIF IS NULL) tes
                                WHERE tes.TGL_UPDATE=(SELECT MAX(TGL_UPDATE) FROM history_container WHERE no_container = '$no_cont' and kegiatan IN ('REQUEST RECEIVING','REQUEST STRIPPING','REQUEST STUFFING','REQUEST RELOKASI','PERPANJANGAN STUFFING','PERPANJANGAN STRIPPING','REALISASI STRIPPING', 'REALISASI STUFFING', 'REQUEST DELIVERY','PERP DELIVERY') AND AKTIF IS NULL)
								ORDER BY KEGIATAN DESC";
                        $row_cek1        = DB::connection('uster')->selectOne($query_cek1);
                        $no_request        = $row_cek1->no_request;
                        $kegiatan        = $row_cek1->kegiatan;

                        if ($kegiatan == 'RECEIVING_LUAR') {
                            $query_cek1        = " SELECT SUBSTR(TO_CHAR(b.TGL_IN,'dd/mm/rrrr'),1,10) START_STACK FROM GATE_IN b WHERE b.NO_CONTAINER = '$no_cont' AND b.NO_REQUEST = '$no_request'";
                            $row_cek1        = DB::connection('uster')->selectOne($query_cek1);
                            $start_stack    = $row_cek1->start_stack;
                            $asal_cont         = 'DEPO';
                        } else if ($kegiatan == 'RECEIVING_TPK') {
                            $query_cek1        = "SELECT TO_CHAR(TGL_BONGKAR,'dd/mm/rrrr') START_STACK FROM container_receiving WHERE NO_CONTAINER = '$no_cont' AND NO_REQUEST = '$no_request'";
                            $row_cek1        = DB::connection('uster')->selectOne($query_cek1);
                            $start_stack    = $row_cek1->start_stack;
                            $asal_cont         = 'TPK';
                        } else if ($kegiatan == 'STUFFING') {
                            $query_cek1        = "SELECT SUBSTR(TO_CHAR(TGL_REALISASI,'dd/mm/rrrr'),1,10) START_STACK FROM container_stuffing WHERE NO_CONTAINER = '$no_cont' AND NO_REQUEST = '$no_request'";
                            $row_cek1        = DB::connection('uster')->selectOne($query_cek1);
                            $start_stack    = $row_cek1->start_stack;
                            $asal_cont         = 'DEPO';
                        } else if ($kegiatan == 'STRIPPING') {
                            $query_cek1        = "SELECT SUBSTR(TO_CHAR(TGL_REALISASI,'dd/mm/rrrr'),1,10) START_STACK FROM container_stripping WHERE NO_CONTAINER = '$no_cont' AND NO_REQUEST = '$no_request'";
                            $row_cek1        = DB::connection('uster')->selectOne($query_cek1);
                            $start_stack    = $row_cek1->start_stack;
                            $asal_cont         = 'DEPO';
                        } else if ($kegiatan == 'DELIVERY') {
                            $query_cek1        = "SELECT SUBSTR(TO_CHAR(TGL_DELIVERY,'dd/mm/rrrr'),1,10) START_STACK FROM container_delivery WHERE NO_CONTAINER = '$no_cont' AND NO_REQUEST = '$no_request'";
                            $row_cek1        = DB::connection('uster')->selectOne($query_cek1);
                            $start_stack    = $row_cek1->start_stack;
                            $asal_cont         = 'DEPO';
                        }
                    }

                    if ($asal == 'TPK') {
                        $start_stack = $tgl_stack;
                        $asal_cont         = 'TPK';

                        $query_cek_cont            = "SELECT NO_CONTAINER
											FROM  MASTER_CONTAINER
											WHERE NO_CONTAINER ='$no_cont'
											";
                        $row_cek_cont     = DB::connection('uster')->selectOne($query_cek_cont);
                        $cek_cont         = $row_cek_cont->no_container;

                        if ($cek_cont == NULL) {

                            $query_insert_mstr    = "INSERT INTO MASTER_CONTAINER(NO_CONTAINER,
																			SIZE_,
																			TYPE_,
																			LOCATION, NO_BOOKING, COUNTER)
																	 VALUES('$no_cont',
																			'$size',
																			'$tipe',
																			'IN_YARD', '$no_booking', 1)
												";
                            DB::connection('uster')->statement($query_insert_mstr);
                        } else {
                            $query_counter = "SELECT COUNTER FROM MASTER_CONTAINER WHERE NO_CONTAINER = '$no_cont'";
                            $rw_counter = DB::connection('uster')->selectOne($query_counter);
                            $last_counter = $rw_counter->counter;
                            if ($jn_repo == "EKS_STUFFING") {
                                $last_counter = $last_counter;
                            } else {
                                $last_counter = $last_counter + 1;
                            }

                            $q_update_book2 = "UPDATE MASTER_CONTAINER SET NO_BOOKING = '$no_booking', TYPE_ = '$tipe', COUNTER = '$last_counter', LOCATION = 'IN_YARD'
						WHERE NO_CONTAINER = '$no_cont'";
                            DB::connection('uster')->statement($q_update_book2);
                        }
                    } else {
                        $q_getc1 = "SELECT NO_BOOKING, COUNTER FROM MASTER_CONTAINER WHERE NO_CONTAINER = '$no_cont' ORDER BY COUNTER DESC";
                        $rw_getc1 = DB::connection('uster')->selectOne($q_getc1);
                        if ($jn_repo == "EKS_STUFFING") {
                            $cur_c1 = $rw_getc1->counter;
                        } else {
                            $cur_c1 = $rw_getc1->counter + 1;
                        }
                        $q_update_book3 = "UPDATE MASTER_CONTAINER SET NO_BOOKING = '$no_booking', TYPE_ = '$tipe', COUNTER = '$cur_c1'
						WHERE NO_CONTAINER = '$no_cont'";
                        DB::connection('uster')->statement($q_update_book3);
                    }

                    $q_getc = "SELECT NO_BOOKING, COUNTER FROM MASTER_CONTAINER WHERE NO_CONTAINER = '$no_cont' ORDER BY COUNTER DESC";
                    $rw_getc = DB::connection('uster')->selectOne($q_getc);
                    $cur_c = $rw_getc->counter;
                    $returnMsg = $msgout;
                } else {
                    $returnMsg = $msgout;
                }
            } else if (($no_cont <> null) && ($location == 'GATO') && ($req_dev <> NULL)) {
                $returnMsg = "NOT_EXIST";
                throw new Exception('Container Tidak ada / Status Lokasi GATO', 400);
            }

            DB::rollBack();
            return [
                'status' => [
                    'code' => 200,
                    'msg' => 'Success Processing Data',
                ],
                'message' => $returnMsg
            ];
        } catch (Exception $th) {
            DB::rollBack();
            return [
                'status' => [
                    'msg' => $th->getMessage() != '' ? $th->getMessage() : 'Err',
                    'code' => $th->getCode() != '' ? $th->getCode() : 500,
                ],
                'data' => null,
                'err_detail' => $th,
                'message' => $th->getMessage() != '' ? $th->getMessage() : 'Terjadi Kesalahan Saat Input Data, Harap Coba lagi!'
            ];
        }
    }

    function editContList($request)
    {
        $no_req    = $request->no_req;
        $no_req2    = $request->no_req2;


        $query_list = " SELECT DISTINCT MASTER_CONTAINER.*, CONTAINER_DELIVERY.*, HISTORY_CONTAINER.TGL_UPDATE
				  FROM MASTER_CONTAINER
				       RIGHT JOIN CONTAINER_DELIVERY
				          ON MASTER_CONTAINER.NO_CONTAINER = CONTAINER_DELIVERY.NO_CONTAINER
				       INNER JOIN HISTORY_CONTAINER
				            ON HISTORY_CONTAINER.NO_CONTAINER = CONTAINER_DELIVERY.NO_CONTAINER
				       AND HISTORY_CONTAINER.NO_REQUEST = CONTAINER_DELIVERY.NO_REQUEST
				       AND HISTORY_CONTAINER.KEGIATAN = 'REQUEST DELIVERY'
				 WHERE CONTAINER_DELIVERY.NO_REQUEST = '$no_req'
				       ORDER BY HISTORY_CONTAINER.TGL_UPDATE ASC";

        // dd($query_list);
        $row_list        = DB::connection('uster')->select($query_list);

        return $row_list;
    }

    function delCont($request)
    {
        DB::beginTransaction();
        try {
            $no_cont    = $request->NO_CONT;
            $no_req        = $request->NO_REQ;
            $no_req2    = $request->NO_REQ2;
            $ex_bp        = $request->EX_BP;


            $query_del_nbs    = "DELETE from BILLING_NBS.req_receiving_d WHERE NO_CONTAINER = '$no_cont' AND ID_REQ = '$no_req2'";

            $qves = "SELECT
			rd.o_vessel,
			rd.o_vessel,
			rd.o_voyin,
			rd.o_voyout,
			vpc.VOYAGE,
			vpc.KD_KAPAL as VESSEL_CODE
		FROM request_delivery rd
		INNER JOIN V_PKK_CONT vpc ON vpc.NO_BOOKING = rd.NO_BOOKING
		WHERE
			rd.NO_REQUEST = '$no_req'";
            $rves = DB::connection('uster')->selectOne($qves);
            $vessel  = $rves->o_vessel;
            $voyage_in  = $rves->o_voyin;
            $voyage_out  = $rves->o_voyout;

            $vessel_code = $rves->vessel_code;
            $voyage = $rves->voyage;
            $qcekop = "SELECT CARRIER from BILLING_NBS.REQ_RECEIVING_D WHERE ID_REQ = '$no_req2' AND NO_CONTAINER = '$no_cont'";
            $rcekop = DB::connection('uster')->selectOne($qcekop);
            $operatorid = $rcekop->carrier ?? null;
            $param_b_var = array(
                "v_nocont" => TRIM($no_cont),
                "v_req" => TRIM($no_req2),
                "flag" => "REC",
                "vessel" => "$vessel_code",
                "voyage" => "$voyage",
                "operatorId" => $operatorid,
                "v_response" => "",
                "v_msg" => ""
            );

            // echo var_dump($param_b_var);die;
            // $query_ops = "declare begin BILLING_NBS.proc_delete_cont(:v_nocont, :v_req, :flag, :vessel, :voyage, :operatorId, :v_response, :v_msg); end;";

            $query_del    = "DELETE FROM CONTAINER_DELIVERY WHERE NO_CONTAINER = '$no_cont' AND NO_REQUEST = '$no_req'";

            $history        = "DELETE FROM history_container
							WHERE NO_CONTAINER = '$no_cont'
								AND NO_REQUEST = '$no_req'
								AND KEGIATAN = 'REQUEST DELIVERY'";
            // DB::connection('uster')->statement($query_ops, $param_b_var);
            $pdo = DB::connection('uster')->getPdo();
            $stmt = $pdo->prepare(
                "DECLARE BEGIN BILLING_NBS.PROC_DELETE_CONT(:v_nocont, :v_req, :flag, :vessel, :voyage, :operatorId, :v_response, :v_msg); end;"
            );

            foreach ($param_b_var as $key => &$value) {
                $stmt->bindParam(":$key", $value, PDO::PARAM_STR);
            }

            $outResp = "";
            $outMsg = "";

            $stmt->bindParam(":v_response", $outResp, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
            $stmt->bindParam(":v_msg", $outMsg, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
            $stmt->execute();

            // $cekmsg = $param_b_var['v_response'];
            // echo $cekmsg;
            if ($outResp == 'OK') {
                DB::connection('uster')->statement($query_del);
                DB::connection('uster')->statement($history);


                // if($db->query($query_del_nbs))
                // {
                //    echo 'OK';
                // }
            }
            DB::commit();
            return [
                'status' => [
                    'code' => 200,
                    'msg' => 'Success Processing Data',
                ],
            ];
        } catch (Exception $th) {
            DB::rollBack();
            return [
                'status' => [
                    'msg' => $th->getMessage() != '' ? $th->getMessage() : 'Err',
                    'code' => $th->getCode() != '' ? $th->getCode() : 500,
                ],
                'data' => null,
                'err_detail' => $th,
                'message' => $th->getMessage() != '' ? $th->getMessage() : 'Terjadi Kesalahan Saat Input Data, Harap Coba lagi!'
            ];
        }
    }

    function pbm($term)
    {
        $nama            = strtoupper($term);
        $query           = "SELECT pbm.KD_PBM,pbm.NM_PBM,pbm.ALMT_PBM,pbm.NO_NPWP_PBM,pbm.NO_ACCOUNT_PBM
                                FROM V_MST_PBM pbm where pbm.KD_CABANG='05' AND UPPER(pbm.NM_PBM) LIKE '%$nama%' AND PELANGGAN_AKTIF = '1' AND pbm.ALMT_PBM IS NOT NULL";
        $result_query    = DB::connection('uster')->select($query);
        return $result_query;
    }

    function refcal($request)
    {
        $sql = "SELECT CEIL((((86400*(TO_DATE('" . $request->nanti . "','YYYY-MM-DD HH24:MI') - TO_DATE('" . $request->mulai . "','YYYY-MM-DD HH24:MI')))/60)/60)/ 8) AS JML FROM DUAL";
        $row = DB::connection('uster')->selectOne($sql);
        echo $row->jml;
    }

    function addDoTpk($request)
    {
        DB::beginTransaction();
        try {

            // debug($request->di;
            $id_yard_                   = session()->get("IDYARD_STORAGE");
            $KD_PELANGGAN               = $request->KD_PELANGGAN;
            $KD_PELANGGAN2                  = $request->KD_PELANGGAN2;
            $TGL_BERANGKAT              = $request->TGL_BERANGKAT;
            $TGL_REQ                    = $request->TGL_REQ;
            $PEB                        = $request->NO_PEB;
            $NPE                        = $request->NO_NPE;
            $KD_PELABUHAN_ASAL          = $request->KD_PELABUHAN_ASAL;
            $KD_PELABUHAN_TUJUAN        = $request->KD_PELABUHAN_TUJUAN;
            $NM_KAPAL                       = $request->NM_KAPAL;
            $CALL_SIGN                  = $request->CALL_SIGN;
            $VOYAGE_IN                  = $request->VOYAGE_IN;
            $VOYAGE_OUT                     = $request->VOYAGE_OUT;
            $NO_BOOKING                 = $request->NO_BOOKING;
            $KETERANGAN                 = $request->KETERANGAN;
            $ID_USER                    = session()->get("LOGGED_STORAGE");
            $NM_USER                    = session()->get("NAME");
            $ID_YARD                    = session()->get("IDYARD_STORAGE");
            $NM_USER                    = session()->get("NAME");
            $NO_UKK                     = $request->NO_UKK;
            $SHIFT_RFR                  = $request->SHIFT_RFR;
            $DI                         = $request->DI;
            $TGL_MUAT                   = $request->TGL_MUAT;
            $TGL_STACKING               = $request->TGL_STACKING;
            $NO_RO                      = $request->NO_RO;
            $JN_REPO                    = $request->JN_REPO;
            $NO_STUFF                   = $request->NO_REQ_STUFF;
            $TGL_MULAI                  = $request->TGL_MULAI;
            $TGL_NANTI                  = $request->TGL_NANTI;
            $NO_ACCOUNT_PBM             = $request->NO_ACCOUNT_PBM;
            $KD_KAPAL                   = $request->KD_KAPAL;
            $ETD                        = $request->ETD;
            $ETA                        = $request->ETA;
            $OPEN_STACK                 = $request->OPEN_STACK;
            $NM_AGEN                    = $request->NM_AGEN;
            $KD_AGEN                    = $request->KD_AGEN;
            $CONT_LIMIT                 = $request->CONT_LIMIT;
            $CLOSING_TIME               = $request->CLOSING_TIME;
            $CLOSING_TIME_DOC           = $request->CLOSING_TIME_DOC;
            $VOYAGE                     = $request->VOYAGE;

            $pdo = DB::connection('uster')->getPdo();

            //Cek tipe delivery TPK, apakah eks stuffing atau empty
            if ($JN_REPO == "EMPTY") {

                //-------------------------------------------------- INSERT TO TPK's RECEIVING --------------------------------------------------------//

                $param = array(
                    "in_accpbm" => $NO_ACCOUNT_PBM,
                    "in_pbm" => $KD_PELANGGAN,
                    "in_peb" => $PEB,
                    "in_npe" => $NPE,
                    "in_noro" => $NO_RO,
                    "in_keterangan" => $KETERANGAN,
                    "in_jenis" => $JN_REPO,
                    "in_user" => $ID_USER,
                    "in_di" => $DI,
                    "in_vessel" => $NM_KAPAL,
                    "in_voyin" => $VOYAGE_IN,
                    "in_voyout" => $VOYAGE_OUT,
                    "in_idvsb" => $NO_UKK,
                    "in_nobooking" => $NO_BOOKING,
                    "in_callsign" => $CALL_SIGN,
                    "in_pod" => $KD_PELABUHAN_TUJUAN,
                    "in_pol" => $KD_PELABUHAN_ASAL,
                    "in_shift" => $SHIFT_RFR,
                    "in_plin" => $TGL_MULAI,
                    "in_plout" => $TGL_NANTI,
                    "in_nostuf" => $NO_STUFF,
                    "in_kdkapal" => $KD_KAPAL,
                    "in_etd" => $ETD,
                    "in_eta" => $ETA,
                    "in_openstack" => $OPEN_STACK,
                    "in_nmagen" => $NM_AGEN,
                    "in_kdagen" => $KD_AGEN,
                    "in_closingtime" => $CLOSING_TIME,
                    "in_closingtimedoc" => $CLOSING_TIME_DOC,
                    "in_voyage" => $VOYAGE,
                    "out_noreq" => '',
                    "out_reqnbs" => '',
                    "out_msg" => ''
                );

                $stmt = $pdo->prepare(
                    "DECLARE BEGIN PACK_CREATE_REQ_DELIVERY_REPO.CREATE_HEADER_REPO_PRAYA(:in_accpbm,:in_pbm,:in_peb,:in_npe,:in_noro,:in_keterangan,:in_jenis ,:in_user,:in_di ,:in_vessel,:in_voyin ,:in_voyout,:in_idvsb,:in_nobooking,:in_callsign,:in_pod,:in_pol,:in_shift,:in_plin,:in_plout,:in_nostuf,:in_kdkapal,:in_etd, :in_eta,:in_openstack,:in_nmagen, :in_kdagen,:in_closingtime,:in_closingtimedoc,:in_voyage,:out_noreq,:out_reqnbs,:out_msg); end;"
                );

                foreach ($param as $key => &$value) {
                    $stmt->bindParam(":$key", $value, PDO::PARAM_STR);
                }

                $outNoReq = "";
                $outNoReqNbs = "";
                $outMsg = "";

                $stmt->bindParam(":out_noreq", $outNoReq, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                $stmt->bindParam(":out_reqnbs", $outNoReqNbs, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                $stmt->bindParam(":out_msg", $outMsg, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                $stmt->execute();

                // echo var_dump($param);
                // die;

                // $query_int = "declare begin pack_create_req_delivery_repo.create_header_repo_praya(:in_accpbm,:in_pbm,:in_peb,:in_npe,:in_noro,:in_keterangan,:in_jenis ,:in_user,:in_di ,:in_vessel,:in_voyin ,:in_voyout,:in_idvsb,:in_nobooking,:in_callsign,:in_pod,:in_pol,:in_shift,:in_plin,:in_plout,:in_nostuf,:in_kdkapal,:in_etd, :in_eta,:in_openstack,:in_nmagen, :in_kdagen,:in_closingtime,:in_closingtimedoc,:in_voyage,:out_noreq,:out_reqnbs,:out_msg); end;";

                // DB::connection('uster')->statement("ALTER SESSION SET NLS_DATE_FORMAT= 'DD-MM-YYYY HH24:MI:SS'");

                // DB::connection('uster')->statement($query_int, $param);

                // $msg = $param["out_msg"];
                // $no_req = $param["out_noreq"];
                // $no_req2 = $param["out_reqnbs"];
                $msg = $outMsg;
                $no_req = $outNoReq;
                $no_req2 = $outNoReqNbs;
                //-------------------------------------------------- END INSERT TPK's RECEIVING -------------------------------------------------------//
            } else {




                //-------------------------------------------------- INSERT TO TPK's RECEIVING --------------------------------------------------------//
                $param = array(
                    "in_accpbm" => $NO_ACCOUNT_PBM,
                    "in_pbm" => $KD_PELANGGAN,
                    "in_peb" => $PEB,
                    "in_npe" => $NPE,
                    "in_noro" => $NO_RO,
                    "in_keterangan" => $KETERANGAN,
                    "in_jenis" => $JN_REPO,
                    "in_user" => $ID_USER,
                    "in_di" => $DI,
                    "in_vessel" => $NM_KAPAL,
                    "in_voyin" => $VOYAGE_IN,
                    "in_voyout" => $VOYAGE_OUT,
                    "in_idvsb" => $NO_UKK,
                    "in_nobooking" => $NO_BOOKING,
                    "in_callsign" => $CALL_SIGN,
                    "in_pod" => $KD_PELABUHAN_TUJUAN,
                    "in_pol" => $KD_PELABUHAN_ASAL,
                    "in_shift" => $SHIFT_RFR,
                    "in_plin" => $TGL_MULAI,
                    "in_plout" => $TGL_NANTI,
                    "in_nostuf" => $NO_STUFF,
                    "in_kdkapal" => $KD_KAPAL,
                    "in_etd" => $ETD,
                    "in_eta" => $ETA,
                    "in_openstack" => $OPEN_STACK,
                    "in_nmagen" => $NM_AGEN,
                    "in_kdagen" => $KD_AGEN,
                    "in_closingtime" => $CLOSING_TIME,
                    "in_closingtimedoc" => $CLOSING_TIME_DOC,
                    "in_voyage" => $VOYAGE,
                    "out_noreq" => '',
                    "out_reqnbs" => '',
                    "out_msg" => ''
                );

                // echo var_dump($param);
                // die;

                // $query_int = "declare begin pack_create_req_delivery_repo.create_header_repo_praya(:in_accpbm,:in_pbm,:in_peb,:in_npe,:in_noro,:in_keterangan,:in_jenis ,:in_user,:in_di ,:in_vessel,:in_voyin ,:in_voyout,:in_idvsb,:in_nobooking,:in_callsign,:in_pod,:in_pol,:in_shift,:in_plin,:in_plout,:in_nostuf,:in_kdkapal,:in_etd, :in_eta,:in_openstack,:in_nmagen, :in_kdagen,:in_closingtime,:in_closingtimedoc,:in_voyage,:out_noreq,:out_reqnbs,:out_msg); end;";
                // DB::connection('uster')->statement($query_int, $param);
                // $msg = $param["out_msg"];
                // $no_req = $param["out_noreq"];
                // $no_req2 = $param["out_reqnbs"];

                $stmt = $pdo->prepare(
                    "DECLARE BEGIN PACK_CREATE_REQ_DELIVERY_REPO.CREATE_HEADER_REPO_PRAYA(:in_accpbm,:in_pbm,:in_peb,:in_npe,:in_noro,:in_keterangan,:in_jenis ,:in_user,:in_di ,:in_vessel,:in_voyin ,:in_voyout,:in_idvsb,:in_nobooking,:in_callsign,:in_pod,:in_pol,:in_shift,:in_plin,:in_plout,:in_nostuf,:in_kdkapal,:in_etd, :in_eta,:in_openstack,:in_nmagen, :in_kdagen,:in_closingtime,:in_closingtimedoc,:in_voyage,:out_noreq,:out_reqnbs,:out_msg); end;"
                );

                foreach ($param as $key => &$value) {
                    $stmt->bindParam(":$key", $value, PDO::PARAM_STR);
                }

                $outNoReq = "";
                $outNoReqNbs = "";
                $outMsg = "";

                $stmt->bindParam(":out_noreq", $outNoReq, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                $stmt->bindParam(":out_reqnbs", $outNoReqNbs, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                $stmt->bindParam(":out_msg", $outMsg, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                $stmt->execute();

                $msg = $outMsg;
                $no_req = $outNoReq;
                $no_req2 = $outNoReqNbs;
                //-------------------------------------------------- END INSERT TPK's RECEIVING -------------------------------------------------------//


                //==== Batas comment



                //==== Batas comment



            }

            if ($msg == 'OK') {
                DB::commit();
                return [
                    'status' => [
                        'code' => 200,
                        'msg' => 'Success Processing Data',
                    ],
                    'redirect' => [
                        'need' => true,
                        'to' => route('uster.new_request.delivery.delivery_ke_luar_tpk.edit', ['no_req' => $no_req, 'no_req2' => $no_req2]),
                    ]
                ];
            } else {
                DB::rollBack();
                return [
                    'status' => [
                        'code' => 400,
                        'msg' => $msg,
                    ],
                    'message' => $msg != '' ? $msg : 'Gagal memproses data ke Praya/TPK'
                ];
            }
        } catch (Exception $th) {
            DB::rollBack();
            return [
                'status' => [
                    'msg' => $th->getMessage() != '' ? $th->getMessage() : 'Err',
                    'code' => $th->getCode() != '' ? $th->getCode() : 500,
                ],
                'data' => null,
                'err_detail' => $th,
                'message' => $th->getMessage() != '' ? $th->getMessage() : 'Terjadi Kesalahan Saat Input Data, Harap Coba lagi!'
            ];
        }
    }
}
