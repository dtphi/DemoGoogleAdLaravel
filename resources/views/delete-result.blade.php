@extends('layouts.default')
@section('content')
    <h2>Result Delete</h2>
    <form>
        <div class="form-group row">
            <label class="col-sm-6 col-form-label">Customer ID: {{ $data['customerId'] }}</label>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-form-label">Delete Campaign Name: {{ $data['campaignName'] }}</label>
        </div>
    </form>

    @include('includes.result-back')
@stop
