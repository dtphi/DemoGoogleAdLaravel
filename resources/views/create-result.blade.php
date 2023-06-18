@extends('layouts.default')
@section('content')
    <h2>Result Create</h2>
    <div>
        <form action="{{ url('get-campaign') }}" method="GET">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="customerId" class="col-sm-2 col-form-label">Customer ID</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="customerId" name="customerId"
                        value="{{ $data['customerId'] }}"
                           aria-describedby="customerIdHelp"
                           placeholder="Enter your customer ID" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-primary">Get Campaign</button>
                </div>
            </div>
        </form>
    </div>
    <div class="">
        <table class="table">
        <thead>
            <tr>
                <th scope="col">Campaign Count</th>
                <th scope="col">Budget Resource Name</th>
            </tr>
        </thead>
            <tbody>
                <tr>
                    <th scope="row">{{ $data['addedCampaignCount'] }}</th>
                    <td>{{ $data['budgetRourceName'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @include('includes.result-back')
@stop
