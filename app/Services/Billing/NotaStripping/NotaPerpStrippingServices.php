<?php

namespace App\Services\Billing\NotaStripping;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PDO;

class NotaPerpStrippingServices
{
    public function getData(Request $request)
    {
        $no_req = $request->no_request;
        $from = $request->tgl_awal;
        $to = $request->tgl_akhir;
        $noReqWhere = '';
        $dateWhere = '';

        if ($no_req != null || $from != null || $to != null) {
            if ($no_req != null && ($from == null && $to == null)) {
                $noReqWhere = "AND request_stripping.NO_REQUEST LIKE '%$no_req%'";
            } else if ($request->no_request == null && ($from != null && $to != null)) {
                $dateWhere = "AND request_stripping.TGL_REQUEST BETWEEN TO_DATE ( '$from', 'YYYY/MM/DD') AND TO_DATE ( '$to', 'YYYY/MM/DD') ";
            } else if ($no_req != null && ($from != null && $to != null)) {
                $noReqWhere = "AND request_stripping.NO_REQUEST LIKE '%$no_req%'";
                $dateWhere = "AND request_stripping.TGL_REQUEST BETWEEN TO_DATE ( '$from', 'YYYY/MM/DD') AND TO_DATE ( '$to', 'YYYY/MM/DD') ";
            }

            $query_list = "SELECT  request_stripping.PERP_DARI , REQUEST_STRIPPING.TGL_REQUEST, REQUEST_STRIPPING.TGL_APPROVE, request_stripping.NO_DO, request_stripping.NO_BL,request_stripping.NO_REQUEST, emkl.NM_PBM AS PENUMPUKAN_OLEH,  COUNT(container_stripping.NO_CONTAINER) AS JML,REQUEST_STRIPPING.NOTA,REQUEST_STRIPPING.KOREKSI
                            FROM REQUEST_STRIPPING INNER JOIN v_mst_pbm emkl ON REQUEST_STRIPPING.KD_PENUMPUKAN_OLEH = emkl.KD_PBM and emkl.KD_CABANG = '05'
                            INNER JOIN container_stripping ON REQUEST_STRIPPING.NO_REQUEST = container_stripping.NO_REQUEST
                            WHERE request_stripping.PERP_DARI IS NOT NULL
                            AND request_stripping.CLOSING = 'CLOSED'
                            $noReqWhere
                            $dateWhere
                            GROUP BY  request_stripping.PERP_DARI , REQUEST_STRIPPING.TGL_REQUEST, request_stripping.NO_REQUEST,  emkl.NM_PBM, request_stripping.TGL_APPROVE,request_stripping.NO_DO, request_stripping.NO_BL,REQUEST_STRIPPING.NOTA,REQUEST_STRIPPING.KOREKSI
                            ORDER BY REQUEST_STRIPPING.NO_REQUEST DESC";
        } else {
            $query_list     = "SELECT request_stripping.PERP_DARI,
                                    REQUEST_STRIPPING.TGL_REQUEST,
                                    REQUEST_STRIPPING.TGL_APPROVE,
                                    request_stripping.NO_DO,
                                    request_stripping.NO_BL,
                                    request_stripping.NO_REQUEST,
                                    emkl.NM_PBM AS PENUMPUKAN_OLEH,
                                    COUNT (container_stripping.NO_CONTAINER) AS JML,
                                    REQUEST_STRIPPING.NOTA,
                                    REQUEST_STRIPPING.KOREKSI
                                FROM REQUEST_STRIPPING
                                    INNER JOIN v_mst_pbm emkl
                                        ON REQUEST_STRIPPING.KD_PENUMPUKAN_OLEH = emkl.KD_PBM
                                            AND emkl.KD_CABANG = '05'
                                    INNER JOIN container_stripping
                                        ON REQUEST_STRIPPING.NO_REQUEST =
                                                container_stripping.NO_REQUEST
                                WHERE request_stripping.PERP_DARI IS NOT NULL
                                AND request_stripping.CLOSING = 'CLOSED'
                                and request_stripping.tgl_request between sysdate - interval '15' day AND last_day(sysdate)
                                GROUP BY request_stripping.PERP_DARI,
                                    REQUEST_STRIPPING.TGL_REQUEST,
                                    request_stripping.NO_REQUEST,
                                    emkl.NM_PBM,
                                    request_stripping.TGL_APPROVE,
                                    request_stripping.NO_DO,
                                    request_stripping.NO_BL,
                                    REQUEST_STRIPPING.NOTA,
                                    REQUEST_STRIPPING.KOREKSI
                                ORDER BY REQUEST_STRIPPING.TGL_REQUEST DESC";
        }

        $query = '(' . $query_list . ') a';
        $data = array();
        ini_set('max_execution_time', 300);
        $data = DB::connection('uster')->table(DB::raw($query))->select()->get();
        // DB::connection('uster')->table(DB::raw($query))->orderBy('a.tgl_request', 'desc')->chunk(20, function ($chunk) use (&$data) {
        //     foreach ($chunk as $dt) {
        //         $data[] = $dt;
        //     }
        // });

        return $data;
    }

