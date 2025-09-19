@extends('layouts.main')
@section('title', 'Report Trial Balance')
@section('content')
    <h1>Trial Balance Report</h1>
    <div class="panel panel-default">
        <div class="panel-body form-inline">
            <div class="form-group">
                <label for="start-date">Start Date:</label>
                <input type="date" id="start-date" class="form-control">
            </div>
            <div class="form-group">
                <label for="end-date">End Date:</label>
                <input type="date" id="end-date" class="form-control">
            </div>
            <button id="generate-report-btn" class="btn btn-primary">Generate Report</button>
        </div>
    </div>

    <table id="trial-balance-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th class="text-right">Opening Balance</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Closing Balance</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="info">
                <th colspan="2" class="text-left">TOTAL</th>
                <th class="text-right" id="total-opening">0.00</th>
                <th class="text-right" id="total-debit">0.00</th>
                <th class="text-right" id="total-credit">0.00</th>
                <th class="text-right" id="total-closing">0.00</th>
            </tr>
        </tfoot>
    </table>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            function formatNumber(num) {
                return parseFloat(num).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            var totalOpening = 0,
                totalDebit = 0,
                totalCredit = 0,
                totalClosing = 0;

            var table = $('#trial-balance-table').DataTable({
                paging: false,
                searching: false,
                info: false,
                columns: [{
                        data: 'code'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'opening_balance',
                        className: 'text-right',
                        render: formatNumber
                    },
                    {
                        data: 'debit',
                        className: 'text-right',
                        render: formatNumber
                    },
                    {
                        data: 'credit',
                        className: 'text-right',
                        render: formatNumber
                    },
                    {
                        data: 'closing_balance',
                        className: 'text-right',
                        render: formatNumber
                    },
                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                    className: 'btn btn-success',
                    title: 'Trial Balance Report',
                    footer: true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var lastRow = $('row', sheet).last();
                        var totalRowNum = parseInt(lastRow.attr('r'));
                        $('c[r="B' + totalRowNum + '"]', sheet).each(function() {
                            $(this).find('is t').text('');
                        });
                    }
                }]
            });

            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            $('#start-date').val(firstDay.toISOString().slice(0, 10));
            $('#end-date').val(lastDay.toISOString().slice(0, 10));

            $('#generate-report-btn').on('click', function() {
                var startDate = $('#start-date').val();
                var endDate = $('#end-date').val();
                var exportBtn = $('#export-excel-btn');

                if (!startDate || !endDate) {
                    alert('Please select both a start and end date.');
                    return;
                }

                // Show loading state
                $(this).html('Generating...').prop('disabled', true);
                exportBtn.addClass('disabled');

                $.get('/api/pages/trial-balance', {
                        start_date: startDate,
                        end_date: endDate
                    })
                    .done(function(response) {
                        // Populate table
                        table.clear().rows.add(response.data).draw();

                        // Calculate and display totals
                        totalOpening = 0;
                        totalDebit = 0;
                        totalCredit = 0;
                        totalClosing = 0;
                        response.data.forEach(function(row) {
                            totalOpening += parseFloat(row.opening_balance) || 0;
                            totalDebit += parseFloat(row.debit) || 0;
                            totalCredit += parseFloat(row.credit) || 0;
                            totalClosing += parseFloat(row.closing_balance) || 0;
                        });
                        $('#total-opening').text(formatNumber(totalOpening));
                        $('#total-debit').text(formatNumber(totalDebit));
                        $('#total-credit').text(formatNumber(totalCredit));
                        $('#total-closing').text(formatNumber(totalClosing));
                    })
                    .fail(function() {
                        alert('Failed to generate report. Please check the console for errors.');
                    })
                    .always(function() {
                        // Restore button state
                        $('#generate-report-btn').html('Generate Report').prop('disabled', false);
                    });
            });

            $('#generate-report-btn').trigger('click');
        });
    </script>
@endsection
