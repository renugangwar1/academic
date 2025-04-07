@if($currentcompiled)
    <div class="w-100 bg-black text-bg-danger d-flex align-content-center fw-bold justify-content-center py-1">
        ( {{$currentcompiled['currentlist']}} ) out of ( {{$currentcompiled['totallist']}} ) has been compiled.
    </div>
@endif
<table id="myTable" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark text-nowrap">
        <tr>
            <th>S.No</th>
            <th>NCHMCT Roll Number</th>
            <th>JNU Roll Number</th>
            <th>Name</th>
            <th>Acadeic Year</th>
            <th>Reappear Sub</th>
            <th>Appear Sub</th>
            <th>Reappear Count</th>
        </tr>
    </thead>
    <tbody class="table-group-divider text-nowrap">
        @foreach($compileresult as $key=>$single)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$single->student->NCHMCT_Rollnumber}}</td>
                <td>{{$single->student->JNU_Rollnumber}}</td>
                <td>{{$single->student->name}}</td>
                <td>{{$single->Stud_academic_year}}</td>
                <td>{{$single->Mid_Reappear_subject}}</td>
                <td>{{$single->Mid_Appear_subject}}</td>
                <td>{{stringcount($single->Mid_Reappear_subject)}}</td>
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
