@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Academic Data') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="myTable" class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">Course Code</th>
                                <th scope="col" class="text-truncate">Duration</th>
                                <th scope="col" class="text-truncate">Semester</th>
                                <th scope="col" class="text-truncate">Subject Code</th>
                                <th scope="col" class="text-truncate">Subject Name</th>
                                <th scope="col" class="text-truncate">Optional Subject</th>
                                <th scope="col" class="text-truncate">Table</th>
                                <th scope="col" class="text-truncate">Credit</th>
                                <th scope="col" class="text-truncate">Mid Max Mark</th>
                                <th scope="col" class="text-truncate">Mid Pass Mark</th>
                                <th scope="col" class="text-truncate">End Max Mark</th>
                                <th scope="col" class="text-truncate">End Pass Mark</th>
                                <th scope="col" class="text-truncate">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <tr>
                                <th scope="row"></th>
                                <td class="text-uppercase"></td>
                                <td class="text-uppercase"></td>
                                <td></td>
                                <td class="text-uppercase"></td>
                                <td class="text-capitalize text-start"></td>
                                <td class="text-uppercase"></td>
                                <td class="text-uppercase"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection