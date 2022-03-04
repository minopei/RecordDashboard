@extends('layouts.app')

@section('content')
    <div id="container"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Record Data</h4>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>流程</th>
                                    <th>進行中</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($record as $item)
                                    <tr>
                                        <td>{{ $item->processInstanceName }}</td>
                                        <td>{{ $item->caseNumber }}</td>
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
