@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 text-uppercase">{{ __('Upload Data') }}</div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    {{-- @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                </div>
                @endif

                {{ __('You are logged in!') }} --}}
                <form method="POST" action="" id="excelForm" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <h5 class="fw-bold">Download Template</h5>
                    <div class="d-flex justify-content-between gap-2 mb-4">
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded text-capitalize" id="op_course" name="tempcourse">
                                <option value="">Select Course</option>
                                @foreach($course as $duration=>$single)
                                <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" id="op_batch" name="tempbatch">
                                <option value="">Select Batch</option>
                                @foreach(batch() as $batch)
                                <option value="{{$batch}}">{{$batch}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" name="tempsemester">
                                <option value="">Select Semester</option>
                                @foreach($semester as $single)
                                <option value="{{$single}}">{{$single.' Semester'}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" name="tempacademicyear">
                                <option value="">Select Academic Year</option>
                                @foreach(academicyear(3) as $single)
                                <option value="{{$single}}">{{$single}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" name="temptermmarks">
                                <option value="">Select Term</option>
                                <option value="mid">Mid</option>
                                <option value="end">End</option>
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <button type="button" onclick="Template()"
                                class="w-100 bg-dark border border-dark rounded text-white p-2">Template</button>
                        </div>
                    </div>
                    <div class="position-relative mb-4 border border-dark w-100">
                        <div
                            class="position-absolute border rounded-circle border-dark top-nv-13 bg-white px-1 start-49 fw-bold">
                            or</div>
                    </div>
                    <h5 class="fw-bold">Upload Excel</h5>
                    <div class="d-flex justify-content-between gap-2 mb-3">
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded text-capitalize" id="course" name="course">
                                <option value="">Select Course</option>
                                @foreach($course as $duration=>$single)
                                <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" id="batch" name="batch">
                                <option value="">Select Batch</option>
                                @foreach(batch() as $batch)
                                <option value="{{$batch}}">{{$batch}}</option>
                                @endforeach
                                <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" name="semester">
                                <option value="">Select Semester</option>
                                @foreach($semester as $single)
                                <option value="{{$single}}">{{$single.' Semester'}}</option>
                                @endforeach
                                <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between gap-2 mb-3">
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" name="institute"
                                @if(Auth::guard('institute')->user()) disabled @endif>
                                <option value="">Select Institute</option>
                                @foreach($institutes as $institute)
                                <option value="{{$institute->InstituteCode}}" @if(Auth::guard('institute')->user() &&
                                    Auth::guard('institute')->user()->InstituteCode == $institute->InstituteCode)
                                    selected @endif>{{$institute->InstituteName}} ( {{$institute->InstituteCode}} )
                                </option>
                                @endforeach
                                <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" name="termmarks">
                                <option value="">Select Term</option>
                                <option value="mid">Mid</option>
                                <option value="end">End</option>
                            </select>
                        </div>
                        <div class="flex-1 w-100">
                            <input class="form-control" type="file" name="importfile" />
                            <!-- <button type="button" onclick="draft()" class="w-100 bg-success border border-dark rounded text-white">Import</button> -->
                        </div>
                    </div>
                    <button type="button" onclick="Import()" id="importbtn"
                        class="w-100 bg-success border border-dark rounded p-2 text-white">Upload Excel File</button>
                </form>
                @if(isset($insdata) && count($insdata) > 0)
                <div class="mt-4">
                    <table id="myTable"
                        class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark text-nowrap">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">Student Name</th>
                                <th scope="col" class="text-truncate">Student NCHM Roll No</th>
                                <th scope="col" class="text-truncate">Student JNU Roll No</th>
                                @foreach($termsub as $sub)
                                <th scope="col" class="text-truncate">{{$sub}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="table-group-divider text-nowrap">
                            @foreach($insdata as $key=>$single)
                            <tr>
                                <th scope="row">{{$key+1}}</th>
                                <td class="text-start">{{$single['name'] ?? 'N/A'}}</td>
                                <td class="text-uppercase">{{$single['NCHMCT_Rollnumber'] ?? 'N/A'}}</td>
                                <td class="text-uppercase">{{$single['JNU_Rollnumber'] ?? 'N/A'}}</td>
                                @foreach($termsub as $sub)
                                <td class="text-uppercase">{{$single[$sub] ?? 'N/A'}}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
<script>
function Import() {
    $url = '{{Auth::guard("institute")->user() ? Route("institute.excel.importdata") : Route("excel.importdata")}}';
    $('#excelForm').attr('action', $url);
    $('#excelForm').submit();
    $('#importbtn').html(`
            <span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Uploading Excel File
        `);
    $('#importbtn').prop('disabled', true);
}

function Template() {
    $url = '{{Route("excel.template")}}';
    $('#excelForm').attr('action', $url);
    $('#excelForm').submit();
}
</script>
@endsection