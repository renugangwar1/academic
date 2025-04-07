<table id="historytable" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark">
        <tr class="text-nowrap">
            @foreach($heading as $head)
                <th>{{$head}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach($newdata as $key=>$data)
            <tr class="text-nowrap">
                @foreach($heading as $key2=>$head)
                    <td>{{$data[$key2]}}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('document').ready(function(){
        new DataTable('#historytable', {
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
        });
        $('input[type=search]').addClass(`bg-light mb-2`);
        $('#compileresult').addClass(`p-1`);
        $('.dataTable').addClass(`border border-dark`);
    });
</script> 
