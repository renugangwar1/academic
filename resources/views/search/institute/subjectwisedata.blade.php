@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark position-relative">
                <div>
                    <a href="{{ route('excel.instituteview') }}" class="btn btn-danger p-1 pb-0 position-absolute top-0 top-nv-13 end-0 border" title="Return to Search Page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
                        </svg> Return
                    </a>
                </div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="subjectview" class="display table-bordered table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr class="text-nowrap align-middle">
                                <th rowspan="2">Ins Code</th>
                                <th rowspan="2">Ins Name</th>
                                <th rowspan="2">Course</th>
                                <th rowspan="2">Semester</th>
                                <th rowspan="2">Students</th>
                                @foreach($heading as $single)
                                    <th colspan="4" class="text-center">{{strtoupper($single)}}</th>
                                @endforeach
                            </tr>
                            <tr class="text-nowrap align-middle">
                                @foreach($heading as $single)
                                    <th class="bg-success">Pass</th>
                                    <th>Pass %</th>
                                    <th class="bg-danger">Not Pass</th>
                                    <th>Not Pass %</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($institute as $single)
                                <tr class="text-nowrap ">
                                    @foreach($single['info'] as $listdata)
                                        <td class="text-capitalize">{{$listdata}}</td>
                                    @endforeach
                                    @foreach($single['result'] as $key=>$subjectdata)
                                        @foreach($heading as $single)
                                            @if($single == $key)
                                                <td>{{$subjectdata['pass']}}</td>
                                                <td>{{$subjectdata['passpercent']}}<span class="fs-5 fw-lighter"> %</span></td>
                                                <td>{{$subjectdata['fail']}}</td>
                                                <td>{{$subjectdata['failpercent']}}<span class="fs-5 fw-lighter"> %</span></td>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        new DataTable('#subjectview', {
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