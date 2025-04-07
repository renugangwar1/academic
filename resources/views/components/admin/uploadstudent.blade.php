<div class="card">
    <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Add New Students') }}
        <a class="btn btn-danger" href="{{Route(!isset(Auth::guard('institute')->user()->id) ? 'admin.students' : 'institute.students')}}">Back</a>
    </div>

    <div class="card-body bg-secondary rounded-bottom-1">
        <div class="d-flex gap-3">
            <form id="studentlistform" class="flex-fill border p-3 rounded bg-light" action="{{Route(!isset(Auth::guard('institute')->user()->id) ? 'admin.importstudentlist' : 'institute.importstudentlist')}}" method="post" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="excel_file" class="fs-4 fw-bold">Upload Excel *</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="form-group flex-fill">
                        <button class="btn btn-success w-100" id="newstudentupload" onclick="uploadnewstudent(this)">Upload list</button>
                    </div>
                </div>
            </form>
            <div class="flex-fill border p-3 rounded bg-light">
                <div class="form-group d-grid">
                    <div class="fs-4 fw-bold">Download Template</div>
                    <form action="{{Route(!isset(Auth::guard('institute')->user()->id) ? 'admin.studentimporttem' : 'institute.studentimporttem')}}" method="get">
                        @csrf
                        @method('get')
                        <div class="d-flex justify-content-between gap-2">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="course" name="course" required>
                                    <option value="">Select Course</option>
                                    @foreach($course as $duration=>$single)
                                    <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="batch" name="batch" required>
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                    <option value="{{$batch}}">{{$batch}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-dark w-100">Student Upload Template</button>
                    </form>

                </div>
            </div>
        </div>
        @if(isset($incomingdata) && count($incomingdata) > 0)
        <hr />
        <table id="myTable" class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" class="text-truncate">Student Name</th>
                    <th scope="col" class="text-truncate">NCHM RollNumber</th>
                    <th scope="col" class="text-truncate">JNU RollNumber</th>
                    <th scope="col" class="text-truncate">Batch</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach($incomingdata as $key=>$student)
                <tr>
                    <th scope="row">{{$key+1}}</th>
                    <td class="text-uppercase text-start">{{$student['name']}}</td>
                    <td class="text-uppercase text-start">{{$student['NCHMCT_Rollnumber']}}</td>
                    <td class="text-uppercase text-start">{{$student['JNU_Rollnumber']}}</td>
                    <td>{{$student['batch']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
<script>
    function uploadnewstudent(){
        $('#newstudentupload').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Uploading`);
    }
</script>