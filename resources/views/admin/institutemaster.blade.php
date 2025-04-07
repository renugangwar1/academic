@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Institute Master') }}
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add New institute
                    </button>
                </div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="myTable" class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">Insitute Name</th>
                                <th scope="col" class="text-truncate">Institute Code</th>
                                <th scope="col" class="text-truncate">Insitute email</th>
                                <th scope="col" class="text-truncate">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($institutes as $key=>$institute)
                            <tr>
                                <th scope="row">{{$key+1}}</th>
                                <td class="text-uppercase text-start">{{$institute->InstituteName}}</td>
                                <td>{{$institute->InstituteCode}}</td>
                                <td class="text-start">{{$institute->email}}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark font-monospace" data-bs-toggle="modal" data-bs-target="#updateinstituteModal" onclick="updateinstitute('{{$institute}}')" type="button">Update</button>
                                        <button class="btn btn-danger font-monospace" onclick="event.preventDefault();
                                                     $('.delete-institute{{$institute->id}}').submit();">delete</button>

                                        <form class="delete-institute{{$institute->id}}" action="{{Route('admin.deleteinstitute')}}" method="POST" class="d-none">
                                            @csrf
                                            <input type="hidden" value="{{$institute->id}}" name="id"/>
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
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New institute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{Route('admin.updateorcreateinstitute')}}" method="Post">
                    @csrf
                    @method('Post')
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="institute_name">institute Name*</label>
                            <input type="text" class="form-control" id="institute_name" name="institute_name" placeholder="Name" required>
                        </div>
                        <div class="form-group flex-fill">
                            <label for="institute_code">Institute Code*</label>
                            <input type="text" class="form-control" id="institute_code" name="institute_code" placeholder="Code" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="email">Institute Email*</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="form-group flex-fill">
                            <button class="btn btn-success w-100">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="updateinstituteModal" tabindex="-1" aria-labelledby="updateinstituteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateinstituteModalLabel">Add New Institute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="updateinstitute">
                
            </div>
        </div>
    </div>
</div>
<script>
    function updateinstitute(data){
        var inst = JSON.parse(data);
        var instname = oldinputvalcheck(`{{old('institute_name')}}`,inst.InstituteName);
        var instcode = oldinputvalcheck(`{{old('institute_code')}}`,inst.InstituteCode);
        var instemail = oldinputvalcheck(`{{old('email')}}`,inst.email);
        var updateform = `
            <form action="{{Route('admin.updateorcreateinstitute')}}" method="Post">
                @csrf
                @method('Post')
                <input type="hidden" value="${inst.id}" name="id"/>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="institute_name">institute Name*</label>
                        <input type="text" class="form-control" id="institute_name" value="${instname}" name="institute_name" placeholder="Name" required>
                    </div>
                    <div class="form-group flex-fill">
                        <label for="institute_code">Institute Code*</label>
                        <input type="text" class="form-control" id="institute_code" value="${instcode}" name="institute_code" placeholder="Code" required>
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="email">Institute Email*</label>
                        <input type="email" class="form-control" id="email" name="email" value="${instemail}" placeholder="Email" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="form-group flex-fill">
                        <button class="btn btn-success w-100">Submit</button>
                    </div>
                </div>
            </form>
        `;
        $('#updateinstitute').html(updateform);
    }
</script>
@endsection