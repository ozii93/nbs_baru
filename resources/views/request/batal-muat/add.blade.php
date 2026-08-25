@extends('layouts.app')

@section('title')
    Batal Muat
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
                <li class="breadcrumb-item"><a href="{{ route('uster.koreksi.batal_muat') }}">Batal Muat</a>
                </li>
                <li class="breadcrumb-item active">View</li>
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
                    <form id="insertForm">
                        <h3><b>Batal Muat</b></h3>

                        <div class="p-3">

                            <h4 class="card-title mb-3 pb-3 border-bottom">Form Batal Muat</h4>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Nama Pemilik : </label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" type="text" id="NM_PELANGGAN" name="NM_PELANGGAN"
                                        style="width: 70%" />
                                    <input class="form-control" type="text" id="KD_PELANGGAN" name="KD_PELANGGAN"
                                        style="width:20%;margin-left:20px" readonly="1" /></td>
                                    <input class="form-control" type="hidden" id="NO_ACCOUNT_PBM" name="NO_ACCOUNT_PBM"
                                        style="width:20%;margin-left:20px" readonly="1" /></td>
                                </div>
                                <div class="col-md-2 py-2">
                                    <label for="tb-fname">Jenis Batal Muat : </label>
                                </div>
                                <div class="col-md-4">

                                    <select class="form-control" name="jenis_batal" id="jenis_batal">
                                        <option value="" selected> PILIH</option>
                                        <option value="alih_kapal"> ALIH KAPAL </option>
                                        <option value="delivery"> DELIVERY </option>
                                    </select>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Dikenakan Biaya : </label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" name="biaya" id="biaya">
                                        <option value="" selected> PILIH</option>
                                        <option value="Y">YA</option>
                                        <option value="T">TIDAK</option>
                                    </select>
                                </div>
                                <div class="col-md-2 py-2">
                                    <label for="tb-fname">Ex Kegiatan : </label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" name="status_gate" id="status_gate">
                                        <option value="" selected> PILIH</option>
                                        <option value="1"> AFTER STUFFING </option>
                                        <option value="3"> BEFORE STUFFING </option>
                                        <option value="2"> EX REPO </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="p-3" id="kapal">
                            <h4 class="card-title mb-3 pb-3 border-bottom">Data Kapal Baru</h4>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Nama Kapal : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="KD_KAPAL" name="KD_KAPAL" type="hidden" />
                                    <select class="form-control" id="NM_KAPAL_SELECT" name="NM_KAPAL_SELECT">

                                    </select>
                                    <input class="form-control" id="NM_KAPAL" name="NM_KAPAL" type="hidden" />
                                    <input class="form-control" id="TGL_BERANGKAT" name="TGL_BERANGKAT" type="hidden" />
                                    <input class="form-control" id="TGL_STACKING" name="TGL_STACKING" type="hidden" />
                                    <input class="form-control" id="TGL_MUAT" name="TGL_MUAT" type="hidden" />
                                    <input class="form-control" id="NO_UKK" name="NO_UKK" type="hidden" />
                                    <input class="form-control" id="NM_AGEN" name="NM_AGEN" type="hidden" />
                                    <input class="form-control" id="VOYAGE" name="VOYAGE" type="hidden" />
                                    <input class="form-control" id="VOYAGE_OUT" name="VOYAGE_OUT" type="hidden" />
                                    <input class="form-control" id="KD_AGEN" name="KD_AGEN" type="hidden" />
                                    <input class="form-control" id="CALL_SIGN" name="CALL_SIGN" type="hidden" />
                                    <input class="form-control" id="NO_BOOKING" name="NO_BOOKING" type="hidden"
                                        size="40" />
                                </div>
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Voyage : </label>
                                </div>
                                <div class="col-md-4">

                                    <input class="form-control"id="VOYAGE_IN" name="VOYAGE_IN" type="text"
                                        readonly="1" />

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">ETA : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="ETA" name="ETA" type="text"
                                        readonly="1" />

                                </div>
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">ETD : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="ETD" name="ETD" type="text"
                                        readonly="1" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Port Of Destination : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="KD_PELABUHAN_ASAL" name="KD_PELABUHAN_ASAL"
                                        type="hidden" class="pod" readonly="1" />
                                    <select class="form-control" id="NM_PELABUHAN_ASAL" name="NM_PELABUHAN_ASAL"
                                        maxlength="100" />
                                    <input class="form-control" id="id_TGL_STACK" name="TGL_STACK" type="hidden"
                                        size="19" maxlength="19" />
                                </div>
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Final Discharge : </label>
                                </div>
                                <div class="col-md-4">

                                    <select class="form-control" id="NM_PELABUHAN_TUJUAN" name="NM_PELABUHAN_TUJUAN" />
                                    <input class="form-control" id="KD_PELABUHAN_TUJUAN" name="KD_PELABUHAN_TUJUAN"
                                        type="hidden" class="pod2" readonly="1" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">NPE : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="NPE" name="NPE" type="text" />
                                </div>
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">PEB : </label>
                                </div>
                                <div class="col-md-4">

                                    <input class="form-control" id="PEB" name="PEB" type="text" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">NPE : </label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" name="OI" id="OI">
                                        <option value="I"> Interinsuler</option>
                                        <option value="O"> Oceangoing</option>
                                    </select></td>
                                </div>

                            </div>


                        </div>

                        <div class="p-3" id="petikemas">
                            <h4 class="card-title mb-3 pb-3 border-bottom">Data Petikemas</h4>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Nomor Container : </label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" name="NO_CONT" ID="NO_CONT"
                                        placeholder="Autocomplete" />
                                    <input class="form-control" id="NO_REQUEST" name="NO_REQUEST" type="hidden" />
                                    <input class="form-control" id="NO_REQ_ICT" name="NO_REQ_ICT" type="hidden" />
                                </div>
                                <div class="col-md-2 py-2">
                                    <label for="tb-fname">Ukuran : </label>
                                </div>
                                <div class="col-md-4">

                                    <input class="form-control" type="text" name="SIZE" id="SIZE"
                                        readonly="readonly" />

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 py-3">
                                    <label for="tb-fname">Type : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" type="text" name="TYPE" id="TYPE"
                                        readonly="readonly" />
                                </div>
                                <div class="col-md-2 py-2">
                                    <label for="tb-fname">Status : </label>
                                </div>
                                <div class="col-md-4">

                                    <input class="form-control" type="text" name="STATUS" id="STATUS"
                                        readonly="readonly" />
                                </div>
                            </div>

                            <div class="row" id="date_penumpukan">
                                <div class="col-md-2 py-2">
                                    <label for="tb-fname">Tgl Penumpukan : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" type="date" name="TGL_PNKN_START"
                                        id="TGL_PNKN_START" />
                                </div>
                                <div class="col-md-2 py-2">
                                    <label for="tb-fname">s/d : </label>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" type="date" name="TGL_PNKN_END" id="TGL_PNKN_END" />
                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-info"><i class="fas fa-plus"></i>Tambah Container</button>

                        <h2>Data Table</h2>
                        <div id="contdiv" class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>No. Petikemas</th>
                                        <th>Size</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Tanggal Awal</th>
                                        <th>Tanggal Akhir</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTableBody">
                                </tbody>
                            </table>
                        </div>

                        <a onclick="save()" class="btn btn-primary"><i class="fas fa-save"></i>Simpan Container</a>
                        <input type="hidden" name="tvalue" id="tvalue" value="0" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush


