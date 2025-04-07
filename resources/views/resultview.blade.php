@extends('layouts.empty')

@section('content')
@foreach($results as $result)
<div class="text-center p-5 mx-auto mt-3 rounded-2 mainbg border-dark bg-white print-container">
    <div class="justify-content-between position-relative">
        <div class="d-block position-absolute start-0 top-0 w-6rem p-1">
            <img src="{{asset('/assets/imgs/jnulogo.png')}}" class="w-100" alt="">
        </div>
        <div class="text-center">
            <div>
                <h5 class="fw-bold">JAWAHARLAL NEHRU UNIVERSITY</h5>
            </div>
            <div class="">
                <h5 class="fw-bold">NEW DELHI - 110067</h5>
            </div>
            <div class="">
                <p>In collaboration with</p>
            </div>
            <div class="">
                <h5 class="fw-bold">NATIONAL COUNCIL FOR HOTEL MANAGEMENT AND CATERING TECHNOLOGY</h5>
            </div>
            <div class="">
                <p>(An Autonomous Body under Ministry of Tourism, Govt. of India)</p>
            </div>
            <div class="">
                <h5 class="fw-bold">SEMESTER GRADE REPORT</h5>
            </div>
        </div>
        <div class="position-absolute end-0 top-0 d-flex justify-content-end">
            <img src="{{asset('/assets/imgs/logo.png')}}" class="w-75" alt="NCHMCT" />
        </div>
    </div>
    <div class="">
        <table class="table table-bordered border-dark">
            <thead>
                <tr>
                    <td scope="col">Student Name</td>
                    <td scope="col">Enrolment Number</td>
                    <td scope="col">Academy</td>
                    <td scope="col">Centre</td>
                </tr>
            </thead>
            <tbody>
                <tr class="fw-bold">
                    <td>{{$result->Stud_name ? $result->Stud_name : '--'}}</td>
                    <td>{{$result->Stud_nchm_roll_number ? $result->Stud_nchm_roll_number : '--'}}</td>
                    <td>{!!('HCHMCT')!!}</td>
                    <td>{{$result->institute_id ? $result->institute_id : '--'}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="">
        <table class="table table-bordered border-dark">
            <thead>
                <tr>
                    <td scope="col" class="flex-1">Semester</td>
                    <td scope="col" class="flex-1">Year</td>
                    <td scope="col" class="flex-1">Programme Of Study</td>
                </tr>
            </thead>
            <tbody>
                <tr class="fw-bold">
                    <td>{{$result->Stud_semester ? $result->Stud_semester : '--'}} Semester</td>
                    <td>{{$result->Stud_academic_year ? $result->Stud_academic_year : '--'}}</td>
                    <td><span class="text-capitalize">{{$result->Stud_course ? $result->Stud_course : '--'}}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="">
        <table class="table table-bordered border-dark">
            <thead>
                <tr>
                    <td scope="col">Course Code</td>
                    <td scope="col">Course Title</td>
                    <td scope="col">Credits</td>
                    <td scope="col">Grade</td>
                </tr>
            </thead>
            <tbody>
                @foreach(arraylistmarkgen($result) as $single)
                <tr class="fw-bold">
                    <td class="text-start text-uppercase">{{$single['coursecode']}}</td>
                    <td class="text-start text-capitalize">{{$single['coursetitle']}}</td>
                    <td>{{$single['coursecredit']}}</td>
                    <td>{{$single['coursegrade']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="">
        <table class="table table-bordered border-dark">
            <thead>
                <tr>
                    <th scope="col" colspan="3">Current Semester Record</th>
                    <th scope="col" colspan="3">Cumulative Record</th>
                </tr>
            </thead>
            <tbody>
                <tr class="">
                    <td>Total Credits</td>
                    <td>Total Points</td>
                    <td><div>Semester Grade Point Average</div><div>( S.G.P.A )</div></td>
                    <td>Total Credits</td>
                    <td>Total Points</td>
                    <td><div>Semester Grade Point Average</div><div>( C.G.P.A )</div></td>
                </tr>
                <tr class="">
                    <td>{{currentsemsterrecord($result)}}</td>
                    <td>{{$result->Grand_Credit_Point}}</td>
                    <td>{{$result->End_Result_SGPA}}</td>
                    <td>{{cumulativerecord($result)['totalcredit']}}</td>
                    <td>{{cumulativerecord($result)['totalpoint']}}</td>
                    <td>{{$result->End_Result_CGPA}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mb-5">
        <div class=""><span>DATE : </span></div>
        <div class=""><span>TOTAL VALID CREDIT EARNED : </span></div>
    </div>
    <div class="d-flex justify-content-between">
        <div class=""><Strong>Prepared By</Strong></div>
        <div class=""><Strong>Controller of Examination</Strong></div>
        <div class=""><Strong>JNU</Strong></div>
    </div>
</div>
@endforeach
<div class="d-flex justify-content-around mt-4">
    {{ $results->links() }}
</div>
@endsection