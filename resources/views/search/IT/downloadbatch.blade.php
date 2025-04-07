<table id="itstudentsearch" class="display table-bordered table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark">
        <tr class="text-nowrap align-middle">
            <th>S.No</th>
            <th>Batch</th>
            <th>Semester</th>
            <th>Student Count</th>
            <th class="text-end">Generated Date</th>
        </tr>
    </thead>
    <tbody>
        @if($getacdmitcard)
            @foreach($getacdmitcard as $key=>$single)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$single->Stud_batch ?? '--'}}</td>
                    <td>{{$single->Stud_semester ?? '--'}}</td>
                    <td>{{$single->distinct_count ?? '--'}}</td>
                    <td class="text-end"><label><input type="radio" name="created_date" value="{{$single->created_at}}"> {{date('d-m-Y',strtotime($single->created_at))}}</label></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
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