@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5">{{ __('Printer Data') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <form method="POST" action="{{Route('excel.exportprinterData')}}" id="historyForm" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" id="course" name="historycourse" required>
                                    <option value="">Select Course *</option>
                                    @foreach($course as $duration=>$single)
                                        <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="batch" name="historybatch" required>
                                    <option value="">Select Batch *</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}">{{$batch}}</option>
                                    @endforeach
                                    <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="expsemester" name="historysemester" required>
                                    <option value="">Select Semester *</option>
                                    @foreach($semester as $single)
                                        <option value="{{$single}}">{{$single.' Semester'}}</option>
                                    @endforeach
                                    <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded text-capitalize" name="exportinstitute">
                                    <option value="">Select Institute</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{$institute->InstituteCode}}">{{$institute->InstituteName}} ( {{$institute->InstituteCode}} )</option>
                                    @endforeach
                                    <!-- <option value="MScfirstsem">M.Sc First Sem</option> -->
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <div class="d-flex w-100 gap-2">
                                    <button type="button" onclick="printer()" id="historybtn" class="flex-1 border border-dark w-100 p-2 rounded bg-dark text-white">Search</button>
                                    <button id="exportexel" onclick="download()" class="flex-1 border border-dark w-100 p-2 rounded bg-dark text-white" disabled>Download Excel</button>
                                </div>
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
    // function download(){
    //     $('#exportexel').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Download Excel');
    // }
    function printer(){
        var formData = $("#historyForm").serialize();

        $('#historybtn').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching');
        $('#historybtn').prop('disabled',true);
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("excel.getprinterData")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#showhistory').removeClass(`d-none`);
                $('#showhistory').html(response);
                $('#exportexel').prop('disabled',false);
                $('#historybtn').html(`Search`);
                $('#historybtn').prop('disabled',false);
            },
            error: function(xhr, status, error) {
                var responseArray = JSON.parse(xhr.responseText);
                $('#showhistory').removeClass(`d-none`);
                $('#showhistory').html(responseArray.exception ? 'Please Provide All Required Fields' : responseArray.message);
                $('#historybtn').html(`Search`);
                $('#exportexel').prop('disabled',true);
                $('#historybtn').prop('disabled',false);
            }
        });
    }
</script>
@endsection
