@extends('adminlte::page')

@section('title', 'Expenses - Audit')

@section('css')

@stop

@section('content_header')
  <h2>Expenses</h2>
  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if (session()->has($msg))
      <div class="fade-message alert alert-{{ $msg }}">
        <strong>{{ session()->get($msg) }}</strong>
      </div>
    @endif
  @endforeach
@stop

@section('meta_tags')
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
@stop

@section('content')
  <style>
    .row {
      margin-right: 0 !important;
      margin-left: 0 !important;
      --bs-gutter-x: 0 !important;
      --bs-gutter-y: 0 !important;
    }

    .card-body {
      padding: 0.5rem !important;
    }

    .row>*,
    .container-fluid,
    .content {
      padding-right: 0 !important;
      padding-left: 0 !important;
    }

    #withdraw-trans-table_filter {
      float: right !important;
    }

    #withdraw-trans-table_wrapper>.row>.col-md-6 {
      width: 50% !important;
    }

    .dt-button {
      margin-left: 10px;
    }

    table.dataTable th.dt-nowrap,
    table.dataTable td.dt-nowrap {
      white-space: nowrap !important;
    }
  </style>
  {{-- TABLE --}}
  <div class="row col-md-12 justify-content-center px-1">
    <div class="card" style="width: 100% !important;">
      <div class="card-body">
        <table class="table dt-responsive table-striped nowrap w-100" id="withdraw-trans-table">
          <thead>
            <tr>
              {{-- <th>#</th> --}}
              <th>Name</th>
              <th>Amount</th>
              <th>Note</th>
              <th>Attachment</th>
              <th>Type</th>
              <th>Posting Date</th>
              {{-- <th>Action</th> --}}
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div class="modal fade" id="add-expenses-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Record</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="/expenses/add" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="form-group row">
                <label for="name" class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="name" id="name">
                </div>
              </div>
              <div class="form-group row">
                <label for="amount" class="col-sm-3 col-form-label">Amount</label>
                <div class="col-sm-9">
                  <input type="number" class="form-control" name="amount" min="0" id="amount">
                </div>
              </div>
              <div class="form-group row">
                <label for="amount" class="col-sm-3 col-form-label">Date</label>
                <div class="input-group date col-sm-9" id="datetimepicker1" data-target-input="nearest">
                  <input type="text" id="posting_date" name="posting_date" data-date-format="yyyy/mm/dd" class="form-control datetimepicker-input" data-target="#datetimepicker1" />
                  <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa-solid fa-calendar"></i></div>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="account_type" class="col-sm-3 col-form-label">Type</label>
                <div class="col-sm-9">
                  <select name="account_type" id="account_type" class="form-control">
                    <option value="debit">Debit</option>
                    <option value="credit">Credit</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Attachment</label>
                <div class="input-group col-sm-9">
                  <div class="custom-file">
                    <input class="custom-file-input" style="cursor: pointer;" name="attachment" type="file" id="attachment" onchange="updateValue(this)">
                    <label class="custom-file-label" id="attachment-label" for="attachment">Choose file</label>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="note" class="col-sm-3 col-form-label">Note</label>
                <div class="col-sm-9">
                  <textarea name="note" class="form-control" style="font-style: italic;" rows="3" placeholder="Enter ..."></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" id="add-record" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@stop

@section('css')
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/svg-with-js.min.css">
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/regular.min.css" /> --}}
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css" /> --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />

@stop

@section('js')
  {{-- Generic files --}}
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" defer></script>

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
    $(function() {
      var withdrawTable = $("#withdraw-trans-table");
      var wPending = 0;
      var unverified = 0;

      const SIDE = {
        M: "MERON",
        W: "WALA",
        D: "DRAW",
      };

      const WINNER = {
        P: "PENDING",
        W: "WIN",
        L: "LOSE",
        D: "DRAW",
        C: "CANCELLED",
      };

      withdrawTable.DataTable({
        ajax: "/expenses/data",
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bInfo: false,
        bAutoWidth: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        pageLength: 15,
        lengthMenu: [
          [15, 25, 50, -1],
          [15, 25, 50, 'ALL'],
        ],
        order: [
          [5, "DESC"]
        ],
        pagingType: "numbers",
        language: {
          search: "",
          lengthMenu: "_MENU_",
        },
        "dom": "<'row'<'col-md-6'B><'col-md-6'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>",
        buttons: [{
          text: 'Add',
          action: function(e, dt, node, config) {
            $('#add-expenses-modal').modal('show')
          }
        }],
        columnDefs: [{
            targets: [-1],
            className: "dt-nowrap",
          },
          {
            targets: [1],
            className: "dt-body-right",
          },
          {
            targets: [1, 3, 4, 5],
            className: "dt-body-center",
          },
          {
            targets: [1, 2, 3, 4, 5],
            className: "dt-head-center",
          },
        ],
        columns: [{
            // className: "dt-control dt-body-left",
            // orderable: false,
            // data: null,
            // defaultContent: "",
            data: "name",
          },
          {
            data: "amount"
          },
          {
            data: null,
            render: (data) => {
              return data.note ? data.note : 'N/A';
            }
          },
          {
            data: null,
            render: (data) => {
              if (data.attachment != '') {
                return `<a href="/storage/${data.attachment}" download>${data.attachment}</a>`;
                // window.open(`${data.attachment}`);

              }
              return "N/A";
            }
          },

          {
            data: null,
            render: (data) => {
              return data.account_type.toUpperCase()
            }
          },
          {
            data: "post_date"
          },
          // {
          //   data: null,
          //   render: (data) => {
          //     return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
        // <i class="fa-solid fa-circle-info"></i></a>`;
          //   },
          // },
        ],
        drawCallback: function(settings) {
          if (settings.json.auditor == 'tas@audit.com') {
            $('.dt-button').prop('disabled', true).addClass('disabled');
          }
        },
      });

      $('#datetimepicker1').datetimepicker({
        format: 'L'
      });

      function format(d) {
        // `d` is the original data object for the row
        let userId = d.user_id;
        let userName = d.user.username;

        if (d.user_id == 666) {
          userId = DUMMY_ID;
          userName = d.user.name;
        }
        var operation = `<button onclick="operation(${d.id})" class="btn btn-link text-primary btn-icon operation" style="padding-left:0;">
          <i class="fa-solid fa-circle-info"></i></button>`;
        var btnCopy = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-phone" id="copy-phone" data-phone-number="${d.mobile_number}"
          onclick="copyPhone(this);"><i class="fa-solid fa-copy"></i></button>`;
        let betHistory = `<button onclick="betHistory(${d.user_id},'${userName}')" class="btn btn-link btn-suucess btn-icon pl-0 bet-history-show">
          <i class="fa-solid fa-money-bill text-success"></i></button>`;
        var expandContent = `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
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
          <tr>
            <td>NOTE:</td>
            <td>${d.note ? d.note : "N/A"}</td>
          </tr>
          <tr>
            <td>ACTION:</td>
            <td>${operation}</td>
          </tr>
          <tr>
            <td>BETS:</td>
            <td>${betHistory}</td>
          </tr>
        </table>`;
        return expandContent;
      }

      $("#withdraw-trans-table tbody").on("click", "td.dt-control", function() {
        var tr = $(this).closest("tr");
        var row = withdrawTable.DataTable().row(tr);

        if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass("shown");
        } else {
          row.child(format(row.data())).show();
          tr.addClass("shown");
          $('[data-bs-toggle="tooltip"]').tooltip({
            placement: "top"
          });
        }
      });

      withdrawTable.on("click", "tbody td .view", async function() {
        var tr = $(this).closest("tr");
        let id = $(this).data("id");
        viewWithdrawForm(tr, id);
      });

      async function operation(id) {
        var tr = $("tbody").find(`tr[data-id='${id}']`);
        viewWithdrawForm(tr[0], id);
      }

      async function viewWithdrawForm(tr, id) {
        clearFields();
        var row = withdrawTable.DataTable().row(tr);
        $("#withdraw-modal").modal("show");
        $(".modal-title").text(row.data().action.toUpperCase());
        $("input#withdraw-id").val(id);
        $("#withdraw-action").val("approve");
        $("#withdraw-note").parent().hide();
        $("#withdraw-ref-code").prop("disabled", false);

        if (row.data().status != "pending") {
          $('input[type="submit"]').prop("disabled", true).addClass("disabled");
        } else {
          $('input[type="submit"]').prop("disabled", false).removeClass("disabled");
        }
      }

      $("#withdraw-form").on("click", 'input[type="submit"]', function(e) {
        e.preventDefault();
        axios
          .post("/transaction/withdraw", {
            id: $("#withdraw-id").val(),
            ref_code: $("#withdraw-ref-code").val(),
            action: $("#withdraw-action").val(),
            note: $("#withdraw-note").val(),
          })
          .then((res) => {
            Swal.fire({
              icon: "success",
              confirmButtonColor: "green",
              title: res.data.msg,
              timer: 1500,
            }).then(() => {
              $("#withdraw-modal").modal("hide");
              $("#operator-pts").html(res.data.points);
              clearFields();
            });

            withdrawTable.DataTable().ajax.reload();
            (wPending = 0), (unverified = 0);
          })
          .catch((err) => {
            console.log(err);
          });
      });

      $("#withdraw-action").on("change", function(e) {
        e.preventDefault();
        let action = $(this).val();
        $("#withdraw-note").parent().hide();

        if (action == "update") {
          $("#withdraw-ref-code").prop("disabled", false);
          $('input[type="submit"]').prop("disabled", false).removeClass("disabled");
        } else if (action == "reject") {
          $("#withdraw-ref-code").prop("disabled", true);
          $('input[type="submit"]').prop("disabled", false).removeClass("disabled");
          $("#withdraw-note").parent().show();
        } else {
          $("#withdraw-ref-code").prop("disabled", true);
          $('input[type="submit"]').prop("disabled", true).addClass("disabled");
        }
      });

      $('[data-dismiss="modal"]').on("click", function() {
        $("#withdraw-modal").modal("hide");
        $("#bethistory-modal").modal("hide");
      });

      // $('#badge-withdraw-unverified').tooltip().show()

      $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function(e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
      });

      // ----------------------------------------------------------------

      async function betHistory(id, username = "") {
        const betHistoryTable = $("#bethistory-table");
        await betHistoryTable.DataTable().clear().destroy();
        $("#bethistory-modal").modal("show");
        $("#bethistory-head").text(username);
        betHistoryTable.DataTable({
          bPaginate: true,
          bLengthChange: true,
          bFilter: true,
          bInfo: false,
          bAutoWidth: true,
          scrollX: true,
          ajax: "/transaction/user-bet-history/" + id,
          pagingType: "numbers",
          processing: true,
          serverSide: true,
          order: [
            [6, "desc"]
          ],
          language: {
            search: "",
            lengthMenu: "_MENU_",
          },
          dom: "<'row'<'col-4'l><'col-8'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
          columnDefs: [{
            targets: [1, 2, 3, 4, 5, 6],
            className: "dt-body-center",
          }, ],
          columns: [{
              data: "fight_no",
            },
            {
              data: null,
              render: (data, type, row, meta) => {
                return SIDE[row.side];
              },
            },
            {
              data: null,
              render: function(data, type, row, meta) {
                return row.betamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
              },
            },
            {
              data: null,
              render: (data, type, row, meta) => {
                return `${row.percent.toFixed(2)}%`;
              },
            },
            {
              data: null,
              render: (data, type, row, meta) => {
                return row.status != "" ? WINNER[row.status] : "PENDING";
              },
            },
            {
              data: null,
              render: function(data, type, row, meta) {
                return row.winamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
              },
            },
            {
              data: null,
              render: function(data, type, row, meta) {
                return row.current_points
                  .toFixed(2)
                  .replace(/\d(?=(\d{3})+\.)/g, "$&,");
              },
            },
            {
              data: "created_at",
            },
          ],
          createdRow: function(row, data, dataIndex) {
            if (data.status == `W`) {
              $(row).find("td").eq(4).attr("style", "color: green !important");
              $(row).find("td").eq(5).attr("style", "color: yellow !important");
            }

            if (data.status == `L`) {
              $(row).find("td").eq(4).attr("style", "color: red !important");
            }

            if (data.side == "M") {
              $(row).find("td").eq(1).attr("style", "color: red !important");
            }

            if (data.side == "W") {
              $(row).find("td").eq(1).attr("style", "color: blue !important");
            }
          },
        });
      }

      function formatBetHistory(d) {
        let win = "",
          status = "",
          side = "";
        if (d.status == "W") {
          win = 'style="color:yellow"';
          status = 'style="color:green"';
        }

        if (d.status == "L") {
          status = 'style="color:red"';
        }

        if (d.side == "M") {
          side = 'style="color:red"';
        }

        if (d.side == "W") {
          side = 'style="color:blue"';
        }
      }

      const form = document.querySelector("form");
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!checkRequired()) {
          return
        }

        const formData = new FormData(form);
        axios.post('/expenses/add', formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }).then((res) => {
          console.log(res);
        }).catch((err) => {
          console.log(err);
        })
      })

      var date = new Date();
      const dateFormatted = new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit"
      }).format(date);
      $('#posting_date').val(dateFormatted)

      setTimeout(function() {
        $('.fade-message').slideUp();
      }, 5000);
    })

    function updateValue(e) {
      let file = e.files[0]
      if (file) {
        $('#attachment-label').text(file.name);
      }
    }

    function checkRequired() {
      if ($('#name').val() == '') {
        $('#name').addClass('is-invalid')
        return
      }

      if ($('#amount').val() == '' || $('#amount').val() == 0) {
        $('#amount').addClass('is-invalid')
        return
      }
      return true;
    }

    $('#name,#amount').on('keyup', function() {
      $(this).removeClass('is-invalid')
    })
  </script>
@stop
