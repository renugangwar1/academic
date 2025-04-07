@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row h-100 justify-content-center">
        <div class="align-items-start h-100 bg-body d-flex p-2 rounded">
            <div class="nav flex-column nav-pills me-3 gap-1 flex-grow-0" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <button class="nav-link border text-body-secondary fw-bold active text-start text-nowrap" id="v-pills-setting-tab" data-bs-toggle="pill" data-bs-target="#v-pills-setting" type="button" role="tab" aria-controls="v-pills-setting" aria-selected="true">Setting</button>
                <button class="nav-link border text-body-secondary fw-bold text-start text-nowrap" id="v-pills-notification-tab" data-bs-toggle="pill" data-bs-target="#v-pills-notification" type="button" role="tab" aria-controls="v-pills-notification" aria-selected="false">Notification</button>
                <button class="nav-link border text-body-secondary fw-bold text-start text-nowrap" id="v-pills-reappear-shadualler-tab" data-bs-toggle="pill" data-bs-target="#v-pills-reappear-shadualler" type="button" role="tab" aria-controls="v-pills-reappear-shadualler" aria-selected="false">Reappear Notice</button>
            </div>
            <div class="tab-content flex-grow-1" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-setting" role="tabpanel" aria-labelledby="v-pills-setting-tab">
                    @include('components.setting.main')
                </div>
                <div class="tab-pane fade" id="v-pills-notification" role="tabpanel" aria-labelledby="v-pills-notification-tab">
                    @include('components.setting.notification')
                </div>
                <div class="tab-pane fade" id="v-pills-reappear-shadualler" role="tabpanel" aria-labelledby="v-pills-reappear-shadualler-tab">
                    @include('components.setting.reappearshadualler')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="newcreateforms">
            
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="updatereapaerModal" tabindex="-1" aria-labelledby="updatereapaerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatereapaerModalLabel">Update Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="updateReappear">
                
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new DataTable('#notification', {
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
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'csvHtml5', className: 'text-bg-dark',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdfHtml5', className: 'text-bg-dark',
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

    function newform(e){
        switch(e){
            case 'reappear':
                $('#newcreateforms').html(`@include('components.setting.createforms.reappear')`);
                $('#course').on('change',function(){
                    var selectedValue = $(this).val();
                    // Find the selected option
                    var duration = $('option[value="' + selectedValue + '"]').attr('duration');
                    $('#batch').html(batch(duration));
                });
                break;
            case 'notification':
                $('#newcreateforms').html(`@include('components.setting.createforms.notification')`);
                break;
        }
    }

    function clickbg(){
        var bgcolor = randomBgColor();
        $('body').css({'background-color': bgcolor});
        $('#bgchangecolor').val(bgcolor);
    }

    function savebg(){
        var formData = $("#bgchange").serialize();
        
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("bgcolorchange")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                var alertdiv = $('#alert');
                
                alertdiv.html(`<div class="bg-body fade-in form-select-sm font-monospace m-1 p-1 position-absolute rounded px-2 bottom-0 border border-2 border-success">${response}</div>`);
                setTimeout(function() {
                    alertdiv.fadeOut();
                }, 2000);
            },
            error: function(xhr, status, error) {
                var alertdiv = $('#alert');
                
                alertdiv.html(`<div class="bg-body fade-in form-select-sm font-monospace m-1 p-1 position-absolute rounded px-2 bottom-0 border border-2 border-danger">${xhr.responseJSON.error}</div>`);
                
                setTimeout(function() {
                    alertdiv.fadeOut();
                }, 2000);
            }
        });
    }

    function updateSetting(){
        var formData = $("#setting").serialize();
        
        // Make Ajax request
        $.ajax({
            type: "POST",
            url: '{{Route("admin.settingupdate")}}', // Replace with your server endpoint
            data: formData,
            success: function(response) {
                var alertdiv = $('#alert');
                
                alertdiv.html(`<div class="bg-body fade-in form-select-sm font-monospace m-1 p-1 position-absolute rounded px-2 bottom-0 border border-2 border-success">${response}</div>`);
                setTimeout(function() {
                    alertdiv.fadeOut();
                }, 2000);
            },
            error: function(xhr, status, error) {
                var alertdiv = $('#alert');

                alertdiv.html(`<div class="bg-body fade-in form-select-sm font-monospace m-1 p-1 position-absolute rounded px-2 bottom-0 border border-2 border-success">${xhr.responseJSON.message}</div>`);
                
                setTimeout(function() {
                    alertdiv.fadeOut();
                }, 2000);
            }
        });
    }


    function updateReappear(data,duration){
        var inst = JSON.parse(data);
        var list = JSON.parse(duration);
        var batchvalu = [];
        $.each(list, function(index, value) {
            // Push an object with value as key and index as value into the array
            batchvalu[value] = index;
        });
        var updateform = `
            <form action="{{Route('admin.Reappearsetting')}}" method="Post">
                @csrf
                @method('Post')
                <input type="hidden" name="id" value="${inst.id}"/>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="updatecourse">Select Course</label>
                        <select class="form-control text-uppercase" id="updatecourse" name="course">
                            <option value="">Select Option</option>
                            @foreach($course as $duration=>$single)
                                <option duration="{{$duration}}" value="{{$single}}">{{$corsename[$single]}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group flex-fill">
                        <label for="updatebatch">Select Batch *</label>
                        <select class="form-control text-uppercase" id="updatebatch" name="batch">
                            ${batch(batchvalu[inst.course])}
                        </select>
                    </div>
                    <div class="form-group flex-fill">
                        <label for="semester">Select Semester *</label>
                        <select class="form-control text-uppercase" id="updatesemester" name="semester">
                            <option value="">Select Option</option>
                            @foreach($semester as $vlaue)
                                <option value="{{$vlaue}}">{{$vlaue}} Semester</option>
                            @endforeach
                        </select>
                    </div>   
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="Reappear_from_date">From Date *</label>
                        <input type="date" class="form-control" id="Reappear_from_date" value="${inst.Reappear_from_date}" name="Reappear_from_date">
                    </div>
                    <div class="form-group flex-fill">
                        <label for="Reappear_to_date">To Date *</label>
                        <input type="date" class="form-control" id="Reappear_to_date" value="${inst.Reappear_to_date}" name="Reappear_to_date">
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="Reappear_late_fee_date">Late Fee Date *</label>
                        <input type="date" class="form-control" id="Reappear_late_fee_date" value="${inst.Reappear_late_fee_date}" name="Reappear_late_fee_date">
                    </div>
                    <div class="form-group flex-fill">
                        <label for="Reappear_late_fee">Late Fee Amount *</label>
                        <input type="text" class="form-control" id="Reappear_late_fee" value="${inst.Reappear_late_fee}" name="Reappear_late_fee">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="form-group flex-fill">
                        <button class="btn btn-success w-100">Submit</button>
                    </div>
                </div>
            </form>`;

        $('#updateReappear').html(updateform);

        $('#updatecourse').val(inst.course != null ? inst.course : '');
        $('#updatebatch').val(inst.batch != null ? inst.batch : '');
        $('#updatesemester').val(inst.semester != null ? inst.semester : '');
       
        $('#updatecourse').on('change',function(){
            var selectedValue = $(this).val();
            // Find the selected option
            var duration = $('option[value="' + selectedValue + '"]').attr('duration');
            $('#updatebatch').html(batch(duration));
        });
    }

    function updatenotification(data){
        var inst = JSON.parse(data);
        var updateform = `
            <form action="{{Route('admin.notification')}}" method="Post">
                @csrf
                @method('Post')
                <input type="hidden" value="${inst.id}" name="id"/>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="Ntitle">Title *</label>
                        <input type="text" class="form-control" id="Ntitle" value="${inst.Ntitle}" name="Ntitle">
                    </div>
                    <div class="form-group flex-fill">
                        <label for="Ntype">Notification For *</label>
                        <select class="form-control text-uppercase" id="updateNtype" name="Ntype">
                            <option value="">Select option</option>
                            <option value="student">Student</option>
                            <option value="institute">Institute</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="Nformdate">From Date *</label>
                        <input type="date" class="form-control" id="Nformdate" value="${inst.Nfrom_date}" name="Nformdate">
                    </div>
                    <div class="form-group flex-fill">
                        <label for="Ntodate">To Date *</label>
                        <input type="date" class="form-control" id="Ntodate" value="${inst.Nto_date}" name="Ntodate">
                    </div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <div class="form-group flex-fill">
                        <label for="Nlink">Link *</label>
                        <input type="text" class="form-control" id="Nlink" name="Nlink" value="${inst.Nlink}" autocomplete=false>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="form-group flex-fill">
                        <button class="btn btn-success w-100">Submit</button>
                    </div>
                </div>
            </form>
            `;

        $('#updateReappear').html(updateform);

        $('#updateNtype').val(inst.Nfor != null ? inst.Nfor : '');
    }
</script>
@endsection