@extends('layouts.app')

@section('title')
    Perencanaan Kegiatan Delivery
@endsection

@section('pages-css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">New Request</a></li>
                <li class="breadcrumb-item">Delivery</li>
                <li class="breadcrumb-item"><a href="{{ route('uster.new_request.delivery.delivery_luar.index') }}">Delivery
                        Ke Luar</a></li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </div>
        <div class="col-md-7 col-4 align-self-center">
            <div class="d-flex m-t-10 justify-content-end">
                <h6>Selamat Datang <p><b>{{ Session::get('name') }}</b></p>
                </h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3><b>Request Delivery - SP2 ke LUAR DEPO</b></h3>
                    <div class="border rounded my-3">
                        <form id="dataCont">
                            @csrf
                            <div class="p-3">
                                <div class="row">
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Jenis Repo : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="JN_REPO" name="JN_REPO" class="form-control">
                                            <option value=""> PILIH </option>
                                            <option value="EKS_STUFFING">EKS STUFFING</option>
                                            <option value="EMPTY">EMPTY</option>
                                            <option value="FULL">FULL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">International/Domestik : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="DI" name="DI" class="form-control">
                                            <option value='D'>Domestik</option>
                                            <option value='I'>International</option>
                                        </select>
                                    </div>
                                    <input id="REQUEST_BY" name="REQUEST_BY" type="hidden" value="PELINDO" />
                                </div>
                                <div class="row">
                                    <div class="col-md-2 py-2">
                                        <label for="NO_REQ_STUFF">No Request Stuffing : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="NO_REQ_STUFF" name="NO_REQ_STUFF" type="text"
                                            value="" />
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">No P.E.B : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="NO_PEB" name="NO_PEB" type="text" value="" />
                                        <input class="form-control" id="id_KD_CABANG" name="KD_CABANG" type="hidden"
                                            value="{$KD_CABANG}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">No. N.P.E : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="NO_NPE" name="NO_NPE" type="text" value="" />
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">No RO : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="NO_RO" name="NO_RO" type="text" value="" />
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">E.M.K.L : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="KD_PELANGGAN" name="KD_PELANGGAN" type="hidden"
                                            value="" readonly />
                                        <input class="form-control" id="NPWP" name="NPWP" type="hidden" value="" readonly />
                                        <input class="form-control" id="NO_ACCOUNT_PBM" name="NO_ACCOUNT_PBM" type="hidden"
                                            value="" readonly />
                                        <input class="form-control" id="ALAMAT" name="ALAMAT" type="hidden" value=""
                                            readonly />
                                        <select class="form-control" name="NM_PELANGGAN" id="NM_PELANGGAN"></select>
                                    </div>
                                    <div class="col-md-2 py-2" style="display: none;">
                                        <label for="tb-fname">Penumpukan Empty Oleh : </label>
                                    </div>
                                    <div class="col-md-4" style="display: none;">
                                        <input class="form-control" id="KD_PELANGGAN2" name="KD_PELANGGAN2" type="hidden"
                                            value="" readonly />
                                        <input class="form-control" id="NM_PELANGGAN2" name="NM_PELANGGAN2" type="text"
                                            value="" title="Autocomplete" class="kdemkl2" />
                                        <input class="form-control" id="NPWP2" name="NPWP2" type="hidden" value=""
                                            readonly />
                                        <input class="form-control" id="ALAMAT2" name="ALAMAT2" type="hidden" value=""
                                            readonly />
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Nama Kapal : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="KD_KAPAL" name="KD_KAPAL" type="hidden" value="" />
                                        <input class="form-control" id="CALL_SIGN" name="CALL_SIGN" type="hidden"
                                            value="" />
                                        <!-- <input class="form-control" id="NM_KAPAL" name="NM_KAPAL" type="text" value="" /> -->
                                        <select class="form-control" name="NM_KAPAL" id="NM_KAPAL"></select>
                                        <input class="form-control" id="TGL_BERANGKAT" name="TGL_BERANGKAT" type="hidden"
                                            value="" />
                                        <input class="form-control" id="TGL_STACKING" name="TGL_STACKING" type="hidden"
                                            value="" />
                                        <input class="form-control" id="TGL_MUAT" name="TGL_MUAT" type="hidden" value="" />
                                        <input class="form-control" id="POD" name="POD" type="hidden" value="" />
                                        <input class="form-control" id="POL" name="POL" type="hidden" value="" />
                                        <input class="form-control" id="CLOSING_TIME" name="CLOSING_TIME" type="hidden"
                                            value="" />
                                        <input class="form-control" id="CLOSING_TIME_DOC" name="CLOSING_TIME_DOC"
                                            type="hidden" value="" />
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Voyage : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input class="form-control" id="VOYAGE_IN" name="VOYAGE_IN" type="text"
                                                    value="" readonly />
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" id="VOYAGE_OUT" name="VOYAGE_OUT" type="text"
                                                    value="" readonly />
                                            </div>
                                        </div>
                                        <input class="form-control" id="VOYAGE" name="VOYAGE" type="hidden" value=""
                                            readonly />
                                        <input class="form-control" id="OPEN_STACK" name="OPEN_STACK" type="hidden"
                                            value="" />
                                        <input class="form-control" id="CONT_LIMIT" name="CONT_LIMIT" type="hidden"
                                            value="" />
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">ETD : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" value="" id="ETD" name="ETD" type="text" readonly />
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">ETA : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" value="" id="ETA" name="ETA" type="text" readonly />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Nama Agen : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="KD_AGEN" name="KD_AGEN" type="hidden" value="" />
                                        <input class="form-control" id="NM_AGEN" name="NM_AGEN" type="text" value=""
                                            readonly />
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">No PKK : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="NO_UKK" name="NO_UKK" type="text" value="" readonly
                                            maxlength="16" title="Autocomplete" class="pkkkapal" />
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">No Booking : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="NO_BOOKING" name="NO_BOOKING" type="text" value=""
                                            maxlength="16" readonly />
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Pelabuhan Asal : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="KD_PELABUHAN_ASAL" name="KD_PELABUHAN_ASAL"
                                            type="hidden" value="IDPNK" class="pod" readonly />
                                        <select class="form-control" name="NM_PELABUHAN_ASAL"
                                            id="NM_PELABUHAN_ASAL"></select>
                                        <input class="form-control" id="id_TGL_STACK" name="TGL_STACK" type="hidden"
                                            value="{$tglskr}" maxlength="19" />
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Port of Discharge : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" id="KD_PELABUHAN_TUJUAN" name="KD_PELABUHAN_TUJUAN"
                                            type="hidden" value="" class="pod2" readonly />
                                        <!-- <input class="form-control" id="NM_PELABUHAN_TUJUAN" name="NM_PELABUHAN_TUJUAN" type="text" value="" maxlength="100" class="pod2" title="Autocomplete" /></td> -->
                                        <select class="form-control" name="NM_PELABUHAN_TUJUAN"
                                            id="NM_PELABUHAN_TUJUAN"></select>
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Keterangan : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <textarea class="form-control" id="KETERANGAN" name="KETERANGAN" cols="40"
                                            rows="1"></textarea>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Bayar Reefer : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" disabled id="SHIFT_RFR" name="SHIFT_RFR" type="text"
                                            value="2" />
                                    </div>
                                    <div class="col-md-2 py-2">
                                        <label for="tb-fname">Calculator Shift Reefer : </label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input class="form-control" type="text" maxlength="19" value=""
                                                    id="ID_TGL_MULAI" name="TGL_MULAI" readonly />&nbsp;
                                            </div>
                                            <div class="col-md-2">
                                                s/d
                                            </div>
                                            <div class="col-md-5">
                                                <input class="form-control" type="text" maxlength="19" value=""
                                                    id="ID_TGL_NANTI" name="TGL_NANTI" readonly />&nbsp;
                                            </div>
                                        </div>
                                        <button id="calculate" class="btn btn-info">Generate</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row text-center mb-3">
                            <div class="col">
                                <button onclick="cekearly()" class="btn btn-info">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pages-js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#NO_REQ_STUFF").prop('disabled', true);
            $("#NO_REQ_STUFF").css("background-color", "#F0F0F0");

            $("#JN_REPO").change(function () {

                if ($("#JN_REPO").val() == "EKS_STUFFING") {
                    //alert("tes");
                    $("#NO_REQ_STUFF").prop('disabled', false);
                    $("#NO_REQ_STUFF").css("background-color", "#FFFFCC");
                    //hari = $("#HARI_STUFF").val();
                    //alert(hari);



                } else {
                    //alert("tes2");
                    $("#NO_REQ_STUFF").css("background-color", "#F0F0F0");
                    $("#NO_REQ_STUFF").prop('disabled', true);
                    //hari = $("#NO_REQ_STUFF").val();
                    //alert(hari);


                }

            });

            $('#NM_PELANGGAN').select2({
                minimumInputLength: 3, // Set the minimum input length
                ajax: {
                    // Implement your AJAX settings for data retrieval here
                    url: '{!! route('uster.new_request.delivery.delivery_ke_luar_tpk.pbm') !!}',
                    dataType: 'json',
                    processResults: function (data) {
                        const arrs = data;
                        return {
                            results: arrs.map((arr, i) => ({
                                id: arr.kd_pbm,
                                text: arr.nm_pbm + " | " + arr.almt_pbm,
                                ...arr
                            }))
                        };
                    }
                }
            });

            $('#NM_PELANGGAN').on('select2:select', function (e) {
                var data = e.params.data;
                $("#KD_PELANGGAN").val(data.kd_pbm);
                $("#NM_PELANGGAN").val(data.nm_pbm);
                $("#ALAMAT").val(data.almt_pbm);
                $("#NPWP").val(data.no_npwp_pbm);
                $("#NO_ACCOUNT_PBM").val(data.no_account_pbm);
            });

            $('#NM_PELABUHAN_ASAL').select2({
                minimumInputLength: 3, // Set the minimum input length
                ajax: {
                    // Implement your AJAX settings for data retrieval here
                    url: '{!! route('uster.new_request.delivery.delivery_luar.master_pelabuhan_palapa') !!}',
                    dataType: 'json',
                    processResults: function (data) {
                        const arrs = data;
                        return {
                            results: arrs.map((arr, i) => ({
                                id: arr.cdg_port_code,
                                text: arr.cdg_port_name,
                                ...arr
                            }))
                        };
                    }
                }
            });

            $('#NM_PELABUHAN_ASAL').on('select2:select', function (e) {
                var data = e.params.data;
                $("#KD_PELABUHAN_ASAL").val(data.cdg_port_code);
                $("#NM_PELABUHAN_ASAL").val(data.cdg_port_name);
            });

            $('#NM_PELABUHAN_TUJUAN').select2({
                minimumInputLength: 3, // Set the minimum input length
                ajax: {
                    // Implement your AJAX settings for data retrieval here
                    url: '{!! route('uster.new_request.delivery.delivery_luar.master_pelabuhan_palapa') !!}',
                    dataType: 'json',
                    processResults: function (data) {
                        const arrs = data;
                        return {
                            results: arrs.map((arr, i) => ({
                                id: arr.cdg_port_code,
                                text: arr.cdg_port_name,
                                ...arr
                            }))
                        };
                    }
                }
            });

            $('#NM_PELABUHAN_TUJUAN').on('select2:select', function (e) {
                var data = e.params.data;
                $("#KD_PELABUHAN_TUJUAN").val(data.cdg_port_code);
                $("#NM_PELABUHAN_TUJUAN").val(data.cdg_port_name);
            });

            $('#NM_KAPAL').select2({
                minimumInputLength: 3, // Set the minimum input length
                ajax: {
                    // Implement your AJAX settings for data retrieval here
                    url: '{!! route('uster.new_request.delivery.delivery_luar.master_vessel_palapa') !!}',
                    dataType: 'json',
                    processResults: function (data) {
                        const arrs = data;
                        return {
                            results: arrs.map((arr, i) => ({
                                id: arr.vessel,
                                text: arr.vessel + ' | ' + arr.voyage_in + '/' + arr
                                    .voyage_out,
                                ...arr
                            }))
                        };
                    }
                }
            });

            $('#NM_KAPAL').on('select2:select', function (e) {
                var data = e.params.data;
                $("#KD_KAPAL").val(data.vessel_code);
                $("#NM_AGEN").val(data.operator_name);
                $("#KD_AGEN").val(data.operator_id);
                $("#NM_KAPAL").val(data.vessel);
                $("#VOYAGE_IN").val(data.voyage_in);
                $("#VOYAGE_OUT").val(data.voyage_out);
                $("#NO_UKK").val(data.id_vsb_voyage);
                if (data.vessel_code && data.id_vsb_voyage) {
                    $("#NO_BOOKING").val(`BP${data.vessel_code}${data.id_vsb_voyage}`);
                }
                $("#CALL_SIGN").val(data.call_sign);
                $("#TGL_BERANGKAT").val(data.atd)
                $("#TGL_STACKING").val(data.open_stack)
                $("#TGL_MUAT").val(data.closing_time_doc)
                $("#ETA").val(data.eta)
                $("#ETD").val(data.etd)
                $("#POL").val(data.id_pol);
                $("#POD").val(data.id_pod);
                $("#NM_PELABUHAN_ASAL").val(data.pol);
                // Create a new option element
                var newOption = new Option(data.pol, data.pol, true, true);

                // Append the new option to the Select2 dropdown
                $('#NM_PELABUHAN_ASAL').append(newOption).trigger('change');

                $("#KD_PELABUHAN_ASAL").val(data.id_pol);
                $("#OPEN_STACK").val(data.open_stack);
                $("#CONT_LIMIT").val(data.container_limit);
                $("#CLOSING_TIME").val(data.closing_time);
                $("#CLOSING_TIME_DOC").val(data.closing_time_doc);
                $("#VOYAGE").val(data.voyage);;
            });


        });


        function cekearly() {
            var formData = $('#dataCont').serialize();
            $NO_BOOKING = $("#NO_BOOKING").val();
            $KD_PBM = $("#KD_PELANGGAN").val();
            $KD_PELABUHAN_ASAL = $("#KD_PELABUHAN_ASAL").val();
            $KD_PELABUHAN_TUJUAN = $("#KD_PELABUHAN_TUJUAN").val();
            //$KD_PBM2    = $("#KD_PELANGGAN2").val();
            $NO_UKK = $("#NO_UKK").val();
            $JN_REPO = $("#JN_REPO").val();

            if ($NO_UKK == '' || $KD_PBM == '') {
                sAlert('Peringatan', 'Kapal dan EMKL Harus Diisi', 'warning');
                return false;
            } else if ($NO_BOOKING == '') {
                sAlert('Peringatan', 'Belum Booking Stack', 'warning');

                return false;
            } else if ($JN_REPO == '') {
                sAlert('Peringatan', 'Jenis Repo Harus Diisi', 'warning');
                return false;
            } else if ($KD_PELABUHAN_ASAL == '' || $KD_PELABUHAN_TUJUAN == '') {
                sAlert('Peringatan', 'POD / Final Discharge Harus Diisi', 'warning');
                return false;
            } else {
                ajaxPostJson('{!! route('uster.new_request.delivery.delivery_ke_luar_tpk.add_do_tpk') !!}', formData, 'input_success')
            }
        }

        // Gunakan event 'click' pada tombol dengan ID #calculate
        $('#calculate').on('click', function (e) {
            calculator(e); // Panggil fungsi calculator
        });

        function input_success(res) {
            if (res.status.code != 200) {
                input_error(res);
                return false;
            }

            $.toast({
                heading: "Berhasil!",
                text: res.message,
                position: "top-right",
                icon: "success",
                hideAfter: 2500,
                beforeHide: function () {
                    if (res.redirect.need) {
                        Swal.fire({
                            html: "<h5>Berhasil input SP2 Ke TPK / Repo,<br> Mengembalikan Anda ke halaman sebelumnya...</h5>",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });

                        Swal.showLoading();
                    } else {
                        return false;
                    }
                },
                afterHidden: function () {
                    if (res.redirect.need) {
                        window.location.href = res.redirect.to;
                    } else {
                        return false;
                    }
                },
            });
        }

        function input_error(err) {
            console.log(err);
            var detailMsg = err.message || (err.status && err.status.msg) || "Terjadi kesalahan saat menyimpan data";
            var formattedMsg = detailMsg.replace(/\n/g, '<br/>');
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses Data',
                html: '<div style="text-align:left; font-size:13px; line-height:1.5;"><p style="color:#d33;">' + formattedMsg + '</p></div>',
                confirmButtonText: 'Tutup'
            });
        }

        function calculator(e) {
            // Mencegah tindakan default (refresh halaman atau post data secara tradisional)
            e.preventDefault();

            // Mengambil nilai dari elemen input
            var $mulai = $('#ID_TGL_MULAI').val();
            var $nanti = $('#ID_TGL_NANTI').val();

            // Pastikan input tidak kosong
            if (!$mulai || !$nanti) {
                alert("Please fill in both the Start Date and End Date.");
                return;
            }

            // Melakukan permintaan Ajax
            $.ajax({
                url: '{!! route('uster.new_request.delivery.delivery_ke_luar_tpk.refcal') !!}',
                type: 'POST',
                data: {
                    'mulai': $mulai,
                    'nanti': $nanti
                },
                success: function (data) {
                    // Memasukkan hasil ke input atau elemen lain
                    $('#SHIFT_RFR').val(data);
                },
                error: function (xhr, status, error) {
                    // Menangani kesalahan jika ada
                    console.error("Error: " + error);
                }
            });
        }
    </script>
@endsection