@section('pages-js')
    <script>
        // Array to store inserted data
        let insertedData = [];
        var ctr = 0;
        var list = new Array();

        // Function to refresh row numbers
        function refreshRowNumbers() {
            $('#dataTableBody tr').each(function(index, tr) {
                $(tr).find('.row-number').val(index + 1);
            });
        }
        // Function to add data to table
        function addToTable() {
            const noCont = $("#NO_CONT").val();

            if (!noCont) {
                Swal.fire({
                    icon: 'error',
                    title: 'Harap Isi Terlebih Dahulu Nomor Container!',
                });
                return;
            }

            if (list.includes(noCont)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Nomor Container sudah ada di dalam daftar!',
                });
                return;
            }

            list.push(noCont);

            const bmNoReq = $("#NO_REQUEST").val();
            const size = $("#SIZE").val();
            const type = $("#TYPE").val();
            const status = $("#STATUS").val();
            const oldVsb = $("#UKK_LAMA").val();
            const oldEtd = $("#ETD_LAMA").val();
            const jenisBatal = $("#jenis_batal").val();
            const awal = jenisBatal === 'alih_kapal' ? $("#TGL_PNKN_START").val() : '';
            const akhir = jenisBatal === 'alih_kapal' ? $("#TGL_PNKN_END").val() : '';

            const tvalue = $("#tvalue");
            const num = parseInt(tvalue.val(), 10) + 1;
            tvalue.val(num);

            const newRow = `
                        <tr id="rec${num}div">
                            <td align="left"><input class="form-control row-number" type="text" value="${num}" style="width:40px" readonly/></td>
                            <td align="left">
                                <input class="form-control" type="text" id="BM_CONT[${num}]" name="BM_CONT[]" value="${noCont}" style="width:170px" readonly/>
                                <input type="hidden" id="BMNO_REQ[${num}]" name="BMNO_REQ[]" value="${bmNoReq}" />
                            </td>
                            <td align="left">
                                <input class="form-control" type="text" name="KDSIZE[]" value="${size}" style="width:50px" readonly/>
                                <input type="hidden" name="UKKLAMA[]" value="${oldVsb}" />
                                <input type="hidden" name="ETDLAMA[]" value="${oldEtd}" />
                            </td>
                            <td align="left"><input class="form-control" type="text" name="KDSTATUS[]" value="${status}" style="width:100px" readonly/></td>
                            <td align="left"><input class="form-control" type="text" name="KDTYPE[]" value="${type}" style="width:100px" readonly/></td>
                            <td align="left"><input class="form-control" type="text" id="AWAL[${num}]" name="AWAL[]" value="${awal}" style="width:100px" readonly/></td>
                            <td align="left"><input class="form-control" type="text" id="AKHIR[${num}]" name="AKHIR[]" value="${akhir}" style="width:100px" readonly/></td>
                            <td><button class="btn btn-danger btn-sm delete-row">Delete</button></td>
                        </tr>
                    `;

            $('#dataTableBody').append(newRow);
            ctr++;
        }

        var xcont = Array();

        function removeRow(divnum, num, cont) {
            xcont[num] = '';
            var d = document.getElementById('contdiv');
            var olddiv = document.getElementById(divnum);
            d.removeChild(olddiv);
            ctr--;
            Array.prototype.remove = function(v) {
                this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1);
            }
            list.remove(cont);
        }

        // Delete row handler
        $(document).on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
            refreshRowNumbers();
        });

        // Submit form handler
        $('#insertForm').submit(function(event) {
            event.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var no_cont_ = $("#NO_CONT").val();
            var no_req_ = $("#NO_REQUEST").val();
            var jenis_bm_ = $("#status_gate").val();
            var newvessel_ = $("#NO_BOOKING").val();

            $.post('{{ route('uster.koreksi.batal_muat.validateContainer') }}', {
                no_req: no_cont_,
                no_cont: no_req_,
                jenis_bm: jenis_bm_,
                newvessel: newvessel_,
            }, function(res) {

                if ($("#NO_CONT").val() == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'No container belum diisi!',
                    });
                } else {

                    if (res == 'T') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Container tidak ditemukan!',
                        });
                        $("#NO_CONT").val('');

                    } else if (res == 'X') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kapal baru sama dengan kapal sebelumnya!',
                        });
                        $("#NO_CONT").val('');
                    } else {
                        insertedData.push();
                        addToTable();
                        refreshRowNumbers();
                    }

                }
            });
        });

        // Submit 2 handler
        $('#submit2').click(function() {
            // Here you can send insertedData array to your backend for further processing
            $.ajax({
                url: 'backend.php', // Ganti dengan URL backend Anda
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    data: insertedData
                }),
                success: function(response) {
                    console.log('Data berhasil dikirim ke backend:', response);
                    // Clear the table and insertedData array
                    $('#dataTableBody').empty();
                    insertedData = [];
                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengirim data ke backend:', error);
                }
            });
            // For this example, let's clear the table and insertedData array
            $('#dataTableBody').empty();
            insertedData = [];
        });

        function save_bm_praya() {
            var formData = $('#insertForm').serialize();
            // Perform AJAX request
            $.ajax({
                type: 'POST',
                url: '{{ route('uster.koreksi.batal_muat.save_bm_praya') }}',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });

                        window.location.href =
                            '{{ route('uster.koreksi.batal_muat') }}';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred: ' + error
                    });
                }
            });
        }

        function save() {
            console.log(list);
            if ($("#jenis_batal").val() == '' || $("#status_gate").val() == '' || $("#KD_PELANGGAN").val() ==
                '' || $(
                    "#biaya").val() == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Nama Pemilik, Biaya, Jenis BM & Ex Kegiatan Harus Dipilih!',
                });

                return false;
            } else {
                if ($("#status_gate").val() == '2' && $("#jenis_batal").val() == 'alih_kapal' && $("#biaya")
                    .val() == 'T') {
                    Swal.fire({
                        title: 'Mendapatkan Data...',
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    var ex_noreq = $("#NO_REQUEST").val();
                    var vessel_id = $("#KD_KAPAL").val();
                    var vessel_name = $("#NM_KAPAL").val();
                    var voyage = $("#VOYAGE").val();
                    var voyage_in = $("#VOYAGE_IN").val();
                    var voyage_out = $("#VOYAGE_OUT").val();
                    var nm_agen = $("#NM_AGEN").val();
                    var kd_agen = $("#KD_AGEN").val();
                    var pelabuhan_tujuan = $("#KD_PELABUHAN_TUJUAN").val();
                    var pelabuhan_asal = $("#KD_PELABUHAN_ASAL").val();
                    var url = '{{ route('uster.koreksi.batal_muat.save_payment_uster_batal_muat') }}';
                    var payload_batal_muat = {
                        ex_noreq,
                        vesselId: vessel_id,
                        vesselName: vessel_name,
                        voyage,
                        voyageIn: voyage_in,
                        voyageOut: voyage_out,
                        nm_agen,
                        kd_agen,
                        pelabuhan_asal,
                        pelabuhan_tujuan,
                        cont_list: list
                    };
                    $.post(url, {
                        payload_batal_muat
                    }, function(data) {
                        console.log(data)
                        if (data) {
                            var parseData = data;
                            if (parseData['code'] == 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: parseData.message,
                                });
                            } else {
                                save_bm_praya();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed send to Praya',
                                text: parseData.message
                            });
                        }
                    });
                } else {
                    save_bm_praya();
                }
            }
        }
    </script>
    <script>
        function convertToHtmlDateFormat(dateStr) {
            // Input: "25/05/2025"
            const parts = dateStr.split('/');
            const day = parts[0];
            const month = parts[1];
            const year = parts[2];
            return `${year}-${month}-${day}`; // Output: "2025-05-25"
        }

        $(document).ready(function() {

            $("#kapal, #petikemas").hide();

            // Tambahkan event listener untuk menangani perubahan dropdown
            $("#jenis_batal").change(function() {
                // Ambil nilai yang dipilih
                var selectedOption = $(this).val();

                // Tampilkan atau sembunyikan elemen sesuai dengan opsi yang dipilih
                if (selectedOption === "alih_kapal") {
                    $("#kapal").show();
                    $("#petikemas").show();
                    $("#date_penumpukan").show();
                } else if (selectedOption === "delivery") {
                    $("#kapal").hide();
                    $("#petikemas").show();
                    $("#date_penumpukan").hide();
                } else {
                    // Jika opsi yang dipilih adalah yang lainnya, sembunyikan keduanya
                    $("#id_kapal, #petikemas").hide();
                }
            });

            $('#service-table').DataTable({
                responsive: true,
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('uster.koreksi.batal_muat.viewContainerByRequest', ['no_req' => request('no_req')]) !!}',
                    type: 'GET',
                    data: function(d) {
                        d.active = 'view'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex', // Use the special 'DT_RowIndex' property provided by DataTables
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            // Render the sequence number
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'no_container',
                        name: 'no_container'
                    },
                    {
                        data: 'size_',
                        name: 'size_'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'type_',
                        name: 'type_'
                    },
                    {
                        data: 'start_pnkn',
                        name: 'start_pnkn'
                    },
                    {
                        data: 'end_pnkn',
                        name: 'end_pnkn'
                    },

                ],
            });
        });

        $('#NM_PELANGGAN').select2({
            minimumInputLength: 3, // Set the minimum input length
            ajax: {
                // Implement your AJAX settings for data retrieval here
                url: '{!! route('uster.koreksi.batal_muat.getPMB') !!}',
                dataType: 'json',
                processResults: function(data) {
                    const arrs = data;
                    return {
                        results: arrs.map((arr, i) => ({
                            id: arr.no_account_pbm,
                            text: arr.nm_pbm + ' | ' + arr.almt_pbm,
                            ...arr
                        }))
                    };
                }
            }
        });


        $('#NM_PELANGGAN').on('select2:select', function(e) {
            var data = e.params.data;
            $("#KD_PELANGGAN").val(data.kd_pbm);
            $("#NM_PELANGGAN").val(data.nm_pbm);
            $("#ALAMAT").val(data.almt_pbm);
            $("#NPWP").val(data.no_npwp_pbm);
            $("#NO_ACCOUNT_PBM").val(data.no_account_pbm);


            return false;


        })
        $('#NM_PELANGGAN').on('select2:clear', function(e) {
            // $form[0].reset()
        })

        $('#NO_CONT').select2({
            minimumInputLength: 3, // Set the minimum input length
            ajax: {
                // Implement your AJAX settings for data retrieval here
                url: '{!! route('uster.koreksi.batal_muat.getContainer') !!}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term, // Input search term
                        jns: $("#status_gate").val() // Value of #status_gate
                    };
                },
                processResults: function(data) {
                    const arrs = data;
                    return {
                        results: arrs.map((arr, i) => ({
                            id: arr.no_container,
                            text: arr.no_container + ' | ' + arr.no_request + ' | ' + arr
                                .nm_kapal + ' | ' + arr.voyage_in,
                            ...arr
                        }))
                    };
                }
            }
        });


        $('#NO_CONT').on('select2:select', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var data = e.params.data;

            var status_gate = $("#status_gate").val();

            if (status_gate === '2') {
                var jenis_batal = $("#jenis_batal").val();
                var url_disable_container = '{{ route('uster.koreksi.batal_muat.prayaGetContainer') }}';
                $.post(url_disable_container, {
                    no_cont: data.no_container,
                    jenis_bm: jenis_batal
                }, function(data) {
                    const returnData = data.dataRec[0]
                    if (data.code === '1') {
                        let containerStatus = returnData.containerStatus == 'EMPTY' ? 'MTY' : 'FCL';
                        if ($("#status_gate").val() == '1') {
                            if (containerStatus != 'FCL') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Container Belum Realisasi!',
                                });

                                $("#NO_CONT").val('');
                                $("#NO_REQUEST").val('');
                                $("#SIZE").val('');
                                $("#TYPE").val('');
                                $("#STATUS").val('');
                            } else {
                                $("#NO_CONT").val(returnData.containerNo);
                                $("#NO_REQUEST").val(returnData.requestId);
                                $("#SIZE").val(returnData.containerSize);
                                $("#TYPE").val(returnData.containerType);
                                $("#STATUS").val(containerStatus);
                                $.ajax({
                                    url: '{{ route('uster.koreksi.batal_muat.getContainerHistory') }}',
                                    method: 'GET',
                                    data: {
                                        no_cont: returnData.containerNo,
                                        no_req: returnData.requestId
                                    },
                                    success: function(data) {
                                        $("#TGL_PNKN_START").val(convertToHtmlDateFormat(data));
                                    },
                                    error: function(xhr) {
                                        console.error(xhr.responseText);
                                    }
                                });
                            }
                        } else {
                            $("#NO_CONT").val(returnData.containerNo);
                            $("#NO_REQUEST").val(returnData.requestId);
                            $("#SIZE").val(returnData.containerSize);
                            $("#TYPE").val(returnData.containerType);
                            $("#STATUS").val(containerStatus);
                            $("#NO_REQ_ICT").val(returnData.no_req_itc);
                            $("#UKK_LAMA").val(returnData.ukk_lama);
                            $("#ETD_LAMA").val(returnData.etd_old);

                            $.ajax({
                                url: '{{ route('uster.koreksi.batal_muat.getContainerHistory') }}',
                                method: 'GET',
                                data: {
                                    no_cont: returnData.containerNo,
                                    no_req: returnData.requestId
                                },
                                success: function(data) {
                                    $("#TGL_PNKN_START").val(convertToHtmlDateFormat(data));
                                },
                                error: function(xhr) {
                                    console.error(xhr.responseText);
                                }
                            });
                        }
                        return false;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: `Container in Service disableContainer : ${json.msg}`,
                        });

                        $("#NO_CONT").val('');
                        return false;
                    }
                });
            } else {
                console.log(data)
                if ($("#status_gate").val() == '1') {
                    if (data.status_cont != 'FCL') {

                        Swal.fire({
                            icon: 'error',
                            title: 'Container Belum Realisasi!',
                            timer: 1000,
                        });
                        $("#NO_CONT").val('');
                        $("#NO_REQUEST").val('');
                        $("#SIZE").val('');
                        $("#TYPE").val('');
                        $("#STATUS").val('');
                    } else {
                        $("#NO_CONT").val(data.no_container);
                        $("#NO_REQUEST").val(data.no_request);
                        $("#SIZE").val(data.size_);
                        $("#TYPE").val(data.type_);
                        $("#STATUS").val(data.status_cont);
                        $.ajax({
                            url: '{{ route('uster.koreksi.batal_muat.getContainerHistory') }}',
                            method: 'GET',
                            data: {
                                no_cont: data.no_container,
                            },
                            success: function(data) {
                                $("#TGL_PNKN_START").val(convertToHtmlDateFormat(data));
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                            }
                        });
                    }
                } else {
                    $("#NO_CONT").val(data.no_container);
                    $("#NO_REQUEST").val(data.no_request);
                    $("#SIZE").val(data.size_);
                    $("#TYPE").val(data.type_);
                    $("#STATUS").val(data.status_cont);
                    $("#NO_REQ_ICT").val(returnData.no_req_ict);
                    $("#UKK_LAMA").val(returnData.ukk_lama);
                    $("#ETD_LAMA").val(returnData.etd_old);

                    $.ajax({
                        url: '{{ route('uster.koreksi.batal_muat.getContainerHistory') }}',
                        method: 'GET',
                        data: {
                            no_cont: data.no_container,
                            no_req: data.no_request
                        },
                        success: function(data) {
                            $("#TGL_PNKN_START").val(convertToHtmlDateFormat(data));
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                }
                return false;
            }


        })
        $('#NO_CONT').on('select2:clear', function(e) {
            // $form[0].reset()
        })

        $('#NM_PELABUHAN_ASAL').select2({
            minimumInputLength: 3, // Set the minimum input length
            ajax: {
                // Implement your AJAX settings for data retrieval here
                url: '{!! route('uster.koreksi.batal_muat.masterPelabuhanPalapa') !!}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term, // Input search term
                    };
                },
                processResults: function(data) {
                    const arrs = data;
                    return {
                        results: arrs.map((arr, i) => ({
                            id: arr.cdg_port_code,
                            text: arr.cdg_port_code + ' | ' + arr.cdg_port_name,
                            ...arr
                        }))
                    };
                }
            }
        });


        $('#NM_PELABUHAN_ASAL').on('select2:select', function(e) {
            var data = e.params.data;
            $("#KD_PELABUHAN_ASAL").val(data.cdg_port_code);
            $("#NM_PELABUHAN_ASAL").val(data.cdg_port_name);

            return false;



        })
        $('#NM_PELABUHAN_ASAL').on('select2:clear', function(e) {
            // $form[0].reset()
        })

        $('#NM_PELABUHAN_TUJUAN').select2({
            minimumInputLength: 3, // Set the minimum input length
            ajax: {
                // Implement your AJAX settings for data retrieval here
                url: '{!! route('uster.koreksi.batal_muat.masterPelabuhanPalapa') !!}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term, // Input search term
                    };
                },
                processResults: function(data) {
                    const arrs = data;
                    return {
                        results: arrs.map((arr, i) => ({
                            id: arr.cdg_port_code,
                            text: arr.cdg_port_code + ' | ' + arr.cdg_port_name,
                            ...arr
                        }))
                    };
                }
            }
        });


        $('#NM_PELABUHAN_TUJUAN').on('select2:select', function(e) {
            var data = e.params.data;
            $("#KD_PELABUHAN_TUJUAN").val(data.cdg_port_code);
            $("#NM_PELABUHAN_TUJUAN").val(data.cdg_port_name);

            return false;



        })
        $('#NM_PELABUHAN_TUJUAN').on('select2:clear', function(e) {
            // $form[0].reset()
        })

        $('#NM_KAPAL_SELECT').select2({
            minimumInputLength: 3, // Set the minimum input length
            ajax: {
                // Implement your AJAX settings for data retrieval here
                url: '{!! route('uster.koreksi.batal_muat.masterVesselPalapa') !!}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term, // Input search term
                    };
                },
                processResults: function(data) {
                    const arrs = data;
                    return {
                        results: arrs.map((arr, i) => ({
                            id: arr.call_sign,
                            text: arr.vessel + ' | ' + arr.voyage_in + ' | ' + arr
                                .voyage_out,
                            ...arr
                        }))
                    };
                }
            }
        });

        function formatDate(inputDate) {
            var parts = inputDate.split(' ');
            var datePart = parts[0];
            var timePart = parts[1];
            var dateParts = datePart.split('-');
            var formattedDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
            return formattedDate;
        }

        $('#NM_KAPAL_SELECT').on('select2:select', function(e) {
            var data = e.params.data;
            $("#KD_KAPAL").val(data.vessel_code);
            $("#NM_AGEN").val(data.operator_name);
            $("#KD_AGEN").val(data.operator_id);
            $("#NM_KAPAL").val(data.vessel);
            $("#VOYAGE_IN").val(data.voyage_in);
            $("#VOYAGE_OUT").val(data.voyage_out);
            $("#NO_UKK").val(data.id_vsb_voyage);
            $("#NO_BOOKING").val(`BS${data.vessel_code}${data.id_vsb_voyage}`);
            $("#TGL_BERANGKAT").val(data.etd)
            $("#TGL_PNKN_END").val(formatDate(data.etd))
            $("#TGL_STACKING").val(data.open_stack)
            $("#TGL_MUAT").val(data.start_work)
            $("#ETA").val(data.eta)
            $("#ETD").val(data.etd)
            $("#VOYAGE").val(data.voyage)
            $("#CALL_SIGN").val(data.call_sign)
            $("#POL").val(data.id_pol);

            return false;



        })
        $('#NM_KAPAL').on('select2:clear', function(e) {
            // $form[0].reset()
        })
    </script>
@endsection
