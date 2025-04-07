@extends('layouts.pdf')
<style>
.admit-card {
    font-size: 12px;
    margin-bottom: 100px;
    border-collapse: collapse;
}

.full-width {
    width: 100%;
}

.logo {
    width: 4.5rem;
}

.center-text {
    text-align: center;
}

.main-title {
    margin: 0;
    font-size: small;
    white-space: nowrap;
}

.subtitle {
    margin-top: 0;
    font-weight: bold;
}

.admission-ticket {
    margin: 5px 0;
    font-weight: bold;
}

.heading {
    margin-bottom: 10px;
}

.line-height-small {
    line-height: 16px;
}

.bold {
    font-weight: bold;
}

.uppercase {
    text-transform: uppercase;
}

.photo-placeholder {
    position: absolute;
    right: 0;
    top: 120px;
    border: 1px solid;
    padding: 70px 10px;
}

.signature-section {
    width: 100%;
    display: flex;
    justify-content: space-between;
    margin-top: 60px;
    padding-bottom: 5px;
    border-bottom: 2px solid;
}

.line-height-large {
    line-height: 25px;
}

.text-left {
    text-align: left;
}

.text-right {
    text-align: right;
}

.important-note {
    text-align: start;
}

.instructions {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.text-center {
    text-align: center;
}

.instruction-list {
    line-height: 14px;
}

.signature-controller {
    width: 100%;
    text-align: right;
}

.signature-img {
    width: 10rem;
    padding-right: 20px;
}

.signature-controller p {
    padding-right: 50px;
}
</style>
@section('content')
@foreach($getacdmitcard as $sn => $singlecard)
<div class="admit-card">
    <div>
        <table class="full-width">
            <tbody>
                <tr>
                    <td><img src="{{ asset('/assets/imgs/jnulogo.png') }}" class="logo" alt="JNU"></td>
                    <td class="center-text">
                        <h4 class="main-title"><strong>NATIONAL COUNCIL FOR HOTEL MANAGEMENT AND CATERING
                                TECHNOLOGY</strong></h4>
                        <h4 class="subtitle">A-34, SECTOR-62, NOIDA-201301</h4>
                        <h4 class="admission-ticket">ADMISSION TICKET</h4>
                    </td>
                    <td><img src="{{ asset('/assets/imgs/logo.png') }}" class="logo" alt="NCHMCT"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="heading">
        <table>
            <tbody class="line-height-small">
                <tr>
                    <td><strong>CENTRE:</strong></td>
                    <td class="bold uppercase">{{ $singlecard['InstituteName'] }}</td>
                </tr>
                <tr>
                    <td><strong>COURSE:</strong></td>
                    <td class="bold uppercase">B.SC.H&HA</td>
                </tr>
                <tr>
                    <td><strong>ROLL NO:</strong></td>
                    <td class="bold">{{ $singlecard['NCHMCT_Rollnumber'] }}</td>
                </tr>
                <tr>
                    <td><strong>STUDENT NAME:</strong></td>
                    <td class="bold uppercase">{{ $singlecard['name'] }}</td>
                </tr>
                <tr>
                    <td><strong>EXAM TYPE:</strong></td>
                    <td class="bold">REGULAR</td>
                </tr>
                <tr>
                    <td><strong>ACADEMIC YEAR:</strong></td>
                    <td class="bold">{{ $singlecard['Stud_academic_year'] }}</td>
                </tr>
                <tr>
                    <td><strong>SEMESTER:</strong></td>
                    <td class="bold">{{ $singlecard['Semester'] }}</td>

                </tr>

                <tr>
                    <td><strong>DATE OF ISSUE:</strong></td>
                    <td class="bold">{{ date('d-M-Y') }}</td>
                </tr>
                <tr>
                    <td><strong>APPEARING SUBJECTS:</strong></td>
                    <td class="bold">{{ $singlecard['IT_Appear_subject'] }}</td>
                </tr>
            </tbody>
        </table>
        <div class="photo-placeholder bold">Paste your photograph</div>
    </div>
    <div class="signature-section">
        <table class="full-width">
            <tbody class="line-height-large">
                <tr>
                    <td class="text-left"><strong>SIGNATURE OF CANDIDATE</strong></td>
                    <td class="text-right"><strong>SIGNATURE OF PRINCIPAL</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="important-note bold">
        <p><strong style="text-decoration:underline;">IMPORTANT:</strong> The Principal may issue this ticket after
            ascertaining eligibility as per the rules and after the attestation of the photo and signature of the
            candidate.</p>
    </div>
    <div class="instructions bold">
        <h3 class="text-center">INSTRUCTION</h3>
        <ol class="instruction-list">
            <li>Admission into the Examination hall will be only on the production of Admission Ticket.</li>
            <li>Candidates should take their seat at least ten minutes prior to the commencement of the examination.
            </li>
            <li>Candidates are advised to read do's and don'ts for the examination.</li>
            <li>No candidates will be allowed to carry any paper other than the admission ticket.</li>
            <li>Personal calculators, any kind of smart watches, mobile phones, or any other electronic/communication
                devices are <strong style="text-decoration:underline;">STRICTLY PROHIBITED</strong> inside the
                examination hall.</li>
            <li>During the examination, candidates may be checked for the possession of any of the prohibited items. If
                the candidate is found to possess any of these items, he/she will be liable to be debarred from the
                examination and/or subjected to disciplinary action.</li>
        </ol>
        <div class="signature-controller">
            <img src="{{ asset('/assets/imgs/signature.png') }}" class="signature-img" alt="Dr. SATVIR SINGH">
            <p><strong>Dr. SATVIR SINGH</strong></p>
            <strong>(CONTROLLER OF EXAMINATION)</strong>
        </div>
    </div>
</div>
@endforeach
@endsection