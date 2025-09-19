@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1>Welcome to Dashboard</h1>
            <p>This template is now fully responsive with a collapsible sidebar.</p>

            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3"><i class="fa fa-comments fa-5x"></i></div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">26</div>
                                    <div>New Comments!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3"><i class="fa fa-tasks fa-5x"></i></div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">12</div>
                                    <div>New Tasks!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3"><i class="fa fa-shopping-cart fa-5x"></i></div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">124</div>
                                    <div>New Orders!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3"><i class="fa fa-support fa-5x"></i></div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">13</div>
                                    <div>Support Tickets!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Recent Activity</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1,001</td>
                                    <td>John Doe</td>
                                    <td>Logged In</td>
                                    <td>2025-09-15 15:30</td>
                                    <td><span class="label label-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>1,002</td>
                                    <td>Jane Smith</td>
                                    <td>Updated Profile</td>
                                    <td>2025-09-15 14:12</td>
                                    <td><span class="label label-info">Info</span></td>
                                </tr>
                                <tr>
                                    <td>1,003</td>
                                    <td>Admin User</td>
                                    <td>Deleted Post #54</td>
                                    <td>2025-09-15 11:05</td>
                                    <td><span class="label label-danger">Critical</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
