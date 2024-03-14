@extends('crawler.layouts.app_crawler_master')
@section('content')
<main role="main" class="container-fluid">
    <h3 class="hoverable-rows">Crawler Story</h3> 
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Author</th>
                <th scope="col">Category</th>
                <th scope="col">Status</th>
                <th scope="col">Link</th>
                <th scope="col">Process Crawl Chapter</th>
                <th scope="col">Lỗi</th>
                <th scope="col">Total Chapter</th>
                <th scope="col">time</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stories as $key => $item)
            <tr>
                <th scope="row">{{ ++$key }}</th>
                <td>
                    <!-- <span class="d-inline-block text-truncate" style="max-width: 200px;">{{ $item->s_name }}</span> -->
                    <span>{{ $item->s_name }}</span>
                </td>
                <td>
                    <span class="d-inline-block text-truncate" style="max-width: 150px;">
                        {{ $item->crawlerAuthor->a_name ?? "[N\A]" }}
                    </span>
                </td>
                <td>
                    @if(!$item->storyCategory->isEmpty())
                        @foreach($item->categories as $_item)
                            <span class="badge badge-light">
                                {{ $_item->c_name ?? "[N\A]" }}
                            </span>
                        @endforeach
                    @else
                        <span class="badge badge-light">[N\A]</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $item->getStatus($item->s_status)['class'] }}">
                        {{ $item->getStatus($item->s_status)['name'] }}
                    </span>
                </td>
                <td><small><a href="{{ $item->s_link }}" target="_blank" rel="noopener noreferrer">{{ $item->s_link }}</a></small></td>
                <td class="text-center">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" @if(count($item->crawlerChapter) > 0) style="width: {{ (count($item->crawlerChapter)/$item->s_total_chapter)*100 }}%" @else style="width:0%" @endif aria-valuemin="0" aria-valuemax="{{ $item->s_total_chapter }}"></div>
                    </div>
                    <span class="text-success">{{ count($item->crawlerChapter) }}</span> / <span class="text-warning">{{ $item->s_total_chapter }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $item->getTypes($item->s_type_id)['class'] }}">
                        {{ $item->getTypes($item->s_type_id)['name'] }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="text-info">{{ $item->s_total_chapter }}</span>
                </td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{route('get_crawler.story.show', [$item->s_slug])}}" class="btn btn-info">Chapter</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</main>
@endsection