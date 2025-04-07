@extends('layouts.html')


@section('content')
<style>
body {
    font-family: 'Arial', 'NotoSansDevanagari', sans-serif;
    font-size: 12px;
    color: #000;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.page-border {


    margin: 0;
    padding: 12px;
    width: 210mm;
    height: 297mm;
    border: 2px solid black;
    box-sizing: border-box;

    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 10px;
    box-sizing: border-box;
    background-color: #f9f9f9;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

}

h1 {
    text-align: center;
    font-weight: bold;
    font-size: 20px;
    text-transform: uppercase;
    text-decoration: underline;
    margin: 5px 0;
}

.form-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    width: 100%;
}

.form-section {
    flex: 3;
    padding-right: 10px;
    width: 100%;
}

.image-section {
    width: 100px;
    height: 100px;
    border: 1px solid #000;
    border-radius: 6px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.student-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.form-group label {
    font-weight: bold;
}

.table {
    width: 100%;
    /* Adjust as needed */
    border-collapse: collapse;
    margin: 10px auto;
    /* Centers the table */
    font-size: 10px;
    text-align: center;
    /* Ensures text is centered */
}


.table th,
.table td {
    border: 1px solid black !important;
    padding: 4px;
    text-align: left;
}

.student-signature,
.principal-signature {
    display: inline-block;
    width: 45%;
    vertical-align: middle;
}

.student-signature {
    text-align: left;
}

.principal-signature {
    text-align: right;
}

.btn {
    display: block;
    width: 100px;
    margin: 10px auto;
    padding: 5px;
    text-align: center;
    background-color: green;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

.space-label {
    margin-left: 20px;
    /* Adjust the margin based on your requirement */
}

@media print {
    body {
        font-size: 12px;
        margin: 0;
        padding: 0;
        display: block;

    }

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    @page {

        margin: 8mm 0;
    }

    .page-border {
        margin: 0;
        width: 100%;
        height: 100%;
        border: 2px solid black;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 10px;
        box-sizing: border-box;
        background-color: #f9f9f9;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

    }

    .table {
        width: 100%;
        /* Reduce width slightly to ensure it fits */
        border-collapse: collapse;
        margin: 10px auto;
        /* Centers the table */
        font-size: 10px;
        table-layout: fixed;
        /* Ensures columns maintain proper width */
    }

    .table th,
    .table td {
        border: 1px solid black !important;
        padding: 4px;
        text-align: left;
        word-wrap: break-word;
        /* Prevents text from overflowing */
        overflow: hidden;
    }

    .table th:nth-child(6),
    .table td:nth-child(6) {
        width: 12%;
        /* Reduce the last column width */
    }

    .table th:nth-child(2),
    .table td:nth-child(2),
    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 20%;
        /* Adjust widths to distribute space properly */
    }

    .table th:nth-child(1),
    .table td:nth-child(1),
    .table th:nth-child(4),
    .table td:nth-child(4),
    .table th:nth-child(5),
    .table td:nth-child(5) {
        width: 15%;
    }


    .btn {
        display: none !important;
    }

    html,
    body {
        height: 100%;
        overflow: hidden;
    }


}
</style>

<div class="page-border">
    <div class="container">
        <h1 class="text-center">Jawaharlal Nehru University<br>Enrollment Form</h1>
        <br>
        <div class="form-row">
            <div class="form-section">

                <div class="form-group"><label class="label">Year of Enrollment:</label>
                    {{ $enrollment->year_of_admission ?? '___________' }}
                </div>
                <div class="form-group">
                    <label class="label">Name of the Institute:</label>
                    {{ $enrollment->institute_name ?? '_______________________' }}
                </div>
                <div class="form-group"><label class="label">Name of the Chapter (IHM):</label>
                    {{ $enrollment->chapter_name ?? '_____________________' }}
                </div>
                <div class="form-group"><label class="label">Programme of Study:</label>
                    {{ $enrollment->programme_of_study ?? '_____________________' }}
                </div>
            </div>
            <div class="image-section">
                <img src="{{ asset('uploads/main/' . basename($enrollment->student_image)) }}" alt="Student Image"
                    class="student-image">
            </div>




        </div>

        <hr>
        <div class="form-group"><label class="label">1. NAME OF STUDENT (BLOCK LETTERS IN ENGLISH):</label>
            {{ $enrollment->student_name_en ?? '_________' }}
        </div>
        <div class="form-group"><label class="label">2. Name of the Student in Hindi:</label>
            <span class="hindi-text">{{ $enrollment->student_name_hi }}</span>
        </div>
        <div class="form-group"><label class="label">3. Date of Birth:</label>
            {{ isset($enrollment->date_of_birth) ? \Carbon\Carbon::parse($enrollment->date_of_birth)->format('d/m/Y') : 'DD/MM/YYYY' }}
        </div>
        <div class="form-group"><label class="label">4. Category (General/OBC-NCL/SC/ST):</label>
            {{ $enrollment->category ?? '________' }}
        </div>
        <div class="form-group"><label class="label">5. Do you belong to PwBD Category?</label>
            {{ $enrollment->pwbd_category ? 'Yes' : 'No' }}
        </div>
        <div class="form-group"><label class="label">(a) (HH/OH/VH) </label>
            {{ $enrollment->hh_oh_vh ?? '________' }}
            <label class="label space-label">(b) Percentage: </label>
            {{ $enrollment->percentage ?? '____%' }}
        </div>
        <div class="form-group"><label class="label">6. NCHM Roll No:</label>
            {{ $enrollment->nchm_roll_no ?? '________' }}
        </div>
        <div class="form-group"><label class="label">7. Father's Name:</label>
            {{ $enrollment->father_name ?? '________' }}
        </div>
        <div class="form-group"><label class="label">8. Father's Mobile No:</label>
            {{ $enrollment->father_mobile ?? '________' }}
        </div>
        <div class="form-group"><label class="label">9. Mother's Name:</label>
            {{ $enrollment->mother_name ?? '________' }}
        </div>
        <div class="form-group"><label class="label">10. Name of Guardian (if father is deceased):</label>
            {{ $enrollment->guardian_name ?? 'N/A' }}
        </div>
        <div class="form-group"><label class="label">11. Local Address:</label>
            {{ $enrollment->local_address ?? 'N/A' }}
        </div>
        <div class="form-group"><label class="label">12. Permanent Address:</label>
            {{ $enrollment->permanent_address ?? 'N/A' }}
        </div>
        <div class="form-group">
            @php
            $nationality = $enrollment->nationality ?? 'N/A';
            $domicileLabel = ($nationality == 'Indian') ? 'State of Domicile' : 'Country Name';
            @endphp


            <label class="label">13. Nationality:</label>
            {{ $nationality }}


            <label class="label space-label">14. {{ $domicileLabel }}:</label>
            {{ $enrollment->state_of_domicile ?? 'N/A' }}
        </div>

        <div class="form-group"><label class="label">15. Student Email:</label>
            {{ $enrollment->student_email ?? 'N/A' }}
            <label class="label space-label">16. Student Mobile No.:</label>
            {{ $enrollment->student_mobile ?? 'N/A' }}
        </div>
        <div class="form-group"><label class="label">17. ABC ID:</label>
            {{ $enrollment->abc_id ?? 'N/A' }}
        </div>
        <div class="form-group"><label class="label">18. Academic Record (Take into account only marks in the subject
                which
                are counted for
                awarding
                class/division)</label>

            <table class="table">
                <thead>
                    <tr>
                        <th>Name of Examination</th>
                        <th>Name of the Board/University</th>
                        <th>School/College/Institute</th>
                        <th>Year of Passing</th>
                        <th>Subject</th>
                        <th>Percentage/Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10th</td>
                        <td>{{ $enrollment->board_10th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->school_10th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->year_10th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->subject_10th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->percentage_10th ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>12th</td>
                        <td>{{ $enrollment->board_12th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->school_12th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->year_12th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->subject_12th ?? 'N/A' }}</td>
                        <td>{{ $enrollment->percentage_12th ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Other</td>
                        <td>{{ $enrollment->board_other ?? 'N/A' }}</td>
                        <td>{{ $enrollment->school_other ?? 'N/A' }}</td>
                        <td>{{ $enrollment->year_other ?? 'N/A' }}</td>
                        <td>{{ $enrollment->subject_other ?? 'N/A' }}</td>
                        <td>{{ $enrollment->percentage_other ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group"><label class="label">Certified that the particulars given by the student at the time
                    of
                    admission have been verified by the
                    Institute
                    from the original records.</label>

                <br><br><br><br> <br> <br>
                <p class="student-signature">Student Signature: </p>
                <p class="principal-signature">Principal Signature with Stamp and Date: </p>
                <BR></BR>


                <button onclick="printForm()" class="btn btn-success">Print</button>

                <script>
                function printForm() {
                    window.print();
                }
                </script>



                @endsection