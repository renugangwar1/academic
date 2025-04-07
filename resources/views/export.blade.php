@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5">{{ __('Export Result Data') }}</div>

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
                        <h5 class="fw-bold">Export Excel</h5>
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
                            <button type="button" onclick="Export()" id="exportbtn" class="w-100 bg-dark border border-dark rounded text-white">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function Export(e){
        $('#exportbtn').html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Exporting');
        // $url = '{{Route("excel.exportdata")}}';
        // $('#excelForm').attr('action',$url);
        // $('#excelForm').submit();
        var course = $('#course').val();
        var batch = $('#batch').val();
        var expsemester = $('#expsemester').val();

        var formData = $("#excelForm").serialize();

        $('#compilebtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Compiling`);
        
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("excel.exportdata")}}', // Replace with your server endpoint
            data: formData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = course+'_'+'sem'+expsemester+'_'+batch+'_Export.xlsx';
                link.click();
                // Handle the success response
                $('#exportbtn').html('Export');
                $('#exportbtn').prop('disabled',flase);
            },
            error: function(jqXHR) {
                if (jqXHR.status === 429) {
                    alert('Request is already in process.');
                } else {
                    alert('An error occurred.');
                }
            }
        });
    }
</script>
@endsection
