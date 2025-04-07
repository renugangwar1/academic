@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('JNU Result') }}
                </div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    {{-- @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }} --}}
                    <form action="{{ Route('showjnuresult') }}" method="get" id="JnuresultForm">
                        @csrf
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="course" name="course" required>
                                    <option value="">Select Course</option>
                                    @foreach($corse as $duration=>$single)
                                        <option duration="{{ $duration }}" value="{{ $single }}" {{ old('course') == $single ? 'selected' : '' }}>{{ $corsename[$single] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" id="batch" name="batch" required>
                                    <option value="">Select Batch</option>
                                    @foreach(batch() as $batch)
                                        <option value="{{ $batch }}" {{ old('batch') == $batch ? 'selected' : '' }}>{{ $batch }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" name="semester" required>
                                    <option value="">Select Semester</option>
                                    @foreach($semester as $single)
                                        <option value="{{ $single }}" {{ old('semester') == $single ? 'selected' : '' }}>{{ $single . ' Semester' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-100">
                                <select class="w-100 p-2 rounded mb-2 text-capitalize" name="institute">
                                    <option value="">Select Institute</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{ $institute->id }}" {{ old('institute') == $institute->id ? 'selected' : '' }}>{{ $institute->InstituteName }} ({{ $institute->InstituteCode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 w-100" id="optionbtn">
                            <button type="submit" onclick="Print()" class="w-50 bg-success border border-dark rounded p-2 text-white">Print Result</button>
                            <button type="button" onclick="Export()" class="w-50 bg-success border border-dark rounded p-2 text-white">Excel Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function Export(e){
        $url = '{{Route("excel.exportjnuresult")}}';
        $('#JnuresultForm').attr('action',$url);
        $('#JnuresultForm').attr('method','post');
        $('#JnuresultForm').submit();
    }

    function Print(){
        $url = `{{Route('showjnuresult')}}`;
        $('#JnuresultForm').attr('action',$url);
        $('#JnuresultForm').attr('method','get');
        $('#JnuresultForm').submit();
    }
</script>
@endsection
