@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Subject Master') }}
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add New Subject
                    </button>
                </div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="subjectmaster" class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">Course Code</th>
                                <th scope="col" class="text-truncate">Duration</th>
                                <th scope="col" class="text-truncate">Semester</th>
                                <th scope="col" class="text-truncate">Subject Code</th>
                                <th scope="col" class="text-truncate">Subject Name</th>
                                <th scope="col" class="text-truncate">Subject Type</th>
                                <th scope="col" class="text-truncate">Optional Subject</th>
                                <th scope="col" class="text-truncate">Credit</th>
                                <th scope="col" class="text-truncate">Mid Max Mark</th>
                                <th scope="col" class="text-truncate">Mid Pass Mark</th>
                                <th scope="col" class="text-truncate">End Max Mark</th>
                                <th scope="col" class="text-truncate">End Pass Mark</th>
                                <th scope="col" class="text-truncate">Reappear Fee</th>
                                <th scope="col" class="text-truncate">Is IT</th>
                                <th scope="col" class="text-truncate">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($subjects as $key=>$subject)
                            <tr>
                                <th scope="row">{{$key+1}}</th>
                                <td class="text-uppercase">{{$subject->Course->Course_name}}</td>
                                <td class="text-uppercase">{{$subject->Course->Min_duration}}</td>
                                <td>{{$subject->Semester}}</td>
                                <td class="text-uppercase">{{$subject->Subject_code}}</td>
                                <td class="text-capitalize text-start text-nowrap">{{$subject->Subject_name}}</td>
                                <td class="text-capitalize text-start">{{$subject->Subject_type}}</td>
                                <td class="text-uppercase">{{$subject->Optional_subject != 1 ? 'No' : 'Yes'}}</td>
                                <td>{{$subject->Credit}}</td>
                                <td>{{$subject->Mid_max_mark}}</td>
                                <td>{{$subject->Mid_pass_mark}}</td>
                                <td>{{$subject->End_max_mark}}</td>
                                <td>{{$subject->End_pass_mark}}</td>
                                <td>{{$subject->Reappear_fee}}</td>
                                <td>{{$subject->It_status == 1 ? 'Yes' : 'No'}}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark font-monospace" data-bs-toggle="modal" data-bs-target="#updatesubjectModal" onclick="updatesubject('{{$subject}}')" type="button">Update</button>
                                        <button class="btn btn-danger font-monospace" onclick="event.preventDefault();
                                                     if(confirm('You are about to delete {{strtoupper($subject->Subject_code)}}')){$('.delete-subject{{$subject->Subject_code}}').submit();}">Delete</button>

                                        <form class="delete-subject{{$subject->Subject_code}}" action="{{Route('admin.deletesubject')}}" method="POST" class="d-none">
                                            @csrf
                                            <input type="hidden" value="{{$subject->id}}" name="id"/>
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
                <h5 class="modal-title" id="exampleModalLabel">Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.addsubject') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="course_code">Course Code *</label>
                            <select class="form-control text-uppercase" id="cours_code" name="cours_code">
                                <option value="">Select Optional</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('cours_code') == $course->id ? 'selected' : '' }}>{{ $course->Course_name }}</option>
                                @endforeach
                            </select>
                            @error('cours_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="optional_subject">Optional Subject</label>
                            <select class="form-control text-uppercase" id="optional_subject" name="optional_subject">
                                <option value="">Select Optional</option>
                                <option value="0" {{ old('optional_subject') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('optional_subject') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('optional_subject')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="semester">Semester *</label>
                            <input type="text" class="form-control" id="semester" name="semester" placeholder="Semester" value="{{ old('semester') }}">
                            @error('semester')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>        
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="subject_name">Subject Name *</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Name" value="{{ old('subject_name') }}">
                            @error('subject_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="it_status">It Select *</label>
                            <select class="form-control text-uppercase" id="it_status" name="it_status" required>
                                <option value="">Select Optional</option>
                                <option value="0" {{ old('it_status') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('it_status') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('optional_subject')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="Subject_code">Subject Code *</label>
                            <input type="text" class="form-control" id="Subject_code" name="Subject_code" placeholder="Code" value="{{ old('Subject_code') }}">
                            @error('Subject_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div> 
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="mid_max_mark">Mid Max Mark *</label>
                            <input type="text" class="form-control" id="mid_max_mark" name="mid_max_mark" placeholder="Marks" value="{{ old('mid_max_mark') }}">
                            @error('mid_max_mark')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="mid_pass_mark">Mid Pass Mark *</label>
                            <input type="text" class="form-control" id="mid_pass_mark" name="mid_pass_mark" placeholder="Marks" value="{{ old('mid_pass_mark') }}">
                            @error('mid_pass_mark')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="end_max_mark">End Max Mark *</label>
                            <input type="text" class="form-control" id="end_max_mark" name="end_max_mark" placeholder="Marks" value="{{ old('end_max_mark') }}">
                            @error('end_max_mark')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="end_pass_mark">End Pass Mark *</label>
                            <input type="text" class="form-control" id="end_pass_mark" name="end_pass_mark" placeholder="Marks" value="{{ old('end_pass_mark') }}">
                            @error('end_pass_mark')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="type_name">Select Type *</label>
                            <select class="form-control text-uppercase" id="type_name" name="type_name" required>
                                <option value="">Select Type</option>
                                <option value="practical" {{ old('type_name') == 'practical' ? 'selected' : '' }}>Practical</option>
                                <option value="theory" {{ old('type_name') == 'theory' ? 'selected' : '' }}>Theory</option>
                            </select>
                            @error('type_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="credit">Credit *</label>
                            <input type="text" class="form-control" id="credit" name="credit" placeholder="Credit" value="{{ old('credit') }}">
                            @error('credit')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group flex-fill">
                            <label for="Reappear_fee">Reappear Fee</label>
                            <input type="text" class="form-control" id="Reappear_fee" name="Reappear_fee" placeholder="Reappear Fee" value="{{ old('Reappear_fee') }}">
                            @error('Reappear_fee')
                                <div class="text-danger">{{ $message }}</div>
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
<div class="modal fade" id="updatesubjectModal" tabindex="-1" aria-labelledby="updatesubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatesubjectModalLabel">Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="updatesubject">
                
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

    function updatesubject(data){
        var inst = JSON.parse(data);

        var semester = oldinputvalcheck(`{{old('semester')}}`,inst.Semester);
        var subname = oldinputvalcheck(`{{old('subject_name')}}`,inst.Subject_name);
        var subcode = oldinputvalcheck(`{{old('Subject_code')}}`,inst.Subject_code);
        var midmax = oldinputvalcheck(`{{old('mid_max_mark')}}`,inst.Mid_max_mark);
        var midpass = oldinputvalcheck(`{{old('mid_pass_mark')}}`,inst.Mid_pass_mark);
        var endmax = oldinputvalcheck(`{{old('end_max_mark')}}`,inst.End_max_mark);
        var endpass = oldinputvalcheck(`{{old('end_pass_mark')}}`,inst.End_pass_mark);
        var credit = oldinputvalcheck(`{{old('credit')}}`,inst.Credit);
        var reappearfee = oldinputvalcheck(`{{old('reappear_fee')}}`,inst.Reappear_fee);
        var optionalsub = oldinputvalcheck(`{{old('optional_subject')}}`,inst.Optional_subject);
        var it_status = oldinputvalcheck(`{{old('it_status')}}`,inst.It_status);
        var subtype = oldinputvalcheck(`{{old('type_name')}}`,inst.Subject_type);
        var courseid = oldinputvalcheck(`{{old('cours_code')}}`,inst.course.id);

        var updateform = `
            <form action="{{ route('admin.addsubject') }}" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" class="form-control text-uppercase" name="id" value="${inst.id}">
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="course_code">Course Code *</label>
                        <select class="form-select text-uppercase" id="upcours_code" name="cours_code">
                            <option value="">Select Optional</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('cours_code') == $course->id ? 'selected' : '' }}>{{ $course->Course_name }}</option>
                            @endforeach
                        </select>
                        @error('cours_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="optional_subject">Optional Subject</label>
                        <select class="form-select text-uppercase" id="upoptional_subject" name="optional_subject">
                            <option value="">Select Optional</option>
                            <option value="0" {{ old('optional_subject') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('optional_subject') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('optional_subject')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="semester">Semester *</label>
                        <input type="text" class="form-control" id="semester" name="semester" placeholder="Semester" value="${semester}">
                        @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>        
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="subject_name">Subject Name *</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Name" value="${subname}">
                        @error('subject_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="upit_status">It Select *</label>
                        <select class="form-control text-uppercase" id="upit_status" name="it_status" required>
                            <option value="">Select Optional</option>
                            <option value="0" {{ old('it_status') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('it_status') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('it_status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="Subject_code">Subject Code *</label>
                        <input type="text" class="form-control" id="Subject_code" name="Subject_code" placeholder="Code" value="${subcode}">
                        @error('Subject_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div> 
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="mid_max_mark">Mid Max Mark *</label>
                        <input type="text" class="form-control" id="mid_max_mark" name="mid_max_mark" placeholder="Marks" value="${midmax}">
                        @error('mid_max_mark')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="mid_pass_mark">Mid Pass Mark *</label>
                        <input type="text" class="form-control" id="mid_pass_mark" name="mid_pass_mark" placeholder="Marks" value="${midpass}">
                        @error('mid_pass_mark')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="end_max_mark">End Max Mark *</label>
                        <input type="text" class="form-control" id="end_max_mark" name="end_max_mark" placeholder="Marks" value="${endmax}">
                        @error('end_max_mark')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="end_pass_mark">End Pass Mark *</label>
                        <input type="text" class="form-control" id="end_pass_mark" name="end_pass_mark" placeholder="Marks" value="${endpass}">
                        @error('end_pass_mark')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="type_name">Select Type *</label>
                        <select class="form-select text-uppercase" id="uptype_name" name="type_name" required>
                            <option value="">Select Type</option>
                            <option value="practical" {{ old('type_name') == 'practical' ? 'selected' : '' }}>Practical</option>
                            <option value="theory" {{ old('type_name') == 'theory' ? 'selected' : '' }}>Theory</option>
                        </select>
                        @error('type_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div> 
                    <div class="form-group flex-fill">
                        <label for="credit">Credit *</label>
                        <input type="text" class="form-control" id="credit" name="credit" placeholder="Credit" value="${credit}">
                        @error('credit')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group flex-fill">
                        <label for="Reappear_fee">Reappear Fee</label>
                        <input type="text" class="form-control" id="Reappear_fee" name="Reappear_fee" placeholder="Reappear Fee" value="${reappearfee ?? 0}">
                        @error('Reappear_fee')
                            <div class="text-danger">{{ $message }}</div>
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
        $('#updatesubject').html(updateform);

        $('#uptype_name').val(subtype != null ? subtype : '');
        $('#upoptional_subject').val(optionalsub != 1 ? 0 : 1);
        $('#upcours_code').val(courseid != null ? courseid : '');
        $('#upit_status').val(it_status != 1 ? 0 : 1);
    }
</script>
@endsection