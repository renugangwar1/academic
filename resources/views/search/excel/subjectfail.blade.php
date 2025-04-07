<table id="subjectview" class="display table-bordered table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark">
        <tr class="text-nowrap align-middle">
            @foreach($heading['head'] as $single)
                <th rowspan="2" class="text-center">{{strtoupper($single)}}</th>
            @endforeach
            @foreach($heading['data'] as $single)
                <th class="text-center" colspan="2">{{$single}}</th>
            @endforeach
        </tr>
        <tr class="text-nowrap align-middle">
            @foreach($heading['data'] as $single)
                <th>Mid Marks</th>
                <th>End Marks</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($newdata as $single)
            <tr class="text-nowrap ">
                @foreach($single['info'] as $listdata)
                    <td class="text-capitalize">{{$listdata}}</td>
                @endforeach 
                @foreach($heading['data'] as $singldata)
                    <td>{{$single['data'][$singldata]['mid_marks']}}</td>
                    <td>{{$single['data'][$singldata]['end_marks'] ?? '--'}}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
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