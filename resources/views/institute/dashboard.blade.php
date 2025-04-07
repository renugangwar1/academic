@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ">
        <div class="col-md-12 mb-5">
            <div class="row">
                <div class="col-md-6 mb-5">
                    <div class="card border-secondary mb-3 h-100">
                        <div class="card-header fw-bold d-flex justify-content-between">
                            <span>Notice</span>
                        </div>
                        <div class="card-body text-secondary">
                            <ul class="p-0">
                                @foreach($notification as $notify)
                                    <li class="text-capitalize ms-3"><a href="{!!$notify->Nlink!!}"><p>{{$notify->Ntitle}}</p></a></li>
                                    <hr>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-5">
                    <div class="card border-secondary mb-3 h-100">
                        <div class="card-header fw-bold d-flex justify-content-between">
                            <span>Last 5 Upload History</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table id="excelloghistory" class="table table-striped table-hover table-bordered table-responsive border border-2 border-black">
                                <thead class="table-success text-nowrap">
                                    <tr>
                                        <th scope="col">File Name</th>
                                        <th scope="col">Tearm</th>
                                        <th scope="col">Batch</th>
                                        <th scope="col">Datetime</th>
                                        <th scope="col">View</th>
                                    </tr>
                                </thead>
                                <tbody class="text-nowrap">
                                    @foreach($exellog as $single)
                                        <tr>
                                            <td>{{$single->excel_title}}</td>
                                            <td><span class="text-capitalize">{{$single->Tearm}}</span></td>
                                            <td>{{$single->Batch}}</td>
                                            <td>{{date('d-m-Y H:m:i',strtotime($single->created_at))}}</td>
                                            <td><a href="{{asset('../storage/'.$single->excel_link)}}" class="btn btn-dark p-0 px-2 pt-1">Download</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        new DataTable('#excelloghistory', {
            paging: false,        // Enable pagination
            searching: false,     // Enable search functionality
            ordering: false,      // Enable column ordering
            info: false,         // Show table information
        });
    });
</script>
@endsection