    public function fetchData($no_req)
    {
        $query = "SELECT NO_NOTA FROM nota_stripping@DBCLOUD_LINK WHERE TRIM(NO_REQUEST) = TRIM('$no_req') AND STATUS <> 'BATAL'";
        $hasil_ = DB::connection('uster_dev')->selectOne($query);
        $notanya = $hasil_->no_nota;

        //NOTA MTI -> NO_NOTA_MTI
        $query = "SELECT c.NO_REQUEST, a.NOTA_LAMA, a.NO_NOTA, a.NO_NOTA_MTI, TO_CHAR(a.ADM_NOTA,'999,999,999,999') ADM_NOTA, TO_CHAR(a.PASS,'999,999,999,999') PASS, a.EMKL NAMA, a.ALAMAT  , a.NPWP, c.PERP_DARI, a.LUNAS,a.NO_FAKTUR, TO_CHAR(a.TAGIHAN,'999,999,999,999') TAGIHAN, TO_CHAR(a.PPN,'999,999,999,999') PPN, TO_CHAR(a.TOTAL_TAGIHAN,'999,999,999,999') TOTAL_TAGIHAN, a.STATUS, TO_CHAR(c.TGL_REQUEST,'dd/mm/yyyy') TGL_REQUEST,
                    CONCAT(TERBILANG(a.TOTAL_TAGIHAN),'rupiah') TERBILANG, a.NIPP_USER, mu.NAME, CASE WHEN TRUNC(TGL_NOTA) < TO_DATE('1/6/2013','DD/MM/RRRR')
                    THEN a.NO_NOTA
                    ELSE A.NO_FAKTUR END NO_FAKTUR_--, F_CORPORATE(c.TGL_REQUEST) CORPORATE
                                        FROM nota_stripping@DBCLOUD_LINK a, request_stripping@DBCLOUD_LINK c, BILLING_NBS.tb_user@DBCLOUD_LINK mu where
                                        a.NO_REQUEST = c.NO_REQUEST
                                        AND a.TGL_NOTA = (SELECT MAX(d.TGL_NOTA) FROM nota_stripping@DBCLOUD_LINK d WHERE d.NO_REQUEST = '$no_req' )
                                        and c.NO_REQUEST = '$no_req'
                                        and a.nipp_user = mu.id(+)";
        $data = DB::connection('uster_dev')->selectOne($query);
        $req_tgl = $data->tgl_request;
        $nama_lengkap  = $data->name;
        $lunas = $data->lunas;

        if (request('first') == null) {
            $nama_lengkap .= '<br/>' . 'Reprinted by ' . Session::get('NAMA_LENGKAP');
        }
        $corporate_name     = $data->corporate ?? 'PT. Multi Terminal Indonesia <br>Cabang Pelabuhan Pontianak';

        /**hitung materai Fauzan 31 Agustus 2020*/
        $query_mtr = "SELECT TO_CHAR (a.BIAYA, '999,999,999,999') BEA_MATERAI, a.BIAYA
                        FROM nota_stripping_d@DBCLOUD_LINK a
                        WHERE a.NO_NOTA = '$notanya' AND a.KETERANGAN ='MATERAI' ";
        //print_r($query_mtr);
        $data_mtr = DB::connection('uster_dev')->selectOne($query_mtr);
        if (!empty($data_mtr) && $data_mtr->biaya > 0) {
            $bea_materai = $data_mtr->bea_materai;
        } else {
            $bea_materai = 0;
        }
        ///print_r($bea_materai);die();
        /*end hitung materai Fauzan 31 Agustus 2020*/

        //get no peraturan 25 nov 2020
        if ($lunas == 'YES') {
            $mat = "SELECT * FROM itpk_nota_header@DBCLOUD_LINK WHERE NO_REQUEST='$no_req'";

            $mat3   = DB::connection('uster_dev')->selectOne($mat);
            $no_mat    = $mat3->no_peraturan;
            
        } else {
            $mat = "SELECT * FROM MASTER_MATERAI@DBCLOUD_LINK WHERE STATUS='Y'";

            $mat3   = DB::connection('uster_dev')->selectOne($mat);
            $no_mat    = $mat3->no_peraturan;
        }
        //end get

        $query_dtl  = "SELECT TO_CHAR(a.START_STACK,'dd/mm/yyyy') START_STACK,TO_CHAR(a.END_STACK,'dd/mm/yyyy') END_STACK,
            a.KETERANGAN,
            a.JML_CONT,
            a.JML_HARI,
            b.SIZE_, b.TYPE_, b.STATUS, a.HZ, TO_CHAR(a.TARIF,'999,999,999,999') TARIF ,
            TO_CHAR(a.BIAYA,'999,999,999,999') BIAYA
            FROM nota_stripping_d@DBCLOUD_LINK a, iso_code@DBCLOUD_LINK b, nota_stripping@DBCLOUD_LINK c
            WHERE a.ID_ISO = b.ID_ISO(+) AND a.NO_NOTA = c.NO_NOTA AND a.NO_NOTA = (SELECT MAX(d.NO_NOTA) FROM NOTA_STRIPPING@DBCLOUD_LINK d WHERE d.NO_REQUEST = '$no_req')
         and a.KETERANGAN NOT IN ('ADMIN NOTA','MATERAI')";
        /**Fauzan modif 31 Agustus 2020 [NOT IN MATERAI]*/
        DB::connection('uster_dev')->statement("ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY'");
        $res = DB::connection('uster_dev')->select($query_dtl);
        
        $qcont = "SELECT A.NO_CONTAINER,'FCL' STATUS,B.SIZE_,B.TYPE_ FROM CONTAINER_STRIPPING@DBCLOUD_LINK A, MASTER_CONTAINER@DBCLOUD_LINK B WHERE A.NO_CONTAINER = B.NO_CONTAINER AND A.NO_REQUEST = '$no_req'";
        $rcont = DB::connection('uster_dev')->select($qcont);
        $listcont = "<br/>Daftar Container<br/><b>";
        foreach ($rcont as $rc) {
            $listcont .= $rc->no_container . "+" . $rc->size_ . "-" . $rc->type_ . "-" . $rc->status . " ";
        }
        $listcont .= "</b>";

        return array(
            'data' => $data,
            'bea_materai' => $bea_materai,
            'no_mat' => $no_mat,
            'row2' => $res,
            'data_mtr' => $data_mtr,
            'nama_lengkap' => $nama_lengkap,
            'lunas' => $lunas,
            'listcont' => $listcont
        );
    }

