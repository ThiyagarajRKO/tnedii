@if($page->id == 1)
@if (function_exists('get_post_by_category_name'))
    @php
        $review_posts = get_post_by_category_name("Breaking News", 0, 0, 'DESC');
        //print_r($review_posts);
    @endphp
    @if (!empty($review_posts))
    <div style="background:#fff">
        <div class="" style="overflow:hidden; position:relative;height:30px;">
            <div id="breaking-news-container">
              <div id="breaking-news-colour" class="slideup animated">
              </div>  
               <span class="breaking-news-title delay-animated slidein">
                  // BREAKING //
                </span> 
                <div class="breaking-news-headline delay-animated2 fadein marquee">
                    <div class="marquee_text">
                        <ul class='mtext'>
                             @foreach($review_posts as $key => $review)
                            <li><a href="{{ $review->url }}">{{ $review->description }} | <strong style="text-transform:uppercase; color: #15bf5e;">{{ $review->name }}</strong></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>  
            </div>  
        </div>
    </div>
    <style>
    .breaking-news-headline {
      display: block;
      position: absolute;
      font-family: arial;
      font-size: 13px;
      margin-top: -22px;
      color: white;
      margin-left: 150px;
    }
    
    .breaking-news-title {
        background-color: #15bf5e;
        display: block;
        width: 90px;
        font-family: arial;
        font-size: 11px;
        position: absolute;
        top: 0px;
        margin-top: 0px;
        margin-left: 20px;
        padding-top: 10px;
        padding-left: 10px;
        z-index: 3;
        padding-bottom: 10px;
        color: #fff;
    }
    .breaking-news-title:before {
      content: "";
      position: absolute;
      display: block;
      width: 0px;
      height: 0px;
      top: 0;
      left: -12px;
      border-left: 12px solid transparent;
      border-right: 0px solid transparent;
      border-bottom: 30px solid #15bf5e;
    }
    .breaking-news-title:after {
      content: "";
      position: absolute;
      display: block;
      width: 0px;
      height: 0px;
      right: -12px;
      top: 0;
      border-right: 12px solid transparent;
      border-left: 0px solid transparent;
      border-top: 30px solid #15bf5e;
    }
    
    #breaking-news-colour {
        height: 30px;
        width: 100%;
        background-color: #01277a;
    }
    
    #breaking-news-container {
      height: 30px;
      width: 100%;
      overflow: hidden;
      position: absolute;
    }
    #breaking-news-container:before {
      content: "";
      width: 30px;
      height: 30px;
     background-color: #01277a;
      position: absolute;
      z-index: 2;
    }
    /******************/
    .marquee_text {
        font-size: 13px;
        font-weight: bold;
        line-height: 17px;
        padding-bottom: 0px;
        background: none;
        color: #fff;
        width: 100%;
        overflow: hidden;
    }
    .mtext{
        list-style:none;
        margin:0px;
        padding:0px;
        display:table;
    }
    .mtext li{
        border-right: 1px solid #15bf5e;
        padding: 0px 35px;
        display:table-cell;
        white-space:nowrap;
        
    }
    .mtext li a{
       color: #fff;
    }
    .mtext li a:hover{
       color: #15bf5e;
    }
    </style>
    @endif
@endif
@endif
@if (!BaseHelper::isHomepage($page->id))
    @php Theme::set('section-name', $page->name) @endphp
    <article class="post post--single">
        <div class="post__content">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="article_container">
                        @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty($galleries = gallery_meta_data($page)))
                            {!! render_object_gallery($galleries) !!}
                        @endif
                        {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, clean($page->content, 'youtube'), $page) !!}
                    </div>
                </div>
            </div>
        </div>
    </article>
@else
    @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty($galleries = gallery_meta_data($page)))
        {!! render_object_gallery($galleries) !!}
    @endif
    {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, clean($page->content, 'youtube'), $page) !!}
     @include(Theme::getThemeNamespace() .'::partials.corners')
@endif