@extends('layouts.default')
@section('content')
    <h2>Result</h2>
    <form action="/update-campaign" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="customerId" value="{{$customerId}}">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Campaign ID</label>
            <div class="col-sm-4">
                <input readonly type="text" class="form-control" name="campaignId" value="{{ $campaign['id'] }}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Campaign Name</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="campaignName" value="{{ $campaign['name'] }}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Campaign Status</label>
            <div class="col-sm-4">
                <input readonly type="text" class="form-control" name="campaignStatus" value="{{ $campaign['status'] }}">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-6">
                <button type="submit" class="btn btn-primary">Update Campaign</button>
            </div>
        </div>
    </form>

    @include('includes.result-back')
@stop
