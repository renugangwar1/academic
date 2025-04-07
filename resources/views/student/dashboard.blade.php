@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ">
        <div class="col-md-12 mb-5">
            <div class="card border-secondary mb-3">
                <div class="card-header fw-bold d-flex justify-content-between">
                    <span>Basic Information</span>
                    <div><span>Verify Email : </span>{{Auth::guard('student')->user()->email}}</div>
                </div>
                <div class="card-body text-secondary">
                    <table class="border-dark table table-bordered table-group-divider table-secondary">
                        <tbody>
                            <tr>
                                <td class="d-flex justify-content-between"><span>Name</span> :</td>
                                <th scope="row">{{Auth::guard('student')->user()->name}}</th>
                                <td class="d-flex justify-content-between"><span>Course</span> :</td>
                                <th scope="row" class="text-capitalize">{{Auth::guard('student')->user()->course}}</th>
                            </tr>
                            <tr>
                                <td class="d-flex justify-content-between"><span>Roll-Number</span> :</td>
                                <th scope="row">{{Auth::guard('student')->user()->rollnumber}}</th>
                                <td class="d-flex justify-content-between"><span>Batch</span> :</td>
                                <th scope="row">{{Auth::guard('student')->user()->batch}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
                    <div class="card border-secondary h-100">
                        <div class="card-header fw-bold d-flex justify-content-between">
                            <span>Prograss</span>
                        </div>
                        <div class="card-body text-secondary">
                            <table class="border-dark table table-bordered table-group-divider table-secondary">
                                <tbody>
                                    <thead class="table-success border-0">
                                        <tr>
                                            <th scope="col">Semester</th>
                                            <th scope="col">SGPA</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Report Card</th>
                                        </tr>
                                    </thead>
                                    @foreach($data as $semvalue)
                                        <tr>
                                            <th scope="row">{{ordinalGet($semvalue['Stud_semester'])}}</th>
                                            <td>{{$semvalue['End_Result_SGPA'] ?? '--'}}</td>
                                            <td>{{$semvalue['End_Result'] ?? '--'}}</td>
                                            <td>@if(isset($semvalue['End_Result_CGPA']) && $semvalue['End_Result'] != 'Fail')<a href="{{Route('student.reportcard',['course' => $semvalue['course_id'],'sem' => $semvalue['Stud_semester'],'rno' => $semvalue['Stud_nchm_roll_number']])}}" class="btn btn-dark p-0 px-2 pt-1">View</a>@endif</td>
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
@endsection