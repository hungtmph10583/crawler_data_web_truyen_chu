@extends('crawler.layouts.app_crawler_master')
@section('content')
<main role="main" class="container-fluid">
<h3 class="hoverable-rows">Chapter</h3> 
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Slug</th>
                <th scope="col">Story</th>
                <th scope="col">Link Chapter</th>
                <th scope="col">time</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chapters as $key => $item)
           <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>
                    <span class="d-inline-block text-truncate" style="max-width: 150px;">
                        {{ $item->c_name ?? "[N\A]" }}
                    </span>
                </td>
                <td>{{$item->c_slug}}</td>
                <td>
                    <span class="d-inline-block text-truncate" style="max-width: 200px;">
                        {{$item->crawlerStory->s_name ?? "[N\A]"}}
                    </span>
                </td>
                <td><small><a href="{{ $item->c_link_chapter }}" target="_blank" rel="noopener noreferrer">{{ $item->c_link_chapter }}</a></small></td>
                <td>{{$item->created_at->format('d/m/Y')}}</td>
                <td>
                    <a class="btn btn-info" href="{{route('get_crawler.chapter.show', [$item->crawlerStory->s_slug, $item->c_slug])}}">Read Chapter</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</main>
@endsection