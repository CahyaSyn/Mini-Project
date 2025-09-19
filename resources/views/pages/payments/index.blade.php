@extends('layouts.main')
@section('title', 'Payments')
@section('content')
    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <h1>Payment</h1>
            </div>
        </div>
        <hr>
        <table id="payments-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Payment Ref</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th class="text-right">Amount Paid</th>
                    <th>Invoice No</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="detail-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Details</h4>
                </div>
                <div class="modal-body" id="detail-modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#payments-table').DataTable({
                processing: true,
                ajax: {
                    url: "/api/payments",
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'payment_ref'
                    },
                    {
                        data: 'paid_at'
                    },
                    {
                        data: 'method'
                    },
                    {
                        data: 'amount_paid',
                        className: 'text-right',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'invoice.invoice_no',
                        defaultContent: '<i>Not found</i>'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<button class="btn btn-info btn-xs detail-btn" data-id="${data}">Detail</button>
                            <button class="btn btn-danger btn-xs delete-btn" data-id="${data}">Delete</button>`;
                        }
                    }
                ]
            });

            // --- Handle "Detail" button click ---
            $('body').on('click', '.detail-btn', function() {
                var payment_id = $(this).data('id');
                var modalBody = $('#detail-modal-body');

                modalBody.html('<p>Loading details...</p>');
                $('#detail-modal').modal('show');

                $.get('/api/payments/' + payment_id, function(data) {
                    modalBody.html(`
                <h4>Payment Info</h4>
                <p><strong>Reference:</strong> ${data.payment_ref || '-'}</p>
                <p><strong>Date:</strong> ${data.paid_at}</p>
                <p><strong>Method:</strong> ${data.method}</p>
                <p><strong>Amount:</strong> ${parseFloat(data.amount_paid).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                <hr>
                <h4>Related Invoice</h4>
                <p><strong>Invoice No:</strong> ${data.invoice ? data.invoice.invoice_no : 'N/A'}</p>
                <p><strong>Customer:</strong> ${data.invoice ? data.invoice.customer : 'N/A'}</p>
            `);
                });
            });

            $('body').on('click', '.delete-btn', function() {
                var payment_id = $(this).data("id");
                if (confirm("Are you sure you want to delete this payment? This will also update the related invoice status.")) {
                    $.ajax({
                        type: "DELETE",
                        url: '/api/payments/' + payment_id,
                        success: function(data) {
                            table.ajax.reload();
                            alert('Payment deleted successfully!');
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseJSON.message);
                        }
                    });
                }
            });
        });
    </script>
@endsection
