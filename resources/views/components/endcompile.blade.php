@if($currentcompiled)
    <div class="w-100 bg-black text-bg-danger d-flex align-content-center fw-bold justify-content-center py-1">
        ( {{$currentcompiled['currentlist']}} ) out of ( {{$currentcompiled['totallist']}} ) has been compiled.
    </div>
@endif
<table id="myTable" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark text-nowrap">
        <tr>
            <th>Name</th>
            <th>NCHMCT Roll Number</th>
            <th>JNU Roll Number</th>
            <th>Reappear Sub</th>
            <th>Reappear Count</th>
        </tr>
    </thead>
    <tbody class="table-group-divider text-nowrap">
        @foreach($compileresult as $key=>$single)
        <tr>
            <td>{{$single->student->name ?? '--'}}</td>
            <td>{{$single->student->NCHMCT_Rollnumber ?? '--'}}</td>
            <td>{{$single->student->JNU_Rollnumber ?? '--'}}</td>
            <td>{{$single->Total_Reappear_subject ?? '--'}}</td>
            <td>{{$single->Reappear_subject_count ?? '--'}}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('document').ready(function() {
        new DataTable('#myTable', {
            layout: {
                topStart: {
                    buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5']
                }
            },
            scrollX: true,
            responsive: true,
        });
        $('input[type=search]').addClass(`bg-light mb-2`);
        $('#compileresult').addClass(`p-1`);
        $('.dataTable').addClass(`border border-dark`);
    });
</script>