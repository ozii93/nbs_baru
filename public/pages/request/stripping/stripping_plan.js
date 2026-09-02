var table, s_no_req, s_tgl_awal, s_tgl_akhir;
$(document).bind("keydown", function (e) {
    if (e.keyCode == 113) addContainer();
    return true;
});

$(function () {
    addContainer();
    if ($("#data-section").length > 0 && $(".alert").css("display") == "none") {
        $("html, body").animate(
            {
                scrollTop: $("#data-section").offset().top,
            },
            1000
        );
    }

    setTimeout(() => {
        if (
            $(".alert").css("display") != "none" &&
            $("#data-section").length > 0
        ) {
            $(".alert.closeable").alert("close");
            $("html, body").animate(
                {
                    scrollTop: $("#data-section").offset().top,
                },
                1250
            );
        }
    }, 2500);

    $("#no_cont").on("keydown", function (e) {
        return e.which !== 32;
    });

    var forms = document.querySelectorAll("form");
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener(
            "submit",
            function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            },
            false
        );
    });

    $("#search-data").on("submit", function (e) {
        if (this.checkValidity()) {
            e.preventDefault();
            $('input[name="search"]').val("true");
            if ($("#search-data").find("input.form-control").val() == "") {
                $('input[name="search"]').val("false");
            }

            $("html, body").animate(
                {
                    scrollTop: $("#data-section").offset().top,
                },
                1250
            );
            table.ajax.reload();
        }

        $(this).addClass("was-validated");
    });

    $("#form-add").on("submit", function (e) {
        if (this.checkValidity()) {
            e.preventDefault();
            saveData("#form-add");
        }
        $(this).addClass("was-validated");
    });

    $("#request-header").on("submit", function (e) {
        if (this.checkValidity()) {
            e.preventDefault();
            saveEdit("#request-header");
        }
        $(this).addClass("was-validated");
    });

    $("#form-add-container").on("submit", function (e) {
        if (this.checkValidity()) {
            e.preventDefault();
            saveCont("#form-add-container");
        }
        $(this).addClass("was-validated");
    });

    $("#start_date").bootstrapMaterialDatePicker({ weekStart: 0, time: false });
    $("#end_date").bootstrapMaterialDatePicker({ weekStart: 0, time: false });
    $("#list-cont-table").DataTable({
        pageLength: 100,
        lengthMenu: [
            [100, 10, 25, 50, 200, -1],
            [100, 10, 25, 50, 200, "All"],
        ],
    });

    getData();
});

