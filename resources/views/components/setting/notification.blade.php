<div class="d-grid">
    <div class="card border border-dark">
        <div class="card-header fw-bold fs-5 d-flex justify-content-between">
            Notification
            <button type="button" class="btn btn-dark font-monospace" onclick="newform('notification')"
                data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add New Notification
            </button>
        </div>
        <div class="card-body bg-secondary rounded-bottom-1">
            <div class="">
                <table id="notification"
                    class="display table table-striped-columns w-100 table-border border-dark">
                    <thead class="table-dark text-nowrap">
                        <tr>
                            <th>Title</th>
                            <th>For</th>
                            <th>Opening Date</th>
                            <th>Closing Date</th>
                            <th>Link</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-bg-info text-nowrap">
                        @foreach($notification as $data)
                        <tr>
                            <td class="text-start">{{$data->Ntitle}}</td>
                            <td>{{$data->Nfor}}</td>
                            <td class="text-start">{{date('d-m-Y',strtotime($data->Nfrom_date))}}</td>
                            <td class="text-start">{{date('d-m-Y',strtotime($data->Nto_date))}}</td>
                            <td class="text-start">{{$data->Nlink ?? 'N/A'}}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($data->Nto_date >= date('Y-m-d'))
                                    <button class="btn btn-dark font-monospace" data-bs-toggle="modal"
                                        data-bs-target="#updatereapaerModal"
                                        onclick="updatenotification('{{$data}}')" type="button">Update</button>
                                    @endif
                                    <button class="btn btn-danger font-monospace"
                                        onclick="event.preventDefault();
                                                                if(confirm('You are about to delete. Are you sure?')){$('.delete-notification_{{$data->id}}').submit();}">delete</button>

                                    <form class="delete-notification_{{$data->id}}"
                                        action="{{Route('admin.deletenotification')}}" method="POST" class="d-none">
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