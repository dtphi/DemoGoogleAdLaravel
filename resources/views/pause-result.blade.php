@extends('layouts.default')
@section('content')
    <h2>Result</h2>
    <form>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Campaign ID</label>
            <div class="col-sm-4">
                <input readonly type="text" class="form-control" value="{{ $campaign['id'] }}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Campaign Name</label>
            <div class="col-sm-4">
                <input readonly type="text" class="form-control" value="{{ $campaign['name'] }}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Campaign Status</label>
            <div class="col-sm-4">
                <input readonly type="text" class="form-control" value="{{ $campaign['status'] }}">
            </div>
        </div>
    </form>

    @include('includes.result-back')
@stop
