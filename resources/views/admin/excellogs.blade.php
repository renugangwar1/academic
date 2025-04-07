@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border border-dark">
                <div class="card-header fw-bold fs-5 d-flex justify-content-between">{{ __('Excel Logs') }}</div>

                <div class="card-body bg-secondary rounded-bottom-1">
                    <table id="myTable" class="table table-striped-columns overflow-y-auto w-100 table-border border-dark">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" class="text-truncate">Excel Title</th>
                                <th scope="col" class="text-truncate">Tearm</th>
                                <th scope="col" class="text-truncate">Batch</th>
                                <th scope="col" class="text-truncate">Upload By</th>
                                <th scope="col" class="text-truncate">Upload Date</th>
                                <th scope="col" class="text-truncate">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach($excels as $key=>$excel)
                            <tr>
                                <th scope="row">{{$key+1}}</th>
                                <td class="text-uppercase text-start">{{$excel->excel_title}}</td>
                                <td class="text-uppercase text-start">{{$excel->Tearm}}</td>
                                <td class="text-uppercase text-start">{{$excel->Batch}}</td>
                                <td class="text-uppercase text-start">{{$excel->UserName}}</td>
                                <td class="text-uppercase text-start">{{date('d-m-Y',strtotime($excel->created_at))}}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{asset('../storage/'.$excel->excel_link)}}" download class="btn btn-dark p-0 px-2 font-monospace">Download</a>
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
@endsection