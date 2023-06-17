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
            </tr>
        </thead>
            <tbody>
                @foreach ($data['campaigns'] as $key => $campaign)
                <tr>
                    <th scope="row">{{ $key }}</th>
                    <td>{{ $campaign['id'] }}</td><td>{{ $campaign['name'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('includes.result-back')
@stop
