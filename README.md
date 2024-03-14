#CSDL
    . categories
        * mô tả: Là bảng danh mục( Tiên hiệp, kiếm hiệp, ngôn tình )
        * Column
            - id
            - c_name
            - c_slug
            - c_description
            - c_status
            - c_hot
            - c_sort
    . types
        * Loại Truyện ( Truyện mới, truyện dài, ngôn tình, ...)
        * Column
            - id
            - t_name
            - t_slug
            - t_description
            - t_hot
            - t_sort
    . stories
        * Bảng truyện ( Hợp đồng yêu, ...)
        * Column
            - id
            - s_name
            - s_slug
            - s_thumbnail
            - s_type_id
            - s_author_id
            - s_category_id
            - s_hot
            - s_sort
            - s_total_chapter
            - s_status
            - s_description
            - s_view
            - s_favourite
            - s_vote_number
            - s_vote_total
    . stories_categories
        * Bảng trung gian giữa danh mục và truyện
        * ( Một truyện có nhiều danh mục - 1 danh mục có nhiều truyện)
        * Column
            - sc_category_id
            - sc_story_id
    . chapters
        * Chương ( Chương 1, chương 2, chương 3)
        * Column
            - id
            - c_story_id
            - c_name
            - c_slug
            - c_content


        //-------------------------------------------------- GOUTTE
        // Lay chapter truyen
        // $chapterArr = $crawler->filter('#list-chapter > div.row > div > ul > li > a')->each(function ($note)
        // {
        //     CliEcho::infonl('-- -- -(5)- -- -- Chapter: ' . $note->text());
        //     $nameChapter = $note->text();
        //     $linkChapter = $note->attr('href');
        //     return compact('nameChapter', 'linkChapter');
        // });
        // dump($_lastPageChapter);

        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3')));
        // $context = stream_context_create(array('http' => array('header' => "Referer: http://www.example.com/\r\n")));
        $html = file_get_html($linkContent, false, $context);