    public function previewNota($no_req, $koreksi)
    {
        $query_nota    = "SELECT c.NM_PBM AS EMKL,
                                c.NO_NPWP_PBM AS NPWP,
                                c.ALMT_PBM AS ALAMAT,
                                c.NO_ACCOUNT_PBM,
                                TO_CHAR(b.TGL_REQUEST,'DD-MM-RRRR') TGL_REQUEST
                                                        -- F_CORPORATE(b.TGL_REQUEST) CORPORATE
                        FROM request_stripping@DBCLOUD_LINK b INNER JOIN
                                V_MST_PBM@DBCLOUD_LINK c ON b.KD_PENUMPUKAN_OLEH = c.KD_PBM
                        WHERE b.NO_REQUEST = '$no_req'";
        $row_nota = DB::connection('uster_dev')->selectOne($query_nota);
        $kd_pbm     = $row_nota->no_account_pbm;
        $display    = 1;
        $req_tgl     = $row_nota->tgl_request;

        $query_tgl    = "SELECT TO_CHAR(TGL_REQUEST,'YYYY-MM-DD') TGL_REQUEST FROM request_stripping@DBCLOUD_LINK WHERE NO_REQUEST = '$no_req'";
        $tgl_req = DB::connection('uster_dev')->selectOne($query_tgl);
        $tgl_re = Carbon::parse($tgl_req->tgl_request)->format('Y/m/d');

        DB::connection('uster')->statement("ALTER SESSION SET NLS_DATE_FORMAT='YYYY/MM/DD'");
        $sql_xpi = "DECLARE
                            id_nota NUMBER;
                            tgl_req DATE;
                            no_request VARCHAR2(100);
                            jenis VARCHAR2 (100);
                            err_msg VARCHAR2(100);
                            BEGIN
                                    id_nota := 6;
                                    tgl_req := '$tgl_re';
                                    no_request := '$no_req';
                                    err_msg := 'NULL';
                                    jenis := 'stripping';
                                    create_detail_nota(id_nota,tgl_req,no_request,jenis, err_msg);
                            END;";

        $exec = DB::connection('uster')->statement($sql_xpi);

        $detail_nota  = "SELECT a.JML_HARI, TO_CHAR(a.TARIF, '999,999,999,999') AS TARIF, TO_CHAR(a.BIAYA, '999,999,999,999') AS BIAYA, a.KETERANGAN, a.HZ, a.JML_CONT, TO_DATE(a.START_STACK,'dd/mm/yyyy') START_STACK, TO_DATE(a.END_STACK,'dd/mm/yyyy') END_STACK, b.SIZE_, b.TYPE_, b.STATUS FROM
	                            temp_detail_nota@DBCLOUD_LINK a, iso_code@DBCLOUD_LINK b WHERE a.id_iso = b.id_iso and a.no_request = '$no_req' and a.keterangan NOT IN ('ADMIN NOTA', 'MATERAI')"; //Fauzan add NOT IN MATERAI 31 Agustus 2020
        DB::connection('uster_dev')->statement("ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY'");
        $row_detail   = DB::connection('uster_dev')->select($detail_nota);

        $total_          = "SELECT SUM(BIAYA) TOTAL, SUM(PPN) PPN, (SUM(BIAYA) + SUM(PPN)) TOTAL_TAGIHAN FROM temp_detail_nota@DBCLOUD_LINK WHERE no_request = '$no_req' AND KETERANGAN NOT IN ('MATERAI')"; //Fauzan add NOT IN MATERAI 31 Agustus 2020
        $total2           = DB::connection('uster_dev')->selectOne($total_);

        $total = $total2->total;
        $total_ppn = $total2->ppn;
        $total_tagihan = $total2->total_tagihan;

        //Discount
        $discount = 0;
        $query_discount        = "SELECT TO_CHAR($discount , '999,999,999,999') AS DISCOUNT FROM DUAL";
        $row_discount        = DB::connection('uster_dev')->selectOne($query_discount);
        //Biaya Administrasi

        $query_adm        = "SELECT TO_CHAR(a.TARIF , '999,999,999,999') AS ADM, a.TARIF FROM MASTER_TARIF@DBCLOUD_LINK a, GROUP_TARIF@DBCLOUD_LINK b WHERE a.ID_GROUP_TARIF = b.ID_GROUP_TARIF AND b.KATEGORI_TARIF = 'ADMIN_NOTA'";
        $row_adm        = DB::connection('uster_dev')->selectOne($query_adm);
        $adm             = $row_adm->tarif;

        //Menghitung Total dasar pengenaan pajak
        $total_ = $total;
        $query_tot        = "SELECT TO_CHAR('$total_' , '999,999,999,999') AS TOTAL_ALL FROM DUAL";
        $row_tot        = DB::connection('uster_dev')->selectOne($query_tot);

        //Menghitung Jumlah PPN
        $ppn = $total_ppn;
        $query_ppn        = "SELECT TO_CHAR('$ppn' , '999,999,999,999') AS PPN FROM DUAL";
        $row_ppn        = DB::connection('uster_dev')->selectOne($query_ppn);

        //Menghitung Bea Materai fauzan modif 31 Agustus 2020
        $sql_mtr       = "SELECT BIAYA AS BEA_MATERAI FROM TEMP_DETAIL_NOTA@DBCLOUD_LINK WHERE no_request = '$no_req' AND KETERANGAN='MATERAI'";
        $row_mtr       = DB::connection('uster_dev')->selectOne($sql_mtr);

        if (!empty($row_mtr->bea_materai) && $row_mtr->bea_materai > 0) {
            $bea_materai = $row_mtr->bea_materai;
        } else {
            $bea_materai = 0;
        }

        /**end modify fauzan 31 aug 2020*/
        $query_materai        = "SELECT TO_CHAR('$bea_materai' , '999,999,999,999') AS MATERAI FROM DUAL";
        $row_materai        = DB::connection('uster_dev')->selectOne($query_materai);

        //Menghitung Jumlah dibayar
        $total_bayar        = $total_tagihan + $bea_materai; //+ $tarif_pass;	/**Fauzan modif 31 AUG 2020 "+ $bea_materai"*/
        $query_bayar        = "SELECT TO_CHAR('$total_bayar' , '999,999,999,999') AS TOTAL_BAYAR FROM DUAL";
        $row_bayar          = DB::connection('uster_dev')->selectOne($query_bayar);

        $pegawai    = "SELECT * FROM MASTER_PEGAWAI@DBCLOUD_LINK WHERE STATUS = 'AKTIF'";
        $nama_peg    = DB::connection('uster_dev')->selectOne($pegawai);

        return response()->json([
            'row_nota' => $row_nota,
            'row_detail' => $row_detail,
            'tgl_req' => $tgl_re,
            'pegawai' => $nama_peg,
            'row_bayar' => $row_bayar->total_bayar,
            'row_materai' => $row_materai->materai,
            'row_discount' => $discount,
            'row_ppn' => $row_ppn,
            'row_adm' => $adm,
            'row_tot' => $row_tot->total_all,
            'bea_materai' => $bea_materai,
            'no_req' => $no_req,
            'koreksi' => $koreksi
        ]);
    }

