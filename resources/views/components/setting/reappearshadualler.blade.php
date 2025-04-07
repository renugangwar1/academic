<div class="d-grid">
    <div class="card border border-dark">
        <div class="card-header fw-bold fs-5 d-flex justify-content-between">
            Reappear Form Shadualer
            <button type="button" class="btn btn-dark font-monospace" onclick="newform('reappear')"
                data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add New Reappear
            </button>
        </div>
        <div class="card-body bg-secondary rounded-bottom-1">
            <div class="">
                <table id="myTable" class="display table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                    <thead class="table-dark text-nowrap">
                        <tr>
                            <th scope="col">Course</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Batch</th>
                            <th scope="col">Opening Date</th>
                            <th scope="col">Closing Date</th>
                            <th scope="col">Late Fee Date</th>
                            <th scope="col">Late Fee Amount</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-nowrap">
                        @foreach($Reappeardata as $data)
                        <tr>
                            <td scope="row" class="text-capitalize">{{$data->Course->Course_name}}</td>
                            <td>{{$data->semester}}</td>
                            <td class="">{{$data->batch}}</td>
                            <td class="">{{date('d-m-Y',strtotime($data->Reappear_from_date))}}</td>
                            <td class="">{{date('d-m-Y',strtotime($data->Reappear_to_date))}}</td>
                            <td class="">{{date('d-m-Y',strtotime($data->Reappear_late_fee_date))}}</td>
                            <td class="">{{$data->Reappear_late_fee}}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($data->Reappear_late_fee_date >= date('Y-m-d'))
                                    <button class="btn btn-dark font-monospace" data-bs-toggle="modal"
                                        data-bs-target="#updatereapaerModal"
                                        onclick="updateReappear('{{$data}}','{{$course}}')"
                                        type="button" title="Update">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                        </svg>
                                    </button>
                                    @endif
                                    <button class="btn btn-danger font-monospace"
                                        onclick="event.preventDefault();
                                                                if(confirm('You are about to delete. Are you sure?')){$('.delete-Reappearsetting_{{$data->id}}').submit();}" title="Remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                        </svg>                            
                                    </button>
    
                                    <form class="delete-Reappearsetting_{{$data->id}}"
                                        action="{{Route('admin.deleteReappearsetting')}}" method="POST"
                                        class="d-none">
                                        @csrf
                                        <input type="hidden" value="{{$data->id}}" name="id" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>