@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5">{{ __('History Search') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <form method="POST" action="{{Route('excel.searchexcel')}}" id="historyForm" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="course" name="historycourse" required>
                                    <option value="">Select Course *</option>
                                    @foreach($course as $duration => $single)
                                        <option duration="{{$duration}}" value="{{$single}}" {{ old('historycourse') == $single ? 'selected' : '' }}>
                                            {{$corsename[$single]}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="batch" name="historybatch" required>
                                    <option value="">Select Batch *</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}" {{ old('historybatch') == $batch ? 'selected' : '' }}>
                                            {{$batch}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="expsemester" name="historysemester" required>
                                    <option value="">Select Semester *</option>
                                    @foreach($semester as $single)
                                        <option value="{{$single}}" {{ old('historysemester') == $single ? 'selected' : '' }}>
                                            {{$single . ' Semester'}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <input type="text" class="flex-1 border border-dark w-100 p-2 rounded" id="rollno" name="rollno" placeholder="Roll No *" value="{{ old('rollno') }}" required/>
                            </div>
                            <div class="flex-1 w-100">
                                <button type="button" onclick="history()" id="historybtn" class="flex-1 border border-dark w-100 p-2 rounded bg-dark text-white">Search</button>
                            </div>
                        </div>
                    </form>
                    <div id="showhistory" class="p-1 d-none bg-dark-subtle rounded-1 w-100 my-3 text-center fw-bold"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function history(){
        var formData = $("#historyForm").serialize();

        $('#historybtn').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching');
        $('#historybtn').prop('disabled',true);
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("excel.searchexcel")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#showhistory').removeClass(`d-none`);
                $('#showhistory').html(response);
                $('#historybtn').html(`Search`);
                $('#historybtn').prop('disabled',false);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                $('#showhistory').removeClass(`d-none`);
                $('#showhistory').html(responseArray.exception ? 'Please Provide All Required Fields' : responseArray.message);
                $('#historybtn').html(`Search`);
                $('#historybtn').prop('disabled',false);
            }
        });
    }
</script>
@endsection
