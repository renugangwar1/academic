@extends('layouts.empty')

@section('content')
@foreach($getacdmitcard as $sn=>$singlecard)
<div class="text-center p-5 mx-auto mt-3 rounded-2 mainbg border-dark bg-white print-container position-relative">
    <div class="hideblock position-absolute start-0 top-0 p-1">
        {{$sn+1}}
    </div>
    <div class="d-block position-absolute start-0 top-0 w-4rem p-1 mt-4 ms-4">
        <img src="{{asset('/assets/imgs/jnulogo.png')}}" class="w-100" alt="">
    </div>
    <div class="d-block position-absolute end-0 top-0 w-6rem p-1 mt-4 me-4">
        <img src="{{asset('/assets/imgs/logo.png')}}" class="w-100" alt="">
    </div>
    <h5 class="fw-semibold">NATIONAL COUNCIL FOR HOTEL MANAGEMENT AND CATERING TECHNOLOGY</h5>
    <h5 class="fw-semibold">A-34, SECTOR-62, NOIDA-201301</h5>
    <h5 class="my-4 fw-semibold">ADMISSION TICKET</h5>
    <div class="text-start position-relative" for="basicinfo">
        <div class="">
            <table class="table w-auto table-borderless">
                <tbody>
                    <tr>
                        <td><strong>CENTRE:</strong></td>
                        <td><span class="text-uppercase">{{$singlecard->Institute_name}}</span></td>
                    </tr>
                    <tr>
                        <td><strong>COURSE:</strong></td>
                        <td><span class="text-uppercase">{{$singlecard->Stud_course}}</span></td>
                    </tr>
                    <tr>
                        <td><strong>ROLL NO:</strong></td>
                        <td>{{$singlecard->Stud_nchm_roll_number}}</td>
                    </tr>
                    <tr>
                        <td><strong>STUDENT NAME:</strong></td>
                        <td><span class="text-uppercase">{{$singlecard->Stud_name}}</span></td>
                    </tr>
                    <tr>
                        <td><strong>EXAM TYPE:</strong></td>
                        <td>REGULAR</td>
                    </tr>
                    <tr>
                        <td><strong>ACADEMIC YEAR:</strong></td>
                        <td>{{$singlecard->Stud_academic_year}}</td>
                    </tr>
                    <tr>
                        <td><strong>SEMESTER:</strong></td>
                        <td>{{ (trim($singlecard->Stud_semester) == '3rd' || (int)$singlecard->Stud_semester == 3) ? '4th' : $singlecard->Stud_semester }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>DATE OF ISSUE:</strong></td>
                        <td>{{date('d-M-Y')}}</td>
                    </tr>
                    <tr>
                        <td><strong>APPEARING SUBJECTS:</strong></td>
                        <td>{{$singlecard->Mid_Appear_subject}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div
            class="border border-dark p-1 position-absolute top-0 end-0 border-1 h-50 d-flex flex-column justify-content-around">
            <span>Paste your photograph</span>
        </div>
    </div>
    <div class="d-flex w-100 justify-content-between border-bottom border-dark border-2 pt-5 mt-5" for="signatures">
        <strong>SIGNATURE OF CANDIDATE</strong>
        <strong>SIGNATURE OF PRINCIPAL</strong>
    </div>
    <div class="pt-2 text-start" for="Important">
        <p><strong>IMPORTANT:</strong>
            The Principal may issue this ticket after ascertaining eligibility as per the rules and after the
            attestation of the photo and signature of the candidate.
        </p>
    </div>
    <div class="position-relative mb-5 pb-2" for="Instruction">
        <div class="pb-8">
            <h5 class="fw-bolder">INSTRUCTION</h5>
            <div class="text-start" for="list">
                <ol>
                    <li>Admission into the Examination hall will be only on the production of Admission Ticket.</li>
                    <li>Candidates should take their seat atleast ten minutes prior to the commencement of the
                        examination.</li>
                    <li>Candidates are advised to read do's and don'ts for the examination.</li>
                    <li>No candidates will be allowed to carry any paper other than the admission ticket.</li>
                    <li>Personal calculators, any kind of smart watches, mobile phones, or any other
                        electronic/communication devices are <strong>STRICTLY PROHIBITED</strong> inside the examination
                        hall.</li>
                    <li>During the examination, candidates may be checked for the possession of any of the prohibited
                        items. If the candidate is found to possess any of these items, he/she will be liable to be
                        debarred from the examination and/or subjected to disciplinary action.</li>
                </ol>
            </div>
            <div class="position-absolute end-0">
                <div class="ratio ratio-16x9 w-75 mx-auto">
                    <img class="" src="{{asset('/assets/imgs/signature.png')}}" alt="Dr. SATVIR SINGH" />
                </div>
                <p class=""><strong class="">Dr. SATVIR SINGH</strong></p>
                <strong class="">(CONTROLLER OF EXAMINATION)</strong>
            </div>
        </div>
    </div>
</div>
@endforeach
<div class="d-flex justify-content-around mt-4">
    {{ $getacdmitcard->links() }}
</div>
@endsection