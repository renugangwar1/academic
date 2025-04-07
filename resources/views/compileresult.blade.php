@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between w-100">
                    @if(Auth::user())
                        <span>{{ __('Compile, View and Print Admitcard') }}</span>
                    @else
                        <span>{{ __('Download Admitcard') }}</span>
                    @endif
                </div>
                
                <div class="card-body bg-secondary rounded-bottom-1">
                    <form method="Get" action="{{ Route(Auth::guard('institute')->user() ? 'institute.compiled_print' : 'compiled_print') }}" id="compileForm">
                        @csrf
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-uppercase" id="course" name="course" required>
                                    <option value="">Select Course</option>
                                    @foreach($corse as $duration => $single)
                                        <option class="text-uppercase" duration="{{$duration}}" value="{{$single}}" 
                                            {{ old('course') == $single ? 'selected' : '' }}>
                                            {{$corsename[$single]}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-uppercase" id="batch" name="batch" required>
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}" {{ old('batch') == $batch ? 'selected' : '' }}>
                                            {{$batch}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-uppercase" id="semester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    @foreach($semester as $single)
                                        <option value="{{$single}}" {{ old('semester') == $single ? 'selected' : '' }}>
                                            {{$single.' Semester'}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if(Auth::user())
                                <div class="flex-1 w-100">
                                    <select class="w-100 p-2 rounded mb-2 text-uppercase" id="term" name="term">
                                        <option value="">Select Term</option>
                                        <option value="Mid" {{ old('term') == 'Mid' ? 'selected' : '' }}>Mid Term</option>
                                        <option value="End" {{ old('term') == 'End' ? 'selected' : '' }}>End Term</option>
                                    </select>
                                </div>
                                <div class="flex-1 w-100">
                                    <select class="w-100 p-2 rounded mb-2 text-uppercase" id="institute" name="institute">
                                        <option value="">Select Institute</option>
                                        @foreach($institutes as $institute)
                                            <option value="{{$institute->id}}" {{ old('institute') == $institute->id ? 'selected' : '' }}>
                                                {{$institute->InstituteName}} ( {{$institute->InstituteCode}} )
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="institute" value="{{ Auth::guard('institute')->user()->id }}"/>
                            @endif
                        </div>
                        
                        <div class="d-flex gap-2 w-100" id="optionbtn">
                            @if(Auth::user())
                                <button type="button" onclick="draft()" id="compilebtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Compile</button>
                                <button type="button" onclick="view()" id="viewbtn" class="w-100 bg-dark border border-dark rounded p-2 text-white">View</button>
                            @endif
                            <button id="downloadbtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Print Admitcard</button>
                        </div>

                        <div id="compileresult" class="p-1 d-none bg-dark-subtle rounded-1 w-100 my-3"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function draft(){
        var formData = $("#compileForm").serialize();

        $('#compilebtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Compiling`);
        // $('#compilebtn').prop('disabled',true);
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("compiling_result")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#compileresult').removeClass(`d-none`);
                $('#compileresult').html(response);
                $('#compilebtn').html(`Compile`);
                $('#compilebtn').prop('disabled',false);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                alert(responseArray.message);
                $('#compilebtn').html(`Compile`);
                $('#compilebtn').prop('disabled',false);
            }
        });
    }

    function view(){
        var formData = $("#compileForm").serialize();

        $('#viewbtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching`);
        
        $.ajax({
            type: "POST",
            url: '{{Route("compiled_view")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#compileresult').removeClass(`d-none`);
                $('#compileresult').html(response);
                $('#viewbtn').html(`Compile`);
                $('#viewbtn').prop('disabled',false);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                alert(responseArray.message);
                $('#viewbtn').html(`Compile`);
                $('#viewbtn').prop('disabled',false);
            }
        });
    }

    function download(){
        var course = $('#course').val();
        var batch = $('#batch').val();
        var expsemester = $('#semester').val();
        if(course != '' && batch != '' &&  expsemester != ''){
            $('#downloadbtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching`);
        }else{
            $('#downloadbtn').html(`Print Admitcard`);
        }
    }

    function option(e){
        var data = $(e).val();
        switch(data){
            case 'print':
            $('#optionbtn').html(`<button class="w-100 bg-success border border-dark rounded p-2 text-white">Print Admitcard</button>`);
            $('#term').attr('disabled','true');
            $('#institute').attr('required','true');
            break;
            case 'compile':
            $('#optionbtn').html(`<button type="button" onclick="draft()" id="compilebtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Compile</button>`);
            $('#term').removeAttr('disabled');
            $('#institute').removeAttr('required');
            break;
        }
    }
</script>
@endsection
