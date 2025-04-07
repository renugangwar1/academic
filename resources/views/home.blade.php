@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ">
        <div class="col-md-12 mb-5">
        <form action="{{Route('search')}}" id="searchingform" method="GET">
            @csrf
            @method('GET')
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex gap-2">
                    <div>{{ __('Search for ') }}<span class="text-danger">*</span></div>
                    <select class="form-select w-auto border border-dark" id="searchfor" name="searchfor" required>
                        <option value="">Search Option</option>
                        <option value="admitcard" {{ old('searchfor') == 'admitcard' ? 'selected' : '' }}>Admitcard</option>
                        <option value="result" {{ old('searchfor') == 'result' ? 'selected' : '' }}>Result</option>
                    </select>
                </div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    <div class="d-flex justify-content-between gap-2 mb-3">
                        <select class="form-select border border-dark w-100 text-uppercase" id="course" name="course" required>
                            <option value="">Select Course *</option>
                            @foreach($corse as $duration=>$single)
                                <option class="text-uppercase" duration="{{ $duration }}" value="{{ $single }}" {{ old('course') == $single ? 'selected' : '' }}>{{ $corsename[$single] }}</option>
                            @endforeach
                        </select>
                        <select class="form-select w-100 border border-dark text-uppercase" name="semester" required>
                            <option value="">Select Semester *</option>
                            @foreach($semester as $single)
                                <option value="{{ $single }}" {{ old('semester') == $single ? 'selected' : '' }}>{{ $single }} Semester</option>
                            @endforeach
                        </select>
                        <select class="form-select w-100 border border-dark text-uppercase" id="batch" name="batch" required>
                            <option value="">Select Batch *</option>
                            @foreach(batch() as $single)
                                <option value="{{ $single }}" {{ old('batch') == $single ? 'selected' : '' }}>{{ $single }}</option>
                            @endforeach
                        </select>
                        <select class="form-select w-100 border border-dark text-uppercase" name="institute">
                            <option value="">Select Institute</option>
                            @foreach($institutes as $single)
                                <option value="{{ $single->id }}" {{ old('institute') == $single->id ? 'selected' : '' }}>{{ $single->InstituteName }} ( {{ $single->InstituteCode }} )</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="align-items-center d-flex justify-content-around pb-3 position-relative w-100">
                        <hr class="w-100">
                        <span class="bg-light border border-dark fw-bold position-absolute px-1 rounded-2">Or</span>
                    </div>
                    <div class="d-flex justify-content-between gap-2 mb-3">
                        <div class="flex-1 w-100">
                            <input type="text" class="flex-1 border border-dark w-100 p-2 rounded" id="rollno" name="rollno" placeholder="Roll No" value="{{ old('rollno') }}"/>
                        </div>
                    </div>
                    <button type="button" id="search" class="w-100 bg-success border border-dark rounded p-2 text-white">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        let search = $('#search').html();
        console.log(search);
        // Your code here
    });
    $('#search').on('click', function(e) {
        e.preventDefault();
        
        // Reset button state
        $(this).prop('disabled', false).text('Search');
        
        // Validate required fields
        var isValid = true;
        $('#searchingform select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (isValid) {
            $(this).html('<span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Search');
            $('#searchingform').submit();
            $(this).prop('disabled', true);
        } else {
            alert('Please fill in all required fields.');
        }
    });

    // Remove is-invalid class when user changes the input
    $('#searchingform select[required]').on('change', function() {
        if ($(this).val() !== '') {
            $(this).removeClass('is-invalid');
        }
    });
</script>
@endsection
