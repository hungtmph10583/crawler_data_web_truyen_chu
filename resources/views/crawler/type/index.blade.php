@extends('crawler.layouts.app_crawler_master')
@section('content')
<main role="main" class="container-fluid">
<h3 class="hoverable-rows">Type</h3> 
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Type</th>
                <th scope="col">Domain</th>
                <th scope="col">Link</th>
                <th scope="col">Time</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $key => $item)
           <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{$item->t_name}}</td>
                <td>{{$item->type->t_name ?? "[N\A]"}}</td>
                <td>{{$item->t_domain}}</td>
                <td><small><a href="{{ $item->t_link }}" target="_blank" rel="noopener noreferrer">{{ $item->t_link }}</a></small></td>
                <td>{{$item->created_at->format('d/m/Y')}}</td>
                <td>
                    <button class="btn btn-info">Edit</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</main>
@endsection