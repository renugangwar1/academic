@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-dark text-white text-center">
            <h4>Enrollment Preview Form </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Header Details (Left Side) -->
                <div class="text-center">
                    <h5 class="text-center">JAWAHARLAL NEHRU UNIVERSITY</h5>
                    <h6 class="text-center">National Council for Hotel Management and Catering Technology</h6>
                </div>

                <!-- Image Section (Top Right) -->



            </div>
            <hr>

            <!-- Admission Details -->
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3"><strong>Year of Enrollment:</strong> {{ $data['year_of_admission'] ?? 'N/A' }}
                    </div>
                    <div class="mb-3"><strong>Name of Institute: </strong>{{ $data['institute_name'] ?? 'N/A' }} </div>
                    <div class="mb-3"><strong>Name of the Chapter (IHM): </strong> {{ $data['chapter_name'] ?? 'N/A' }}
                    </div>
                    <div class="mb-3"><strong>Programme of Study: </strong> {{ $data['programme_of_study'] ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4 d-flex flex-column align-items-center">
                    <div class="text-center">
                        <img id="student_image" name="student_image"
                            src="{{ isset($data['student_image']) && file_exists(public_path($data['student_image'])) ? asset($data['student_image']) : asset('uploads/main') }}"
                            class="img-fluid rounded border shadow-sm"
                            style="max-width: 150px; max-height: 150px; height: auto;" alt="Student Photo">
                        <p class="mt-2 fw-bold">Student Photograph</p>
                    </div>
                </div>
            </div>

            <h5 class="mt-4">Student Details</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>NAME OF STUDENT (BLOCK LETTERS IN ENGLISH):</strong>
                    {{ $data['student_name_en'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Name of the Student in Hindi:</strong>
                    {{ $data['student_name_hi'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Date of Birth:</strong>
                    {{ \Carbon\Carbon::parse($data['date_of_birth'])->format('d/m/Y') ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Category:</strong> {{ $data['category'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>PwBD Category:</strong> {{ $data['pwbd_category'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>HH/OH/VH:</strong> {{ $data['hh_oh_vh'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Percentage:</strong> {{ $data['percentage'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>NCHM Roll No:</strong> {{ $data['nchm_roll_no'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Father's Name:</strong> {{ $data['father_name'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Father's Mobile No:</strong> {{ $data['father_mobile'] ?? 'N/A' }}
                </li>
                <li class="list-group-item"><strong>Mother's Name:</strong> {{ $data['mother_name'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Name of Guardian (if father is deceased):</strong>
                    {{ $data['guardian_name'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Local Address:</strong> {{ $data['local_address'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Permanent Address:</strong>
                    @if(!empty($data['permanent_address']))
                    {{ $data['permanent_address'] }}
                    @elseif(!empty($data['local_address']) && isset($data['same_as_local_address']) &&
                    $data['same_as_local_address'] == 1)
                    {{ $data['local_address'] }} (Same as Local Address)
                    @else
                    N/A
                    @endif
                </li>
                <li class="list-group-item"><strong>Nationality:</strong> {{ $data['nationality'] ?? 'N/A' }}</li>
                <li class="list-group-item">
                    <strong>
                        <!-- Change the label based on nationality -->
                        @if($data['nationality'] === 'Foreign')
                        Country Name:
                        @else
                        State of Domicile :
                        @endif
                    </strong>
                    {{ $data['state_of_domicile'] ?? 'N/A' }}
                </li>

                <li class="list-group-item"><strong>Student Email:</strong> {{ $data['student_email'] ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Student Mobile No.:</strong> {{ $data['student_mobile'] ?? 'N/A' }}
                </li>
                <li class="list-group-item"><strong>ABC ID:</strong> {{ $data['abc_id'] ?? 'N/A' }}</li>
            </ul>

            <h5 class="mt-3"> Academic Record (Take into account only marks in the subject which are counted for
                awarding
                class/division)</h5>
            <table class="table table-bordered">
                <thead class="table-dark text-white">
                    <tr>
                        <th>Name of Examination</th>
                        <th>Name of the Board/University</th>
                        <th>School/College/Institute</th>
                        <th>Year of Passing</th>
                        <th>Subject</th>
                        <th>Percentage/Division</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10th</td>
                        <td>{{ $data['board_10th'] ?? 'N/A' }}</td>
                        <td>{{ $data['school_10th'] ?? 'N/A' }}</td>
                        <td>{{ $data['year_10th'] ?? 'N/A' }}</td>
                        <td>{{ $data['subject_10th'] ?? 'N/A' }}</td>
                        <td>{{ $data['percentage_10th'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>12th</td>
                        <td>{{ $data['board_12th'] ?? 'N/A' }}</td>
                        <td>{{ $data['school_12th'] ?? 'N/A' }}</td>
                        <td>{{ $data['year_12th'] ?? 'N/A' }}</td>
                        <td>{{ $data['subject_12th'] ?? 'N/A' }}</td>
                        <td>{{ $data['percentage_12th'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Other</td>
                        <td>{{ $data['board_other'] ?? 'N/A' }}</td>
                        <td>{{ $data['school_other'] ?? 'N/A' }}</td>
                        <td>{{ $data['year_other'] ?? 'N/A' }}</td>
                        <td>{{ $data['subject_other'] ?? 'N/A' }}</td>
                        <td>{{ $data['percentage_other'] ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
            <p> Certified that the Particulars given by student at the time of admission have been verified by the
                institute from the origional records</p>
            <div class="mt-4">
                <form action="{{ route('institute.previewEnrollment.store') }}" method="POST" class="d-inline"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="data" value="{{ json_encode($data) }}">
                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection