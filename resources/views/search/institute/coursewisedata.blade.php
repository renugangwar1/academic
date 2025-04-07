@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark position-relative">
                <div>
                    <a href="{{ route('excel.instituteview') }}" class="btn btn-danger p-1 pb-0 position-absolute top-0 top-nv-13 end-0 border" title="Return to Search Page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
                        </svg> Return
                    </a>
                </div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="myTable" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr class="text-nowrap">
                                <th>Institute Name</th>
                                <th>Institute Code</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Students</th>
                                <th>Pass</th>
                                <th>Pass %</th>
                                <th>Fail</th>
                                <th>Fail %</th>
                                <th>Reappear</th>
                                <th>Reappear %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arange as $data)
                                <tr class="text-nowrap">
                                    <td class="text-start">{{$data[0]}}</td>
                                    <td class="text-start">{{$data[1]}}</td>
                                    <td class="text-capitalize">{{$data[2]}}</td>
                                    <td>{{ordinalget($data[3])}} Semester</td>
                                    <td>{{$data[4]}}</td>
                                    <td>{{$data[5]}}</td>
                                    <td>{{$data[6]}}</td>
                                    <td>{{$data[7]}}</td>
                                    <td>{{$data[8]}}</td>
                                    <td>{{$data[9]}}</td>
                                    <td>{{$data[10]}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-active fw-bold table-dark">
                            <tr>
                                <td colspan="4">Total</td>
                                <td>{{$grandtotal}}</td>
                                <td>{{$totalpass}}</td>
                                <td>{{$totalpasspercent}}</td>
                                <td>{{$totalfail}}</td>
                                <td>{{$totalfailpercent}}</td>
                                <td>{{$totalreappear}}</td>
                                <td>{{$totalreappearpercent}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection