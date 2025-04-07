@extends('layouts.pdf')

@section('content')
<style>
    .pagebreak {
        page-break-after: always;
    }
</style>
@foreach($results as $result)
<div class="pagebreak pagespacer" style="background:white; position:relative; font-weight:700;">
    <div>
        <table style="width:100%; color:#008000; ">
            <tbody>
                <tr>
                    <td style="text-align:left; border:0px;"><img src="{{asset('/assets/imgs/jnulogo.png')}}" style="width: 80%;" alt="JNU"></td>
                    <td style="text-align:center; border:0px;">
                        <div style="white-space: nowrap; font-size:small; font-weight:700;">जवाहरलाल नेहरू विश्वविद्यालय</div>
                        <div style="margin-bottom:4px; ">नई दिल्ली - 110067</div>
                        <div style="white-space: nowrap; font-size:small; font-weight:700;">JAWAHARLAL NEHRU UNIVERSITY</div>
                        <div style="margin-bottom:4px;  ">NEW DELHI – 110067 </div>
                        <div style="white-space: nowrap; font-weight:700;">NATIONAL COUNCIL FOR HOTEL MANAGEMENT AND CATERING TECHNOLOGY</div>
                        <div style="margin-bottom:4px; ">(An Autonomous Body under Ministry of Tourism, Govt. of India)</div>
                        <div style="margin-bottom:4px; ">राष्ट्रीय होटल प्रबंधन एवं केटरिंग टेक्नॉलॉजी परिषद</div>
                    </td>
                    <td style="text-align:right; border:0px;"><img src="{{asset('/assets/imgs/logo.png')}}" style="width: 80%;" alt="NCHMCT" /></td>
                </tr>
            </tbody>
        </table>
        <div style="position:relative;">
            <table style="width:100%; color:#008000; margin-top: 4px; ">
                <tbody>
                    <tr>
                        <td style="text-align:center; border:0px;">
                            <strong>SEMESTER GRADE REPORT</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="position:absolute; top:0; right:0;"><span style="color:#008000;">SL. No.</span><span style="color:black;">{{isset($result['data']['Stud_jnu_roll_number']) ? slnumber($result['data']['Stud_jnu_roll_number']) : ''}}</span></div>
        </div>

        <table style="width:100%; margin-top: 10px; text-align:center; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td style="border: 1px solid black; font-weight:700;">
                        छात्र/छात्रा का नाम<br>
                        STUDENT NAME
                    </td>
                    <td style="border: 1px solid black; font-weight:700;">
                        JNU<br>
                        नामांकन संख्या<br>
                        ENROLMENT NUMBER
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
                    <td style="border: 1px solid black; white-space: nowrap; padding:4px;">{{isset($result['data']['Stud_name']) ? $result['data']['Stud_name'] : 'XXXXXXXXXXXXXXXX'}}</td>
                    <td style="border: 1px solid black; white-space: nowrap; padding:4px;">{{isset($result['data']['Stud_jnu_roll_number']) ? $result['data']['Stud_jnu_roll_number'] : 'NCHMCT/BSC/2023/XXXXX'}}</td>
                    <td style="border: 1px solid black; white-space: nowrap; padding:4px;">{{isset($result['data']['Stud_nchm_roll_number']) ? $result['data']['Stud_nchm_roll_number'] : 'XXXXXXXXXX'}}</td>
                    <td style="border: 1px solid black; white-space: nowrap; padding:4px;">{{isset($result['data']['institute_id']) ? $result['data']['institute_id'] : 'XXX'}}</td>
                </tr>
            </tbody>
        </table>

        <table style="width:100%; margin-top: 10px; border-collapse: collapse; text-align:center; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td style="border: 1px solid black; font-weight:700;">
                        सत्र<br>
                        SEMESTER
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
                    <td style="border: 1px solid black;">SEM-{{isset($result['data']['Stud_semester']) ? $result['data']['Stud_semester'] : 'X'}}</td>
                    <td style="border: 1px solid black;">{{isset($result['data']['Stud_academic_year']) ? $result['data']['Stud_academic_year'] : 'XXXX-XXXX'}}</td>
                    <td style="border: 1px solid black;    text-transform: uppercase;">{{isset($result['data']['Stud_course']) ? $result['data']['Stud_course'] : 'XXXXX'}}</td>
                </tr>
            </tbody>
        </table>

        <table style="width:100%; margin-top: 10px; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        पाठ्यक्रम कोड<br>
                        Course Code
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        पाठ्यक्रम शीर्षक<br>
                        Course Title
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        क्रेडिट<br>
                        Credits
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        श्रेणी<br> Grade
                    </td>
                </tr>
            </thead>
            <tbody>
                @foreach($result['subjectarray'] as $single)
                <tr>
                    <td style="border: 1px solid black; text-align:center; white-space: nowrap; text-transform: uppercase;">{{$single['coursecode'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:left; white-space: nowrap;  padding: 4px 8px;  text-transform: capitalize;">{{$single['coursetitle'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:center; white-space: nowrap; ">{{$single['coursecredit'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:center; white-space: nowrap; ">{{$single['coursegrade'] ?? '--'}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width:100%; margin-top: 10px; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td colspan="3" style="border: 1px solid black; text-align:center; font-weight:700;">
                        CURRENT SEMESTER RECORD
                    </td>
                    <td colspan="3" style="border: 1px solid black; text-align:center; font-weight:700;">
                        CUMULATIVE RECORD
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        TOTAL CREDITS
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        TOTAL POINTS
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        SEMESTER GRADE POINT
                        AVERAGE (S.G.P.A)
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        TOTAL
                        CREDITS
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        TOTAL
                        POINTS
                    </td>
                    <td style="border: 1px solid black; text-align:center; font-weight:700;">
                        {{checkEven($result['data']['Stud_semester']) === true ? 'CUMULATIVE GRADE POINT AVERAGE (C.G.P.A)' : 'SEMESTER GRADE POINT AVERAGE (S.G.P.A)'}}
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; text-align:center;">{{$result['currensemrecord']['totalcredit'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:center;">{{$result['currensemrecord']['totalpoint'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:center;">{{$result['data']['End_Result_SGPA'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:center;">{{$result['cumulativerecord']['totalcredit'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:center;">{{$result['cumulativerecord']['totalpoint'] ?? '--'}}</td>
                    <td style="border: 1px solid black; text-align:center;">{{$result['data']['End_Result_CGPA'] ?? '--'}}</td>
                </tr>
            </tbody>
        </table>

        <table style="width:100%; margin-top: 10px;">
            <tbody style="color:#008000; ">
                <tr>
                    <td style="text-align:left; font-weight:700; border:0px;">DATE : <span style="color:black;">{{date('d-m-Y')}}</span></td>
                    <td style="text-align:right; font-weight:700; border:0px;">TOTAL VALID CREDIT EARNED : <span style="color:black;">{{$result['totalcredit'] ?? '--'}}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="position: absolute; bottom:0; width:100%;">
        <table style="width:100%; margin-top: 80px;">
            <tbody style="color:#008000; ">
                <tr>
                    <td style="text-align:left; border:0px;">
                        <strong>Controller of Examination </strong></br>
                        <strong>NCHMCT</strong>
                    </td>
                    <td style="text-align:right; border:0px;">
                        <strong>Controller of Examination </strong></br>
                        <strong>JNU</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- end front print view -->

<!-- back page print view -->
<div class="pagebreak" style="background:white; font-size:x-small; position:relative; font-family: \'Noto Sans Devanagari\'; font-weight:700;">
    <div>
        <table style="width:100%; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td colspan="2" style="border: 1px solid black; text-align:center;">
                        <strong>GRADING SYSTEM</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border: 1px solid black; padding:1px 6px;">
                        The University follows a ten point letter grading scale (as given below) for evaluation of student's
                        academic performance in the courses during the semester:
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align:center;">
                        <strong>Grade</strong>
                    </td>
                    <td style="border: 1px solid black; text-align:center;">
                        <strong>Grade Point (Numerical Value)</strong>
                    </td>
                </tr>
            </thead>
            <tbody style="color:#008000; font-size:smaller; text-align:center;">
                <tr>
                    <td style="border: 1px solid black;">A+</td>
                    <td style="border: 1px solid black;">9</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">A</td>
                    <td style="border: 1px solid black;">8</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">A-</td>
                    <td style="border: 1px solid black;">7</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">B+</td>
                    <td style="border: 1px solid black;">6</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">B</td>
                    <td style="border: 1px solid black;">5</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">B-</td>
                    <td style="border: 1px solid black;">4</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">C+</td>
                    <td style="border: 1px solid black;">3</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">C</td>
                    <td style="border: 1px solid black;">2</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">C-</td>
                    <td style="border: 1px solid black;">1</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">F</td>
                    <td style="border: 1px solid black;">0</td>
                </tr>
            </tbody>
        </table>
        <div style="color:#008000; ">
            <ul>
                <li>
                    <div>There is no rounding off either of the Semester Grade Point Average or Cumulative Grade Point Average or Final Grade Point Average at the end of each successive semester as well as at the end of the programme.</div>
                </li>
                <li>
                    <div>SGPA/CGPA/FGPA obtained by the student is out of maximum possible ‘9’ point.</div>
                </li>
                <li>
                    <div>The Final Grade Point Average obtained by the student shall be classified into following divisions.</div>
                </li>
            </ul>
        </div>
        <table style="width:100%; margin-top: 10px; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;   border: 1px solid black;">
                <tr>
                    <td colspan="2" style="border: 1px solid black; text-align:center;">
                        <strong>The Final Grade Point Average obtained by the student shall be classified intofollowingdivisions:</strong>
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="2" style="border: 1px solid black; padding:0px 10px;">
                        The University follows a ten point letter grading scale (as given below) for evaluation of
                        student's </br>
                        academic performance in the courses during the semester
                    </td>
                </tr> -->
                <tr>
                    <td style="border: 1px solid black; text-align:center;">
                        <strong>F.G.P.A.</strong>
                    </td>
                    <td style="border: 1px solid black; text-align:center;">
                        <strong>Class Division</strong>
                    </td>
                </tr>
            </thead>
            <tbody style="color:#008000;">
                <tr>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">8.5 and above </td>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">High First Class</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">7.5 and above but less than 8.5 </td>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">Middle First Class</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">6.5 and above but less than 7.5</td>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">Lower First Class</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">5.5 and above but less than 6.5</td>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">High Second Class</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">4.5 and above but less than 5.5</td>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">Middle Second Class</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">3.5 and above but less than 4.5</td>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">Lower Second Class</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">3.0 and above but less than 3.5</td>
                    <td style="border: 1px solid black; padding:0px 4px; text-align:left;">Pass</td>
                </tr>
            </tbody>
        </table>
        <div style="color:#008000; ">
            <ul>
                <li>
                    <div>For proper evaluation of student's academic standing his/her entire transcript representing his/her performance in all the semesters should be taken into consideration.</div>
                </li>
            </ul>
        </div>
        <table style="width:100%; margin-top: 80px;">
            <tbody style="color:#008000; ">
                <tr>
                    <td style="text-align:left; border:0px;"><strong>Prepared By</strong></td>
                    <td style="text-align:right; border:0px;"><strong>Authorized Signatory of Institution</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="position: absolute; width:100%; bottom:0;">
        <table style="width:100%; margin-top: 10px; border-collapse: collapse; border: 1px solid black;">
            <thead style="color:#008000;">
                <tr>
                    <td colspan="2" style="text-align:center; padding:20px; border:0px;">
                        <strong>ACADEMIC CHAPTER ENDORSEMENT</strong>
                    </td>
                </tr>
            </thead>
            <tbody style="color:#008000;  ">
                <tr>
                    <td style="text-align:left; padding:0px 20px 20px; border:0px;">Ref. No.:...................................</td>
                    <td style="text-align:right; padding:0px 20px 20px; border:0px;">Date:............................</td>
                </tr>
                <tr>
                    <td style="text-align:left; padding:40px 20px 10px; border:0px;">Academic Chapter</td>
                    <td style="text-align:right; padding:40px 20px 10px; border:0px;">Principal</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endsection