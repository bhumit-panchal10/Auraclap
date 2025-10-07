@extends('layouts.app')

@section('title', 'Edit Meta Data List')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Edit Meta Data</h4>
                            <div class="page-title-right">
                                <a href="{{ route('metaData.index') }}"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($data as $metaData)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="live-preview">

                                        <form action="{{ route('metaData.update', [$metaData->id]) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-md-6 mt-2">
                                                    <div class="form-group {{ $errors->has('pagename') ? 'has-error' : '' }} mb-2">
                                                        <label for="pagename">*Page Name</label>
                                                        <input type="text" id="pagename" name="pagename"
                                                            class="form-control"
                                                            value="{{ old('pagename', isset($metaData) ? $metaData->pagename : '') }}"
                                                            readonly required>
                                                        @if ($errors->has('pagename'))
                                                            <em class="invalid-feedback">
                                                                {{ $errors->first('pagename') }}
                                                            </em>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mt-2">

                                                    <div
                                                        class="form-group {{ $errors->has('metaTitle') ? 'has-error' : '' }} mb-2">

                                                        <label for="metaTitle">*Meta Title</label>

                                                        <input type="text" id="metaTitle" name="metaTitle"
                                                            class="form-control"
                                                            value="{{ old('metaTitle', isset($metaData) ? $metaData->metaTitle : '') }}"
                                                            required>
                                                        @if ($errors->has('metaTitle'))
                                                            <em class="invalid-feedback">
                                                                {{ $errors->first('metaTitle') }}
                                                            </em>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mt-2">
                                                    <div
                                                        class="form-group {{ $errors->has('metaKeyword') ? 'has-error' : '' }} mb-2">

                                                        <label for="metaKeyword">*Meta Keyword</label>

                                                        <textarea id="metaKeyword" name="metaKeyword" rows="7" class="form-control" required>{{ old('metaKeyword', isset($metaData) ? $metaData->metaKeyword : '') }}</textarea>
                                                        @if ($errors->has('metaKeyword'))
                                                            <em class="invalid-feedback">
                                                                {{ $errors->first('metaKeyword') }}
                                                            </em>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mt-2">
                                                    <div
                                                        class="form-group {{ $errors->has('metaDescription') ? 'has-error' : '' }} mb-2">
                                                        <label for="metaDescription">Meta Description</label>
                                                        <textarea id="metaDescription" name="metaDescription" rows="7" class="form-control">{{ old('metaDescription', isset($metaData) ? $metaData->metaDescription : '') }}</textarea>
                                                        @if ($errors->has('metaDescription'))
                                                            <em class="invalid-feedback">
                                                                {{ $errors->first('metaDescriptions') }}
                                                            </em>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group {{ $errors->has('head') ? 'has-error' : '' }} mb-2">
                                                <label for="head">Head</label>
                                                <textarea id="head" name="head" rows="8" class="form-control">{{ old('head', isset($metaData) ? $metaData->head : '') }}</textarea>
                                                @if ($errors->has('head'))
                                                    <em class="invalid-feedback">
                                                        {{ $errors->first('head') }}
                                                    </em>
                                                @endif
                                            </div>

                                            <div class="form-group {{ $errors->has('body') ? 'has-error' : '' }} mb-2">
                                                <label for="body">Body</label>
                                                <textarea id="body" name="body" rows="8" class="form-control">{{ old('body', isset($metaData) ? $metaData->body : '') }}</textarea>
                                                @if ($errors->has('body'))
                                                    <em class="invalid-feedback">
                                                        {{ $errors->first('body') }}
                                                    </em>
                                                @endif
                                            </div>

                                            <div class="card-footer mt-2" style="float: right;">
                                                <button type="submit"
                                                    class="btn btn-primary btn-user float-right mb-3 mx-2">Update</button>
                                                <a class="btn btn-primary float-right mr-3 mb-3 mx-2"
                                                    href="{{ route('metaData.edit', $metaData->id) }}">Cancel</a>
                                            </div>


                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