/** GET DATA & Auto Complete Section */
/** ================================ */
function getData() {
    table = $(".data-table").DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}request/stripping/stripping-plan/data`,
            method: "POST",
            data: function (data) {
                data._token = `${$('meta[name="csrf-token"]').attr("content")}`;
                data.cari = $('input[name="search"]').val();
                data.no_request = $('input[name="no_req"]').val();
                data.tgl_awal = $('input[name="tgl_awal"]').val();
                data.tgl_akhir = $('input[name="tgl_akhir"]').val();
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                className: "text-center",
                width: "20px",
                orderable: false,
                searchable: false,
                responsivePriority: -1,
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
                className: "text-center",
                width: "200px",
                responsivePriority: -1,
            },
            {
                data: "no_request",
                name: "no_request",
            },
            {
                data: "no_request_app",
                name: "no_request_app",
            },
            {
                data: "tgl_request",
                name: "tgl_request",
                className: "text-center",
            },
            {
                data: "nama_pemilik",
                name: "nama_pemilik",
            },
            {
                data: "no_do_bl",
                name: "no_do_bl",
                responsivePriority: 1,
            },
            {
                data: "type_stripping",
                name: "type_stripping",
                responsivePriority: 1,
            },
        ],
    });
}

// $("#CONSIGNEE").on("keyup", function () {
//     $("#ID_CONSIGNEE").val("");
//     $("#ALMT_CONSIGNEE").val("");
//     $("#NPWP_CONSIGNEE").val("");
//     $("#PENUMPUKAN").val("");
//     $("#ID_PENUMPUKAN").val("");
//     $("#ALMT_PENUMPUKAN").val("");
//     $("#NPWP_PENUMPUKAN").val("");
//     $("#NO_ACC_CONS").val("");
// });

$("#CONSIGNEE").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}request/stripping/stripping-plan/data-pbm`,
            type: "GET",
            dataType: "json",
            data: {
                search: request.term,
            },
            error: function (err) {
                get_error(err.responseJSON);
            },
            success: function (data) {
                response(
                    data.map(function (value) {
                        return {
                            label: value.nm_pbm,
                            kd_pbm: value.kd_pbm,
                            almt_pbm: value.almt_pbm,
                            npwp: value.no_npwp_pbm,
                            no_account: value.no_account_pbm,
                        };
                    })
                );
            },
        });
    },
    select: function (event, ui) {
        $("#CONSIGNEE").val(ui.item.label);
        $("#ID_CONSIGNEE").val(ui.item.kd_pbm);
        $("#ALMT_CONSIGNEE").val(ui.item.almt_pbm);
        $("#NPWP_CONSIGNEE").val(ui.item.npwp);
        $("#PENUMPUKAN").val(ui.item.label);
        $("#ID_PENUMPUKAN").val(ui.item.kd_pbm);
        $("#ALMT_PENUMPUKAN").val(ui.item.almt_pbm);
        $("#NPWP_PENUMPUKAN").val(ui.item.npwp);
        $("#NO_ACC_CONS").val(ui.item.no_account);
        return false;
    },
});

// $("#NM_KAPAL").on("keyup", function () {
//     $("#VOYAGE_IN").val("");
//     $("#VOYAGE_OUT").val("");
//     $("#NO_BOOKING").val("");
//     $("#IDVSB").val("");
//     $("#CALLSIGN").val("");
//     $("#VESSEL_CODE").val("");
//     $("#TANGGAL_JAM_TIBA").val("");
//     $("#TANGGAL_JAM_BERANGKAT").val("");
//     $("#OPERATOR_NAME").val("");
//     $("#OPERATOR_ID").val("");
//     $("#POD").val("");
//     $("#POL").val("");
//     $("#VOYAGE").val("");
// });

