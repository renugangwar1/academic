@extends('layouts.pdf')
@section('content')
<div style="">
    <table style="width:100%; margin-top: 10px; text-align:center; border: 1px solid black; font-weight:500;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; text-transform:capitalize; position:relative;">Institute Name</th>
                <th style="border: 1px solid #000; text-transform:capitalize; position:relative;">{{$instinfo->InstituteName}}</th>
                <th style="border: 1px solid #000; text-transform:capitalize; position:relative;">Institute Code</th>
                <th style="border: 1px solid #000; text-transform:capitalize; position:relative;">{{$instinfo->InstituteCode}}</th>
            </tr>
        </thead>
    </table>
    <table style="width:100%; margin-top: 10px; text-align:center; border-collapse: collapse; border: 1px solid black; font-weight:500;">
        <thead>
            <tr>
                <td scope="col" style="border: 1px solid #000; text-transform:capitalize; position:relative;" rowspan="3">S.No</td>
                <td scope="col" style="border: 1px solid #000; text-transform:capitalize; position:relative;" rowspan="3">Enrolment Number</td>
                <td scope="col" style="border: 1px solid #000; text-transform:capitalize; position:relative;" rowspan="2">Name of Participants</td>
                @foreach($db as $code)
                    <td scope="col" style="border: 1px solid #000; padding:6px;" colspan="2">{{substr($code->Subject_code,3)}}</td>
                @endforeach
                <td scope="col" style="border: 1px solid #000; text-transform:capitalize; position:relative;" rowspan="2">Total Points</td>
                <td scope="col" style="border: 1px solid #000; text-transform:capitalize; position:relative;" rowspan="3">{{checkEven($db[0]->Semester) == true ? 'CGPA' : 'SGPA'}}</td>
            </tr>
            <tr>
                @foreach($db as $subject)
                    <td scope="col" style="border: 1px solid #000; text-transform:capitalize; text-wrap:wrap; position:relative; height: 35%;" colspan="2"><span style="transform: rotate(90deg); display: grid; white-space: nowrap;">{{$subject->Subject_name}}</span></td>
                @endforeach
            </tr>
            <tr>
                <td scope="col" style="text-align: end; font-weight: bold;">Credits</td>
                @foreach($db as $credit)
                    <td scope="col" style="border: 1px solid #000;" colspan="2">{{$credit->Credit}}</td>
                @endforeach
                <td scope="col" style="border: 1px solid #000;">{{$db->sum('Credit')-($db->where('Optional_subject',1)->sum('Credit')/2)}}</td>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $sn=>$result)
                <tr style="font-weight: bold;">
                    <td style="border: 1px solid #000;">{{$sn+1}}</td>
                    <td style="border: 1px solid #000;">{{$result->student->NCHMCT_Rollnumber ?? '--'}}</td>
                    <td style="border: 1px solid #000;">{{$result->student->name ?? '--'}}</td>
                    @foreach($db as $sub)
                        @php
                            $subgrad = (array)$subgrad = json_decode($result->Marks_grade);
                            $subgradpoint = (array)$subgradpoint = json_decode($result->Marks_grade_point);
                        @endphp
                        <td style="border: 1px solid #000; text-align:center; padding:4px;">{{$subgradpoint[$sub->Subject_code] ?? ''}}</td>
                        <td style="border: 1px solid #000; text-align:center; padding:4px;">{{$subgrad[$sub->Subject_code] ?? ''}}</td>
                    @endforeach
                    <td style="border: 1px solid #000;">{{$result->Grand_Credit_Point}}</td>
                    <td style="border: 1px solid #000;">{{$result->End_Result_CGPA}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
