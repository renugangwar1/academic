@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Users Master') }}
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add New User
                    </button>
                </div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="myTable" class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">User Name</th>
                                <th scope="col" class="text-truncate">Email ID</th>
                                <th scope="col" class="text-truncate">Role</th>
                                <th scope="col" class="text-truncate">Created</th>
                                <th scope="col" class="text-truncate">Updated</th>
                                <th scope="col" class="text-truncate">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($users as $key=>$users)
                            <tr class="table-sm">
                                <th scope="row">{{$key+1}}</th>
                                <td class="text-uppercase text-start">{{$users->name}}</td>
                                <td class="text-start">{{$users->email}}</td>
                                <td class="text-uppercase text-start">{{$users->role != 8 ? 'Level'.$users->role : 'Institute'}}</td>
                                <td class="text-uppercase text-start">{{date('d-m-Y',strtotime($users->created_at))}}</td>
                                <td class="text-uppercase text-start">{{date('d-m-Y',strtotime($users->updated_at))}}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark font-monospace" data-bs-toggle="modal" data-bs-target="#updateuserModal" onclick="updateuser('{{$users}}')" type="button">Update</button>
                                        <button class="btn btn-danger font-monospace" onclick="event.preventDefault(); if(confirm('Your Are Aboute to Remove ( {{strtoupper($users->name)}} )')){
                                                     $('.delete-user').submit();}">delete</button>

                                        <form class="delete-user" action="{{Route('admin.userdelete')}}" method="POST" class="d-none">
                                            @csrf
                                            <input type="hidden" value="{{$users->id}}" name="id"/>
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
                <h5 class="modal-title" id="exampleModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{Route('admin.updateorcreateuser')}}" method="Post">
                    @csrf
                    @method('Post')
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="user_name">User Name</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="User Name" autocomplete="FALSE">
                        </div>
                        <div class="form-group flex-fill">
                            <label for="user_email">Email</label>
                            <input type="email" class="form-control" id="user_email" name="user_email" placeholder="User Email" autocomplete="FALSE">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="user_role">Select Role</label>
                            <select class="form-select" onchange="instituteselect(this)" id="user_role" name="user_role">
                                <option value="">Default</option>
                                <option value="1">Level 1</option>
                                <option value="2">Level 2</option>
                            </select>
                        </div>
                        <div id="addinscode"></div>
                        <div class="form-group flex-fill">
                            <label for="user_pass">User Password</label>
                            <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="User Password" autocomplete="FALSE">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-group flex-fill">
                            <label for="user_pass">User Execess</label>
                            <div class="d-grid border border-dark form-control">
                                @foreach(config('constants.menuename') as $key=>$menu)
                                    <div class="d-flex gap-1">
                                        <input type="checkbox" value="{{$key}}" name="accessmenu[]"/><div>{{$menu}}</div>
                                    </div>
                                @endforeach
                            </div>
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
<div class="modal fade" id="updateuserModal" tabindex="-1" aria-labelledby="updateuserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateuserModalLabel">Update User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="updateuser">
                
            </div>
        </div>
    </div>
</div>
<script>
    function instituteselect(e){
        var role = $(e).val();
        if(role === '0'){
            $('#addinscode').html(`
                <div class="form-group flex-fill">
                    <label for="ins_code">Institute Code</label>
                    <input type="text" class="form-control" id="ins_code" value="" name="ins_code" placeholder="Insitute Code" autocomplete="FALSE">
                </div>
            `); 
        }else{
            $('#addinscode').html('');
        }
        return;
    }
    function updateuser(data){
        var user = JSON.parse(data);
        console.log(user);
        var updateform = `
            <form action="{{Route('admin.updateorcreateuser')}}" method="Post">
                @csrf
                @method('Post')
                <input type="hidden" value="${user.id}" name="id"/>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="user_name">User Name</label>
                        <input type="text" class="form-control" id="user_name" value="${user.name}" name="user_name" placeholder="User Name" autocomplete="FALSE">
                    </div>
                    <div class="form-group flex-fill">
                        <label for="user_email">Email</label>
                        <input type="email" class="form-control" id="user_email" value="${user.email}" name="user_email" placeholder="User Email" autocomplete="FALSE">
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="user_role">Select Role</label>
                        <select class="form-select" id="update_user_role" name="user_role">
                            <option value="0">Default</option>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                        </select>
                    </div>
                    <div id="updateinscode"></div>
                    <div class="form-group flex-fill">
                        <label for="user_pass">User Password</label>
                        <input type="password" class="form-control" id="user_pass" value="" name="user_pass" placeholder="User Password" autocomplete="FALSE">
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="user_pass">User Execess</label>
                        <div class="d-grid border border-dark form-control">
                            @foreach(config('constants.menuename') as $key=>$menu)
                                <div class="d-flex gap-1">
                                    <input type="checkbox" value="{{$key}}" id="mylistaccess{{$key}}" name="accessmenu[]"></input><div>{{$menu}}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="form-group flex-fill">
                        <button class="btn btn-success w-100">Submit</button>
                    </div>
                </div>
            </form>
        `;
        
        
        $('#updateuser').html(updateform);
        $('#update_user_role').val(user.role);


        if(user.role === 8){
           
            $('#updateinscode').html(`
                <div class="form-group flex-fill">
                    <label for="up_ins_code">Institute Code</label>
                    <input type="text" class="form-control" id="up_ins_code" value="" name="ins_code" placeholder="Insitute Code" autocomplete="FALSE">
                </div>
            `);

            $('#up_ins_code').val(user.institute['Institute_code']);
        }

        if(user.menu_access !== null){
            var menuarray = user.menu_access.split(',');
    
            menuarray.forEach(function(element){
                $('#mylistaccess'+element).attr('checked',true);  
            });

        }

    }
</script>
@endsection