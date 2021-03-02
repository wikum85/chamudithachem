jQuery(document).ready(function ($) {

    var ajaxUrl = '/wp-admin/admin-ajax.php';

    $("#schedule-date").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $('#student-list').dataTable({
        bLengthChange: false,
        responsive: true
    });

    $('#schedule-list').dataTable({
        bLengthChange: false,
        responsive: true
    });

    $('#user-pay-list').dataTable({
        bLengthChange: false,
        responsive: true
    });

    $('#user-log-list').dataTable({
        bLengthChange: false,
        responsive: true
    });

    $('#my-pay-list').dataTable({
        bLengthChange: false,
        responsive: true
    });

    $('#payment-list').dataTable({
        orderCellsTop: true,
        ordering: false,
        dom: 'Bfrtip',
        responsive: true,
        scrollX: true,
        buttons: [{
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ],
        initComplete: function () {
            var api = this.api();
            $('.filterhead', api.table().header()).each(function (i) {
                var column = api.column(i);
                if (i == 7) {
                    $(this).empty();
                    return;
                }
                var select = $('<select><option value="">All</option></select>')
                    .appendTo($(this).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }
    });

    $('#not-paid-list').dataTable({
        initComplete: function () {
            var api = this.api();
            $('.filterhead', api.table().header()).each(function (i) {
                var column = api.column(i);
                if (i == 7) {
                    $(this).empty();
                    return;
                }
                var select = $('<select><option value="">All</option></select>')
                    .appendTo($(this).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }
    });

    $('#history-list').dataTable({
        initComplete: function () {
            var api = this.api();
            $('.filterhead', api.table().header()).each(function (i) {
                var column = api.column(i);
                if (i == 7) {
                    $(this).empty();
                    return;
                }
                var select = $('<select><option value="">All</option></select>')
                    .appendTo($(this).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }
    });


    $('#student-list').on('click', '.btn-ul-delete', function (e) {
        e.preventDefault();

        if (confirm('Are you sure want to delete ? Student Id: ' + $(this).data('student-id'))) {

            var data = new FormData();
            data.set("action", "student_delete_by_id");
            data.set("id", this.id);

            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                    var type = response.success ? "success" : "error";
                    var title = response.success ? "Success" : "Error";
                    notif({
                        msg: `<b>${title}:</b> ${response.data}`,
                        type: type
                    });
                },
                error: function (e) {
                    notif({
                        msg: `<b>Error:</b> Error in Delete Student`,
                        type: 'error'
                    });
                }
            });
        }
    });

    $('#student-list').on('click', '.btn-ul-status', function (e) {
        e.preventDefault();
        stdStatus(this);
    });

    $('.btn-ul-statuss').on('click', function (e) {
        e.preventDefault();
        stdStatus(this);
    });

    $('.btn-rw-status').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure want to Change Status ? Student Id: ' + $(this).data('student-id'))) {

            var data = new FormData();
            data.set("action", "student_review_status");
            data.set("id", this.id);
            data.set("status", $(this).data('student-status'));
            var massage = "";
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        if(response.data.status === "2"){
                            $(".btn-aprove").hide();
                            $(".btn-decline").show();
                        }else if(response.data.status === "1"){
                            $(".btn-aprove").show();
                            $(".btn-decline").hide();
                        }else{
                            $(".btn-aprove").hide();
                            $(".btn-decline").hide();
                        }
                        massage = "Student Updated Successfully.";
                    }else{
                        massage = response.data;
                    }
                    var type = response.success ? "success" : "error";
                    var title = response.success ? "Success" : "Error";
                    notif({
                        msg: `<b>${title}:</b> ${massage}`,
                        type: type
                    });
                },
                error: function (e) {
                    notif({
                        msg: `<b>Error:</b> Error in Update Student`,
                        type: 'error'
                    });
                }
            });
        }
    });

    function stdStatus(selector) {
        if (confirm('Are you sure want to Change Status ? Student Id: ' + $(selector).data('student-id'))) {

            var data = new FormData();
            data.set("action", "student_change_status");
            data.set("id", selector.id);
            data.set("status", $(selector).data('student-status'));

            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                    var type = response.success ? "success" : "error";
                    var title = response.success ? "Success" : "Error";
                    notif({
                        msg: `<b>${title}:</b> ${response.data}`,
                        type: type
                    });
                },
                error: function (e) {
                    notif({
                        msg: `<b>Error:</b> Error in Update Student`,
                        type: 'error'
                    });
                }
            });
        }
    }

    $('#user-pay-list').on('click', '.btn-pay-delete', function (e) {
        e.preventDefault();
        deletePay(this);
    });

    $('#payment-list').on('click', '.btn-pay-delete', function (e) {
        e.preventDefault();
        deletePay(this);
    });

    function deletePay(btn) {
        if (confirm('Are you sure want to delete this Payment?')) {

            var data = new FormData();
            data.set("action", "payment_delete_by_id");
            data.set("id", btn.id);

            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    var type = response.success ? "success" : "error";
                    var title = response.success ? "Success" : "Error";
                    notif({
                        msg: `<b>${title}:</b> ${response.data}`,
                        type: type
                    });
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function (e) {
                    notif({
                        msg: `<b>Error:</b> Error in Delete Payment`,
                        type: 'error'
                    });
                }
            });
        }
    }

    $('#edit-student-form').validate({
        rules: {
            'user_login': {
                required: true
            },
            'phone': {
                required: true,
                minlength: 12
            },
            'user_email': {
                required: true
            },
            'nic': {
                required: true
            }
        },
        messages: {
            'user_login': 'Student Name is Required',
            'user_email': 'Email is Required',
            'nic': 'NIC Number is Required',
            'phone': {
                'required': 'Phone Number is Required',
                'minlength': 'Not a valid Phone Number'
            }
        }
    });

    $('.save-student').on('click', function (e) {
        e.preventDefault();

        if (!$('#edit-student-form').valid()) {
            return;
        }

        if ($('#nic-pic-image').attr('src') == '') {
            notif({
                msg: `<b>Warning:</b> NIC Photo Required`,
                type: 'warning'
            });
            return;
        }

        var data = new FormData($('#edit-student-form')[0]);
        data.set("action", "update_student_data");

        $(".save-student").prop("disabled", true);
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function (response) {
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });
                $(".save-student").prop("disabled", false);
                if (response.success) {
                    window.location = "/sampath/admin-portal/";
                }
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Save Student`,
                    type: 'error'
                });
                $(".save-student").prop("disabled", false);
            }
        });
    });

    $('#register-student-form').validate({
        rules: {
            'phone': {
                required: true,
                minlength: 12
            },
            'address': {
                required: true
            },
            'nic': {
                required: true
            },
            'course_id': {
                required: true
            },
            'year': {
                required: true
            }
        },
        messages: {
            'phone': {
                'required': 'Phone Number is Required',
                'minlength': 'Not a valid Phone Number'
            },
            'address': 'Address is Required',
            'course_id': 'Class is Required',
            'nic': 'NIC is Required',
            'year': 'Year is Required'
        }
    });

    $('.register-student').on('click', function (e) {
        e.preventDefault();

        if (!$('#register-student-form').valid()) {
            return;
        }

        if ($('#nic-pic-image').attr('src') == '') {
            notif({
                msg: `<b>Warning:</b> NIC Photo Required`,
                type: 'warning'
            });
            return;
        }

        var year = $('#year option:selected').text().trim();
        var course = $('#course_id option:selected').text().trim();

        notif_confirm({
            'textaccept': 'Yes',
            'textcancel': 'No',
            'message': `<div><h2>Are the below details are correct ?</h2></div><div><h3>Year : ${year}</h3></div><div><h3>Class : ${course}</h3></div> `,
            'callback': registerStudent
        })
    });

    function registerStudent(feedBack) {

        if (!feedBack) {
            return;
        }

        var data = new FormData($('#register-student-form')[0]);
        data.set("action", "update_student_data");

        $(".register-student").prop("disabled", true);
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function (response) {
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });

                $(".register-student").prop("disabled", false);

                if (response.success) {
                    location.reload();
                }
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Register`,
                    type: 'error'
                });
                $(".register-student").prop("disabled", false);
            }
        });
    }

    $('.save-payment').on('click', function (e) {
        e.preventDefault();

        var data = new FormData($('#save-payment-form')[0]);
        data.set("action", "save_payment_data");

        $(".save-payment").prop("disabled", true);
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function (response) {
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });
                $(".save-payment").prop("disabled", false);
                if (response.success) {
                    window.location = "/sampath/student-payments/";
                }
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Save Payment`,
                    type: 'error'
                });
                $(".save-payment").prop("disabled", false);
            }
        });
    });

    $('#save-payment-form').validate({
        rules: {
            'receipt-upload': {
                required: true
            }
        },
        messages: {
            'receipt-upload': 'Please upload the Receipt'
        }
    });

    $('.save-my-payment').on('click', function (e) {
        e.preventDefault();

        if (!$('#save-payment-form').valid()) {
            return;
        }

        var data = new FormData($('#save-payment-form')[0]);
        data.set("action", "save_payment_data");

        $(".save-my-payment").prop("disabled", true);
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function (response) {
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });
                $(".save-my-payment").prop("disabled", false);
                if (response.success) {
                    window.location = "/sampath/student-portal/";
                }
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Save Payment`,
                    type: 'error'
                });
                $(".save-my-payment").prop("disabled", false);
            }
        });
    });

    $('#btn-receipt-upload').on('click', function (e) {
        e.preventDefault();

        var receipt_data = $('#receipt-upload').prop('files')[0];

        if (!receipt_data) {
            notif({
                msg: `<b>Warning:</b> Please Select Receipt`,
                type: 'warning'
            });
            return;
        }

        var data = new FormData();
        data.set("action", "receipt_upload");
        data.set("user-id", $('#user-id').val());
        data.set("receipt", receipt_data);

        $("#btn-receipt-upload").prop("disabled", true);

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $('#receipt-upload').hide();
                    $('#btn-receipt-upload').hide();
                    $('#btn-receipt-remove').show();
                    $('#receipt-image').attr("src", 'data:image/png;base64,' + response.data.image);
                    $('#receipt-id').val(response.data.receipt_id);
                } else {
                    notif({
                        msg: `<b>Error:</b> Error in Uploading Receipt.`,
                        type: 'error'
                    });
                }
                $("#btn-receipt-upload").prop("disabled", false);
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Uploading Receipt.`,
                    type: 'error'
                });
                $("#btn-receipt-upload").prop("disabled", false);
            }
        });
    });

    $('#btn-receipt-remove').on('click', function (e) {
        e.preventDefault();

        var data = new FormData();
        data.set("action", "remove_receipt");
        data.set("receipt-id", $('#receipt-id').val());

        $("#btn-receipt-remove").prop("disabled", true);

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $('#receipt-upload').show();
                    $('#btn-receipt-upload').show();
                    $('#btn-receipt-remove').hide();
                    $('#receipt-image').attr("src", "");
                    $('#receipt-id').val("");
                }
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });
                $("#btn-receipt-remove").prop("disabled", false);
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Removing Receipt`,
                    type: 'error'
                });
                $("#btn-receipt-remove").prop("disabled", false);
            }
        });
    });

    $('#schedule-list').on('click', '.btn-cal-delete', function (e) {
        e.preventDefault();

        if (confirm('Are you sure want to delete Schedule ?')) {

            var data = new FormData();
            data.set("action", "schedule_delete_by_id");
            data.set("id", this.id);

            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    var type = response.success ? "success" : "error";
                    var title = response.success ? "Success" : "Error";
                    notif({
                        msg: `<b>${title}:</b> ${response.data}`,
                        type: type
                    });
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function (errorThrown) {
                    notif({
                        msg: `<b>Error:</b> Error in Delete Schedule`,
                        type: 'error'
                    });
                    console.log(errorThrown);
                }
            });
        }
    });

    $('#edit-student-form').validate({
        rules: {
            'title': {
                required: true
            },
            'date': {
                required: true
            },
            'time': {
                required: true
            }
        },
        messages: {
            'title': 'Schedule Name is Required',
            'date': 'Schedule Date is Required',
            'time': 'Schedule Date is Required'
        }
    });

    $('.save-schedule').on('click', function (e) {
        e.preventDefault();

        if (!$('#edit-schedule-form').valid()) {
            return;
        }

        var data = new FormData($('#edit-schedule-form')[0]);
        data.set("action", "update_schedule_data");

        $(".save-student").prop("disabled", true);
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function (response) {
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });
                $(".save-schedule").prop("disabled", false);
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Save Schedule`,
                    type: 'error'
                });
                $(".save-schedule").prop("disabled", false);
            }
        });
    });

    $('#btn-pic-upload').on('click', function (e) {
        e.preventDefault();

        var pic_data = $('#nic-pic-upload').prop('files')[0];

        if (!pic_data) {
            notif({
                msg: `<b>Warning:</b> Please Select a Image`,
                type: 'warning'
            });
            return;
        }

        var data = new FormData();
        data.set("action", "nic_upload");
        data.set("user-id", $('#user-table-id').val());
        data.set("nic_pic", pic_data);

        $("#btn-pic-upload").prop("disabled", true);

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $('#nic-pic-upload').hide();
                    $('#btn-pic-upload').hide();
                    $('#btn-pic-remove').show();
                    $('#nic-pic-image').attr("src", 'data:image/png;base64,' + response.data.image);
                } else {
                    notif({
                        msg: `<b>Error:</b> Error in Uploading Image`,
                        type: 'error'
                    });
                }
                $("#btn-pic-upload").prop("disabled", false);
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Uploading Image`,
                    type: 'error'
                });
                $("#btn-pic-upload").prop("disabled", false);
            }
        });
    });

    $('#btn-pic-remove').on('click', function (e) {
        e.preventDefault();

        var data = new FormData();
        data.set("action", "nic_remove");
        data.set("user-id", $('#user-table-id').val());

        $("#btn-pic-remove").prop("disabled", true);

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $('#nic-pic-upload').show();
                    $('#btn-pic-upload').show();
                    $('#btn-pic-remove').hide();
                    $('#nic-pic-image').attr("src", "");
                }
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });
                $("#btn-pic-remove").prop("disabled", false);
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Removing Image`,
                    type: 'error'
                });
                $("#btn-pic-remove").prop("disabled", false);
            }
        });
    });

    $("#anc-start-date").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $("#anc-end-date").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $('#announcement-list').dataTable({
        bLengthChange: false,
        responsive: true
    });

    $('#edit-announcement-form').validate({
        rules: {
            'title': {
                required: true
            },
            'announcement': {
                required: true
            }
        },
        messages: {
            'title': 'Title is Required',
            'announcement': 'Announcement is Required'
        }
    });

    $('.save-announcement').on('click', function (e) {
        e.preventDefault();

        if (!$('#edit-announcement-form').valid()) {
            return;
        }

        var data = new FormData($('#edit-announcement-form')[0]);
        data.set("action", "update_announcement_data");

        $(".save-announcement").prop("disabled", true);
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function (response) {
                var type = response.success ? "success" : "error";
                var title = response.success ? "Success" : "Error";
                notif({
                    msg: `<b>${title}:</b> ${response.data}`,
                    type: type
                });
                $(".save-announcement").prop("disabled", false);
                if (response.success) {
                    window.location = "/sampath/announcements/";
                }
            },
            error: function (errorThrown) {
                notif({
                    msg: `<b>Error:</b> Error in Save Announcement`,
                    type: 'error'
                });
                $(".save-announcement").prop("disabled", false);
            }
        });
    });

    $('#announcement-list').on('click', '.btn-anc-delete', function (e) {
        e.preventDefault();

        if (confirm('Are you sure want to delete ?')) {

            var data = new FormData();
            data.set("action", "announcement_delete_by_id");
            data.set("id", this.id);

            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                    var type = response.success ? "success" : "error";
                    var title = response.success ? "Success" : "Error";
                    notif({
                        msg: `<b>${title}:</b> ${response.data}`,
                        type: type
                    });
                },
                error: function (e) {
                    notif({
                        msg: `<b>Error:</b> Error in Delete Announcement`,
                        type: 'error'
                    });
                }
            });
        }
    });

    $('#admin-year').change(function () {
        window.location = "?std-id=" + $('#user-id').val() + "&pay-id=0&pay-year=" + $(this).val();
    });

    $('#student-year').change(function () {
        window.location = "?&pay-id=0&pay-year=" + $(this).val();
    });

    $('input#phone').keydown(function (e) {
        var key = e.charCode || e.keyCode || 0;
        $text = $(this);
        if ($text.val().length >= 12) {
            if (key !== 8 && key !== 46) {
                e.preventDefault();
                return;
            }
        }
        if (key !== 8 && key !== 9) {
            if ($text.val().length === 3) {
                $text.val($text.val() + '-');
            }
            if ($text.val().length === 7) {
                $text.val($text.val() + '-');
            }
        }
        return (key == 8 || key == 9 || key == 46 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105));
    });
});