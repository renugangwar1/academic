@extends('layouts.html')

@section('content')
<style>
    .pagebreak {
        page-break-after: always;
    }

    table,tr {
        background-color: transparent!important;
    }

    tr>td{
        padding-top:2px;
        font-weight:700;
    }

    .votarmark{
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: center;
        align-items: center;
        z-index: -1;
        opacity: 0.1;
        height: 100%;
    }
</style>
@php $sn=1; @endphp
@foreach($results as $key=>$result)
<span class="hide">{{$sn++}}</span>
<div class="pagebreak h-100 position-relative d-flex flex-column justify-content-between">
<div class="votarmark"><img src="{{asset('/assets/imgs/logo.png')}}" style="width: 100%;" alt="NCHMCT" /></div>
    <div class="d-block">
        <table class="px-5" style="width:100%; color:#008000;">
            <tbody>
                <tr>
                    <td style="text-align:center; border:0px;"><img src="{{asset('/assets/imgs/logo.png')}}" style="width: 10%;" alt="NCHMCT" /></td>
                </tr>
                <tr>
                    <td style="text-align:center; border:0px;" colspan="3">
                        <div style="white-space: nowrap; font-size:small; font-weight:700;">NATIONAL COUNCIL FOR HOTEL MANAGEMENT AND CATERING TECHNOLOGY</div>
                        <div style="margin-bottom:4px; ">(An Autonomous Body under Ministry of Tourism, Govt. of India)</div>
                        <div style="white-space: nowrap; font-size:small; font-weight:700;">राष्ट्रीय होटल प्रबन्ध एवं केटरिंग टेक्नोलॉजी परिषद</div>
                        <div style="margin-bottom:4px; ">(पर्यटन मंत्रालय, भारत सरकार के अधीन स्वायत्तशासी निकाय)</div> 
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="position:relative;">
            <table style="width:100%; color:#008000; margin-top: 4px; ">
                <tbody>
                    <tr>
                        <td style="text-align:center; border:0px;">
                            <strong>SEMESTER REPORT</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table style="width:100%; margin-top: 10px; text-align:center; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td style="border: 1px solid black; font-weight:700;">
                        छात्र/छात्रा का नाम<br>
                        STUDENT NAME
                    </td>
                    <td style="border: 1px solid black; font-weight:700;">
                        NCHMCT<br>
                        नामांकन संख्या<br>
                        ENROLMENT NUMBER
                    </td>
                    <td style="border: 1px solid black; font-weight:700;">
                        शैक्षणिक अध्याय कोड<br>
                        ACADEMIC CHAPTER CODE
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; white-space: nowrap; padding:4px; font-weight:700;">{{isset($result['data']['Stud_name']) ? $result['data']['Stud_name'] : 'XXXXXXXXXXXXXXXX'}}</td>
                    <td style="border: 1px solid black; white-space: nowrap; padding:4px; font-weight:700;">{{isset($result['data']['Stud_nchm_roll_number']) ? $result['data']['Stud_nchm_roll_number'] : 'XXXXXXXXXX'}}</td>
                    <td style="border: 1px solid black; white-space: nowrap; padding:4px; font-weight:700;">{{isset($result['data']['institute_id']) ? $result['data']['institute_id'] : 'XXX'}}</td>
                </tr>
            </tbody>
        </table>

        <table style="width:100%; margin-top: 10px; border-collapse: collapse; text-align:center; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td style="border: 1px solid black; font-weight:700;">
                        सत्र<br>
                        SEMESTER/Year
                    </td>
                    <td style="border: 1px solid black; font-weight:700;">
                        शैक्षणिक सत्र<br>
                        ACADEMIC SESSION
                    </td>
                    <td style="border: 1px solid black; font-weight:700;">
                        अध्ययन कार्यक्रम<br>
                        PROGRAMME OF STUDY
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; font-weight:700;">{{isset($result['data']['Stud_semester']) ? $result['data']['Stud_semester'] : 'X'}}</td>
                    <td style="border: 1px solid black; font-weight:700;">{{isset($result['data']['Stud_academic_year']) ? $result['data']['Stud_academic_year'] : 'XXXX-XXXX'}}</td>
                    <td style="border: 1px solid black;    text-transform: uppercase; font-weight:700;">{{isset($result['data']['Stud_course']) ? $result['data']['Stud_course'] : 'XXXXX'}}</td>
                </tr>
            </tbody>
        </table>

        <table style="width:100%; margin-top: 10px; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        कोर्स का नाम<br>
                        Course Name
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        कोर्स का प्रकार<br>
                        Course Type
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        अधिकतम अंक<br>
                        Max Marks
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        उत्तीर्ण अंक<br> Pass Marks
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        अंक प्राप्त किये<br> Obtained Marks
                    </td>
                </tr>
            </thead>
            <tbody>
                @foreach($result['subjectarray'] as $single)
                    <tr>
                        <td style="border: 1px solid black; text-align:left; padding:3px 4px; white-space: nowrap; text-transform: uppercase;">{{$single['course_name'] ?? '--'}}</td>
                        <td style="border: 1px solid black; text-align:center; white-space: nowrap; text-transform: uppercase;">{{$single['course_type'] ?? '--'}}</td>
                        <td style="border: 1px solid black; text-align:center; white-space: nowrap; text-transform: uppercase;">{{$single['max'] ?? '--'}}</td>
                        <td style="border: 1px solid black; text-align:center; white-space: nowrap; text-transform: uppercase;">{{$single['min'] ?? '--'}}</td>
                        <td style="border: 1px solid black; text-align:center; white-space: nowrap; text-transform: uppercase;">{{$single['obtain'] ?? '--'}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width:100%; margin-top: 10px;">
            <tbody style="color:#008000; ">
                <tr>
                    <td style="text-align:left; font-weight:700; border:0px;">Overall Result : <span style="color:black;">{{$result['result'] != 'Compartment' ?  'Pass' : 'Compartment'}}</span></td>
                    <td style="text-align:right; font-weight:700; border:0px;">DIVISION : <span style="color:black;">{{$result['result'] ?? '--'}}</span></td>
                </tr>
                <tr>
                    <td style="text-align:left; font-weight:700; border:0px; margin-top: 10px;">DATE : <span style="color:black;">{{date('M Y')}}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pt-4">
        <table style="width:100%;">
            <tbody style="color:#008000;">
                <tr>
                    <td style="text-align:left; border:0px; font-size:13px; width:33.33%;">
                        <strong>ई.डी.पी.सेल</strong></br>
                        <strong>E.D.P.Cell</strong>
                    </td>

                    <td style="text-align:center; border:0px; font-size:13px; width:33.33%;">
                        <strong>द्वारा सत्यापित</strong></br>
                        <strong>Verified By</strong>
                    </td>

                    <td style="text-align:right; border:0px; font-size:13px; width:33.33%;">
                        <strong>निदेशक (अध्ययन)</strong></br>
                        <strong>Director(Studies)</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- end front print view -->

<!-- back page print view -->
<div class="pagebreak mt-5" style="font-size:x-small; position:relative; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;">
    <div style="width:100%;">
        <table style="width:100%; margin-top: 10px; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;">
                <tr>
                    <td colspan="2" style="text-align:center; padding:20px; border:0px;">
                        <strong>INSTITUTE ENDORSEMENT</strong>
                    </td>
                </tr>
            </thead>
            <tbody style="color:#008000;  ">
                <tr>
                    <td style="text-align:left; padding:40px 20px 20px; border:0px;">Ref. No.:...................................</td>
                    <td style="text-align:right; padding:40px 20px 20px; border:0px;">Date:............................</td>
                </tr>
                <tr>
                    <td style="text-align:left; padding:100px 20px 10px; border:0px;">Institute Seal</td>
                    <td style="text-align:right; padding:100px 20px 10px; border:0px;">Principal</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<hr class="border-2 border-danger bg-danger">
@endforeach
@endsection