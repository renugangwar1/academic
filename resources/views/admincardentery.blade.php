@extends('layouts.app')

@section('content')
    <div class="text-center w-75 mx-auto p-4 rounded-2 shadow-lg border border-dark bg-warning-subtle">
        <h5 class="fw-semibold">NATIONAL COUNCIL FOR HOTEL MANAGEMENT AND CATERING TECHNOLOGY</h5>
        <h5 class="fw-semibold">A-34,SECTOR-62,NOIDA-201301</h5>
        <h5 class="my-4 fw-semibold">ADMISSION TICKET</h5>
        <div class="text-start position-relative" for="basicinfo">
            <div class="">
                <p><strong>CENTRE:</strong>
                    <span>INSTITUTE OF HOTEL MANAGEMENT, NCHMCT</span>
                </p>
                <p><strong>ADMIT:</strong>
                    <span>SHRADDHA SHARMA</span>
                </p>
                <p class="w-75"><strong>ROLL NO:</strong>
                    ( <span class="fw-semibold">2341122009</span> )
                    to the Regular end term examination (2023-24) for FIRST SEMESTER of B.Sc. in Hospitality Administration.
                </p>
                <p><strong>SUBJECT(S):</strong>
                    <span>BHA101, BHA102, BHA103, BHA104,BHA105,BHA106,BHA107,BHA108,BHA110,BHA111</span>
                </p>
                <p><strong>DATE OF ISSUE:</strong>
                    <span>09 OCTOBER 2023</span>
                </p>
            </div>
            <div class="border border-dark p-5 position-absolute top-0 end-0 border-1">
                <img src="" alt=""/>
            </div>
        </div>
        <div class="d-flex w-100 justify-content-between border-bottom border-dark border-2 pt-5" for="signatures">
            <strong>SIGNATURE OF CANDIDATE</strong>
            <strong>SIGNATURE OF PRINCIPAL</strong>
        </div>
        <div class="pt-2 text-start" for="Important">
            <p><strong>IMPORTANT:</strong>
                The Principal may issue this card to the candidate after the attestation of photo and signature of the candidate.
            </p>
        </div>
        <div class="position-relative" for="Instruction">
            <h5 class="fw-bolder">INSTRUCTION</h5>
            <div class="text-start" for="list">
                <ol>
                    <li>Admission into the examination hall will be only on production of admission ticket.</li>
                    <li>Candidates should take their seat atleast ten minutes prior to commencement of the examination.</li>
                    <li>For date and time of examination, please see DATESHEET.</li>
                    <li>Candidates are advised to read do's and don'ts for the examination.</li>
                    <li>No candidates will be allowed to carry the examination hall any paper Other than the admission ticket.</li>
                </ol>
            </div>
            <strong class="position-absolute end-0">CONTROLLER OF EXAMINATION</strong>
        </div>
    </div>
@endsection
