@extends('layouts.app')

@section('title', 'Meta Data List')

@section('content')
      <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="d-flex justify-content-between card-header">
                                <h5 class="card-title mb-0">Meta Data List</h5>
                            </div>
                            <div class="card-body">
                                <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Sr. No</th>
                                            <th scope="col">Page Name</th>
                                            <th scope="col">Meta Title</th>
                                            <th scope="col">Meta Keyword</th>
                                            <th scope="col">Meta Description</th>
                                            <th scope="col">Created Date</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($data as $datas)
                                            <tr class="text-center">
                                                <td width="5%">{{ $datas->id ?? '' }}</td>
                                                <td width="5%">{{ $datas->pagename ?? '' }}</td>
                                                <td width="20%">{{ $datas->metaTitle ?? '' }}</td>
                                                <td width="30%">{{ $datas->metaKeyword ?? '' }}</td>
                                                <td width="25%">{{ $datas->metaDescription ?? '' }}</td>
                                                <td class="text-center">{{ date('d-m-Y',strtotime($datas->created_at)) }}</td>
                                                <td width="5%">
                                                    <a class="mx-1"  href="{{route('metaData.view',[$datas->id])}}"  title="View"><i class="fa fa-eye" aria-hidden="true"></i>
                                                        </a>
                                                    <a class="btn btn-link p-0" title="Edit"
                                                        href="{{ route('metaData.edit', $datas->id) }}">
                                                        <span class="text-500 fas fa-edit"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


@endsection
