<table id="itstudentsearch" class="display table-bordered table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark">
        <tr class="text-nowrap align-middle">
            <th>S.No</th>
            <th>Name</th>
            <th>NCHM Roll Number</th>
        </tr>
    </thead>
    <tbody>
        @if($itstudent)
            @foreach($itstudent as $key=>$single)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$single['name'] ?? '--'}}</td>
                    <td>{{$single['NCHMCT_Rollnumber'] ?? '--'}}</td>
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