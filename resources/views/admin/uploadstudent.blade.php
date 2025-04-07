@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="mb-3">
                @include('components.admin.uploadstudent')
            </div>
            <div class="mb-3">
                @include('components.admin.updatestudentoptionalsubject')
            </div>
        </div>
    </div>
</div>
@endsection