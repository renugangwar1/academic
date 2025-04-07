@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Student Master') }}
                    <!-- Button trigger modal -->
                    <a href="{{Route(!isset($ins->InstituteCode) ? 'admin.uploadstudentlist' : 'institute.uploadstudentlist')}}" class="btn btn-dark">
                        Update student list
                    </a>
                </div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="studenttable" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">Student Name</th>
                                <th scope="col" class="text-truncate">NCHM RollNumber</th>
                                <th scope="col" class="text-truncate">JNU RollNumber</th>
                                <th scope="col" class="text-truncate">Batch</th>
                                <th scope="col" class="text-truncate">Course</th>
                                <th scope="col" class="text-truncate">Optional Subjects</th>
                                <th scope="col" class="text-truncate">Status</th>
                                @if(!isset($ins))
                                    <th scope="col" class="text-truncate">Action</th>
                                @endif
                            </tr>
                        </thead>  
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        new DataTable('#studenttable', {
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
                        },
                        {
                            extend: 'pdfHtml5', className: 'text-bg-dark',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ],
                }
            },
            processing: true,
            ajax: {
                url: "{{ route('ajaxstudentdata',$ins->InstituteCode ?? 0) }}",
                type: 'GET',
                dataSrc: function(json) {
                    return json.data;
                },
                error: function(xhr, error, code) {
                    console.log(xhr.responseText); // Log any AJAX errors
                }
            },
            columns: [
                { 
                    data: null, 
                    name: 'id', 
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data, type, row) {
                        return `<td class="text-start"><span class="text-uppercase">${row.name}</span></td>`;
                    }
                },
                { 
                    data: 'NCHMCT_Rollnumber', 
                    name: 'NCHMCT_Rollnumber',
                    render: function(data, type, row) {
                        return `<td class="text-uppercase text-start">${row.NCHMCT_Rollnumber}</td>`;
                    }
                },
                { 
                    data: 'JNU_Rollnumber', 
                    name: 'JNU_Rollnumber',
                    render: function(data, type, row) {
                        return `<td class="text-uppercase text-start">${row.JNU_Rollnumber}</td>`;
                    }
                },
                { 
                    data: 'batch', 
                    name: 'batch',
                    render: function(data, type, row) {
                        return `<td class="text-uppercase text-nowrap">${row.batch}</td>`;
                    }
                },
                { 
                    data: 'course_id', 
                    name: 'course_id',
                    render: function(data, type, row) {
                        return `<td><span class="text-uppercase">${row.course.Course_name}</span></td>`;
                    }
                },
                { 
                    data: 'optionalSubject', 
                    name: 'optionalSubject',
                    render: function(data, type, row) {
                        if(row.optionalSubject) {
                            let subjects = '';
                            $.each(row.optionalSubject, function(key, val) {
                                if(val) {
                                    subjects += `<div class="text-uppercase">(${key}:<strong>${val || 'N/A'}</strong>)</div>`;
                                }
                            });
                            return `<td>${subjects}</td>`;
                        }
                        return '';
                    }
                },
                { 
                    data: 'email_verified_at', 
                    name: 'email_verified_at',
                    render: function(data, type, row) {
                        if(row.email_verified_at) {
                            return `<span class="btn text-success fw-bold border p-2 rounded border-success font-monospace">Active</span>`;
                        } else {
                            return `<span class="btn text-danger fw-bold border p-2 rounded border-danger font-monospace">Inactive</span>`;
                        }
                    }
                },
                @if(!isset($ins->InstituteCode))
                { 
                    data: null, 
                    name: 'actions',
                    render: function(data, type, row) {
                        return `
                            @if(!isset($ins->InstituteCode))
                                <div class="d-flex gap-2">
                                    <button class="btn btn-danger font-monospace" onclick="event.preventDefault(); if(confirm('Action Required')){ $('.delete-student_${row.id}').submit(); }">Delete</button>
                                    <form class="delete-student_${row.id} font-monospace" action="{{Route('admin.deletestudent')}}" method="POST" class="d-none">
                                        @csrf
                                        <input type="hidden" value="${row.id}" name="id"/>
                                    </form>
                                </div>
                            @endif
                        `;
                    }
                }
                @endif
            ]
        });
    });
</script>
@endsection