@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5">{{ __('Course Wise Data') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <form method="POST" action="{{Route('excel.searchinstituteview')}}" id="excelForm" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="course" name="exportcourse">
                                    <option value="">Select Course</option>
                                    @foreach($course as $duration=>$single)
                                        <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="batch" name="exportbatch">
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}">{{$batch}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="expsemester" name="exportsemester">
                                    <option value="">Select Semester</option>
                                    @foreach($semester as $single)
                                        <option value="{{$single}}">{{$single.' Semester'}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <!-- <select class="w-100 p-2 rounded text-capitalize" name="exportinstitute">
                                    <option value="">Select Institute</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{$institute->InstituteCode}}">{{$institute->InstituteName}}</option>
                                    @endforeach
                                </select> -->
                                <button type="button" onclick="Export()" id="exportbtn" class="w-100 h-100 bg-dark border border-dark rounded text-white">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5">{{ __('Subject Wise Data') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <form method="POST" action="{{Route('excel.subjectwiseview')}}" id="SubjectsearchForm" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="op_course" name="exportcourse">
                                    <option value="">Select Course</option>
                                    @foreach($course as $duration=>$single)
                                        <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="op_batch" name="exportbatch">
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}">{{$batch}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="expsemester" name="exportsemester">
                                    <option value="">Select Semester</option>
                                    @foreach($semester as $single)
                                        <option value="{{$single}}">{{$single.' Semester'}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <!-- <select class="w-100 p-2 rounded text-capitalize" name="exportinstitute">
                                    <option value="">Select Institute</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{$institute->InstituteCode}}">{{$institute->InstituteName}}</option>
                                    @endforeach
                                </select> -->
                                <button type="button" onclick="Subjectsearch()" id="Subjectsearchbtn" class="w-100 h-100 bg-dark border border-dark rounded text-white">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function Export(e){
        $('#exportbtn').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching');
        $('#exportbtn').prop('disabled',true);
        $('#excelForm').submit();
    }

    function Subjectsearch(e){
        $('#Subjectsearchbtn').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching');
        $('#Subjectsearchbtn').prop('disabled',true);
        $('#SubjectsearchForm').submit();
    }
</script>
@endsection
