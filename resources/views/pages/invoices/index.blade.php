@extends('layouts.main')
@section('title', 'Invoices')
@section('content')
    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <h1>Invoices (AR)</h1>
                <button class="btn btn-success pull-right" id="create-invoice-btn">Create New Invoice</button>
            </div>
        </div>
        <hr>
        <table id="invoices-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Tax</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="modal fade" id="invoice-modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-title"></h4>
                    </div>
                    <form id="invoice-form" name="invoice-form" class="form-horizontal">
                        <div class="modal-body">
                            <input type="hidden" name="invoice_id" id="invoice_id">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Invoice No</label>
                                <div class="col-sm-9"><input type="text" class="form-control" name="invoice_no" required></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Date</label>
                                <div class="col-sm-9"><input type="date" class="form-control" name="invoice_date" required></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Customer</label>
                                <div class="col-sm-9"><input type="text" class="form-control" name="customer" required></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Amount</label>
                                <div class="col-sm-9"><input type="number" class="form-control" name="amount" required></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Tax Amount</label>
                                <div class="col-sm-9"><input type="number" class="form-control" name="tax_amount"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Status</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control" required>
                                        <option value="open">Open</option>
                                        <option value="partial">Partial</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detail-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Invoice Details</h4>
                    </div>
                    <div class="modal-body" id="detail-modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="payment-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Record Payment</h4>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 20px;">
                        <strong>Invoice No:</strong> <span id="payment-invoice-no"></span><br>
                        <strong>Total Amount:</strong> <span id="payment-total-amount"></span><br>
                        <strong>Balance Due:</strong> <span id="payment-balance-due" class="text-danger"></span>
                    </div>
                    <hr>

                    <form id="payment-form" name="payment-form" class="form-horizontal">
                        <input type="hidden" name="invoice_id" id="payment-invoice-id">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Payment Date</label>
                            <div class="col-sm-9"><input type="date" class="form-control" name="paid_at" required></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Amount Paid</label>
                            <div class="col-sm-9"><input type="number" step="0.01" class="form-control" name="amount_paid" required></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Method</label>
                            <div class="col-sm-9">
                                <select name="method" class="form-control" required>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Credit Card">Credit Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">Save Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#invoices-table').DataTable({
                processing: true,
                ajax: {
                    url: "/api/invoices",
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'invoice_no'
                    },
                    {
                        data: 'invoice_date'
                    },
                    {
                        data: 'customer'
                    },
                    {
                        // Amount Column
                        data: 'amount',
                        className: 'text-right',
                        render: function(data, type, row) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        // Tax Column
                        data: 'tax_amount',
                        className: 'text-right',
                        render: function(data, type, row) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            let badgeClass = 'label-default';
                            if (data === 'paid') badgeClass = 'label-success';
                            if (data === 'partial') badgeClass = 'label-warning';
                            if (data === 'open') badgeClass = 'label-danger';
                            return `<span class="label ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<button class="btn btn-info btn-xs detail-btn" data-id="${data}">Detail</button>
                        <button class="btn btn-success btn-xs payment-btn" data-id="${data}" style="margin-top: 2px;">Payment</button>`;
                        }
                    }
                ]
            });

            // --- CRUD Operations ---
            // Open modal for CREATING a new invoice
            $('#create-invoice-btn').on('click', function() {
                $('#invoice-form').trigger("reset");
                $('#invoice_id').val('');
                $('#modal-title').html("Create New Invoice");
                $('#invoice-modal').modal('show');
            });

            // Open modal for EDITING an invoice
            $('body').on('click', '.edit-btn', function() {
                var invoice_id = $(this).data('id');
                $.get('/api/invoices/' + invoice_id, function(data) {
                    $('#modal-title').html("Edit Invoice");
                    $('#invoice_id').val(data.id);
                    // Populate form fields dynamically
                    $.each(data, function(key, value) {
                        $(`[name=${key}]`).val(value);
                    });
                    $('#invoice-modal').modal('show');
                });
            });

            // Handle form SUBMISSION (Create & Update)
            $('#invoice-form').on('submit', function(e) {
                e.preventDefault();

                var invoice_id = $('#invoice_id').val();
                var url = invoice_id ? '/api/invoices/' + invoice_id : '/api/invoices';
                var method = invoice_id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#invoice-modal').modal('hide');
                        table.ajax.reload();
                        alert('Invoice saved successfully!');
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        alert('Error saving invoice. Check console for details.');
                        console.log(xhr.responseJSON);
                    }
                });
            });

            // Handle DELETION
            $('body').on('click', '.delete-btn', function() {
                var invoice_id = $(this).data("id");
                if (confirm("Are you sure you want to delete this invoice?")) {
                    $.ajax({
                        type: "DELETE",
                        url: '/api/invoices/' + invoice_id,
                        success: function(data) {
                            table.ajax.reload();
                            alert('Invoice deleted successfully!');
                        }
                    });
                }
            });

            // Handle DETAIL button click
            $('body').on('click', '.detail-btn', function() {
                var invoice_id = $(this).data('id');
                var modalBody = $('#detail-modal-body');

                modalBody.html('<p>Loading details...</p>');
                $('#detail-modal').modal('show');

                $.get('/api/invoices/' + invoice_id, function(data) {
                    const totalAmount = parseFloat(data.amount) + parseFloat(data.tax_amount || 0);
                    let paymentsHtml = '<h4>Payments:</h4>';
                    if (data.payments.length > 0) {
                        paymentsHtml += '<table class="table table-condensed"><thead><tr><th>Date</th><th>Ref</th><th class="text-right">Amount</th></tr></thead><tbody>';
                        data.payments.forEach(function(p) {
                            paymentsHtml += `<tr><td>${p.paid_at}</td><td>${p.payment_ref || '-'}</td><td class="text-right">${parseFloat(p.amount_paid).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td></tr>`;
                        });
                        paymentsHtml += '</tbody></table>';
                    } else {
                        paymentsHtml += '<p>No payments recorded for this invoice.</p>';
                    }

                    modalBody.html(`
                <p><strong>Invoice No:</strong> ${data.invoice_no}</p>
                <p><strong>Customer:</strong> ${data.customer}</p>
                <p><strong>Date:</strong> ${data.invoice_date}</p>
                <p><strong>Status:</strong> ${data.status.toUpperCase()}</p>
                <p><strong>Total Amount:</strong> ${totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                <hr>
                ${paymentsHtml}
            `);
                });
            });

            // Handle Payment
            $('body').on('click', '.payment-btn', function() {
                var invoice_id = $(this).data('id');

                // Fetch latest invoice data to calculate balance due
                $.get('/api/invoices/' + invoice_id, function(data) {
                    // Calculate totals
                    const totalAmount = parseFloat(data.amount) + parseFloat(data.tax_amount || 0);
                    const totalPaid = data.payments.reduce((sum, p) => sum + parseFloat(p.amount_paid), 0);
                    const balanceDue = totalAmount - totalPaid;

                    // Populate modal display
                    $('#payment-invoice-no').text(data.invoice_no);
                    $('#payment-total-amount').text(totalAmount.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    }));
                    $('#payment-balance-due').text(balanceDue.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    }));

                    // Populate form
                    $('#payment-invoice-id').val(data.id);

                    // Set default payment date to today
                    var today = new Date().toISOString().split('T')[0];
                    $('#payment-form [name="paid_at"]').val(today);

                    // Pre-fill amount with the balance due
                    $('#payment-form [name="amount_paid"]').val(balanceDue.toFixed(2));

                    $('#payment-modal').modal('show');
                });
            });

            // --- Handle Payment Form Submission ---
            $('#payment-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/api/payments',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#payment-modal').modal('hide');
                        $('#invoices-table').DataTable().ajax.reload();
                        alert('Payment recorded successfully!');
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert('Error: ' + errorMsg);
                    }
                });
            });

        });
    </script>
@endsection
