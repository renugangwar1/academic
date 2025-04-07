<table id="myTable" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark">
        <tr>
            <th>S.No</th>
            <th>Name</th>
            <th>Reappear Sub</th>
            <th>Appear Sub</th>
            <th>Reappear Count</th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach($generateresult as $key=>$single)
            <tr>
                <td>{{$single->Stud_nchm_roll_number}}</td>
                <td>{{$single->Stud_name}}</td>
                <td>{{$single->End_Reappear_subject}}</td>
                <td>{{$single->End_Result}}</td>
                <td>{{$single->Reappear_subject_count}}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('document').ready(function(){
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
