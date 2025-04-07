@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Course Master') }}
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add New Course
                    </button>
                </div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="subjectmaster" class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">Course Name</th>
                                <th scope="col" class="text-truncate">Min Duration</th>
                                <th scope="col" class="text-truncate">Max Duration</th>
                                <th scope="col" class="text-truncate">Course type</th>
                                <th scope="col" class="text-truncate">Course Credit</th>
                                <th scope="col" class="text-truncate">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($courses as $key=>$course)
                            <tr>
                                <th scope="row">{{$key+1}}</th>
                                <td class="text-uppercase">{{$course->Course_name}}</td>
                                <td class="text-uppercase">{{$course->Min_duration}}</td>
                                <td class="text-uppercase">{{$course->Max_duration}}</td>
                                <td class="text-capitalize text-start">{{$course->Course_type == '1' ? 'Digree' : 'Diploma'}}</td>
                                <td class="text-capitalize text-start">{{$course->Course_credit}}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark font-monospace" data-bs-toggle="modal" data-bs-target="#updatecourseModal" onclick="updatecourse('{{$course}}')" type="button">Update</button>
                                        <button class="btn btn-danger font-monospace" onclick="event.preventDefault();
                                                     if(confirm('You are about to delete {{strtoupper($course->Course_name)}}')){$('.delete-course{{$course->id}}').submit();}">Delete</button>

                                        <form class="delete-course{{$course->id}}" action="{{Route('admin.deletecourse')}}" method="POST" class="d-none">
                                            @csrf
                                            <input type="hidden" value="{{$course->id}}" name="id"/>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{Route('admin.addcours')}}" method="Post">
                    @csrf
                    @method('Post')
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="course_name">Course Name *</label>
                            <input type="text" class="form-control @error('course_name') is-invalid @enderror" id="course_name" name="course_name" value="{{ old('course_name') }}" placeholder="Name">
                            @error('course_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="semester">Course Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                <option value="">Select Option</option>
                                <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>Digree</option>
                                <option value="2" {{ old('type') == '2' ? 'selected' : '' }}>Diploma</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="min_duration">Min Duration *</label>
                            <input type="text" class="form-control @error('min_duration') is-invalid @enderror" id="min_duration" name="min_duration" value="{{ old('min_duration') }}" placeholder="Min Duration">
                            @error('min_duration')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="max_duration">Max Duration *</label>
                            <input type="text" class="form-control @error('max_duration') is-invalid @enderror" id="max_duration" name="max_duration" value="{{ old('max_duration') }}" placeholder="Max Duration">
                            @error('max_duration')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="credit">Credit *</label>
                            <input type="text" class="form-control @error('credit') is-invalid @enderror" id="credit" name="credit" value="{{ old('credit') }}" placeholder="Credit">
                            @error('credit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="form-group flex-fill">
                            <button class="btn btn-success w-100">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="updatecourseModal" tabindex="-1" aria-labelledby="updatecourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatecourseModalLabel">Update Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="updatecourse">
                
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        new DataTable('#subjectmaster', {
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
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
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

    function draft() {
        var formData = $("#compileForm").serialize();

        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("compiling_result")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                // Handle the success response
                $('#compileresult').removeClass(`p-1`);
                $('#compileresult').html(response);
                // console.log(response);
            },
            error: function(xhr, status, error) {
                // Handle errors
                alert(xhr.responseText);
            }
        });
    }

    function updatecourse(data){
        var inst = JSON.parse(data);
        var updateform = `
            <form action="{{Route('admin.addcours')}}" method="Post">
                @csrf
                @method('Post')
                <input type="hidden" class="form-control text-uppercase" name="id" value="${inst.id}">
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="course_name">Course Name *</label>
                        <input type="text" class="form-control @error('course_name') is-invalid @enderror" id="course_name" name="course_name" value="${inst.Course_name} {{ old('course_name') }}" placeholder="Name">
                        @error('course_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="uptype">Course Type *</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="uptype" name="type">
                            <option value="">Select Option</option>
                            <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>Degree</option>
                            <option value="2" {{ old('type') == '2' ? 'selected' : '' }}>Diploma</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="min_duration">Min Duration *</label>
                        <input type="text" class="form-control @error('min_duration') is-invalid @enderror" id="min_duration" name="min_duration" value="${inst.Min_duration} {{ old('min_duration') }}" placeholder="Min Duration">
                        @error('min_duration')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="max_duration">Max Duration *</label>
                        <input type="text" class="form-control @error('max_duration') is-invalid @enderror" id="max_duration" name="max_duration" value="${inst.Max_duration} {{ old('max_duration') }}" placeholder="Max Duration">
                        @error('max_duration')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="credit">Credit *</label>
                        <input type="text" class="form-control @error('credit') is-invalid @enderror" id="credit" name="credit" value="${inst.Course_credit} {{ old('credit') }}" placeholder="Credit">
                        @error('credit')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="form-group flex-fill">
                        <button class="btn btn-success w-100">Submit</button>
                    </div>
                </div>
            </form>
        `;
        $('#updatecourse').html(updateform);
        
        $('#uptype').val(inst.Course_type != null ? inst.Course_type : '');
    }
</script>
@endsection