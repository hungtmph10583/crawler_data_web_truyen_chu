@extends('crawler.layouts.app_crawler_master')
@section('content')
<main role="main" class="container">
<h3 class="hoverable-rows">Chapter Detail</h3> 
    <div class="bg-body-tertiary p-5 rounded">
        <h3 class="text-center text-info">{{ $chapter->crawlerStory->s_name ?? "N/A" }}</h3>
        <h5 class="text-center text-secondary">{{ $chapter->c_name }}</h5>
        <div class="mb-5 mt-3">
            <div class="row">
                <div class="col text-right"><button type="button" class="btn btn-info" disabled>Chương trước</button></div>
                <div class="col-4 p-0">
                    <select name="select_chapter" class="custom-select" id="select_chapter">
                        @foreach($chapters as $item)
                            <option value="{{route('get_crawler.chapter.show', [$item->crawlerStory->s_slug, $item->c_slug])}}">{{ $item->c_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col text-left"><button type="button" class="btn btn-info">Chương sau</button></div>
            </div>
        </div>
        <p class="lead">
            <!-- // Sử dụng cách này dễ bị tấn công XSS nếu ndung xuất phát từ người dùng (VD: comment) -->
            {!! nl2br($chapter->c_content) !!}
        </p>
        <!-- <p class="lead">
            @foreach(explode(PHP_EOL, $chapter->c_content) as $line )
                {{ $line }} <br>
            @endforeach
            {{ nl2br($chapter->c_content) }}
        </p> -->
        <!-- <a class="btn btn-lg btn-primary" href="/docs/5.3/components/navbar/" role="button">View navbar docs »</a> -->
    </div>
</main>
@endsection
@section('script')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript">
    $('#select_chapter').on('change',function() {
        var url = $(this).val();
        // alert(url);
        if (url) {
            window.location = url;
        }
        return false;
    });

    current_chapter();

    function current_chapter(){
        var url = window.location.href;
        $('#select_chapter[name="select_chapter"]').find('option[value="'+url+'"]').attr("selected", true);
    }
</script>
@endsection