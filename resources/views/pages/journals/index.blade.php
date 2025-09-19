@extends('layouts.main')
@section('title', 'Journal')
@section('content')
    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <h1>Journals</h1>
                <button class="btn btn-success pull-right" id="create-journal-btn">Create New Journal</button>
            </div>
        </div>
        <hr>
        <table id="journals-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Ref No</th>
                    <th>Memo</th>
                    <th class="text-right">Debit Total</th>
                    <th class="text-right">Credit Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    {{-- Modal create/edit --}}
    <div class="modal fade" id="journal-modal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal-title"></h4>
                </div>
                <form id="journal-form" name="journal-form" class="form-horizontal">
                    <div class="modal-body">
                        <input type="hidden" name="journal_id" id="journal_id">

                        <div class="form-group">
                            <label for="posting_date" class="col-sm-2 control-label">Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="posting_date" name="posting_date" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="memo" class="col-sm-2 control-label">Memo</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="memo" name="memo" required>
                            </div>
                        </div>
                        <hr>

                        <h4>Journal Lines</h4>
                        <div id="journal-lines-container">
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-info btn-sm" id="add-line-btn">
                                    <span class="glyphicon glyphicon-plus"></span> Add Line
                                </button>
                                <strong class="pull-right">Totals: Debit: <span id="total-debit">0.00</span> | Credit: <span id="total-credit">0.00</span></strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="save-btn">Save Journal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="line-template" style="display: none;">
        <div class="form-group journal-line-row">
            <div class="col-sm-5">
                <select name="account_id" class="form-control account-select">
                    <option value="">Select Account...</option>
                </select>
            </div>
            <div class="col-sm-3">
                <input type="number" name="debit" class="form-control debit-input" placeholder="Debit" value="0">
            </div>
            <div class="col-sm-3">
                <input type="number" name="credit" class="form-control credit-input" placeholder="Credit" value="0">
            </div>
            <div class="col-sm-1">
                <button type="button" class="btn btn-danger btn-sm remove-line-btn">
                    <span class="glyphicon glyphicon-trash"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal show --}}
    <div class="modal fade" id="detail-modal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="detail-modal-title">Journal Details</h4>
                </div>
                <div class="modal-body" id="detail-modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var accounts = [];
            var table = $('#journals-table').DataTable({
                processing: true,
                ajax: {
                    url: "/api/journals",
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'posting_date'
                    },
                    {
                        data: 'ref_no'
                    },
                    {
                        data: 'memo'
                    },
                    {
                        data: 'journal_lines',
                        className: 'text-right',
                        render: function(data, type, row) {
                            var total = data.reduce((sum, line) => sum + parseFloat(line.debit), 0);
                            return total.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'journal_lines',
                        className: 'text-right',
                        render: function(data, type, row) {
                            var total = data.reduce((sum, line) => sum + parseFloat(line.credit), 0);
                            return total.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    // {
                    //     data: 'id',
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function(data) {
                    //         return `<button class="btn btn-info btn-xs detail-btn" data-id="${data}">Detail</button>
                //     <button class="btn btn-primary btn-xs edit-btn" data-id="${data}">Edit</button>
                //     <button class="btn btn-danger btn-xs delete-btn" data-id="${data}">Delete</button>`;
                    //     }
                    // }
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            if (row.is_period_locked) {
                                // If the period is locked, only show the Detail button
                                return `<button class="btn btn-info btn-xs detail-btn" data-id="${data}">Detail</button>
                        <span class="label label-default" style="margin-left: 5px;">Locked</span>`;
                            } else {
                                // If not locked, show all action buttons
                                return `<button class="btn btn-info btn-xs detail-btn" data-id="${data}">Detail</button>
                        <button class="btn btn-primary btn-xs edit-btn" data-id="${data}">Edit</button>
                        <button class="btn btn-danger btn-xs delete-btn" data-id="${data}">Delete</button>`;
                            }
                        }
                    }
                ]
            });

            function fetchAccounts() {
                $.get('/api/accounts', function(response) {
                    accounts = response.data;
                });
            }
            fetchAccounts();

            function addLine(lineData = null) {
                var newLine = $('#line-template').clone().removeAttr('id').show();
                var accountSelect = newLine.find('.account-select');
                accounts.forEach(function(account) {
                    accountSelect.append(new Option(account.code + ' - ' + account.name, account.id));
                });
                $('#journal-lines-container').append(newLine);

                if (lineData) {
                    newLine.find('.account-select').val(lineData.account_id);
                    newLine.find('.debit-input').val(parseFloat(lineData.debit).toFixed(2));
                    newLine.find('.credit-input').val(parseFloat(lineData.credit).toFixed(2));
                }
            }

            function calculateTotals() {
                var totalDebit = 0;
                var totalCredit = 0;
                $('.journal-line-row:visible').each(function() {
                    totalDebit += parseFloat($(this).find('.debit-input').val()) || 0;
                    totalCredit += parseFloat($(this).find('.credit-input').val()) || 0;
                });
                $('#total-debit').text(totalDebit.toFixed(2));
                $('#total-credit').text(totalCredit.toFixed(2));
            }

            $('#add-line-btn').on('click', function() {
                addLine();
            });

            $('#journal-lines-container').on('click', '.remove-line-btn', function() {
                $(this).closest('.journal-line-row').remove();
                calculateTotals();
            });

            $('#journal-lines-container').on('input', '.debit-input, .credit-input', function() {
                calculateTotals();
            });


            $('#create-journal-btn').on('click', function() {
                $('#journal-form').trigger("reset");
                $('#journal_id').val('');
                $('#modal-title').html("Create New Journal");
                $('#journal-lines-container').empty();
                addLine();
                addLine();
                calculateTotals();
                $('#journal-modal').modal('show');
            });

            $('body').on('click', '.edit-btn', function() {
                var journal_id = $(this).data('id');
                $.get('/api/journals/' + journal_id, function(data) {
                    $('#modal-title').html("Edit Journal");
                    $('#journal_id').val(data.id);
                    $('#posting_date').val(data.posting_date);
                    $('#memo').val(data.memo);

                    $('#journal-lines-container').empty();
                    data.journal_lines.forEach(function(line) {
                        addLine(line);
                    });

                    calculateTotals();
                    $('#journal-modal').modal('show');
                });
            });

            $('#journal-form').on('submit', function(e) {
                e.preventDefault();

                var journal_id = $('#journal_id').val();
                var url = journal_id ? '/api/journals/' + journal_id : '/api/journals';
                var method = journal_id ? 'PUT' : 'POST';

                var payload = {
                    posting_date: $('#posting_date').val(),
                    memo: $('#memo').val(),
                    lines: []
                };

                $('.journal-line-row:visible').each(function() {
                    payload.lines.push({
                        account_id: $(this).find('.account-select').val(),
                        debit: parseFloat($(this).find('.debit-input').val()) || 0,
                        credit: parseFloat($(this).find('.credit-input').val()) || 0
                    });
                });

                $.ajax({
                    url: url,
                    method: method,
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        $('#journal-modal').modal('hide');
                        table.ajax.reload();
                        alert('Journal saved successfully!');
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        var errorString = 'Please fix the following errors:\n';
                        if (errors.message) {
                            errorString += '- ' + errors.message + '\n';
                        }
                        if (errors.errors) {
                            $.each(errors.errors, function(key, value) {
                                errorString += '- ' + value[0] + '\n';
                            });
                        }
                        alert(errorString);
                    }
                });
            });

            $('body').on('click', '.detail-btn', function() {
                var journal_id = $(this).data('id');
                var modalBody = $('#detail-modal-body');

                modalBody.html('<p>Loading details...</p>');
                $('#detail-modal').modal('show');

                $.get('/api/journals/' + journal_id, function(data) {
                    var detailsHtml = `
            <p><strong>Date:</strong> ${new Date(data.posting_date).toLocaleDateString('id-ID')}</p>
            <p><strong>Ref No:</strong> ${data.ref_no}</p>
            <p><strong>Memo:</strong> ${data.memo}</p>
            <hr>
            <h4>Lines:</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Account Code</th>
                        <th>Account Name</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                    </tr>
                </thead>
                <tbody id="detail-lines-container">
                    </tbody>
            </table>
        `;

                    modalBody.html(detailsHtml);

                    var linesContainer = $('#detail-lines-container');
                    data.journal_lines.forEach(function(line) {
                        var debit = parseFloat(line.debit).toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        });
                        var credit = parseFloat(line.credit).toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        });

                        var rowHtml = `
                <tr>
                    <td>${line.account.code}</td>
                    <td>${line.account.name}</td>
                    <td class="text-right">${debit}</td>
                    <td class="text-right">${credit}</td>
                </tr>
            `;
                        linesContainer.append(rowHtml);
                    });

                }).fail(function() {
                    modalBody.html('<p class="text-danger">Could not load journal details. Please try again.</p>');
                });
            });

            $('body').on('click', '.delete-btn', function() {
                var journal_id = $(this).data("id");
                if (confirm("Are you sure you want to delete this journal?")) {
                    $.ajax({
                        type: "DELETE",
                        url: '/api/journals/' + journal_id,
                        success: function(data) {
                            table.ajax.reload();
                            alert('Journal deleted successfully!');
                        }
                    });
                }
            });
        });
    </script>
@endsection