$("#NM_KAPAL").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}request/stripping/stripping-plan/data-kapal`,
            type: "GET",
            dataType: "json",
            data: {
                search: request.term,
            },
            error: function (err) {
                get_error(err.responseJSON);
            },
            success: function (data) {
                response(
                    data.map(function (value) {
                        return {
                            label: `${value.vessel} | ${value.voyage_in}/${value.voyage_out}`,
                            VOYAGE_IN: value.voyage_in,
                            VOYAGE_OUT: value.voyage_out,
                            VESSEL_CODE: value.vessel_code,
                            VESSEL: value.vessel,
                            ID_VSB_VOYAGE: value.id_vsb_voyage,
                            CALL_SIGN: value.call_sign,
                            ATA: value.ata,
                            ETA: value.eta,
                            ATD: value.atd,
                            ETD: value.etd,
                            OPERATOR_NAME: value.operator_name,
                            OPERATOR_ID: value.operator_id,
                            ID_POD: value.id_pod,
                            ID_POL: value.id_pol,
                            VOYAGE: value.voyage,
                        };
                    })
                );
            },
        });
    },
    select: function (event, ui) {
        console.log(ui.item);
        $("#VOYAGE_IN").val(ui.item.VOYAGE_IN);
        $("#VOYAGE_OUT").val(ui.item.VOYAGE_OUT);
        if (ui.item.VESSEL_CODE && ui.item.ID_VSB_VOYAGE) {
            $("#NO_BOOKING").val(
                `BP${ui.item.VESSEL_CODE}${ui.item.ID_VSB_VOYAGE}`
            );

            $("#_NO_BOOKING").val(
                `BP${ui.item.VESSEL_CODE}${ui.item.ID_VSB_VOYAGE}`
            );
        }
        $("#IDVSB").val(ui.item.ID_VSB_VOYAGE);
        $("#CALLSIGN").val(ui.item.CALL_SIGN);
        $("#VESSEL_CODE").val(ui.item.VESSEL_CODE);
        $("#NM_KAPAL").val(ui.item.VESSEL);
        $("#TANGGAL_JAM_TIBA").val(ui.item.ATA ? ui.item.ATA : ui.item.ETA);
        $("#TANGGAL_JAM_BERANGKAT").val(
            ui.item.ATD ? ui.item.ATD : ui.item.ETD
        );
        $("#OPERATOR_NAME").val(ui.item.OPERATOR_NAME);
        $("#OPERATOR_ID").val(ui.item.OPERATOR_ID);
        $("#POD").val(ui.item.ID_POD);
        $("#POL").val(ui.item.ID_POL);
        $("#VOYAGE").val(ui.item.VOYAGE);
        $("#_VOYAGE").val(ui.item.VOYAGE);
        return false;
    },
});

$("#NO_CONT").autocomplete({
    minLength: 6,
    source: function (request, response) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}request/stripping/stripping-plan/data-cont`,
            type: "POST",
            dataType: "json",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                search: request.term,
                voyage: $("#CURR_VOYAGE").val(),
                voyage_in: $("#VOYAGE_IN").val(),
                voyage_out: $("#VOYAGE_OUT").val(),
                vessel: $("#NM_KAPAL").val(),
                idvsb: $("#IDVSB").val(),
                vessel_code: $("#VESCODE").val(),
            },
            error: function (err) {
                get_error(err.responseJSON);
            },
            success: function (data) {
                if (data.length > 0) {
                    response(
                        data.map(function (value) {
                            console.log(value);
                            return {
                                label: `${value.containerNo} | ${value.voyageIn}/${value.voyageOut}`,
                                vesselName: value.vesselName,
                                voyageIn: value.voyageIn,
                                containerNo: value.containerNo,
                                containerSize: value.containerSize,
                                containerStatus: value.containerStatus,
                                containerType: value.containerType,
                                vesselConfirm: value.vesselConfirm,
                                dischargeDate: value.dischargeDate,
                                ydBlock: value.ydBlock,
                                ydSlot: value.ydSlot,
                                ydRow: value.ydRow,
                                ydTier: value.ydTier,
                            };
                        })
                    );
                }

                $("#NO_CONT").removeClass("ui-autocomplete-loading");
            },
        });
    },
    select: function (event, ui) {
        $("#NO_CONT").val(ui.item.containerNo);
        $("#SIZE").val(ui.item.containerSize);
        $("#STATUS").val(ui.item.containerStatus);
        $("#TYPE").val(ui.item.containerType);
        $("#VOYAGE").val(ui.item.voyageIn);
        $("#VESSEL").val(ui.item.vesselName);
        $("#tgl_mulai").val(ui.item.vesselConfirm);
        $("#TGL_BONGKAR").val(ui.item.vesselConfirm);
        $("#TGL_STACK").val(ui.item.dischargeDate);
        $("#NO_UKK").val($("#IDVSB").val());
        $("#NM_AGEN").val(ui.item.NM_AGEN);
        $("#BP_ID").val("BP" + $("#VESCODE").val() + "" + $("#IDVSB").val());
        $("#ASAL_CONT").val("TPK");
        $("#NO_BOOKING").val(
            "BP" + $("#VESCODE").val() + "" + $("#IDVSB").val()
        );
        $("#BLOK").val(ui.item.ydBlock);
        $("#SLOT").val(ui.item.ydSlot);
        $("#ROW").val(ui.item.ydRow);
        $("#TIER").val(ui.item.ydTier);

        //start  updated by clara ilcs 27 November 2023
        if (ui.item && ui.item.dischargeDate) {
            var containerDischDateStr = ui.item.dischargeDate; // Format: YYYYMMDDHHmmss

            // Parsing the date components correctly
            var year = parseInt(containerDischDateStr.substring(0, 4));
            var month = parseInt(containerDischDateStr.substring(4, 6)) - 1; // JavaScript months are 0-based
            var day = parseInt(containerDischDateStr.substring(6, 8));
            var hours = parseInt(containerDischDateStr.substring(8, 10));
            var minutes = parseInt(containerDischDateStr.substring(10, 12));
            var seconds = parseInt(containerDischDateStr.substring(12, 14));

            // Create the date object
            var containerDischDate = new Date(year, month, day);

            // Check if date creation is valid
            if (isNaN(containerDischDate.getTime())) {
                console.error("Invalid date format");
            } else {
                // Add 4 days
                containerDischDate.setDate(containerDischDate.getDate() + 4);

                // Format the date as YYYY-MM-DD HH:mm:ss
                var formattedDate =
                    containerDischDate.getFullYear() +
                    "-" +
                    ("0" + (containerDischDate.getMonth() + 1)).slice(-2) +
                    "-" +
                    ("0" + containerDischDate.getDate()).slice(-2);
            }

            $("#TGL_SELESAI").val(formattedDate);

            return false;
        }
    },
});

