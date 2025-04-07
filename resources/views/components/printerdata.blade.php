<table id="printerdata" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
    <thead class="table-dark">
        <tr class="text-nowrap">
            @foreach($headingstart as $key=>$head)
                <th class="text-center align-content-center" rowspan="{{$head}}">{{$key}}</th>
            @endforeach
            @foreach($subject as $key=>$head)
                <th class="text-center align-content-center" colspan="{{$head}}">{{$key}}</th>
            @endforeach
            @foreach($headingend as $key=>$head)
                <th class="text-center align-content-center" colspan="{{$head}}">{{$key}}</th>
            @endforeach
        </tr>
        <tr class="text-nowrap">
            @foreach($subject as $key=>$head)
                @foreach($subjectinner as $inner)
                    <th>{{$inner}}</th>
                @endforeach
            @endforeach
            @foreach($headingend as $key=>$head)
                @foreach($headingendinner as $inner)
                    <th>{{$inner}}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach($results as $single)
            <tr>
                @foreach($single['data'] as $key=>$info)
                    <td key="{{$key}}" class="text-nowrap align-content-center @if($key === 'Stud_name') text-start @endif text-capitalize">{{$info}}</td>
                @endforeach
                <td class="text-nowrap align-content-center">{{$single['totalcredit']}}</td>
                @foreach($subject as $key=>$head)
                    @if(isset($single['subjectarray'][removeNonDigits($key)]))
                        <td class="text-nowrap align-content-center">{{$single['subjectarray'][removeNonDigits($key)]['coursecode']}}</td>
                        <td class="text-nowrap align-content-center text-capitalize">{{$single['subjectarray'][removeNonDigits($key)]['coursetitle']}}</td>
                        <td class="text-nowrap align-content-center">{{$single['subjectarray'][removeNonDigits($key)]['coursecredit']}}</td>
                        <td class="text-nowrap align-content-center">{{$single['subjectarray'][removeNonDigits($key)]['coursegrade']}}</td>
                    @else
                        <td class="text-nowrap align-content-center">--</td>
                        <td class="text-nowrap align-content-center text-capitalize">--</td>
                        <td class="text-nowrap align-content-center">--</td>
                        <td class="text-nowrap align-content-center">--</td>
                    @endif
                @endforeach
                @foreach($single['currensemrecord'] as $eone)
                    <td class="text-nowrap align-content-center">{{$eone}}</td>
                @endforeach
                @foreach($single['cumulativerecord'] as $etwo)
                    <td class="text-nowrap align-content-center">{{$etwo}}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('document').ready(function(){
        new DataTable('#printerdata', {
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
