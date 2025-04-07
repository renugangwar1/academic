@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <div class="card border border-dark mb-3">
                <form method="POST" action="{{Route('admin.printitadmicard')}}" id="itstudForm" enctype="multipart/form-data">
                    <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('IT Student Search') }}
                        <!-- <label><input type="checkbox" name="ttype" value="3_to_it" title="Transfer student from third Semester to IT"> Transfer Student from thired semester to IT</label> -->
                    </div>

                    <div class="card-body bg-body-secondary rounded-bottom-1">
                        @csrf
                        @method('post')
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="form-select w-100 p-2 rounded text-capitalize" id="course" name="exportcourse" required>
                                    <option value="">Select Course</option>
                                    @foreach($course as $duration=>$single)
                                        <option duration="{{$duration}}" value="{{$single}}" {{ old('exportcourse') == $single ? 'selected' : '' }}>{{$corsename[$single]}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please Select Course</div>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="form-select w-100 p-2 rounded mb-2 text-capitalize" id="batch" name="exportbatch" required>
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{$batch}}" {{ old('exportbatch') == $batch ? 'selected' : '' }}>{{$batch}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please Select Batch</div>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="form-select w-100 p-2 rounded text-capitalize @error('exportinstitute') is-invalid @enderror" id="exportinstitute" name="exportinstitute">
                                    <option value="">Select Institute</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{$institute->InstituteCode}}" {{ old('exportinstitute') == $institute->InstituteCode ? 'selected' : '' }}>{{$institute->InstituteName}} ( {{$institute->InstituteCode}} )</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please Select Course</div>
                                @if ($errors->has('exportinstitute'))
                                    <span class="text-danger">{{ $errors->first('exportinstitute') }}</span>
                                @endif
                            </div>
                            <div class="flex-1 w-100">
                                <label class="d-flex gap-2">
                                    <div class="align-content-around border-black mt-0 pt-0 px-2 ratio rounded" style="width: 30px;">
                                        <input type="checkbox" name="ttype" value="3_to_it" title="Transfer student from third Semester to IT">
                                    </div>
                                    <strong>Transfer Student from 3 semester to IT</strong>
                                </label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between gap-2">
                            <button type="button" onclick="search()" id="searchtbtn" class="w-100 btn btn-dark border border-dark rounded text-white text-uppercase">Search</button>
                            <button type="button" onclick="transfertoit()" id="transferbtn" class="w-100 btn btn-success border border-dark rounded text-white text-uppercase">Transfer to IT</button>
                            <button type="button" id="printbtn" onclick="print()" class="w-100 btn btn-warning border border-dark rounded text-white text-uppercase">Print</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card border border-dark mb-3">
                <div class="card-body bg-body-secondary rounded" id="showsub">
                    <table id="itstudentsearch" class="display table-bordered table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr class="text-nowrap align-middle">
                                <th>S.No</th>
                                <th>Name</th>
                                <th>NCHM Roll Number</th>
                                <th>JNU Roll Number</th>
                                <th>IT Subjects</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function search(e){
        $('#searchtbtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Search`);
        var formData = $("#itstudForm").serialize();

        var formDataObject = {};
        formData.split("&").forEach(function(pair) {
            var [key, value] = pair.split("=");
            formDataObject[decodeURIComponent(key)] = decodeURIComponent(value.replace(/\+/g, ' '));
        });
        
        if(formDataObject.exportcourse != '' && formDataObject.exportbatch != ''){
            $('select').removeClass(`is-invalid`);
            $.ajax({
                type: 'POST',
                url: '{{Route("admin.searchforitstudent")}}', // Replace with your server endpoint
                data: formData,
                success: function(response) {
                    $('#searchtbtn').html(`Search`);
                    $('#showsub').html(response);    
                },
                error: function(xhr, status, error) {
                    var responseArray = JSON.parse(xhr.responseText);
                    $('#showsub').html(``);
                    $('#searchtbtn').html(`Search`);
                }
            });
        }else{
            formDataObject.exportcourse == '' ? $('#course').addClass(`is-invalid`) : $('#course').removeClass(`is-invalid`);
            formDataObject.exportbatch == '' ? $('#batch').addClass(`is-invalid`) : $('#batch').removeClass(`is-invalid`);
        }
    }

    function transfertoit(){
        $('#transferbtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Transfer to IT`);

        var formData = $("#itstudForm").serialize();

        var formDataObject = {};
        
        formData.split("&").forEach(function(pair) {
            var [key, value] = pair.split("=");
            formDataObject[decodeURIComponent(key)] = decodeURIComponent(value.replace(/\+/g, ' '));
        });
        
        if(formDataObject.exportcourse != '' && formDataObject.exportbatch != ''){
            $('select').removeClass(`is-invalid`);
            $.ajax({
                type: 'POST',
                url: '{{Route("admin.transfertoitstudents")}}', // Replace with your server endpoint
                data: formData,
                success: function(response) {
                    $('#transferbtn').html(`Transfer to IT`);
                    $('#showsub').html(response);    
                },
                error: function(xhr, status, error) {
                    var responseArray = JSON.parse(xhr.responseText);
                    $('#Message').html(`<div class="bg-body fade-in form-select-sm font-monospace m-1 p-1 rounded px-2 border border-2 border-danger">${responseArray.errors}</div>`).show();
                    $('#transferbtn').html(`Transfer to IT`);
                }
            });

            setTimeout(function(){
                $('#Message').fadeOut('slow');
            },'5000');
        }else{
            formDataObject.exportcourse == '' ? $('#course').addClass(`is-invalid`) : $('#course').removeClass(`is-invalid`);
            formDataObject.exportbatch == '' ? $('#batch').addClass(`is-invalid`) : $('#batch').removeClass(`is-invalid`);
        }
    }

    function print(){

        $('#printbtn').html(`<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Print`);
        
        var selectedGender = $('input[name="created_date"]:checked').val();
        
        if(selectedGender != undefined){
            $('#itstudForm').append(`<input type="hidden" value="${selectedGender}" name="created_at"/>`);
            $('#itstudForm').submit();
            return;
        }

        var formData = $("#itstudForm").serialize();

        var formDataObject = {};
        
        formData.split("&").forEach(function(pair) {
            var [key, value] = pair.split("=");
            formDataObject[decodeURIComponent(key)] = decodeURIComponent(value.replace(/\+/g, ' '));
        });

        if(formDataObject.exportcourse != '' && formDataObject.exportbatch != '' && formDataObject.exportinstitute != ''){
            $('select').removeClass(`is-invalid`);
            $.ajax({
                type: 'POST',
                url: '{{Route("admin.itadmitcardprint")}}', // Replace with your server endpoint
                data: formData,
                success: function(response) {
                    $('#printbtn').html(`Print`);
                    $('#showsub').html(response);    
                },
                error: function(xhr, status, error) {
                    var responseArray = JSON.parse(xhr.responseText);
                    $('#showsub').html(``);
                    $('#printbtn').html(`Print`);
                }
            });
        }else{
            formDataObject.exportcourse == '' ? $('#course').addClass(`is-invalid`) : $('#course').removeClass(`is-invalid`);
            formDataObject.exportbatch == '' ? $('#batch').addClass(`is-invalid`) : $('#batch').removeClass(`is-invalid`);
            formDataObject.exportinstitute == '' ? $('#exportinstitute').addClass(`is-invalid`) : $('#exportinstitute').removeClass(`is-invalid`);
            $('#printbtn').html(`Print`);
        }
    }
</script>

<script>
    $(document).ready(function(){
        new DataTable('#itstudentsearch', {
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'copyHtml5', className: 'text-bg-dark',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'excelHtml5', className: 'text-bg-dark',
                            // exportOptions: {
                            //     columns: ':not(:last-child)'
                            // }
                        },
                        {
                            extend: 'csvHtml5', className: 'text-bg-dark',
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
</script>
@endsection
