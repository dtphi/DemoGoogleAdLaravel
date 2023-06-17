@extends('layouts.default')
@section('content')
    <h2>Result</h2>
    @include('includes.result-back')
    <div class="container-fluid mt-2">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                @foreach ($selectedFields as $field)
                    <th scope="col">{{ $field }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @forelse ($paginatedResults as $index => $row)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    @foreach ($selectedFields as $field)
                        <td>{{
                            $row[explode(".", $field)[0]][explode(".",$field)[1]] ?? 'N/A' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr class="text-center"><td colspan="{{ count($selectedFields) + 1 }}">
                        <strong>No data for this query.</strong></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $paginatedResults->links() }}
    @include('includes.result-back')
@stop
