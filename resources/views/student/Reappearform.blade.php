@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ">
        <div class="col-md-12 mb-5">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex gap-2">
                    <div>{{ __('Reappear Form') }}</div>
                </div>
                <div class="card-body bg-secondary rounded-bottom-1">
                    <form id="searchreappear" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="course" class="form-control mb-2" readonly value="{{Auth::guard('student')->user()->course}}"/>
                        <input type="hidden" name="batch" class="form-control mb-2" readonly value="{{Auth::guard('student')->user()->batch}}"/>
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <div class="form-control mb-2 bg-dark-subtle text-capitalize" disabled>{{Auth::guard('student')->user()->course}}</div>
                            <div class="form-control mb-2 bg-dark-subtle" disabled>{{Auth::guard('student')->user()->batch}}</div>
                            <select class="w-100 p-2 rounded mb-2 text-capitalize" name="semester" id="semester" required>
                                <option value="">Select Semester</option>
                                @foreach($semester as $single)
                                    <option value="{{$single}}">{{$single}} Semester</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" onclick="searchrepear()" id="searchbtn" class="w-100 bg-dark border border-dark rounded p-2 text-white">Search</button>
                    </form>
                    @if(Route::currentRouteName() === 'student.searchreappear')
                    @if(isset($Reappear) && count($Reappear) > 0)
                        <hr>
                        <div class="">
                            <table class="table w-100 table-bordered border border-dark">
                                <tbody class="text-bg-info">
                                    <tr>
                                        <td class="text-start"><span>Name : </span><strong>{{$studentsubinfo->Stud_name ?? 'N/A'}}</strong></td>
                                        <td class="text-start"><span>NCHMCT Roll Number : </span><strong>{{$studentsubinfo->Stud_nchm_roll_number ?? 'N/A'}}</strong></td>
                                        <td class="text-start"><span>JNU Roll Number : </span><strong>{{$studentsubinfo->Stud_jnu_roll_number ?? 'N/A'}}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><span>Course : </span><strong class="text-uppercase">{{$studentsubinfo->course->Course_name ?? 'N/A'}}</strong></td>
                                        <td class="text-start"><span>Semester : </span><strong>{{ordinalget($studentsubinfo->Stud_semester) ?? 'N/A'}}</strong></td>
                                        <td class="text-start"><span>Batch : </span><strong>{{$studentsubinfo->Stud_batch ?? 'N/A'}}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="mb-3">
                        @if(isset($Reappear) && count($Reappear) > 0)
                        <form id="paynowform" method="post">
                            @csrf
                            @method('post')
                            <input type="hidden" name="sendsemester" class="form-control mb-2" value="{{$studentsubinfo->Stud_semester}}"/>
                            <h5 class="text-center text-bg-dark m-0 p-1 pt-2 text-uppercase">Reappear Subjects</h5>
                                <table class="table w-100 table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Selection</th>
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th class="text-end">Reappear Fee</th>
                                            <th class="text-end">Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-bg-info">

                                        @foreach($Reappear as $key=>$single)
                                        <tr>
                                            @foreach($single as $key=>$value)
                                                @if($key === 'code')
                                                    <td class="text-start">
                                                        <input type="checkbox" value="{{$single['fee']}}" name="fee[{{trim($value)}}]" class="feeCheckbox"/>
                                                    </td>
                                                    <td class="text-start">{{$value}}</td>
                                                @endif
                                                @if($key === 'name')
                                                    <td class="text-start">{{$value}}</td>
                                                @endif
                                                @if($key === 'fee')
                                                    <td class="text-end">₹ {{$value}}</td>
                                                    <td class="text-end"><span class="paymentcount">0</span></td>
                                                @endif
                                            @endforeach
                                        </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot class="table-secondary table-group-divider">
                                        <tr>
                                            <td colspan="4">Total Fee to be paid</td>
                                            <td class="text-end"><span id="total">0</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            <button type="button" onclick="paynow()" id="paybtn" class="w-100 bg-success border border-dark rounded p-2 text-white">Pay Now</button>
                        </form>
                        @else
                        <h5 class="text-center text-bg-dark m-0 p-1">No Data Found</h5>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    function searchrepear() {
        $url = '{{Route("student.searchreappear")}}';
        $('#searchreappear').attr('action', $url);
        $('#searchreappear').submit();
        $('#searchbtn').html(`
            <span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Searching
        `);
    }

    function paynow() {
        $url = '{{Route("student.feepayment")}}';
        $('#paynowform').attr('action', $url);
        $('#paynowform').submit();
        $('#paybtn').html(`
            <span class="accordion-flush spinner-border align-middle spinner-grow-sm"></span> Pay Now
        `);
    }

    document.addEventListener('DOMContentLoaded', function() {
        var checkboxes = document.querySelectorAll('.feeCheckbox');

        if (checkboxes.length > 0) {
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    var old = document.getElementById('total');
                    var currentTotal = parseFloat(old.textContent) || 0;

                    if (checkbox.checked) {
                        var value = parseFloat(checkbox.value);
                        var payout = parseFloat(checkbox.value);
                        currentTotal += value;
                    } else {
                        var value = parseFloat(checkbox.value);
                        var payout = 0;
                        currentTotal -= value;
                    }
                    checkbox.parentElement.parentElement.querySelector('.paymentcount').textContent = payout;
                    old.textContent = currentTotal.toFixed(2);
                });
            });
        }
    });
</script>
@endsection