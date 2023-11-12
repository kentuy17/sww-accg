@extends('adminlte::page')

@section('title', 'Deposits - Audit')

@section('content_header')
  <h2>Deposits</h2>
@stop

@section('content')
  {{-- TABLE --}}
  <div class="row col-md-12 justify-content-center">
    <div class="card" style="width: 100% !important;">
      <div class="card-body">
        <table class="table dt-responsive table-striped nowrap w-100" id="deposit-trans-table">
          <thead>
            <tr>
              <th>Player</th>
              <th>Amount</th>
              <th>Mobile#</th>
              <th>Operator</th>
              <th>Ref Code</th>
              <th>Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
@stop

@section('css')
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/solid.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@stop

@section('js')
  {{-- Generic files --}}
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js" type="text/javascript" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/fontawesome.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

  <script>
    $(document).ready(function() {
      const TYPE = {
        pending: "PENDING",
        completed: "COMPLETED",
        failed: "FAILED",
      };

      var transactionsTable = $("#deposit-trans-table");
      var pendingCount = 0;

      transactionsTable.DataTable({
        ajax: "/deposit/data",
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bInfo: false,
        bAutoWidth: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        pagingType: "numbers",
        pageLength: 15,
        lengthMenu: [
          [15, 25, 50, -1],
          [15, 25, 50, 'ALL'],
        ],
        language: {
          search: "",
          lengthMenu: "_MENU_",
        },
        order: [
          [5, "DESC"]
        ],
        "dom": "<'row'<'col-md-6'B><'col-md-6'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>",
        preInit: function(e, settings) {
          pendingCount = 0;
        },
        columnDefs: [{
            targets: [1],
            className: "dt-body-right",
          },
          {
            targets: [0, 2, 3, 4],
            className: "dt-body-center",
          },
          {
            targets: [0, 2, 3, 4, 5],
            className: "dt-head-center",
          },
        ],
        columns: [
          // {
          //   className: 'dt-control dt-body-left',
          //   orderable: false,
          //   data: null,
          //   defaultContent: '',
          //   data: "user_id",
          // },
          {
            data: "user.name",
          },
          // {
          //   "data": "outlet"
          // },
          {
            data: null,
            render: (data) => {
              return data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
            },
          },
          {
            data: "mobile_number",
          },

          {
            data: null,
            render: (data) => {
              return data.operator != null ? data.operator.username : "--";
            },
          },
          {
            data: "reference_code",
          },

          {
            data: "created_at",
          },
          {
            data: null,
            render: (data) => {
              return data.status.toUpperCase();
            },
          },
          {
            data: null,
            render: (data) => {
              // if (data.status == "completed" && data.completed_at != null) {
              //   return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
            //       <i class="fa-solid fa-circle-info"></i></a>
            //       <a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view-undo">
            //       <i class="fa-solid fa-undo"></i></a>`;
              // }
              return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
          <i class="fa-solid fa-circle-info"></i></a>`;
            },
          },
        ],
        createdRow: function(row, data, dataIndex) {
          $(row).attr("data-id", data.id).addClass("cursor-pointer expandable");
          if (data.status == `pending`) {
            $(row).css({
              "background-color": "var(--bs-red)"
            });
            pendingCount++;

            let timeDiff = moment(data.created_at, "MM-DD-YYYY hh:mm:ss").fromNow();
            $(row).find("td").eq(5).text(timeDiff);
          }

          if (data.status == `failed`) {
            $(row).addClass("failed");
          }
        },
        initComplete: function(settings, json) {
          if (pendingCount > 0) {
            $("#badge-deposit").show().text(pendingCount);
          } else {
            $("#badge-deposit").hide().text(pendingCount);
          }
        },
      });

      function formatDeposit(d) {
        let userId = d.user_id;
        let userName = d.user.username;

        if (d.user_id == 666) {
          userId = DUMMY_ID;
          userName = d.user.name;
        }

        let copyRefCode = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-ref-code py-0" id="copy-ref-code" data-ref-code="${d.reference_code}"
      onclick="copyRefCode(this);"><i class="fa-solid fa-copy"></i></button>`;
        let note = d.note ? `<tr><td>NOTE:</td><td>${d.note}</td></tr>` : "";
        let refCode =
          d.reference_code && d.reference_code != null ?
          `<tr><td>REFCODE:</td><td>${d.reference_code} ${copyRefCode}</td></tr>` :
          "";
        let btnCopy = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-phone" id="copy-phone" data-phone-number="${d.mobile_number}"
      onclick="copyPhone(this);"><i class="fa-solid fa-copy"></i></button>`;
        let action = `<tr><td>ACTION:</td><td><button onclick="viewDeposit(${d.id});" class="btn btn-link text-primary btn-icon" style="padding-left:0;">
      <i class="fa-solid fa-circle-info"></i></button></td></tr>`;
        return `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
      <tr>
        <td>ID:</td>
        <td>#${userId}</td>
      </tr>
      <tr>
        <td>PLAYER:</td>
        <td>${userName}</td>
      </tr>
      <tr>
        <td>MOBILE#:</td>
        <td>${d.mobile_number} ${btnCopy}</td>
      </tr>
      <tr>
        <td>AMOUNT:</td>
        <td>${d.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")}</td>
      </tr>
      ${note}
      ${refCode}

    </table>`;
      }

      $("#deposit-trans-table tbody").on("click", "tr.expandable", function() {
        var tr = $(this);
        var row = transactionsTable.DataTable().row(tr[0]);
        if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass("shown");
        } else {
          row.child(formatDeposit(row.data())).show();
          tr.addClass("shown");
        }
      });

      transactionsTable.on("click", "tbody td .view", async function() {
        var tr = $(this).closest("tr");
        var row = transactionsTable.DataTable().row(tr);
        $("#modal-center").modal("show");
        $(".modal-title").text(row.data().action.toUpperCase());
        $("input#trans-id").val($(this).data("id"));

        let storage = $("#trans-receipt").data("storage");
        if (row.data().filename) {
          $("#trans-receipt").attr("src", storage + "/" + row.data().filename);
        }

        if (row.data().status != "pending") {
          $('input[type="submit"]').prop("disabled", true).addClass("disabled");
        } else {
          $('input[type="submit"]').prop("disabled", false).removeClass("disabled");
        }
      });

      async function viewDeposit(id) {
        clearFields();
        var tr = $("tbody").find(`tr[data-id='${id}']`);
        var row = transactionsTable.DataTable().row(tr[0]);
        $("#modal-center").modal("show");
        $(".modal-title").text(row.data().action.toUpperCase());
        $("input#trans-id").val(row.data().id);

        let storage = $("#trans-receipt").data("storage");
        if (row.data().filename) {
          $("#trans-receipt").attr("src", storage + "/" + row.data().filename);
        }
      }

      $("#deposit-form").on("click", 'input[type="submit"]', function(e) {
        e.preventDefault();
        axios
          .post("/transaction/deposit", {
            id: $("#trans-id").val(),
            amount: $("#trans-pts").val().replace(",", ""),
            ref_code: $("#ref-code").val(),
            action: $("#trans-action").val(),
            note: $("#trans-note").val(),
          })
          .then((res) => {
            Swal.fire({
              icon: "success",
              confirmButtonColor: "green",
              title: res.data.msg,
              timer: 1500,
            }).then(() => {
              console.log(res);
              $("#modal-center").modal("hide");
              $("#operator-pts").html(res.data.points);
              clearFields();
            });

            transactionsTable.DataTable().ajax.reload();
            pendingCount = 0;
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              confirmButtonColor: "red",
              title: err.response.data.msg,
              timer: 1500,
            });
          });
      });

      $("#trans-action").on("change", function(e) {
        e.preventDefault();
        let action = $(this).val();
        if (action == "reject") {
          $("#trans-pts,#ref-code").prop("disabled", true);
          $("#trans-note").parent().show();
        } else {
          $("#trans-pts,#ref-code").prop("disabled", false);
          $("#trans-note").parent().hide();
        }
      });

      function clearFields() {
        $("#trans-pts").val("");
        $("#ref-code").val("");
        $("#trans-note").val("");
        $("#trans-action").val("approve");
        $("#trans-note").parent().hide();
        $("#withdraw-note").val("");
        $("#withdraw-ref-code").val("");
      }

      $('[data-dismiss="modal"]').on("click", function() {
        $("#modal-center").modal("hide");
      });

      //Revert Points
      transactionsTable.on("click", "tbody td .view-undo", async function() {
        clearFields();
        var tr = $(this).closest("tr");
        var row = transactionsTable.DataTable().row(tr);
        $("#modal-undo-points").modal("show");
        $("input#undo-id").val($(this).data("id"));

        let storage = $("#trans-receipt-und").data("storage");
        if (row.data().filename) {
          $("#trans-receipt-undo").attr("src", storage + "/" + row.data().filename);
        }
        if (row.data().amount) {
          $("#trans-pts-undo").val(row.data().amount);
        }
        if (row.data().reference_code) {
          $("#ref-code-undo").val(row.data().reference_code);
        }

        if (row.data().status != "completed") {
          $('input[type="submit"]').prop("disabled", true).addClass("disabled");
        } else {
          $('input[type="submit"]').prop("disabled", false).removeClass("disabled");
        }
      });

      $("#deposit-undo-form").on("click", 'input[type="submit"]', function(e) {
        e.preventDefault();
        axios
          .post("/transaction/deposit/revert", {
            id: $("#undo-id").val(),
            curr_amount: $("#trans-pts-undo").val(),
            amount: $("#updated-trans-pts").val(),
            ref_code: $("#ref-code-undo").val(),
            note: $("#trans-note-undo").val(),
          })
          .then((res) => {
            Swal.fire({
              icon: "success",
              confirmButtonColor: "green",
              title: res.data.msg,
              timer: 1500,
            }).then(() => {
              console.log(res);
              $("#modal-undo-points").modal("hide");
              $("#operator-pts").html(res.data.points);
              clearFields();
            });

            transactionsTable.DataTable().ajax.reload();
            pendingCount = 0;
          })
          .catch((err) => {
            console.log(err);
          });
      });

      function copyRefCode(e) {
        const num = $(e).data("ref-code");
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(num).select();
        document.execCommand("copy");
        $temp.remove();
        $(e).removeClass("text-primary").addClass("text-success");
        $(e).find("i").removeClass("fa-copy").addClass("fa-check");
        setTimeout(() => {
          $(e).tooltip("hide");
          $(e).removeClass("text-success").addClass("text-primary");
          $(e).find("i").removeClass("fa-check").addClass("fa-copy");
        }, 3000);
      }

      function copyPhone(e) {
        const num = $(e).data("phone-number");
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(num).select();
        document.execCommand("copy");
        $temp.remove();
        $(e).removeClass("text-primary").addClass("text-success");
        $(e).find("i").removeClass("fa-copy").addClass("fa-check");
        setTimeout(() => {
          $(e).tooltip("hide");
          $(e).removeClass("text-success").addClass("text-primary");
          $(e).find("i").removeClass("fa-check").addClass("fa-copy");
        }, 3000);
      }

      $('[data-dismiss="modal"]').on("click", function() {
        $("#modal-undo-points").modal("hide");
      });

      function showNotification(message) {
        const img = "img/sabong-aficionado-icon.png";
        console.log(img);
        const text = message;
        new Notification("Sabong Aficionado", {
          body: text,
          icon: img,
        });
      }

      $("#allow-notifications").on("click", () => {
        if (!("Notification" in window)) {
          // Check if the browser supports notifications
          alert("This browser does not support desktop notification");
        } else if (Notification.permission === "granted") {
          // Check whether notification permissions have already been granted;
          // if so, create a notification
          const notification = new Notification("Hi there!");
          // …
        } else if (Notification.permission !== "denied") {
          // We need to ask the user for permission
          Notification.requestPermission().then((permission) => {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
              const notification = new Notification("Hi there!");
              // …
            }
          });
        }
      });
    })
  </script>
@stop
