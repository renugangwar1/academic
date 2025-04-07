@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <div class="card border border-dark mb-3">
                <div class="card-header fw-bold fs-5">{{ __('Data Search') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <form method="POST" action="{{Route('excel.viewexcel')}}" id="excelForm" enctype="multipart/form-data">
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
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="batch" name="exportbatch">
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}">{{$batch}}</option>
                                    @endforeach
                                    <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="expsemester" name="exportsemester">
                                    <option value="">Select Semester</option>
                                    @foreach($semester as $single)
                                        <option value="{{$single}}">{{$single.' Semester'}}</option>
                                    @endforeach
                                    <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 pb-2">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" name="exportinstitute">
                                    <option value="">Select Institute</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{$institute->InstituteCode}}">{{$institute->InstituteName}} ( {{$institute->InstituteCode}} )</option>
                                    @endforeach
                                    <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                </select>
                            </div>
                            <button type="button" onclick="Export()" id="exportbtn" class="w-100 bg-dark border border-dark rounded text-white">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5">{{ __('Data Search By end tearm faild Subjects') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <form id="searhsubForm" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="container">    
                            <div class="row justify-content-between mb-3">
                                <div class="col-md-4">
                                    <select class="w-100 p-2 mb-2 rounded text-capitalize" onchange="findsubject()" id="op_course" name="exportcourse">
                                        <option value="">Select Course</option>
                                        @foreach($course as $duration=>$single)
                                            <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                                        @endforeach
                                    </select>
    
                                    <select class="w-100 p-2 rounded mb-2 text-capitalize" onchange="findsubject()" id="op_expsemester" name="exportsemester">
                                        <option value="">Select Semester</option>
                                        @foreach($semester as $single)
                                            <option value="{{$single}}">{{$single.' Semester'}}</option>
                                        @endforeach
                                        <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                    </select>
    
                                    <select class="w-100 p-2 rounded mb-2 text-capitalize" id="op_batch" name="exportbatch">
                                        <option value="">Select Batch</option>
                                        @foreach(batch() as $batch)
                                            <option value="{{$batch}}">{{$batch}}</option>
                                        @endforeach
                                        <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                    </select>
    
                                    <select class="w-100 p-2 rounded mb-2 text-capitalize" onchange="findsubject()" id="tearm" name="exporttearm">
                                        <option value="">Select Tearm</option>
                                        <option value="Mid">Mid</option>
                                        <option value="End">End</option>
                                    </select>
    
                                    <select class="w-100 p-2 rounded text-capitalize" name="exportinstitute">
                                        <option value="">Select Institute</option>
                                        @foreach($institutes as $institute)
                                            <option value="{{$institute->InstituteCode}}">{{$institute->InstituteName}} ( {{$institute->id}} )</option>
                                        @endforeach
                                        <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div class="bg-white border border-dark h-100 overflow-y-auto position-relative rounded">
                                        <div class="position-absolute h-100 w-100 p-3" id="showsub"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between gap-2 pb-2">
                                <button type="button" onclick="search()" id="searchbtn" class="btn w-100 bg-dark border border-dark rounded text-white">Search</button>
                            </div>
                        </div>
                    </form>
                    <div id="showsearchdata" class="p-1 d-none bg-dark-subtle rounded-1 w-100 my-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function Export(e){
        // e.preventDefault();
        $('#exportbtn').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching');
        $('#exportbtn').prop('disabled',true);
        $('#excelForm').submit();
    }

    function findsubject(){
        const course = $('#op_course').val();
        const semeseter = $('#op_expsemester').val();
        const tearm = $('#tearm').val();
        console.log(tearm);
        if(course != '' && semeseter != '' && tearm != ''){
            $.ajax({
            type: 'GET',
            url: '{{Route("excel.findsubject")}}', // Replace with your server endpoint
            data: {'course':course,'semester':semeseter},
            success: function(response) {
                // Handle the success response
                // Loop through the data to create checkboxes
                var vieform = '';
                $.each(response, function(index, item) {
                    vieform +=(
                        '<div>' +
                        '<input type="checkbox" id="subject_' + item.id + '" name="subject_codes['+tearm+'_' + item.Subject_code + ']" value=0> ' +
                        '<label class="text-uppercase" for="subject_' + item.id + '">' + item.Subject_name + ' (' + item.Subject_code + ')</label>' +
                        '</div>'
                    );
                });
                $('#showsub').html(vieform);
                
                // Select/Deselect all checkboxes when 'Select All' is clicked
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                $('#historybtn').html(`Search`);
            }
        });
        }
    }

    function search(e){
        var formData = $("#searhsubForm").serialize();

        $('#searchbtn').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching');
        
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("excel.failsubjects")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#showsearchdata').removeClass(`d-none`);
                $('#showsearchdata').html(response);
                $('#searchbtn').html(`Search`);
                $('#searchbtn').prop('disabled',false);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                alert(responseArray.message);
                $('#searchbtn').html(`Search`);
                $('#searchbtn').prop('disabled',false);
            }
        });
    }
</script>
@endsection
