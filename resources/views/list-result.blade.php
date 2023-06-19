@extends('layouts.default')
@section('content')
    <h2>Result List</h2>
    <div class="">
        <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID</th>
                <th scope="col">Campaign Name</th>
                <th scope="col">Action</div>
            </tr>
        </thead>
            <tbody>
                @foreach ($data['campaigns'] as $key => $campaign)
                <tr>
                    <th scope="row">{{ $key }}</th>
                    <td>{{ $campaign['id'] }}</td><td>{{ $campaign['name'] }}</td>
                    <td>
                        <div class="float-left">
                            <a href="/pause-campaign/?customerId={{$data['customerId']}}&campaignId={{$campaign['id']}}" class="btn btn-primary">Edit</a>
                        </div>
                        <div class="float-right">
                            <form action="/delete-campaign" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" class="form-control" id="customerId" name="customerId"
                                aria-describedby="customerIdHelp"
                                value="{{$data['customerId']}}">
                                <input type="hidden" class="form-control" id="campaignId" name="campaignId"
                            aria-describedby="campaignIdHelp"
                            value="{{$campaign['id']}}">
                            <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('includes.result-back')
@stop
