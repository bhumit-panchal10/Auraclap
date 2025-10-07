@extends('layouts.app')

@section('title', 'Meta Data View')

@section('content')

<style type="text/css">
    th, td {
  padding: 15px;
}

</style>
    {{-- Alert Messages --}}
    @include('common.alert')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0"></h4>
                                <div class="page-title-right">
                                    <a href="{{ route('metaData.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Back</a>
                                </div>

                        </div>
                    </div>
                </div> 

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">

                            <div class="card-header">
                                <h5 class="card-title mb-0">Meta Data View</h5>

                            </div>
                            <div class="card-body">
                                <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                        <tr>
                                            <th scope="col" width="20%">Page Name</th>
                                            <td>{{ $datas->pagename ?? '' }}</td>
                                        </tr>
        
                                        <tr>
                                            <th scope="col" width="20%">Meta Title</th>
                                            <td>{{ $datas->metaTitle ?? '' }}</td>
                                        </tr>
        
                                        <tr>
                                            <th scope="col" width="20%">Meta Keyword</th>
                                            <td>{{ $datas->metaKeyword ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="col" width="20%">Meta Description</th>
                                            <td>{{ $datas->metaDescription ?? '' }}</td>
                                        </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