$("#KOMODITI").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}request/stripping/stripping-plan/data-komoditi`,
            type: "GET",
            dataType: "json",
            data: {
                search: request.term,
            },
            success: function (data) {
                response(
                    data.map(function (value) {
                        return {
                            label: value.nm_commodity,
                            kd_komoditi: value.kd_commodity,
                        };
                    })
                );
            },
        });
    },
    select: function (event, ui) {
        $("#KOMODITI").val(ui.item.label);
        $("#kd_komoditi").val(ui.item.kd_komoditi);
        return false;
    },
});

$("#VOYAGE").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}request/stripping/stripping-plan/data-voyage`,
            type: "GET",
            dataType: "json",
            data: {
                search: request.term,
            },
            error: function (err) {
                $("#NO_CONT").removeClass("ui-autocomplete-loading");
                // get_error(err.responseJSON)
            },
            success: function (data) {
                response(
                    data.map(function (value) {
                        return {
                            label: value.voyage,
                            VOYAGE: value.voyage,
                            VESSEL: value.vessel,
                            NO_BOOKING: value.no_booking,
                        };
                    })
                );
            },
        });
    },
    select: function (event, ui) {
        $("#VOYAGE").val(ui.item.VOYAGE);
        $("#VESSEL").val(ui.item.VESSEL);
        $("#NO_BOOKING").val(ui.item.NO_BOOKING);
        return false;
    },
});

