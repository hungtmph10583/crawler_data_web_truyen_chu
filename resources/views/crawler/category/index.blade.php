@extends('crawler.layouts.app_crawler_master')
@section('content')
<main role="main" class="container-fluid">
    <h3 class="hoverable-rows">Crawler Category</h3> 
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Category</th>
                <th scope="col">Domain</th>
                <th scope="col">Status</th>
                <th scope="col">Link</th>
                <th scope="col">Story Crawled</th>
                <th scope="col">Total Page</th>
                <th scope="col">PROCESS</th>
                <th scope="col">Page Processed</th>
                <th scope="col">Time</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $key => $item)
            <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{ $item->c_name }}</td>
                <td>{{ $item->category->c_name ?? "[N\A]" }}</td>
                <td>{{ $item->c_domain }}</td>
                <td>
                    <span class="badge badge-{{ $item->getStatus($item->c_status)['class'] }}">
                        {{ $item->getStatus($item->c_status)['name'] }}
                    </span>
                </td>
                <td><small><a href="{{ $item->c_link }}" target="_blank" rel="noopener noreferrer">{{ $item->c_link }}</a></small></td>
                <td class="text-center">{{ count($item->stories) }}</td>
                <td class="text-center">{{ $item->c_total_page }}</td>
                <td class="text-center">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" @if($item->c_page_process > 1) style="width: 100%" @else style="width:{{ ($item->c_page_process / $item->c_total_page)*100 }}%" @endif aria-valuemin="0" aria-valuemax="{{ $item->s_total_chapter }}"></div>
                    </div>
                    <span class="text-success">{{ $item->c_total_page }}</span> / <span class="text-warning">{{ $item->c_page_process }}</span>
                </td>
                <td class="text-center">{{ $item->c_page_process }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    <button class="btn btn-info">Edit</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</main>
@endsection