    public function insertProforma($no_req, $koreksi)
    {
        $query_cek  = "SELECT NVL(LPAD(MAX(TO_NUMBER(SUBSTR(NO_NOTA,10,15))+1),6,0), '000001') JUM_,
                              TO_CHAR(SYSDATE, 'MM') AS MONTH,
                              TO_CHAR(SYSDATE, 'YY') AS YEAR
                        FROM NOTA_STRIPPING
                       WHERE NOTA_STRIPPING.TGL_NOTA BETWEEN TRUNC(SYSDATE,'MONTH') AND LAST_DAY(SYSDATE)";

        $dataNota       = DB::connection('uster')->selectOne($query_cek);
        $jum        = $dataNota->jum_;
        $month      = $dataNota->month;
        $year       = $dataNota->year;

        $no_nota    = "0605" . $month . $year . $jum;

        // Cek NO NOTA MTI
        // firman 20 agustus 2020
        $query_mti = "SELECT NVL(LPAD(MAX(TO_NUMBER(SUBSTR(NO_NOTA_MTI,10,15))+1),6,0),'000001') JUM_,
                        TO_CHAR(SYSDATE, 'YYYY') AS YEAR
                        FROM MTI_COUNTER_NOTA WHERE TAHUN =  TO_CHAR(SYSDATE,'YYYY')";

        $jum_mti    = DB::connection('uster')->selectOne($query_mti);
        $jum_nota_mti   = $jum_mti->jum_;
        $year_mti       = $jum_mti->year;

        $no_nota_mti    = "17." . $year_mti . "." . $jum_nota_mti;

        //select master pbm
        $query_master   = "SELECT b.KD_PBM, b.NM_PBM, b.ALMT_PBM, b.NO_NPWP_PBM FROM request_stripping a, v_mst_pbm b, v_mst_pbm c WHERE a.KD_CONSIGNEE = b.KD_PBM AND a.KD_PENUMPUKAN_OLEH = c.KD_PBM AND a.NO_REQUEST = '$no_req'";
        //echo $query_master;die;
        $master     = DB::connection('uster')->selectOne($query_master);
        $kd_pbm     = $master->kd_pbm;
        $nm_pbm     = $master->nm_pbm;
        $almt_pbm   = $master->almt_pbm;
        $npwp       = $master->no_npwp_pbm;


        $total_     = "SELECT SUM(BIAYA) TOTAL, SUM(PPN) PPN, (SUM(BIAYA) + SUM(PPN)) TOTAL_TAGIHAN FROM temp_detail_nota
                        WHERE no_request = '$no_req' AND KETERANGAN NOT IN ('MATERAI')";

        /**Fauzan modif 31 AUG 2020 "AND KETERANGAN NOT IN ('MATERAI')"*/
        $total2         = DB::connection('uster')->selectOne($total_);

        //Biaya Administrasi
        $query_adm      = "SELECT TO_CHAR(a.TARIF , '999,999,999,999') AS ADM, a.TARIF FROM MASTER_TARIF a, GROUP_TARIF b WHERE a.ID_GROUP_TARIF = b.ID_GROUP_TARIF AND b.KATEGORI_TARIF = 'ADMIN_NOTA'";
        $row_adm        = DB::connection('uster')->selectOne($query_adm);
        $adm            = $row_adm->tarif;

        /*Fauzan add materai 31 Agustus 2020*/
        $query_materai      = "SELECT SUM(BIAYA) BEA_MATERAI FROM temp_detail_nota WHERE no_request = '$no_req' AND KETERANGAN = 'MATERAI'";
        $row_materai        = DB::connection('uster')->selectOne($query_materai);
        $materai            = $row_materai->bea_materai;
        /*end Fauzan add materai 31 Agustus 2020*/

        $total = $total2->total;
        $total_ppn = $total2->ppn;
        $total_tagihan = $total2->total_tagihan;

        $total_ = $total;
        $ppn   = $total_ppn;
        $tagihan = $total_tagihan + $materai;   // + $tarif_pass;       /**Fauzan modif 31 AUG 2020 "+ $materai"*/


        if ($koreksi <> 'Y') {
            $status_nota = 'PERP';
            $nota_lama = '';
        } else {
            $status_nota = 'KOREKSI';
            $faktur     = "SELECT NO_NOTA, NO_FAKTUR, KD_EMKL FROM NOTA_STRIPPING WHERE NO_REQUEST = '$no_req' AND NO_NOTA =(SELECT MAX(NO_NOTA) FROM NOTA_RECEIVING WHERE NO_REQUEST = '$no_req')";
            $faktur_     = DB::connection('uster')->selectOne($faktur);
            $nota_lama  = $faktur_->no_faktur;
            $update = "UPDATE NOTA_STRIPPING SET STATUS = 'BATAL' WHERE NO_NOTA = '$nota_lama'";
            $execUpdateNotaStrip = DB::connection('uster')->statement($update);
        }

        $rawParamInsertNota = array(
            'NO_NOTA' => $no_nota,
            'TAGIHAN' => $total_,
            'PPN' => $ppn,
            'TOTAL_TAGIHAN' => $tagihan,
            'NO_REQUEST' => $no_req,
            'NIPP_USER' => Session::get('LOGGED_STORAGE'),
            'LUNAS' => 'NO',
            'CETAK_NOTA' => 1,
            'TGL_NOTA' => OCI_SYSDATE,
            'EMKL' => $nm_pbm,
            'ALAMAT' => $almt_pbm,
            'NPWP' => $npwp,
            'STATUS' => $status_nota,
            'ADM_NOTA' => $adm,
            'KD_EMKL' => $kd_pbm,
            'TGL_NOTA_1' => OCI_SYSDATE,
            'NOTA_LAMA' => $nota_lama,
            'NO_NOTA_MTI' => $no_nota_mti,
        );

        DB::beginTransaction();
        try {
            $paramInsertNota = generateQuerySimpan($rawParamInsertNota);
            $queryInsertNota = "INSERT INTO NOTA_STRIPPING $paramInsertNota";
            $execInsertNota = DB::connection('uster')->statement($queryInsertNota);

            if ($execInsertNota) {
                //UPDATE COUNTER MTI DAN PENAMBAHAN FIELD NO_NOTA_MTI DI HEADER DAN DETAIL
                //firman 20 agustus 2020
                $query_mti = "INSERT INTO MTI_COUNTER_NOTA(NO_NOTA_MTI,TAHUN,NO_REQUEST)
                                VALUES('$no_nota_mti',TO_CHAR(SYSDATE,'YYYY'),'$no_req')";
                $execQueryMti = DB::connection('uster')->statement($query_mti);

                $query_detail   = "SELECT * FROM temp_detail_nota@DBCLOUD_LINK WHERE no_request = '$no_req' ";
                $row = DB::connection('uster_dev')->select($query_detail);

                foreach ($row as $key => $item) {
                    $id_iso         = $item->id_iso;
                    $tarif          = $item->tarif;
                    $biaya          = $item->biaya;
                    $ket            = $item->keterangan;
                    $jml_cont          = $item->jml_cont;
                    $hz             = $item->hz;
                    $start          = $item->start_stack;
                    $end            = $item->end_stack;
                    $jml            = $item->jml_hari;
                    $coa             = $item->coa;
                    $ppn             = $item->ppn;

                    $rawParamInsertDetail = array(
                        'ID_ISO' => $id_iso,
                        'TARIF' => $tarif,
                        'BIAYA' => $biaya,
                        'KETERANGAN' => $ket,
                        'NO_NOTA' => $no_nota,
                        'JML_CONT' => $jml_cont,
                        'HZ' => $hz,
                        'START_STACK' => $start != null ? "TO_DATE('$start','YYYY-MM-DD HH24:MI:SS')@ORA" : null,
                        'END_STACK' => $start != null ? "TO_DATE('$end','YYYY-MM-DD HH24:MI:SS')@ORA" : null,
                        'JML_HARI' => $jml,
                        'COA' => $coa,
                        'LINE_NUMBER' => $key + 1,
                        'PPN' => $ppn,
                        'NO_NOTA_MTI' => $no_nota_mti,
                    );
                    $paramInsertDetail = generateQuerySimpan($rawParamInsertDetail);
                    $queryInsertDetail    = "INSERT INTO nota_stripping_d $paramInsertDetail";
                    try {
                        $execInsertDetail = DB::connection('uster')->statement($queryInsertDetail);
                        if (!$execInsertDetail) {
                            // Log or inspect the error
                            $error = DB::connection('uster')->getPdo()->errorInfo();
                            Log::error("Error inserting data into nota_stripping_d: " . implode(', ', $error));
                        }
                    } catch (Exception $e) {
                        // Handle the exception
                        Log::error("Exception during insert: " . $e->getMessage());
                    }
                }

                $update_nota = "UPDATE NOTA_STRIPPING SET CETAK_NOTA = 'Y' WHERE NO_NOTA = '$no_nota'";
                $update_req = "UPDATE REQUEST_STRIPPING SET NOTA = 'Y' WHERE NO_REQUEST = '$no_req'";
                $delete_temp = "DELETE from temp_detail_nota@DBCLOUD_LINK WHERE no_request = '$no_req'";

                // DB::connection('uster')->statement($update_nota);
                DB::connection('uster')->statement($update_req);
                DB::connection('uster_dev')->statement($delete_temp);

                DB::commit();
                return response()->json([
                    'status' => 'OK',
                    'msg' => 'Berhasil Input Data Proforma',
                    'code' => 200
                ], 200);
            } else {
                throw new Exception('Gagal Menyimpan Proforma', 500);
            }
        } catch (Exception $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'Error',
                'msg' => 'Gagal Menyimpan Proforma Nota Ini',
                'code' => 500,
            ], 500);
        }
    }
}
