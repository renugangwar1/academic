@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Generate & Print for Result') }}
                    
                </div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    {{-- @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }} --}}
                    <form action="{{Route('generated_print')}}" id="generateForm">
                        @csrf
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-uppercase" id="course" name="course" required>
                                    <option value="">Select Course</option>
                                    @foreach($corse as $duration=>$single)
                                        <option class="text-uppercase" duration="{{$duration}}" value="{{$single}}" {{ old('course') == $single ? 'selected' : '' }}>{{$corsename[$single]}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-uppercase" id="batch" name="batch" required>
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}" {{ old('batch') == $batch ? 'selected' : '' }}>{{$batch}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-uppercase" name="semester" required>
                                    <option value="">Select Semester</option>
                                    @foreach($semester as $single)
                                        <option value="{{$single}}" {{ old('semester') == $single ? 'selected' : '' }}>{{$single.' Semester'}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-uppercase" name="institute">
                                    <option value="">Select Institute</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{$institute->id}}" {{ old('institute') == $institute->id ? 'selected' : '' }}>{{$institute->InstituteName}} ( {{$institute->InstituteCode}} )</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 w-100" id="optionbtn">
                            @if(Auth::user())
                                <button type="button" onclick="draft()" id="resultbtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Generate Result</button>
                                <button type="button" onclick="cgpa()" id="cgpabtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Final Result</button>
                                <button type="button" onclick="view()" id="viewbtn" class="w-100 bg-dark border border-dark rounded p-2 text-white">View Generate Result</button>
                            @endif
                            <button id="downloadbtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Print Result</button>
                        </div>
                        <div id="generateresult" class="p-1 d-none bg-dark-subtle rounded-1 w-100 my-3"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function draft(){
        var formData = $("#generateForm").serialize();
        
        $('#resultbtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Generate Result`);
        
        $.ajax({
            type: "POST",
            url: '{{Route("generating_result")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#generateresult').removeClass(`d-none`);
                $('#generateresult').html(response);
                $('#resultbtn').html(`Generate Result`);
                $('#resultbtn').prop('disabled',false);
                // console.log(response);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                alert(responseArray.message);
                // Handle errors
                $('#generateresult').addClass(`d-none`);
                $('#generateresult').html(responseArray.message);
                $('#resultbtn').html(`Generate Result`);
                $('#resultbtn').prop('disabled',false);
                // location.reload();
            }
        });
    }


    function cgpa(){
        var formData = $("#generateForm").serialize();
        
        $('#cgpabtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Final Result`);
        
        $.ajax({
            type: "POST",
            url: '{{Route("generating_cgpa")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#generateresult').removeClass(`d-none`);
                $('#generateresult').html(response);
                $('#cgpabtn').html(`Final Result`);
                $('#cgpabtn').prop('disabled',false);
                // console.log(response);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                alert(responseArray.message);
                // Handle errors
                $('#generateresult').addClass(`d-none`);
                $('#generateresult').html(responseArray.message);
                $('#cgpabtn').html(`Final Result`);
                $('#cgpabtn').prop('disabled',false);
                // location.reload();
            }
        });
    }


    function view(){
        var formData = $("#generateForm").serialize();
        
        $('#viewbtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching`);
        // $('#viewbtn').prop('disabled',true);
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("view_generating_result")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#generateresult').removeClass(`d-none`);
                $('#generateresult').html(response);
                $('#viewbtn').html(`View Generate Result`);
                $('#viewbtn').prop('disabled',false);
                // console.log(response);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                alert(responseArray.message);
                // console.log(responseArray.message);
                // Handle errors
                $('#generateresult').addClass(`d-none`);
                $('#generateresult').html(responseArray.message);
                $('#viewbtn').html(`View Generate Result`);
                $('#viewbtn').prop('disabled',false);
                // location.reload();
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
            $('#downloadbtn').html(`Print Result`);
        }
    }

    function option(value){
        var data = $(value).val();
        switch(data){
            case 'print':
            $('#optionbtn').html(`<button class="w-100 bg-success border border-dark rounded p-2 text-white">Print Result</button>`);
            break;
            case 'compile':
            $('#optionbtn').html(`<button type="button" onclick="draft()" id="compilebtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Generate Result</button>`);
            break;
        }
    }
</script>
<!-- $('input[type=radio]').on('change', function () {
            
        }); -->
@endsection
