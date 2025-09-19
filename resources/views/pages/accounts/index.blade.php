@extends('layouts.main')
@section('title', 'Accounts')
@section('content')
    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <h1>Chart of Accounts</h1>
                <button class="btn btn-success pull-right" id="create-btn">Create New Account</button>
            </div>
        </div>
        <hr>
        <table id="accounts-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Normal Balance</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="ajax-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                </div>
                <div class="modal-body">
                    @csrf
                    <form id="account-form" name="account-form" class="form-horizontal">
                        <input type="hidden" name="account_id" id="account_id">
                        <div class="form-group">
                            <label for="code" class="col-sm-2 control-label">Code</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="code" name="code" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="normal_balance" class="col-sm-2 control-label">Normal Balance</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="normal_balance" name="normal_balance">
                                    <option value="">--Select--</option>
                                    <option value="DR">DR</option>
                                    <option value="CR">CR</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_active" class="col-sm-2 control-label">Is Active</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="is_active" name="is_active">
                                    <option value="">--Select--</option>
                                    <option value="1">Active</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle datatable
            var table = $('#accounts-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "/api/accounts",
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'code'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'normal_balance'
                    },
                    {
                        data: 'is_active',
                        render: function(data) {
                            return data ? '<span class="label label-success">Aktif</span>' : '<span class="label label-danger">Non-Aktif</span>';
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<button class="btn btn-primary btn-sm edit-btn" data-id="' + data + '">Edit</button> ' +
                                '<button class="btn btn-danger btn-sm delete-btn" data-id="' + data + '">Delete</button>';
                        }
                    }
                ]
            });

            // Handle button create
            $('#create-btn').click(function() {
                $('#save-btn').html("Save");
                $('#account_id').val('');
                $('#account-form').trigger("reset");
                $('#modal-title').html("Create New Account");
                $('#ajax-modal').modal('show');
            });

            // Handle button edit
            $('body').on('click', '.edit-btn', function() {
                var account_id = $(this).data('id');
                $.get('/api/accounts/' + account_id, function(response) {
                    var data = response.data ? response.data : response;
                    $('#modal-title').html("Edit Account");
                    $('#save-btn').html("Save Changes");
                    // Populate value
                    $('#account_id').val(data.id);
                    $('#code').val(data.code);
                    $('#name').val(data.name);
                    $('#normal_balance').val(data.normal_balance);
                    $('#is_active').val(
                        data.is_active === null || typeof data.is_active === 'undefined' ?
                        '' :
                        String(Number(data.is_active))
                    );
                    $('#ajax-modal').modal('show');
                });
            });

            // Handle form data
            $('#account-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var account_id = $('#account_id').val();
                var type = 'POST';
                var url = '/api/accounts';

                // Check id
                if (account_id) {
                    type = 'PUT';
                    url = '/api/accounts/' + account_id;
                }

                $.ajax({
                    type: type,
                    url: url,
                    data: formData,
                    success: function(data) {
                        $('#account-form').trigger("reset");
                        $('#ajax-modal').modal('hide');
                        table.ajax.reload();
                        alert('Operation Successful!');
                    },
                    error: function(data) {
                        // Handle error
                        var errors = data.responseJSON;
                        var errorString = 'Please fix the following errors:\n';
                        $.each(errors, function(key, value) {
                            errorString += '- ' + value + '\n';
                        });
                        alert(errorString);
                    }
                });
            });

            // Handle delete
            $('body').on('click', '.delete-btn', function() {
                var account_id = $(this).data("id");
                if (confirm("Are you sure you want to delete this account?")) {
                    $.ajax({
                        type: "DELETE",
                        url: '/api/accounts/' + account_id,
                        success: function(data) {
                            table.ajax.reload();
                            alert('Account deleted successfully!');
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            alert('An error occurred.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
