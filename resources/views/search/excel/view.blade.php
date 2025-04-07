@extends('layouts.app')

@section('content')
@php
 $searchinfo = json_decode($jsonArray);
@endphp
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark position-relative">
                <div>
                    <div class="position-absolute top-nv-13 d-flex gap-1">
                        <div class="bg-black rounded text-white p-1 pb-0 border">{{strtoupper($course[$searchinfo[0]])}}</div>
                        <div class="bg-black rounded text-white p-1 pb-0 border">{{ordinalget($searchinfo[2])}} Semester</div>
                        <div class="bg-black rounded text-white p-1 pb-0 border">{{$searchinfo[1]}}</div>
                        @if(isset($searchinfo[3]))
                            <div class="bg-black rounded text-white p-1 pb-0 border">Institute Code: {{$searchinfo[3]}}</div>
                        @endif
                    </div>
                    <a href="{{ route('excel.viewdata') }}" class="btn btn-danger p-1 pb-0 position-absolute top-0 top-nv-13 end-0 border" title="Return to Search Page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
                        </svg> Return
                    </a>
                </div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="excelview" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2" class="align-content-center">S.No</th>
                                @foreach($heading as $head)
                                    <th class="text-nowrap align-content-center" rowspan="2">{{$head}}</th>
                                @endforeach
                                @foreach($subjecthead as $subject)
                                    <th class="text-nowrap text-center align-content-center" colspan="5">{{$subject['code']}} ( {{$subject['credit']}} )</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($subjecthead as $subject)
                                    <th class="text-nowrap">Mid</th>
                                    <th class="text-nowrap">End</th>
                                    <th class="text-nowrap">GP</th>
                                    <th class="text-nowrap">CP</th>
                                    <th class="text-nowrap">Grade</th>
                                @endforeach
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Parse the JSON data
        var provid = JSON.parse(`{!! $jshead !!}`);
        
        // Create the columns array
        var columns = [{
            data: null,
            name: 'id',
            render: function(data, type, row, meta) {
                return meta.row + 1; // Row index + 1 for serial number
            }
        }];

        // Iterate over the provid array and add columns dynamically
        provid.forEach(function(item, index) {
            columns.push({
                data: item,
                name: item,
                // title: item // You can customize the title if needed
                createdCell: function(cell) {
                    $(cell).addClass('text-nowrap');
                }
            });
        });
        
        // Initialize the DataTable
        new DataTable('#excelview', {
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'copyHtml5', className: 'text-bg-dark',
                        },
                        {
                            extend: 'excelHtml5', className: 'text-bg-dark',
                        },
                        {
                            extend: 'csvHtml5', className: 'text-bg-dark',
                        }
                    ],
                }
            },
            scrollX: true,
            responsive: true,
            processing: true,
            ajax: {
                url: "{{ route('ajaxdataview', $jsonArray) }}",
                type: 'GET',
                dataSrc: function(json) {
                    return json.data; // Ensure dataSrc returns the correct data array
                },
                error: function(xhr, error, code) {
                    console.log(xhr.responseText); // Log any AJAX errors
                }
            },
            columns: columns
        });

        // Additional UI adjustments
        $('input[type=search]').addClass('bg-light mb-2');
        $('#compileresult').addClass('p-1');
        $('.dataTable').addClass('border border-dark');
    });
</script>
@endsection