@if ($events->count() > 0)
    @foreach ($events as $event)
        <article class="post post__horizontal mb-40 clearfix">
            <div class="post__thumbnail">
                <img src="{{ RvMedia::getImageUrl($event->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="{{ $event->title }}">
            </div>
            <div class="post__content-wrap">
                <header class="post__header">
                    <h3 class="post__title">{{ $event->title }}</h3>
                    <div class="post__meta"><span class="post__created-at"><i class="ion-clock"></i>{{ $event->date_time }} </span>
                        @if ($event->author->username)
                            <span class="post__author"><i class="ion-android-person"></i><span>{{ $event->author->name }}</span></span>
                        @endif
                        @if($event->place)
                            <br><span class="post__author"><i class="ion-location"></i><span>{{ $event->place }}</span></span>
                        @endif
                        </div>
                </header>
                <div class="post__content" style="padding: 0">
                    <p data-number-line="4">{{ $event->description }}</p>
                </div>
            </div>
        </article>
    @endforeach
    
@endif

<style>
    .section.pt-50.pb-100 {
        background-color: #ecf0f1;
    }
</style>
