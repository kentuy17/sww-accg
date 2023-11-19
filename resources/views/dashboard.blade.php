@extends('adminlte::page')

@section('title', 'Bets - Audit')

{{-- @section('content_header')
  <h2>Bets</h2>
@stop --}}

@section('content')
  <div class="col-lg-6 m-auto" style="margin-top: 10px !important;">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">EVENT DATE</h3>
      </div>
      <form class="form-horizontal">
        <div class="card-body">
          <div class="form-group row my-2">
            <label for="inputPassword3" class="col-sm-2 col-form-label">DATE</label>
            <div class="col-sm-10">
              <input type="date" class="form-control" id="sched-date">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button id="filter-date" class="btn btn-primary float-right mb-2">FILTER</button>
        </div>
      </form>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="row col-md-12 justify-content-center">
    <div class="card col-md-12">
      <div class="card-body">
        <table class="table table-striped w-100" id="bet-summary-table">
          <thead>
            <tr>
              <th>EVENT NAME</th>
              <th>FIGHT#</th>
              <th>RESULT</th>
              <th>ACTUAL MERON</th>
              <th>ACTUAL WALA</th>
              <th>DATE</th>
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
      const getResult = (r) => {
        switch (r) {
          case 'M':
            return 'MERON'
          case 'W':
            return 'WALA'
          case 'D':
            return 'DRAW'
          case 'C':
            return 'CANCELLED'
          default:
            return 'FIGHTING'
        }
      }

      const fetchBetSummary = async (schedDate) => {
        const betSummaryTable = $('#bet-summary-table');
        await betSummaryTable.DataTable().clear().destroy();

        betSummaryTable.DataTable({
          "bPaginate": true,
          "bLengthChange": true,
          "bFilter": true,
          "bInfo": false,
          "bAutoWidth": false,
          "pagingType": 'numbers',
          "processing": true,
          "serverSide": true,
          "pageLength": 15,
          "lengthMenu": [
            [15, 25, 50, -1],
            [15, 25, 50, 'ALL'],
          ],
          "ajax": {
            "type": "GET",
            "url": "/summary-bet/filter-date",
            "data": {
              "event_date": schedDate,
            }
          },
          "language": {
            "search": "",
            "lengthMenu": "_MENU_",
          },
          "dom": "<'row'<'col-md-6'B><'col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>",
          //   "dom": 'Bfrtip',
          "buttons": [
            "csv"
          ],
          "scrollX": true,
          "order": [
            [0, 'DESC']
          ],
          "columnDefs": [{
              "targets": [1, 2],
              className: 'dt-body-center',
            },
            {
              "targets": [3, 4],
              className: 'dt-body-right',
            },
          ],
          "columns": [{
              "data": "event.name"
            }, {
              "data": "fight_no"
            }, {
              "data": null,
              render: (data) => getResult(data.game_winner)
            }, {
              "data": null,
              render: (data) => {
                let totalMeron = data.bet_legit_meron_sum_amount ?? 0
                return parseFloat(totalMeron).toFixed(2)
              }
            }, {
              "data": null,
              render: (data) => {
                let totalWala = data.bet_legit_wala_sum_amount ?? 0
                return parseFloat(totalWala).toFixed(2)
              }
            },
            {
              "data": "created_at",
            }
          ],
          "createdRow": function(row, data, dataIndex) {
            if (data.game_winner == 'M') {
              $(row).find('td').eq(2).attr('style', 'color: red !important');
            }

            if (data.game_winner == 'W') {
              $(row).find('td').eq(2).attr('style', 'color: blue !important');
            }

            if (data.bet_legit_meron_sum_amount) {
              $(row).find('td').eq(3).attr('style', 'color: green !important');
            }

            if (data.bet_legit_wala_sum_amount) {
              $(row).find('td').eq(4).attr('style', 'color: green !important');
            }
          }
        });
      }

      $('#time-start').val('08:00');
      $('#sched-date').val(moment().format('YYYY-MM-DD'));

      var schedDate = $('#sched-date').val();
      fetchBetSummary(schedDate)

      $('#filter-date').on('click', function(e) {
        e.preventDefault();
        let schedDate = $('#sched-date').val()
        fetchBetSummary(schedDate)
      })
    });
  </script>
@stop