function updateTglApprove(noCont, index) {
    const tglApprove = $(`input[name="TGL_APPROVE_${index}"]`).val();
    const tglBongkar = $(`input[name="tgl_bongkar_${index}"]`).val();
    const no_Cont = $(`input[name="no_cont_${index}"]`).val();
    const tgl_app_sel = $(`input[name="TGL_APPROVE_SELESAI_${index}"]`).val();
    const remark = $(`input[name="remarks_${index}"]`).val();
    var asal_cont = $(`input[name="asal_cont_${index}`).val();
    let canInput = true;

    if (tglApprove == "") {
        $.toast({
            heading: "Tanggal Approve Harus Diisi",
            position: "top-right",
            loaderBg: "#ff6849",
            icon: "error",
            hideAfter: 3500,
        });

        canInput = false;
    } else if (tgl_app_sel == "") {
        $.toast({
            heading: "Tanggal Approve Selesai Harus Diisi",
            position: "top-right",
            loaderBg: "#ff6849",
            icon: "error",
            hideAfter: 3500,
        });

        canInput = false;
    } else if (
        new Date(tglApprove).getTime() > new Date(tgl_app_sel).getTime()
    ) {
        $.toast({
            heading: "Tgl Approve lebih besar dari Tgl Approve Selesai",
            position: "top-right",
            loaderBg: "#ff6849",
            icon: "error",
            hideAfter: 3500,
        });

        canInput = false;
    }

    if (canInput) {
        var formContainer = new FormData();
        formContainer.append("_token", $('input[name="_token"]').val());
        formContainer.append("search", noCont);
        formContainer.append("voyage", $("#_VOYAGE").val());
        formContainer.append("voyage_out", $("#VOYAGE_OUT").val());
        formContainer.append("voyage_in", $("#VOYAGE_IN").val());
        formContainer.append("vessel_code", $("#VESCODE").val());

        $.ajax({
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}request/stripping/stripping-plan/data-cont`,
            type: "POST",
            data: formContainer,
            processData: false,
            contentType: false,
            error: function (err) {
                get_error(err.responseJSON);
            },
            beforeSend: function () {
                Swal.fire({
                    html: "<h5>Please Wait...</h5>",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                });

                Swal.showLoading();
            },
            success: function (data) {
                Swal.close();
                if (data.length == 0) {
                    $.toast({
                        heading: "Gagal Approve!!",
                        text: "Container Not Found In Praya",
                        position: "top-right",
                        loaderBg: "#ff6849",
                        icon: "error",
                        hideAfter: 3500,
                    });
                } else {
                    var container_praya = data[0];
                    var tgl_approve = tglApprove;
                    var no_req = $("#no_req").val();
                    var no_req2_ = $("#no_req2").val();
                    var no_do_ = $("#no_do").val();
                    var no_bl_ = $("#no_bl").val();
                    var sp2_ = $("#SP2").val();
                    var kd_consignee_ = $("#ID_CONSIGNEE").val();

                    var no_req_rec = $("#NO_REQUEST_RECEIVING").val();

                    $.ajax({
                        url: `${$('meta[name="baseurl"]').attr(
                            "content"
                        )}request/stripping/stripping-plan/approve-cont`,
                        type: "POST",
                        data: {
                            _token: $('input[name="_token"]').val(),
                            tgl_app_selesai: tgl_app_sel,
                            tgl_approve: tgl_approve,
                            no_cont: no_Cont,
                            no_req: no_req,
                            NO_REQ2: no_req2_,
                            NO_REQ_REC: no_req_rec,
                            NO_DO: no_do_,
                            NO_BL: no_bl_,
                            SP2: sp2_,
                            KD_CONSIGNEE: kd_consignee_,
                            ASAL_CONT: asal_cont,
                            tgl_bongkar: tglBongkar,
                            REMARK: remark,
                            // Start update by Clara ILCS - 27 November 2023
                            // Updated: fallback derive size dari ISO code jika containerSize kosong
                            CONTAINER_SIZE: (() => {
                                const size = container_praya?.containerSize;
                                if (size === "21") return "20";
                                if (size) return size;
                                // Fallback: derive dari ISO code (ISO 6346)
                                const isoCode = container_praya?.isoCode;
                                if (isoCode) {
                                    const isoSizeMap = { "2": "20", "4": "40", "L": "45", "M": "48" };
                                    return isoSizeMap[isoCode.charAt(0)] || null;
                                }
                                return null;
                            })(),
                            // End update by Clara ILCS - 27 November 2023
                            CONTAINER_TYPE:
                                container_praya?.containerType ?? "",
                            CONTAINER_STATUS:
                                container_praya?.containerStatus ?? "",
                            CONTAINER_HZ: container_praya?.hz ?? "",
                            CONTAINER_IMO: container_praya?.imo ?? "",
                            CONTAINER_ISO_CODE: container_praya?.isoCode ?? "",
                            CONTAINER_HEIGHT:
                                container_praya?.containerHeight ?? "",
                            CONTAINER_CARRIER: container_praya?.carrier ?? "",
                            CONTAINER_REEFER_TEMP:
                                container_praya?.reeferTemp ?? "",
                            CONTAINER_BOOKING_SL:
                                container_praya?.bookingSl ?? "",
                            CONTAINER_OVER_WIDTH:
                                container_praya?.overWidth ?? "",
                            CONTAINER_OVER_LENGTH:
                                container_praya?.overLength ?? "",
                            CONTAINER_OVER_HEIGHT:
                                container_praya?.overHeight ?? "",
                            CONTAINER_OVER_FRONT:
                                container_praya?.overFront ?? "",
                            CONTAINER_OVER_REAR:
                                container_praya?.overRear ?? "",
                            CONTAINER_OVER_LEFT:
                                container_praya?.overLeft ?? "",
                            CONTAINER_OVER_RIGHT:
                                container_praya?.overRight ?? "",
                            CONTAINER_UN_NUMBER:
                                container_praya?.unNumber ?? "",
                            CONTAINER_POD: container_praya?.pod ?? "",
                            CONTAINER_POL: container_praya?.pol ?? "",
                            CONTAINER_VESSEL_CONFIRM:
                                container_praya?.vesselConfirm ?? "",
                            CONTAINER_COMODITY_TYPE_CODE:
                                container_praya?.commodity ?? "",
                        },
                        processData: true,
                        error: function (err) {
                            Swal.close();
                            get_error(err.responseJSON);
                        },
                        beforeSend: function () {
                            Swal.fire({
                                html: "<h5>Please Wait...</h5>",
                                showConfirmButton: false,
                                allowOutsideClick: false,
                            });

                            Swal.showLoading();
                        },
                        success: function (data) {
                            Swal.close();
                            input_success(data);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        },
                    });
                }
            },
        });
    }
}
/** End Of Get Data & Auto Complete Section */
/** ======================================= */

/** Post Data (Save / Edit / Delete) Section */
/** ======================================== */
async function saveData(formId) {
    // Cek Saldo Consignee
    const kd_consignee = btoa(
        $(formId).find('input[name="ID_CONSIGNEE"]').val()
    );
    if (kd_consignee == "") {
        $.toast({
            heading: "Gagal Input Data",
            text: "Harap pilih Penerima / Consignee dengan benar",
            position: "top-right",
            loaderBg: "#ff6849",
            icon: "error",
            hideAfter: 3500,
        });

        return false;
    }

    await ajaxGetJson(
        `/request/stripping/stripping-plan/cek-saldo-emkl/${kd_consignee}`,
        "afterCekSaldo",
        "get_error"
    );
}

function afterCekSaldo(data) {
    if (!data.available) {
        Swal.fire({
            title: "Tidak bisa input perencanaan stripping",
            text: `${data.dataSaldo.consignee} masih memiliki ${data.dataSaldo.saldo} container yang belum terealisasi`,
            type: "info",
            confirmButtonText: "Cetak Saldo",
            showConfirmButton: true,
            showCancelButton: false,
        }).then((result) => {
            if (result.value) {
                window.open(
                    `${$('meta[name="baseurl"]').attr(
                        "content"
                    )}request/stripping/stripping-plan/cetak-saldo/${
                        data.dataSaldo.kd_consignee
                    }`,
                    "_blank"
                );
            } else {
                return false;
            }
        });
    } else {
        postData();
    }
}

async function postData() {
    const form = $("#form-add").serialize();
    let formArray = $("#form-add").find("input[required]");
    let canInput = true;
    var text = "";
    for (let i = 0; i < formArray.length; i++) {
        const formVal = $(formArray[i]).val();
        const formName = $(formArray[i]).attr("name");
        if (formVal == "") {
            if (formName == "ID_CONSIGNEE") {
                text = "Penerima / Consignee";
            }

            canInput = false;
        }
    }

    if (canInput == false) {
        $.toast({
            heading:
                "Gagal Input Data, masih ada data yang kosong atau data tidak dipilih dengan benar",
            text: text,
            position: "top-right",
            loaderBg: "#ff6849",
            icon: "error",
            hideAfter: 3500,
        });
    } else {
        await ajaxPostJson(
            "/request/stripping/stripping-plan/post-praya",
            form,
            "input_success"
        );
        $("#form-add").removeClass("was-validated");
    }
}

async function saveEdit(formId) {
    const form = $(formId).serialize();
    await ajaxPostJson(
        "/request/stripping/stripping-plan/save-edit",
        form,
        "input_success"
    );
    $(formId).removeClass("was-validated");
}

async function saveCont(formId) {
    const form = $(formId).serialize();
    await ajaxPostJson(
        "/request/stripping/stripping-plan/save-cont",
        form,
        "input_success"
    );
}

function saveReq(total) {
    Swal.fire({
        title: "Approve Request",
        text: "Apakah Anda yakin ingin melakukan approve request ini?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Approve",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.value) {
            for (let i = 1; i <= total; i++) {
                var formData = new FormData();
                var remark = $(`input[name='remarks_${i}']`).val();
                var no_cont = $(`input[name='no_cont_${i}']`).val();
                var no_req = $("#no_req").val();

                formData.append("_token", $('input[name="_token"]').val());
                formData.append("no_req", no_req);
                formData.append("remark", remark);
                formData.append("no_cont", no_cont);

                ajaxPostFile(
                    "/request/stripping/stripping-plan/save-req",
                    formData,
                    "input_success"
                );
            }
        } else {
            return false;
        }
    });
}

function delCont(noCont, noReq, noReq2) {
    Swal.fire({
        title: `Apakah anda yakin ingin menghapus data Container (${noCont})?`,
        text: `Data yang sudah dihapus tidak dapat dikembalikan`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Batal",
        confirmButtonText: "Ya, Hapus Data",
    }).then(async (result) => {
        if (result.value == true) {
            await ajaxGetJson(
                `/request/stripping/stripping-plan/delete-cont/${noCont}/${noReq}/${noReq2}`,
                "input_success",
                "get_error"
            );
        } else {
            return false;
        }
    });
}
/** End Of Post Data (Save / Edit / Delete) Section */
/** =============================================== */

/** Other Function Section */
/** ====================== */
function resetSearch() {
    $("#search-data").find("input.form-control").val("").trigger("blur");
    $("#search-data").find("input.form-control").removeClass("was-validated");
    $('input[name="search"]').val("false");
    table.ajax.reload();
}

function addContainer() {
    if ($("#form-card-container").css("display") == "none") {
        $("#form-card-container").slideDown();
    } else {
        $("#form-card-container").slideUp();
    }
}

function cancelAddCont() {
    if ($("#form-card-container").css("display") != "none") {
        $("#form-card-container").slideUp();
    }
}
/** End Of Other Function Section */
/** ============================= */

/** Pra Save Notif Section */
/** ====================== */
function input_success(res) {
    if (res.status != 200) {
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
                    html: "<h5>Berhasil input Perencanaan Stripping,<br> Mengembalikan Anda ke halaman sebelumnya...</h5>",
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
    $.toast({
        heading: "Gagal memproses data!",
        text: err.message,
        position: "top-right",
        icon: "error",
        hideAfter: 5000,
    });
}

function get_error(err) {
    console.log(err);
    $.toast({
        heading: "Gagal mengambil data!",
        text: err.message,
        position: "top-right",
        icon: "error",
        hideAfter: 5000,
    });
}
/** End Of Pra Save Notif Section */
/** ============================= */
