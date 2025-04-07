@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('New Enrollment') }}

                    <!-- Button trigger modal -->


                    <div>
                        @if (auth('institute')->user()->type === 'master')
                        <button id="toggleButton"
                            class="btn {{ cache('master_toggle', 0) ? 'btn-success' : 'btn-danger' }} toggle-access-btn"
                            onclick="toggleAccess()">
                            <i id="toggleIcon" class="bi bi-toggle-{{ cache('master_toggle', 0) ? 'on' : 'off' }}"></i>
                            <span id="toggleText">{{ cache('master_toggle', 0) ? 'Active' : 'Inactive' }}</span>
                        </button>
                        @endif


                        <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            Add New Student
                        </button>
                    </div>
                </div>


                <div class="card-body bg-secondary rounded-bottom-1">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table id="newenrollment"
                            class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" class="text-truncate">Year of Admission</th>
                                    <th scope="col" class="text-truncate">Name of Institute</th>
                                    <th scope="col" class="text-truncate">Name of Chapter</th>
                                    <th scope="col" class="text-truncate">Programme of Study</th>
                                    <th scope="col" class="text-truncate">Name of the Student (En)</th>
                                    <th scope="col" class="text-truncate">Name of the Student (Hi)</th>
                                    <th scope="col" class="text-truncate">Date of Birth</th>
                                    <th scope="col" class="text-truncate">Category</th>
                                    <th scope="col" class="text-truncate">PwBD Category</th>
                                    <th scope="col" class="text-truncate">HH/OH/VH</th>
                                    <th scope="col" class="text-truncate">Percentage</th>
                                    <th scope="col" class="text-truncate">Father's Name</th>
                                    <th scope="col" class="text-truncate">Father's Mobile No</th>
                                    <th scope="col" class="text-truncate">Mother's Name</th>
                                    <th scope="col" class="text-truncate">Name of Guardian (if father is deceased)
                                    </th>
                                    <th scope="col" class="text-truncate">Local Address with Pin Code</th>
                                    <th scope="col" class="text-truncate">Permanent Address with Pin Code</th>
                                    <th scope="col" class="text-truncate">State of Domicile</th>
                                    <th scope="col" class="text-truncate">Nationality</th>
                                    <th scope="col" class="text-truncate">Student's Email ID</th>
                                    <th scope="col" class="text-truncate">Student's Mobile No</th>
                                    <th scope="col" class="text-truncate">ABC (Academic Bank of Credit) ID</th>
                                    <th scope="col" class="text-truncate">Image</th>
                                    <th scope="col" class="text-truncate">NCHM Roll Number</th>
                                    <th scope="col" class="text-truncate">Board (10th)</th>
                                    <th scope="col" class="text-truncate">School (10th)</th>
                                    <th scope="col" class="text-truncate">Year (10th)</th>
                                    <th scope="col" class="text-truncate">Subject (10th)</th>
                                    <th scope="col" class="text-truncate">Percentage (10th)</th>
                                    <th scope="col" class="text-truncate">Board (12th)</th>
                                    <th scope="col" class="text-truncate">School (12th)</th>
                                    <th scope="col" class="text-truncate">Year (12th)</th>
                                    <th scope="col" class="text-truncate">Subject (12th)</th>
                                    <th scope="col" class="text-truncate">Percentage (12th)</th>
                                    <th scope="col" class="text-truncate">Board (Other)</th>
                                    <th scope="col" class="text-truncate">School (Other)</th>
                                    <th scope="col" class="text-truncate">Year (Other)</th>
                                    <th scope="col" class="text-truncate">Subject (Other)</th>
                                    <th scope="col" class="text-truncate">Percentage (Other)</th>
                                    <th scope="col" class="text-truncate">Action</th>
                                </tr>
                            </thead>

                            <tbody class="table-group-divider">
                                @foreach ($enrollments as $key => $enrollment)
                                <tr>
                                    <th scope="row">{{ $key + 1 }}</th>
                                    <td class="text-uppercase">{{ $enrollment->year_of_admission }}</td>
                                    <td class="text-uppercase">{{ $enrollment->institute_name }}</td>
                                    <td class="text-uppercase">{{ $enrollment->chapter_name }}</td>
                                    <td class="text-uppercase">{{ $enrollment->programme_of_study }}</td>
                                    <td class="text-capitalize">{{ $enrollment->student_name_en }}</td>
                                    <td>{{ $enrollment->student_name_hi }}</td>
                                    <td>{{ $enrollment->date_of_birth }}</td>
                                    <td class="text-uppercase">{{ $enrollment->category }}</td>
                                    <td class="text-uppercase">{{ $enrollment->pwbd_category }}</td>
                                    <td class="text-uppercase">{{ $enrollment->hh_oh_vh }}</td>
                                    <td>{{ $enrollment->percentage }}</td>
                                    <td class="text-capitalize">{{ $enrollment->father_name }}</td>
                                    <td>{{ $enrollment->father_mobile }}</td>
                                    <td class="text-capitalize">{{ $enrollment->mother_name }}</td>
                                    <td class="text-capitalize">{{ $enrollment->guardian_name }}</td>
                                    <td class="text-start">{{ $enrollment->local_address }}</td>
                                    <td class="text-start">{{ $enrollment->permanent_address }}</td>
                                    <td class="text-uppercase">{{ $enrollment->state_of_domicile }}</td>
                                    <td class="text-uppercase">{{ $enrollment->nationality }}</td>
                                    <td class="text-lowercase">{{ $enrollment->student_email }}</td>
                                    <td>{{ $enrollment->student_mobile }}</td>
                                    <td class="text-uppercase">{{ $enrollment->abc_id }}</td>
                                    <td>
                                        <img src="{{ asset($enrollment->student_image) }}"
                                            class="img-fluid rounded-circle" width="40px" height="40px"
                                            alt="Student Image">
                                    </td>


                                    <td class="text-uppercase">{{ $enrollment->nchm_roll_no }}</td>
                                    <td>{{ $enrollment->board_10th }}</td>
                                    <td>{{ $enrollment->school_10th }}</td>
                                    <td>{{ $enrollment->year_10th }}</td>
                                    <td>{{ $enrollment->subject_10th }}</td>
                                    <td>{{ $enrollment->percentage_10th }}</td>
                                    <td>{{ $enrollment->board_12th }}</td>
                                    <td>{{ $enrollment->school_12th }}</td>
                                    <td>{{ $enrollment->year_12th }}</td>
                                    <td>{{ $enrollment->subject_12th }}</td>
                                    <td>{{ $enrollment->percentage_12th }}</td>
                                    <td>{{ $enrollment->board_other }}</td>
                                    <td>{{ $enrollment->school_other }}</td>
                                    <td>{{ $enrollment->year_other }}</td>
                                    <td>{{ $enrollment->subject_other }}</td>
                                    <td>{{ $enrollment->percentage_other }}</td>
                                    <td>
                                        <div class="d-flex gap-2">

                                            <a href="{{ route('institute.enrollment.previewF', $enrollment->id) }}"
                                                class="btn btn-primary">
                                                <i class="bi bi-arrow-down-square-fill"></i>
                                            </a>

                                            <button id="updateButton_{{ $enrollment->id }}"
                                                class="btn btn-success font-monospace update-btn" data-bs-toggle="modal"
                                                data-bs-target="#updateenrollmentModal"
                                                onclick="updateEnrollment({{ json_encode($enrollment) }})" type="button"
                                                style="display: none;">
                                                {{ session('master_toggle', 1) ? '' : 'display: none;' }}

                                                Update
                                            </button>
                                            <button id="deleteButton_{{ $enrollment->id }}"
                                                class="btn btn-danger font-monospace delete-btn"
                                                onclick="confirmDeletion('{{ $enrollment->id }}')"
                                                style="display: none;">
                                                {{ session('master_toggle', 1) ? '' : 'display: none;' }}
                                                Delete
                                            </button>





                                            <form id="delete-form-{{ $enrollment->id }}"
                                                action="{{ route('institute.deleteEnrollment') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                <input type="hidden" value="{{ $enrollment->id }}" name="id" />
                                            </form>

                                        </div>
                                        <br>
                                        @if (auth('institute')->user()->type === 'master')
                                        <button id="reopenToggle_{{ $enrollment->id }}"
                                            class="btn btn-warning reopen-btn px-4 text-center"
                                            style="min-width: 180px;" data-id="{{ $enrollment->id }}"
                                            onclick="toggleReopen({{ $enrollment->id }})">
                                            <i id="reopenIcon_{{ $enrollment->id }}"
                                                class="bi bi-toggle-{{ cache('reopen_toggle_' . $enrollment->id, false) ? 'on' : 'off' }}"></i>
                                            <span id="reopenText_{{ $enrollment->id }}">
                                                {{ cache('reopen_toggle_' . $enrollment->id, false) ? 'Reopen Enabled' : 'Reopen Disabled' }}
                                            </span>
                                        </button>
                                        @endif



                                    </td>
                                </tr>
                                @endforeach


                                <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    let globalToggle = localStorage.getItem("toggleState") === "true";

                                    document.querySelectorAll(".update-btn, .delete-btn").forEach(button => {
                                        let enrollmentId = button.id.split("_")[1];
                                        let reopenToggle = localStorage.getItem("reopenToggle_" +
                                            enrollmentId) === "true";

                                        if (globalToggle || reopenToggle) {
                                            button.style.display = "inline-block";
                                        } else {
                                            button.style.display = "none";
                                        }
                                    });
                                });


                                function updateButtonVisibility(enrollmentId) {
                                    let globalToggle = localStorage.getItem("toggleState") === "true"; // Master Toggle
                                    let reopenToggle = localStorage.getItem("reopenToggle_" + enrollmentId) ===
                                        "true"; // Enrollment Toggle

                                    let updateButton = document.getElementById("updateButton_" + enrollmentId);
                                    let deleteButton = document.getElementById("deleteButton_" + enrollmentId);

                                    if (updateButton && deleteButton) {
                                        if (globalToggle || reopenToggle) { // Show buttons if any toggle is ON
                                            updateButton.style.display = "inline-block";
                                            deleteButton.style.display = "inline-block";
                                        } else { // Hide buttons if both are OFF
                                            updateButton.style.display = "none";
                                            deleteButton.style.display = "none";
                                        }
                                    }
                                }


                                function toggleReopen(enrollmentId) {
                                    fetch("{{ url('/institute/enrollment/toggleEnrollment') }}", {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                            },
                                            body: JSON.stringify({
                                                enrollment_id: enrollmentId
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (!data.success) {
                                                throw new Error(data.error || "Toggle failed");
                                            }

                                            // Store the new toggle state
                                            localStorage.setItem("reopenToggle_" + enrollmentId, data
                                                .reopen_toggle ? "true" : "false");

                                            // Update the toggle button UI
                                            document.getElementById("reopenIcon_" + enrollmentId).className =
                                                data.reopen_toggle ? "bi bi-toggle-on" : "bi bi-toggle-off";

                                            document.getElementById("reopenText_" + enrollmentId).textContent =
                                                data.reopen_toggle ? "Reopen Enabled" : "Reopen Disabled";


                                            updateButtonVisibility(enrollmentId);
                                        })
                                        .catch(error => console.error("Error toggling reopen state:", error));
                                }
                                </script>





                                <!-- -----------------------------------main toggle button  ---------------------------------->

                                <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    let toggleButton = document.getElementById('toggleButton');
                                    let toggleIcon = document.getElementById('toggleIcon');
                                    let toggleText = document.getElementById('toggleText');

                                    // Function to update UI based on toggle state
                                    function updateToggleUI(state) {
                                        if (toggleButton) {
                                            toggleButton.classList.toggle('btn-success', state);
                                            toggleButton.classList.toggle('btn-danger', !state);
                                            toggleIcon.classList.toggle('bi-toggle-on', state);
                                            toggleIcon.classList.toggle('bi-toggle-off', !state);
                                            toggleText.innerText = state ? "Active" : "Inactive";
                                        }

                                        // Apply visibility changes to all update/delete buttons
                                        document.querySelectorAll(".update-btn, .delete-btn").forEach(
                                            button => {
                                                let enrollmentId = button.id.split("_")[1];
                                                updateButtonVisibility(enrollmentId);
                                            });
                                    }

                                    // Fetch toggle state on page load (from session)
                                    fetch("{{ url('/institute/enrollments/toggle-state') }}")
                                        .then(response => response.json())
                                        .then(data => {
                                            let isActive = data.is_active;
                                            updateToggleUI(isActive);

                                            // Store the toggle state in localStorage for persistence
                                            localStorage.setItem("toggleState", isActive ? "true" :
                                                "false");
                                        })
                                        .catch(error => console.error("Error fetching toggle state:", error));

                                    // Toggle Access Function (only for Master Institute)
                                    window.toggleAccess = function() {
                                        if (!toggleButton) return;

                                        fetch("{{ url('/institute/enrollments/toggle') }}", {
                                                method: "POST",
                                                headers: {
                                                    "Content-Type": "application/json",
                                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                                }
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    let isActive = data.is_active;
                                                    updateToggleUI(isActive);

                                                    // Store state globally and notify other tabs
                                                    localStorage.setItem("toggleState", isActive ?
                                                        "true" : "false");
                                                    window.dispatchEvent(new Event("toggleUpdated"));
                                                } else {
                                                    alert("Error: " + data.error);
                                                }
                                            })
                                            .catch(error => console.error("Error:", error));
                                    };

                                    // Listen for toggle state updates from other tabs
                                    window.addEventListener("toggleUpdated", function() {
                                        let isActive = localStorage.getItem("toggleState") === "true";
                                        updateToggleUI(isActive);
                                    });

                                    // Ensure UI state is preserved on page reload
                                    let storedState = localStorage.getItem("toggleState") === "true";
                                    updateToggleUI(storedState);
                                });
                                </script>

                                <script>
                                // Download button logic
                                document.addEventListener("DOMContentLoaded", function() {
                                    document.querySelectorAll(".download-btn").forEach(button => {
                                        button.addEventListener("click", function() {
                                            let enrollmentId = this.getAttribute("data-id");
                                            window.location.href =
                                                `/institute/institute/enrollment/download/${enrollmentId}`;
                                        });
                                    });
                                });

                                function updateEnrollment(enrollment) {
                                    // Step 1: Check if enrollment data is valid
                                    if (!enrollment || Object.keys(enrollment).length === 0 || !enrollment.id) {
                                        console.warn(
                                            "No valid enrollment data provided. Update form will not be displayed.");
                                        document.getElementById('updateenrollment').innerHTML =
                                            "<p class='text-danger'>No enrollment data available to update.</p>";
                                        return;
                                    }

                                    // Step 2: Set the form action dynamically
                                    let updateFormAction = `/institute/newenrollment/update/${enrollment.id}`;
                                    console.log("Form action URL:", updateFormAction);


                                    // Step 3: Inject the form HTML
                                    document.getElementById('updateenrollment').innerHTML =
                                        `
 <form action="{{ isset($enrollment) ? route('institute.newenrollment.update', ['id' => $enrollment->id]) : '#' }}" method="POST" novalidate>
  <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').getAttribute('content')}">
       <input type="hidden" name="_method" value="PUT">
         <input type="hidden" name="id" value="{{ $enrollment->id ?? '' }}">


   <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="student_name_en" class="form-label">Student Name (English)</label>
                <input type="text" id="student_name_en" name="student_name_en"
                    class="form-control @error('student_name_en') is-invalid @enderror"
                    pattern="[A-Z\s]+" title="Only capital letters allowed" required
                    oninput="validateInput(this)"
                    value="{{ old('student_name_en', $enrollment->student_name_en ?? '') }}">

                <div id="student_name_en_error" class="text-danger" style="display: none;"></div> 

                @error('student_name_en')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="student_name_hi" class="form-label">Student Name (Hindi)</label>
                <input type="text" id="student_name_hi" name="student_name_hi"
                    class="form-control @error('student_name_hi') is-invalid @enderror"
                    pattern="[\u0900-\u097F\s]+" title="Only Hindi characters allowed" required
                    oninput="validateInput(this)"
                    value="{{ old('student_name_hi', $enrollment->student_name_hi ?? '') }}">

                <div id="student_name_hi_error" class="text-danger" style="display: none;"></div> 

                @error('student_name_hi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth"
                    class="form-control @error('date_of_birth') is-invalid @enderror"
                    value="{{ old('date_of_birth', $enrollment->date_of_birth ?? '') }}" 
                    oninput="validateInput(this)" required>

                <div id="date_of_birth_error" class="text-danger" style="display: none;"></div> 

                @error('date_of_birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="abc_id" class="form-label">ABC ID</label>
                <input type="text" id="abc_id" name="abc_id"
                    class="form-control @error('abc_id') is-invalid @enderror"
                    value="{{ old('abc_id', $enrollment->abc_id ?? '') }}" 
                    oninput="validateInput(this)" maxlength="12" required>

             <div id="abc_id_error" class="text-danger" style="display: none;"></div>
                @error('abc_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


 <div class="col-md-6">
            <div class="mb-3">
                <label for="father_mobile" class="form-label">Father's Mobile Number</label>
                <input type="text" id="father_mobile" name="father_mobile"
                    class="form-control @error('father_mobile') is-invalid @enderror"
                    pattern="\d{10}" title="Must be exactly 10 digits" required
                    oninput="validateInput(this)" maxlength="10"
                    value="{{ old('father_mobile', $enrollment->father_mobile ?? '') }}">

                <div id="father_mobile_error" class="text-danger" style="display: none;"></div>

                @error('father_mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>



         <div class="col-md-6">
            <div class="mb-3">
                <label for="guardian_name" class="form-label">guardian name</label>
                <input type="email" id="guardian_name" name="guardian_name"
                    class="form-control @error('guardian_name') is-invalid @enderror"
                    value="{{ old('guardian_name', $enrollment->guardian_name ?? '') }}" 
                    oninput="validateInput(this)" required>

                <div id="guardian_name_error" class="text-danger" style="display: none;"></div>

                @error('guardian_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
</div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="student_email" class="form-label">Student Email</label>
                <input type="email" id="student_email" name="student_email"
                    class="form-control @error('student_email') is-invalid @enderror"
                    value="{{ old('student_email', $enrollment->student_email ?? '') }}" 
                    oninput="validateInput(this)" required>

                <div id="student_email_error" class="text-danger" style="display: none;"></div>

                @error('student_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="student_mobile" class="form-label">Student Mobile</label>
                <input type="text" id="student_mobile" name="student_mobile"
                    class="form-control @error('student_mobile') is-invalid @enderror"
                    pattern="\d{10}" title="Must be exactly 10 digits" required
                    oninput="validateInput(this)" maxlength="10"
                    value="{{ old('student_mobile', $enrollment->student_mobile ?? '') }}">

                <div id="student_mobile_error" class="text-danger" style="display: none;"></div>

                @error('student_mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="permanent_address" class="form-label">Permanent Address</label>
                <textarea id="permanent_address" name="permanent_address"
                    class="form-control @error('permanent_address') is-invalid @enderror"
                    oninput="validateAddress(this)" required>{{ old('permanent_address', $enrollment->permanent_address ?? '') }}</textarea>

               <div id="permanent_address_error" class="text-danger" style="display: none;"></div>

                @error('permanent_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="local_address" class="form-label">Local Address</label>
                <textarea id="local_address" name="local_address"
                    class="form-control @error('local_address') is-invalid @enderror"
                    oninput="validateAddress(this)" required>{{ old('local_address', $enrollment->local_address ?? '') }}</textarea>

              <div id="local_address_error" class="text-danger" style="display: none;"></div>

                @error('local_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-12 text-center">
            <button type="submit" class="btn btn-success" {{ isset($enrollment) ? '' : 'disabled' }}>Update</button>
        </div>
    </div>
</form>
       
        `;





                                }

                                function confirmDeletion(enrollmentId) {
                                    if (confirm("Are you sure you want to delete this enrollment?")) {
                                        document.getElementById(`delete-form-${enrollmentId}`).submit();
                                    }
                                }



                                function validateInput(input) {
                                    const value = input.value;
                                    const id = input.id;

                                    input.classList.remove('is-invalid', 'is-valid');
                                    const errorElement = document.getElementById(id + "_error");
                                    if (errorElement) {
                                        errorElement.style.display = "none"; // Hide the error message
                                    }

                                    // Validation logic for each field
                                    if (id === "student_name_en") {
                                        if (!/^[A-Z\s]+$/.test(value)) {
                                            input.classList.add('is-invalid');
                                            document.getElementById(id + "_error").style.display = "block";
                                            document.getElementById(id + "_error").innerText =
                                                "Only capital letters are allowed!";
                                        } else {
                                            input.classList.add('is-valid');
                                        }
                                    } else if (id === "student_name_hi") {
                                        if (!/^[\u0900-\u097F\s]+$/.test(value)) {
                                            input.classList.add('is-invalid');
                                            document.getElementById(id + "_error").style.display = "block";
                                            document.getElementById(id + "_error").innerText =
                                                "Only Hindi characters are allowed!";
                                        } else {
                                            input.classList.add('is-valid');
                                        }
                                    } else if (id === "student_mobile") {
                                        if (!/^\d{10}$/.test(value)) {
                                            input.classList.add('is-invalid');
                                            document.getElementById(id + "_error").style.display = "block";
                                            document.getElementById(id + "_error").innerText =
                                                "Mobile number must be exactly 10 digits!";
                                        } else {
                                            input.classList.add('is-valid');
                                        }
                                    } else if (id === "father_mobile") {

                                        if (!/^\d{10}$/.test(value)) {
                                            input.classList.add('is-invalid');
                                            document.getElementById("father_mobile_error").style.display = "block";
                                            document.getElementById("father_mobile_error").innerText =
                                                "Mobile number must be exactly 10 digits!";
                                        } else {
                                            input.classList.add('is-valid');
                                        }

                                    } else if (id === "abc_id") {
                                        let errorMessage = document.getElementById("abc_id_error");
                                        let abcPattern = /^\d{12}$/; // Exactly 12 digits

                                        // Remove "N/A" when user focuses on the field
                                        input.addEventListener("focus", function() {
                                            if (input.value.trim() === "0") {
                                                input.value = "";
                                            }
                                            input.classList.remove("is-valid",
                                                "is-invalid"); // Reset validation colors
                                            errorMessage.style.display = "none"; // Hide error message on focus
                                        });

                                        // Allow only numbers while typing
                                        input.addEventListener("input", function() {
                                            input.value = input.value.replace(/\D/g,
                                                ""); // Remove non-numeric characters
                                        });

                                        // Validate on blur (when user leaves the field)
                                        input.addEventListener("blur", function() {
                                            let value = input.value.trim();

                                            if (value === "" || value.toUpperCase() === "0") {
                                                // If empty or "N/A", set to "N/A" and mark as valid
                                                input.value = "0";
                                                input.classList.add("is-valid"); // Green color
                                                input.classList.remove("is-invalid");
                                                errorMessage.style.display = "none"; // Hide error
                                            } else if (!abcPattern.test(value)) {
                                                // If not exactly 12 digits, show error
                                                input.classList.remove("is-valid");
                                                input.classList.add("is-invalid");
                                                errorMessage.textContent = "ABC ID must be exactly 12 digits.";
                                                errorMessage.style.display = "block"; // Show error message
                                            } else {
                                                // If exactly 12 digits, mark as valid
                                                input.classList.add("is-valid");
                                                input.classList.remove("is-invalid");
                                                errorMessage.style.display = "none"; // Hide error message
                                            }
                                        });
                                    } else if (id === "date_of_birth") {
                                        if (value === '') {
                                            input.classList.add('is-invalid');
                                            document.getElementById(id + "_error").style.display = "block";
                                            document.getElementById(id + "_error").innerText =
                                                "Date of birth is required!";
                                        } else {
                                            input.classList.add('is-valid');
                                        }
                                    } else if (id === "guardian_name") {
                                        if (value === '') {
                                            showError(input, "Guardian name is required!");
                                        } else {
                                            input.classList.add('is-valid');
                                        }
                                    } else if (id === "student_email") {
                                        if (value === '') {
                                            input.classList.add('is-invalid');
                                            document.getElementById(id + "_error").style.display = "block";
                                            document.getElementById(id + "_error").innerText =
                                                "This field is required!";
                                        } else {
                                            input.classList.add('is-valid');
                                        }
                                    } else if (id === "permanent_address" || id === "local_address") {
                                        if (value === '') {
                                            showError(input, "This field is required!");
                                        } else if (!/\d{6}/.test(value)) {
                                            showError(input, "Address must contain a 6-digit PIN code!");
                                        } else {
                                            input.classList.add('is-valid');
                                        }
                                    }

                                    function showError(input, errorMessage) {
                                        input.classList.add('is-invalid');
                                        const errorElement = document.getElementById(input.id + "_error");
                                        if (errorElement) {
                                            errorElement.style.display = "block";
                                            errorElement.innerText = errorMessage;
                                        }
                                    }

                                }
                                </script>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80%; width: auto; margin-top: 5%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="enrollmentForm" action="{{ route('institute.previewEnrollment') }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @method('POST')
                        <div class="bg-light p-3 mb-3">
                            <div class="d-flex gap-4 mb-2">
                                <div class="form-group flex-fill">
                                    <label for="year_of_admission">Year of Enrollment *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="year_of_admission"
                                            name="year_of_admission" placeholder="Year of Enrollment"
                                            value="{{ old('year_of_admission') }}" maxlength="4" required>
                                        <div class="invalid-feedback">Please enter a valid 4-digit year {{ date('Y') }}
                                            .</div>
                                    </div>

                                </div>



                                <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    let yearField = document.getElementById("year_of_admission");
                                    let errorMessage = yearField.closest(".form-group").querySelector(
                                        ".invalid-feedback"); // Get correct error message

                                    yearField.addEventListener("input", function() {
                                        validateYear(this);
                                    });

                                    function validateYear(inputField) {
                                        const yearRegex = /^(19|20)\d{2}$/; // Allows years from 1900-2099
                                        const currentYear = new Date().getFullYear();
                                        const financialYearStart = currentYear -
                                            1; // Allow only the current financial year or later
                                        const value = inputField.value.trim();

                                        if (value === "" || !yearRegex.test(value) || parseInt(value) <
                                            financialYearStart) {
                                            inputField.classList.remove("is-valid");
                                            inputField.classList.add("is-invalid");
                                            errorMessage.style.display = "block"; // Show error message
                                        } else {
                                            inputField.classList.remove("is-invalid");
                                            inputField.classList.add("is-valid");
                                            errorMessage.style.display = "none"; // Hide error message
                                        }
                                    }

                                })
                                </script>






                                <div class="form-group flex-fill">
                                    <label for="institute_name">Institute Name*</label>
                                    <input type="text" class="form-control" id="institute_name" name="institute_name"
                                        value=" National Council For Hotel Management and Catering Technology, Noida"
                                        readOnly>
                                </div>
                            </div>



                            <!--
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="form-group flex-fill">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label for="institute_name">Institute Name</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="form-control">{{ Auth::guard('institute')->user()->InstituteName }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <input type="hidden" class="form-control" id="institute_id" name="institute_id"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            value="{{ Auth::guard('institute')->user()->id }}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->

                            <div class="d-flex gap-4 mb-2">
                                <div class="form-group flex-fill">
                                    <label for="chapter_name">Name of Chapter *</label>
                                    <input type="hidden" name="institute_id" value="{{ $institute->id }}">
                                    <input type="text" class="form-control" id="chapter_name" name="chapter_name"
                                        value="{{ $institute->InstituteName }}" readonly>

                                </div>
                                <div class="form-group flex-fill">
                                    <label for="programme_of_study">Programme of Study *</label>
                                    <select class="form-select" id="programme_of_study" name="programme_of_study"
                                        required>
                                        <option selected disabled value="">Select Programme</option>
                                        <option value="B.Sc HHA">B.Sc HHA</option>
                                        <option value="M.Sc HA">M.Sc HA</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a valid Programme of Study.</div>
                                </div>

                                <script>
                                document.getElementById('programme_of_study').addEventListener('change', function() {
                                    validateProgramme(this);
                                });

                                function validateProgramme(selectField) {
                                    if (!selectField.value.trim()) {
                                        selectField.classList.remove("is-valid");
                                        selectField.classList.add("is-invalid");
                                    } else {
                                        selectField.classList.remove("is-invalid");
                                        selectField.classList.add("is-valid");
                                    }
                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    let programmeField = document.getElementById('programme_of_study');
                                    validateProgramme(programmeField);

                                    if (programmeField.classList.contains("is-invalid")) {
                                        event.preventDefault(); // Stop form submission if validation fails
                                        alert("Please select a valid Programme of Study.");
                                    }
                                });
                                </script>

                            </div>
                            <div class="d-flex gap-4 mb-2">
                                <div class="d-flex gap-4 mb-2">
                                    <div class="form-group flex-fill">
                                        <label for="student_name_en">NAME OF STUDENT (BLOCK LETTERS IN ENGLISH)
                                            *</label>
                                        <input type="text"
                                            class="form-control {{ $errors->has('student_name_en') ? 'is-invalid' : (old('student_name_en') ? 'is-valid' : '') }}"
                                            id="student_name_en" name="student_name_en"
                                            placeholder="Student Name (BLOCK LETTERS)"
                                            value="{{ old('student_name_en') }}" required>

                                        <div class="invalid-feedback">Please enter the name of student (BLOCK LETTERS IN
                                            ENGLISH).</div>
                                    </div>
                                </div>

                                <script>
                                document.getElementById('student_name_en').addEventListener('input', function() {
                                    this.value = this.value.toUpperCase(); // Convert input to uppercase
                                    validateStudentName(this);
                                });

                                function validateStudentName(inputField) {
                                    const regex = /^[A-Z\s]+$/; // Only uppercase letters and spaces allowed
                                    const checkmark = document.getElementById("nameCheck");

                                    if (!inputField.value.trim()) {
                                        inputField.classList.remove("is-valid");
                                        inputField.classList.add("is-invalid");
                                        checkmark.classList.add("d-none"); // Hide checkmark
                                    } else if (!regex.test(inputField.value)) {
                                        inputField.classList.remove("is-valid");
                                        inputField.classList.add("is-invalid");
                                        checkmark.classList.add("d-none"); // Hide checkmark
                                        inputField.nextElementSibling.textContent =
                                            "Only uppercase letters (A-Z) and spaces are allowed.";
                                    } else {
                                        inputField.classList.remove("is-invalid");
                                        inputField.classList.add("is-valid");
                                        checkmark.classList.remove("d-none"); // Show checkmark ✅
                                    }


                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    let studentNameField = document.getElementById('student_name_en');
                                    validateStudentName(studentNameField);

                                    if (studentNameField.classList.contains("is-invalid")) {
                                        event.preventDefault(); // Stop form submission if validation fails
                                        alert("Please enter the student's name in BLOCK LETTERS (A-Z only).");
                                    }
                                });
                                </script>






                                <div class="form-group flex-fill">
                                    <label for="student_name_hi">Name of the Student in Hindi *</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('student_name_hi') ? 'is-invalid' : (old('student_name_hi') ? 'is-valid' : '') }}"
                                        id="student_name_hi" name="student_name_hi"
                                        placeholder="छात्र का नाम (हिन्दी में)" value="{{ old('student_name_hi') }}"
                                        required>

                                    <div class="invalid-feedback">Please enter the student's name in Hindi (only Hindi
                                        letters are allowed).</div>
                                </div>

                                <script>
                                document.getElementById('student_name_hi').addEventListener('input', function() {
                                    validateHindiName(this);
                                });

                                function validateHindiName(inputField) {
                                    const hindiRegex =
                                        /^[\u0900-\u097F\s]+$/; // Only Hindi characters and spaces allowed
                                    const inputValue = inputField.value.trim();

                                    if (inputValue === "") {
                                        inputField.classList.remove("is-valid");
                                        inputField.classList.add("is-invalid");
                                        inputField.nextElementSibling.textContent = "This field is required.";
                                    } else if (!hindiRegex.test(inputValue)) {
                                        inputField.classList.remove("is-valid");
                                        inputField.classList.add("is-invalid");
                                        inputField.nextElementSibling.textContent =
                                            "Only Hindi characters are allowed.";
                                    } else {
                                        inputField.classList.remove("is-invalid");
                                        inputField.classList.add("is-valid");
                                    }
                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    let studentNameField = document.getElementById('student_name_hi');
                                    validateHindiName(studentNameField);

                                    if (studentNameField.classList.contains("is-invalid")) {
                                        event.preventDefault(); // Stop form submission if validation fails
                                        alert("Please enter the student's name correctly in Hindi (अ-ह only).");
                                    }
                                });
                                </script>



                                <div class="form-group flex-fill">
                                    <label for="date_of_birth">Date of Birth *</label>
                                    <input type="date"
                                        class="form-control {{ $errors->has('date_of_birth') ? 'is-invalid' : (old('date_of_birth') ? 'is-valid' : '') }}"
                                        id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                        required>

                                    <div class="invalid-feedback">Please enter a valid Date of Birth (cannot be a
                                        future
                                        date).</div>
                                </div>

                                <script>
                                document.getElementById('date_of_birth').addEventListener('input', function() {
                                    validateDOB(this);
                                });

                                function validateDOB(inputField) {
                                    const today = new Date().toISOString().split("T")[
                                        0]; // Get today's date in YYYY-MM-DD format
                                    const inputValue = inputField.value;

                                    if (!inputValue) {
                                        inputField.classList.remove("is-valid");
                                        inputField.classList.add("is-invalid");
                                        inputField.nextElementSibling.textContent = "This field is required.";
                                    } else if (inputValue > today) {
                                        inputField.classList.remove("is-valid");
                                        inputField.classList.add("is-invalid");
                                        inputField.nextElementSibling.textContent =
                                            "Date of Birth cannot be in the future.";
                                    } else {
                                        inputField.classList.remove("is-invalid");
                                        inputField.classList.add("is-valid");
                                    }
                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    let dobField = document.getElementById('date_of_birth');
                                    validateDOB(dobField);

                                    if (dobField.classList.contains("is-invalid")) {
                                        event.preventDefault(); // Stop form submission if validation fails
                                        alert("Please enter a valid Date of Birth (past dates only).");
                                    }
                                });
                                </script>

                            </div>
                            <div class="d-flex gap-2 mb-2">
                                <div class="form-group flex-fill">
                                    <label for="student_image" class="form-label">
                                        Upload Student Image in jpeg / png / jpg - (Max: 100KB)* <b>Colour photograph
                                            with white background </b>
                                    </label>
                                    <input type="file"
                                        class="form-control {{ $errors->has('student_image') ? 'is-invalid' : '' }}"
                                        id="student_image" name="student_image"
                                        accept="image/jpeg, image/png, image/jpg" onchange="validateImage(this)"
                                        required>
                                    <div class="invalid-feedback">please upload an image
                                    </div>

                                    <div class="mt-2">
                                        <img id="preview_student_image" src="{{ asset('default-image.png') }}"
                                            class="img-thumbnail border"
                                            style="width: 150px; height: 150px; object-fit: cover; display: none;">
                                    </div>

                                    <div id="student_image_error" class="text-danger"></div>

                                    @error('student_image')
                                    <div class="text-danger rounded mx-auto d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <script>
                                function validateImage(input) {
                                    const file = input.files[0];
                                    const preview = document.getElementById("preview_student_image");
                                    const errorDiv = document.getElementById("student_image_error");

                                    // Reset previous validation
                                    errorDiv.textContent = "";
                                    input.classList.remove("is-invalid", "is-valid");

                                    if (file) {
                                        const allowedExtensions = ["image/jpeg", "image/png", "image/jpg"];
                                        const maxSize = 100 * 1024; // 100KB

                                        if (!allowedExtensions.includes(file.type)) {
                                            errorDiv.textContent = "Only JPEG, PNG, or JPG images are allowed.";
                                            input.classList.add("is-invalid");
                                            preview.style.display = "none";
                                            return;
                                        }

                                        if (file.size > maxSize) {
                                            errorDiv.textContent = "Image size should not exceed 100KB.";
                                            input.classList.add("is-invalid");
                                            preview.style.display = "none";
                                            return;
                                        }

                                        // If valid, show preview
                                        input.classList.add("is-valid");
                                        preview.style.display = "block";
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            preview.src = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    } else {
                                        preview.style.display = "none";
                                    }
                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    let imageField = document.getElementById("student_image");
                                    validateImage(imageField);

                                    if (imageField.classList.contains("is-invalid")) {
                                        event.preventDefault();
                                        alert(
                                            "Please upload a valid student image (JPEG, PNG, JPG, max 100KB)."
                                        );
                                    }
                                });
                                </script>

                                <script>
                                function previewImage(event) {
                                    const file = event.target.files[0];
                                    const reader = new FileReader();
                                    const imgElement = document.getElementById('preview_student_image');

                                    if (file) {
                                        reader.onload = function(e) {
                                            imgElement.src = e.target.result;
                                            imgElement.style.display = 'block'; // Show the image after upload
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                }
                                </script>
                                <div class="form-group flex-fill">
                                    <label for="nchm_roll_no">NCHM Roll No. *</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('nchm_roll_no') ? 'is-invalid' : (old('nchm_roll_no') ? 'is-valid' : '') }}"
                                        id="nchm_roll_no" name="nchm_roll_no" placeholder="Enter 10-digit NCHM Roll No."
                                        value="{{ old('nchm_roll_no') }}" required maxlength="10"
                                        oninput="validateRollNo(this)">

                                    <div class="invalid-feedback">NCHM Roll No. must be exactly 10 digits.</div>

                                    @error('nchm_roll_no')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <script>
                                function validateRollNo(input) {
                                    const rollNoRegex = /^\d{10}$/; // Exactly 10 digits
                                    if (!rollNoRegex.test(input.value.trim())) {
                                        input.classList.remove("is-valid");
                                        input.classList.add("is-invalid");
                                    } else {
                                        input.classList.remove("is-invalid");
                                        input.classList.add("is-valid");
                                    }
                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    let rollNoField = document.getElementById("nchm_roll_no");
                                    validateRollNo(rollNoField);

                                    if (rollNoField.classList.contains("is-invalid")) {
                                        event.preventDefault();
                                        alert("NCHM Roll No. must be exactly 10 digits.");
                                    }
                                });
                                </script>

                            </div>
                            <div class="d-flex gap-2 mb-2">
                                <div class="form-group flex-fill">
                                    <label for="category">Category *</label>
                                    <select
                                        class="form-select {{ $errors->has('category') ? 'is-invalid' : (old('category') ? 'is-valid' : '') }}"
                                        id="category" name="category" required>
                                        <option selected disabled value="">Select Category</option>
                                        <option value="General">General</option>
                                        <option value="OBC-NCL">OBC-NCL</option>
                                        <option value="SC">SC</option>
                                        <option value="ST">ST</option>
                                    </select>

                                    <div class="invalid-feedback" id="category_error">Please select a category.</div>

                                    @error('category')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <script>
                                document.getElementById('category').addEventListener('change', function() {
                                    validateCategory(this);
                                });

                                function validateCategory(selectField) {
                                    const errorMessage = document.getElementById("category_error");

                                    if (selectField.value.trim() === "") {
                                        selectField.classList.remove("is-valid");
                                        selectField.classList.add("is-invalid");
                                        errorMessage.style.display = "block";
                                    } else {
                                        selectField.classList.remove("is-invalid");
                                        selectField.classList.add("is-valid");
                                        errorMessage.style.display = "none";
                                    }
                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    let categoryField = document.getElementById("category");
                                    validateCategory(categoryField);

                                    if (categoryField.classList.contains("is-invalid")) {
                                        event.preventDefault();
                                        alert("Please select a valid category.");
                                    }
                                });
                                </script>

                                <div class="form-group flex-fill">
                                    <label for="pwbd_category">Do you belong to PwBD category?</label><br>
                                    <input type="radio" id="pwbd_yes" name="pwbd_category" value="yes"
                                        onclick="togglePwbdFields(true)"> Yes
                                    <input type="radio" id="pwbd_no" name="pwbd_category" value="no"
                                        onclick="togglePwbdFields(false)" checked> N/A
                                </div>
                            </div>

                            <!-- Fields that show if 'Yes' is selected for PwBD -->
                            <div id="pwbdFields" style="display: none;">
                                <div class="d-flex gap-2 mb-2">
                                    <!-- Dropdown for HH/OH/VH -->
                                    <div class="form-group flex-fill">
                                        <label for="hh_oh_vh">HH/OH/VH *</label>
                                        <select class="form-control" id="hh_oh_vh" name="hh_oh_vh"
                                            onchange="togglePercentage()">
                                            <option selected disabled>Select </option>
                                            <option value="HH">HH</option>
                                            <option value="OH">OH</option>
                                            <option value="VH">VH</option>
                                        </select>
                                        @error('hh_oh_vh')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Percentage field, initially hidden -->
                                    <div class="form-group flex-fill" id="percentage" style="display: none;">
                                        <label for="percentage">Percentage *</label>
                                        <input type="text" class="form-control" id="percentage" name="percentage"
                                            placeholder="Percentage" value="{{ old('percentag') }}">
                                        @error('percentage')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <script>
                            function togglePwbdFields(show) {
                                const pwbdFields = document.getElementById('pwbdFields');
                                if (show) {
                                    pwbdFields.style.display = 'block';
                                } else {
                                    pwbdFields.style.display = 'none';
                                    document.getElementById('percentage').style.display =
                                        'none'; // Hide percentage field if PwBD is not selected
                                }
                            }

                            function togglePercentage() {
                                const hh_oh_vh = document.getElementById('hh_oh_vh').value;
                                const percentage = document.getElementById('percentage');

                                if (hh_oh_vh !== "Select" && hh_oh_vh !== "") {
                                    percentage.style.display = 'block';
                                    percentage.required = true; // Ensure field is required when an option is selected
                                } else {
                                    percentage.style.display = 'none';
                                    percentage.value = ""; // Clear value if selection is removed
                                    percentage.required = false;
                                }
                            }

                            // Ensure all hidden fields are enabled before submitting the form
                            document.querySelector("form").addEventListener("submit", function() {
                                document.getElementById("hh_oh_vh").disabled = false;
                                document.getElementById("percentage").disabled = false;
                            });
                            </script>
                            <div class="d-flex gap-2 mb-2">
                                <!-- Father's Name -->
                                <div class="form-group flex-fill">
                                    <label for="father_name">Father's Name *</label>
                                    <input type="text" class="form-control" id="father_name" name="father_name"
                                        placeholder="Father's Name" value="{{ old('father_name') }}"
                                        oninput="validateText(this, 'father_name_error')" required>
                                    <div class="invalid-feedback" id="father_name_error">Please enter Father's Name.
                                    </div>
                                    @error('father_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Father's Mobile No -->
                                <div class="form-group flex-fill">
                                    <label for="father_mobile">Father's Mobile No *</label>
                                    <input type="text" class="form-control" id="father_mobile" name="father_mobile"
                                        placeholder="Father's Mobile No" value="{{ old('father_mobile') }}"
                                        maxlength="10" oninput="validateMobile(this, 'father_mobile_error')" required>
                                    <div class="invalid-feedback" id="father_mobile_error">Please enter a valid
                                        10-digit
                                        mobile number.</div>
                                    @error('father_mobile')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-2">
                                <!-- Mother's Name -->
                                <div class="form-group flex-fill">
                                    <label for="mother_name">Mother's Name *</label> <input type="text"
                                        class="form-control" id="mother_name" name="mother_name"
                                        placeholder="Mother's Name" value="{{ old('mother_name') }}"
                                        oninput="validateText(this, 'mother_name_error')" required>
                                    <div class="invalid-feedback" id="mother_name_error">Please enter Mother's Name.
                                    </div>
                                    @error('mother_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <script>
                            // Function to validate names (only letters and spaces allowed)
                            function validateText(inputField, errorId) {
                                const regex = /^[A-Za-z\s]+$/;
                                const errorMessage = document.getElementById(errorId);
                                if (inputField.value.trim() && regex.test(inputField.value)) {
                                    inputField.classList.remove("is-invalid");
                                    inputField.classList.add("is-valid");
                                    errorMessage.style.display = "none";
                                } else {
                                    inputField.classList.remove("is-valid");
                                    inputField.classList.add("is-invalid");
                                    errorMessage.style.display = "block";
                                }
                            }

                            // Function to validate mobile number (only 10-digit numbers allowed)
                            function validateMobile(inputField, errorId) {
                                const regex = /^[6-9]\d{9}$/; // 10-digit number starting with 6-9
                                const errorMessage = document.getElementById(errorId);

                                // Prevent entering more than 10 digits
                                if (inputField.value.length > 10) {
                                    inputField.value = inputField.value.slice(0, 10); // Trim to 10 digits
                                }

                                // Validate the mobile number
                                if (regex.test(inputField.value)) {
                                    inputField.classList.remove("is-invalid");
                                    inputField.classList.add("is-valid");
                                    errorMessage.style.display = "none";
                                } else {
                                    inputField.classList.remove("is-valid");
                                    inputField.classList.add("is-invalid");
                                    errorMessage.style.display = "block";
                                }
                            }
                            </script>


                            <div class="d-flex gap-2 mb-2">
                                <!-- Guardian Name Field -->
                                <div class="form-group flex-fill position-relative">
                                    <label for="guardian_name">Name of Guardian (if father is deceased) *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="guardian_name" name="guardian_name"
                                            placeholder="Guardian Name" value="{{ old('guardian_name') }}"
                                            oninput="validateGuardianName(this)" required>
                                        <div class="invalid-feedback" id="guardian_name_error">Please enter a valid
                                            Guardian's Name (only letters allowed).</div>
                                    </div>

                                    @error('guardian_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Checkbox for "Same as Father" -->
                                <div class="form-group flex-fill">
                                    <label>
                                        <input type="checkbox" id="same_as_father" name="same_as_father" value="1"
                                            onclick="fillFatherName()"> Same as Father
                                    </label>
                                </div>
                            </div>

                            <script>
                            function fillFatherName() {
                                const sameAsFather = document.getElementById('same_as_father').checked;
                                const guardianNameField = document.getElementById('guardian_name');
                                const fatherNameField = document.getElementById('father_name');

                                if (sameAsFather) {
                                    guardianNameField.value = fatherNameField.value.trim();
                                    guardianNameField.readOnly = true;
                                    guardianNameField.classList.remove("is-invalid");
                                    guardianNameField.classList.add("is-valid");
                                } else {
                                    guardianNameField.value = '';
                                    guardianNameField.readOnly = false;
                                    guardianNameField.classList.remove("is-valid");
                                    guardianNameField.classList.remove("is-invalid");
                                }
                            }

                            function validateGuardianName(inputField) {
                                const regex = /^[A-Za-z\s]+$/;
                                const errorMessage = document.getElementById("guardian_name_error");

                                if (inputField.value.trim() && regex.test(inputField.value)) {
                                    inputField.classList.remove("is-invalid");
                                    inputField.classList.add("is-valid");
                                    errorMessage.style.display = "none";
                                } else {
                                    inputField.classList.remove("is-valid");
                                    inputField.classList.add("is-invalid");
                                    errorMessage.style.display = "block";
                                }
                            }

                            // Prevent form submission if Guardian Name is invalid
                            document.querySelector("form").addEventListener("submit", function(event) {
                                const guardianNameField = document.getElementById("guardian_name");

                                if (!guardianNameField.readOnly) {
                                    validateGuardianName(guardianNameField);
                                }

                                if (guardianNameField.classList.contains("is-invalid")) {
                                    event.preventDefault();
                                    alert("Please enter a valid Guardian's Name before submitting the form.");
                                }
                            });
                            </script>


                            <div class="d-flex gap-2 mb-2">
                                <div class="form-group flex-fill">
                                    <label for="local_address">Local Address with Pin Code*</label>
                                    <input type="text" class="form-control" id="local_address" name="local_address"
                                        placeholder="Local Address with Pin Code" value="{{ old('local_address') }}"
                                        required>
                                    <div class="invalid-feedback">Please enter a valid Local Address with a 6-digit PIN
                                        code.</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-2">
                                <div class="form-group flex-fill">
                                    <label for="permanent_address">Permanent Address with Pin Code*</label>
                                    <input type="text" class="form-control" id="permanent_address"
                                        name="permanent_address" placeholder="Permanent Address with Pin Code"
                                        value="{{ old('permanent_address') }}" required>
                                    <div class="invalid-feedback">Please enter a valid Permanent Address with a 6-digit
                                        PIN code.</div>
                                </div>

                                <div class="form-group flex-fill align-self-end">
                                    <label>
                                        <input type="checkbox" id="same_as_local_address" name="same_as_local_address"
                                            value="1" onclick="fillPermanentAddress()"> Same as Local Address
                                    </label>
                                </div>
                            </div>

                            <script>
                            function fillPermanentAddress() {
                                const sameAsLocal = document.getElementById('same_as_local_address').checked;
                                const permanentAddressField = document.getElementById('permanent_address');
                                const localAddress = document.getElementById('local_address').value;

                                if (sameAsLocal) {
                                    permanentAddressField.value = localAddress;
                                    permanentAddressField.readOnly = true;
                                } else {
                                    permanentAddressField.value = '';
                                    permanentAddressField.readOnly = false;
                                }

                                validateAddress(permanentAddressField);
                            }

                            document.getElementById('local_address').addEventListener('input', function() {
                                if (document.getElementById('same_as_local_address').checked) {
                                    document.getElementById('permanent_address').value = this.value;
                                }
                                validateAddress(this);
                            });

                            document.getElementById('permanent_address').addEventListener('input', function() {
                                validateAddress(this);
                            });

                            function validateAddress(inputField) {
                                const pinRegex = /\b\d{6}\b$/; // Regex to match a 6-digit PIN code at the end
                                const value = inputField.value.trim();

                                if (!pinRegex.test(value)) {
                                    inputField.classList.remove("is-valid");
                                    inputField.classList.add("is-invalid");
                                } else {
                                    inputField.classList.remove("is-invalid");
                                    inputField.classList.add("is-valid");
                                }
                            }

                            document.querySelector("form").addEventListener("submit", function(event) {
                                let localAddressField = document.getElementById('local_address');
                                let permanentAddressField = document.getElementById('permanent_address');

                                validateAddress(localAddressField);
                                validateAddress(permanentAddressField);

                                if (localAddressField.classList.contains("is-invalid") || permanentAddressField
                                    .classList.contains(
                                        "is-invalid")) {
                                    event.preventDefault(); // Stop form submission if validation fails
                                    alert("Please enter a valid address with a 6-digit PIN code.");
                                }
                            });
                            </script>



                            <div class="d-flex gap-2 mb-2">
                                <!-- Nationality Field -->
                                <div class="form-group flex-fill">
                                    <label for="nationality">Nationality *</label>
                                    <select class="form-select" id="nationality" name="nationality"
                                        onchange="toggleDomicileField(); validateSelect('nationality', 'nationality_error')"
                                        required>

                                        <option selected disabled value="">Select Nationality</option>
                                        <option value="Indian">Indian</option>
                                        <option value="Foreign">Foreign</option>
                                    </select>
                                    <div class="invalid-feedback" id="nationality_error">Please select a
                                        nationality.
                                    </div>
                                    @error('nationality')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- State of Domicile Field -->
                                <div class="form-group flex-fill">
                                    <label for="state_of_domicile">State / UT of Domicile *</label>
                                    <div id="domicileContainer">
                                        <select class="form-select" id="state_of_domicile" name="state_of_domicile"
                                            onchange="validateSelect('state_of_domicile', 'domicile_error')" required>

                                            <option selected disabled value="">Select State</option>
                                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                            <option value="Assam">Assam</option>
                                            <option value="Bihar">Bihar</option>
                                            <option value="Chhattisgarh">Chhattisgarh</option>
                                            <option value="Goa">Goa</option>
                                            <option value="Gujarat">Gujarat</option>
                                            <option value="Haryana">Haryana</option>
                                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                                            <option value="Jharkhand">Jharkhand</option>
                                            <option value="Karnataka">Karnataka</option>
                                            <option value="Kerala">Kerala</option>
                                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                                            <option value="Maharashtra">Maharashtra</option>
                                            <option value="Manipur">Manipur</option>
                                            <option value="Meghalaya">Meghalaya</option>
                                            <option value="Mizoram">Mizoram</option>
                                            <option value="Nagaland">Nagaland</option>
                                            <option value="Odisha">Odisha</option>
                                            <option value="Punjab">Punjab</option>
                                            <option value="Rajasthan">Rajasthan</option>
                                            <option value="Sikkim">Sikkim</option>
                                            <option value="Tamil Nadu">Tamil Nadu</option>
                                            <option value="Telangana">Telangana</option>
                                            <option value="Tripura">Tripura</option>
                                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                                            <option value="Uttarakhand">Uttarakhand</option>
                                            <option value="West Bengal">West Bengal</option>
                                            <option value="Ladakh">Ladakh</option>
                                            <option value="Jammu & Kashmir">Jammu & Kashmir</option>
                                            <option value="Puducherry">Puducherry</option>
                                            <option value="Lakshadweep">Lakshadweep</option>
                                            <option value="Chandigarh">Chandigarh</option>
                                            <option value="Delhi">Delhi</option>
                                            <option value="Dadra and Nagar Haveli and Daman & Diu">Dadra and Nagar
                                                Haveli and Daman & Diu</option>
                                            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands
                                            </option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <div class="invalid-feedback" id="domicile_error">Please select a state of
                                            domicile.
                                        </div>
                                    </div>

                                    @error('state_of_domicile')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <script>
                                function toggleDomicileField() {
                                    var nationality = document.getElementById("nationality").value;
                                    var domicileContainer = document.getElementById("domicileContainer");
                                    var domicileLabel = document.querySelector("label[for='state_of_domicile']");

                                    if (nationality === "Foreign") {
                                        domicileLabel.innerText = "Country of Residence *"; // Update label
                                        domicileContainer.innerHTML = `
            <input type="text" class="form-control" id="state_of_domicile" name="state_of_domicile"
            placeholder="Enter Country Name" oninput="validateText(this, 'domicile_error')">
        `;
                                    } else {
                                        domicileLabel.innerText = "State of Domicile *"; // Reset label
                                        domicileContainer.innerHTML = `
            <select class="form-select" id="state_of_domicile" name="state_of_domicile"
            onchange="validateSelect('state_of_domicile', 'domicile_error')">
                <option selected disabled value="">Select State</option>
                <option value="Andhra Pradesh">Andhra Pradesh</option>
                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                <option value="Assam">Assam</option>
                <option value="Bihar">Bihar</option>
                <option value="Chhattisgarh">Chhattisgarh</option>
                <option value="Goa">Goa</option>
                <option value="Gujarat">Gujarat</option>
                <option value="Haryana">Haryana</option>
                <option value="Himachal Pradesh">Himachal Pradesh</option>
                <option value="Jharkhand">Jharkhand</option>
                <option value="Karnataka">Karnataka</option>
                <option value="Kerala">Kerala</option>
                <option value="Madhya Pradesh">Madhya Pradesh</option>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Manipur">Manipur</option>
                <option value="Meghalaya">Meghalaya</option>
                <option value="Mizoram">Mizoram</option>
                <option value="Nagaland">Nagaland</option>
                <option value="Odisha">Odisha</option>
                <option value="Punjab">Punjab</option>
                <option value="Rajasthan">Rajasthan</option>
                <option value="Sikkim">Sikkim</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
                <option value="Telangana">Telangana</option>
                <option value="Tripura">Tripura</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
                <option value="Uttarakhand">Uttarakhand</option>
                <option value="West Bengal">West Bengal</option>
                <option value="Other">Other</option>
            </select>
        `;
                                    }
                                }

                                function validateSelect(fieldId, errorId) {
                                    var field = document.getElementById(fieldId);
                                    var errorMessage = document.getElementById(errorId);

                                    if (field.value === "") {
                                        field.classList.add("is-invalid");
                                        errorMessage.style.display = "block";
                                    } else {
                                        field.classList.remove("is-invalid");
                                        field.classList.add("is-valid");
                                        errorMessage.style.display = "none";
                                    }
                                }

                                function validateText(inputField, errorId) {
                                    const errorMessage = document.getElementById(errorId);

                                    if (inputField.value.trim() === "") {
                                        inputField.classList.add("is-invalid");
                                        errorMessage.style.display = "block";
                                    } else {
                                        inputField.classList.remove("is-invalid");
                                        inputField.classList.add("is-valid");
                                        errorMessage.style.display = "none";
                                    }
                                }

                                document.querySelector("form").addEventListener("submit", function(event) {
                                    validateSelect("nationality", "nationality_error");

                                    var domicileField = document.getElementById("state_of_domicile");
                                    if (domicileField.tagName === "SELECT") {
                                        validateSelect("state_of_domicile", "domicile_error");
                                    } else {
                                        validateText(domicileField, "domicile_error");
                                    }

                                    if (document.querySelector(".is-invalid")) {
                                        event.preventDefault(); // Stop form submission if validation fails
                                    }
                                });
                                </script>


                            </div>

                            <div class="d-flex gap-2 mb-2">
                                <div class="form-group flex-fill">
                                    <label for="student_email">Student's Email *</label>
                                    <input type="text" class="form-control" id="student_email" name="student_email"
                                        placeholder="Email ID" value="{{ old('student_email') }}"
                                        oninput="validateStudentEmail(this, 'student_email_error')" required>

                                    <!-- Error message for invalid email -->
                                    <div class="invalid-feedback" id="student_email_error">Please enter a valid Email
                                        ID.</div>

                                    @error('student_email')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <script>
                                document.getElementById("student_email").addEventListener("input", function() {
                                    var emailField = this;
                                    var errorDiv = document.getElementById(
                                        "student_email_error"); // Fixed the ID

                                    // Reset validation states
                                    errorDiv.innerHTML = "";
                                    errorDiv.style.display = "none"; // Hide the error initially
                                    emailField.classList.remove("is-invalid", "is-valid");

                                    var emailValue = emailField.value.trim();
                                    var emailPattern =
                                        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Improved regex for domain validation

                                    // Check if the field is empty
                                    if (emailValue.length === 0) {
                                        errorDiv.innerHTML = "The email field is required.";
                                        errorDiv.style.display = "block";
                                        emailField.classList.add("is-invalid");
                                        return;
                                    }

                                    // Check if email format is valid
                                    if (!emailPattern.test(emailValue)) {
                                        errorDiv.innerHTML = "Please enter a valid email address.";
                                        errorDiv.style.display = "block";
                                        emailField.classList.add("is-invalid");
                                        return;
                                    }

                                    // If valid, apply success styling
                                    emailField.classList.add("is-valid");
                                });
                                </script>



                                <div class="form-group flex-fill">
                                    <label for="student_mobile">Student's Mobile No *</label>
                                    <input type="text" class="form-control" id="student_mobile" name="student_mobile"
                                        placeholder="Mobile No" value="{{ old('student_mobile') }}" maxlength="10"
                                        oninput="validateMobile(this, 'student_mobile_error')" required>
                                    <div class="invalid-feedback" id="student_mobile_error">Please enter a valid
                                        10-digit
                                        mobile number.</div>
                                    @error('student_mobile')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>




                                <script>
                                document.getElementById("student_mobile").addEventListener("input", function() {
                                    var mobileField = this;
                                    var errorDiv = document.getElementById("mobileError");
                                    var invalidFeedback = mobileField
                                        .nextElementSibling; // Get the invalid-feedback div
                                    var mobileValue = mobileField.value.trim();

                                    // Reset error messages & validation states
                                    errorDiv.innerHTML = "";
                                    invalidFeedback.style.display = "none";
                                    mobileField.classList.remove("is-invalid", "is-valid");

                                    // Check if the field is empty
                                    if (mobileValue.length === 0) {
                                        errorDiv.innerHTML = "The mobile number field is required.";
                                        mobileField.classList.add("is-invalid");
                                        invalidFeedback.style.display = "block";
                                        return; // Stop further validation
                                    }

                                    // Check if the length is less than 10
                                    if (mobileValue.length < 10) {
                                        errorDiv.innerHTML = "The mobile number must be exactly 10 digits.";
                                        mobileField.classList.add("is-invalid");
                                        invalidFeedback.style.display = "block";
                                        return; // Stop further validation
                                    }

                                    // If valid, apply success styling
                                    mobileField.classList.add("is-valid");
                                });
                                </script>




                                <div class="form-group flex-fill">
                                    <label for="abc_id">ABC (Academic Bank of Credit) ID</label>
                                    <input type="text" class="form-control" id="abc_id" name="abc_id"
                                        placeholder="ABC ID" value="{{ old('abc_id') }}" maxlength="12"
                                        oninput="validateABCID(this)">
                                    <div class="invalid-feedback" id="abc_id_error">ABC ID must be exactly 12 digits.
                                    </div>

                                    @error('abc_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <script>
                                function validateABCID(inputField) {
                                    const abcRegex = /^\d{12}$/; // Only 12-digit numbers allowed
                                    const errorMessage = document.getElementById("abc_id_error");

                                    // Remove any non-numeric characters
                                    inputField.value = inputField.value.replace(/\D/g, '');

                                    if (inputField.value.trim() === "") {
                                        // If empty, reset validation styles
                                        inputField.classList.remove("is-invalid", "is-valid");
                                        errorMessage.style.display = "none";
                                    } else if (abcRegex.test(inputField.value.trim())) {
                                        // Valid 12-digit number
                                        inputField.classList.remove("is-invalid");
                                        inputField.classList.add("is-valid");
                                        errorMessage.style.display = "none";
                                    } else {
                                        // Invalid input
                                        inputField.classList.remove("is-valid");
                                        inputField.classList.add("is-invalid");
                                        errorMessage.style.display = "block";
                                    }
                                }
                                </script>


                            </div>
                            <br>
                            <div class="form-group flex-fill">
                                <label for=" academic_records"> Academic Record (Take into account only marks in
                                    the
                                    subject which are counted for awarding
                                    class/division)*</label>
                                <br><br>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name of Examination Passed</th>
                                            <th>Name of the Board/University</th>
                                            <th>School/College/Institute</th>
                                            <th>Year of Passing</th>
                                            <th>Subject</th>
                                            <th>Percentage/Division</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 10th Examination -->
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control " value="10th" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="board_10th"
                                                    name="board_10th" placeholder="Board/University"
                                                    value="{{ old('board_10th') }}" required>
                                                <div class="invalid-feedback" id="board_10th">This field is required
                                                </div>
                                                @error('board_10th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="school_10th"
                                                    name="school_10th" placeholder="School/College/Institute"
                                                    value="{{ old('school_10th') }}" required>
                                                <div class="invalid-feedback" id="school_10th">This field is required
                                                </div>
                                                @error('school_10th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="year_10th" name="year_10th"
                                                    placeholder="Year of Passing" value="{{ old('year_10th') }}"
                                                    required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('year_10th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="subject_10th"
                                                    id="subject_10th" placeholder="Subject"
                                                    value="{{ old('subject_10th') }}" required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('subject_10th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="percentage_10th"
                                                    id="percentage_10th" placeholder="Percentage"
                                                    value="{{ old('percentage_10th') }}" required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('percentage_10th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <!-- 12th Examination -->
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" value="12th" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="board_12th"
                                                    id="board_12th" placeholder="Board/University"
                                                    value="{{ old('board_12th') }}" required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('board_12th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="school_12th"
                                                    id="school_12th" placeholder="School/College/Institute"
                                                    value="{{ old('school_12th') }}" required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('school_12th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="year_12th" id="year_12th"
                                                    placeholder="Year of Passing" value="{{ old('year_12th') }}"
                                                    required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('year_12th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="subject_12th"
                                                    id="subject_12th" placeholder="Subject"
                                                    value="{{ old('subject_12th') }}" required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('subject_12th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="percentage_12th"
                                                    id="percentage_12th" placeholder="Percentage"
                                                    value="{{ old('percentage_12th') }}" required>
                                                <div class="invalid-feedback" id="abc_id_error">This field is required
                                                </div>
                                                @error('percentage_12th')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <!-- Other Examination -->
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" value="Other" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="board_other"
                                                    id="board_other" placeholder="Board/University"
                                                    value="{{ old('board_other') }}">
                                                @error('board_other')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="school_other"
                                                    id="school_other" placeholder="School/College/Institute"
                                                    value="{{ old('school_other') }}">
                                                @error('school_other')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="year_other"
                                                    id="year_other" placeholder="Year of Passing"
                                                    value="{{ old('year_other') }}">
                                                @error('year_other')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="subject_other"
                                                    id="subject_other" placeholder="Subject"
                                                    value="{{ old('subject_other') }}">
                                                @error('subject_other')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="percentage_other"
                                                    id="percentage_other" placeholder="Percentage"
                                                    value="{{ old('percentage_other') }}">
                                                @error('percentage_other')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            const fields = [
                                                "board_10th", "school_10th", "year_10th", "subject_10th",
                                                "percentage_10th",
                                                "board_12th", "school_12th", "year_12th", "subject_12th",
                                                "percentage_12th"
                                            ];

                                            function validateField(field) {
                                                let input = document.getElementById(field);
                                                let errorDiv = input
                                                    .nextElementSibling; // The error div right after input

                                                if (input.value.trim() === "") {
                                                    input.classList.add("is-invalid");
                                                    input.classList.remove("is-valid");
                                                    errorDiv.innerHTML = "This field is required.";
                                                } else {
                                                    input.classList.add("is-valid");
                                                    input.classList.remove("is-invalid");
                                                    errorDiv.innerHTML = ""; // Clear error message
                                                }
                                            }

                                            // Attach input event listener for real-time validation
                                            fields.forEach(field => {
                                                let input = document.getElementById(field);
                                                input.addEventListener("input", () => validateField(
                                                    field));
                                            });

                                            // Validate all fields before form submission
                                            document.querySelector("form").addEventListener("submit", function(
                                                event) {
                                                let isValid = true;
                                                fields.forEach(field => {
                                                    validateField(field);
                                                    if (document.getElementById(field).classList
                                                        .contains("is-invalid")) {
                                                        isValid = false;
                                                    }
                                                });

                                                if (!isValid) {
                                                    event
                                                        .preventDefault(); // Stop form submission if validation fails
                                                }
                                            });
                                        });
                                        </script>

                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex gap-2">
                                <div class="form-group flex-fill">
                                    <button type="button" class="btn btn-success w-100" id="previewButton"
                                        onclick="previewForm()">Preview</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class=" modal fade" id="updateenrollmentModal" tabindex="-1" aria-labelledby="updateenrollmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateenrollmentModalLabel">Enrollment</h5>
                    <button type=" button" class="btn-close" data-bs-dismiss="modal" aria-label=" Close">
                    </button>
                </div>
                <div class="modal-body" id="updateenrollment">
                    <!-- Content will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
$(document).ready(function() {
    new DataTable('#newenrollment', {
        layout: {
            topStart: {
                buttons: [{
                        extend: 'copyHtml5',
                        className: 'text-bg-dark',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'text-bg-dark',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        className: 'text-bg-dark',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
            }
        },
        scrollX: true,
        responsive: true,
    });
});


// Define the previewForm function in the global scope

function previewForm() {
    let form = document.getElementById("enrollmentForm");

    if (!form) {
        alert("Enrollment form not found!");
        return;
    }

    let isValid = true;
    let inputs = form.querySelectorAll("input, select, textarea");

    inputs.forEach(input => {
        // Validate only fields with "required" attribute
        if (input.hasAttribute("required") && !input.value.trim()) {
            isValid = false;
            input.classList.add("is-invalid"); // Bootstrap invalid style
        } else {
            input.classList.remove("is-invalid");
            input.classList.add("is-valid"); // Bootstrap valid style
        }
    });

    if (!isValid) {
        alert("Please fill all required fields before previewing.");
        return;
    }

    // Submit the form to the preview route
    form.action = "{{ route('institute.previewEnrollment') }}"; // Ensure correct preview route
    form.method = "POST";
    form.submit();
}






/////////////////




$('#updateEnrollmentForm').on('submit', function(event) {
    event.preventDefault(); // Prevent page reload

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#updateModal').modal('hide'); // Close modal
                table.ajax.reload(); // Reload DataTable
                alert('Updated successfully!');
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert('Update failed!');
        }
    });
});
</script>
@endsection