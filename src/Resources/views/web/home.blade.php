<x-hgrh::layouts.app pageName="Home">
    <!-- Most recent posts -->
    <section id="trending-category" class="trending-category section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="container" data-aos="fade-up">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <div class="post-entry lg">
                            @if ($latest[0]['image']>'')
                                <a href="{{url('/' . $latest[0]['url'] . '/' . substr($latest[0]['date'],0,4) . '/' . substr($latest[0]['date'],5,2) . '/' . $latest[0]['slug'])}}">
                                    <img src="{{asset('storage/' . $latest[0]['image'])}}" alt="" class="img-fluid">
                                </a>
                            @endif
                            <div class="post-meta">
                                @foreach ($latest[0]['tags'] as $tag)
                                    <a href="{{url('/subjects/' . $tag->slug)}}"><span class="date">{{$tag['name']}}</span></a> <span class="mx-1">•</span> 
                                @endforeach
                                <span>{{date("j M 'y",strtotime($latest[0]['date']))}}</span>
                            </div>
                            <h2><a href="{{url('/' . $latest[0]['url'] . '/' . substr($latest[0]['date'],0,4) . '/' . substr($latest[0]['date'],5,2) . '/' . $latest[0]['slug'])}}">{{$latest[0]['title']}}</a></h2>
                            <p class="mb-4 d-block">{!!$latest[0]['excerpt']!!}</p>
                            <div class="post-entry">
                                <a href="{{url('/' . $latest[1]['url'] . '/' . substr($latest[1]['date'],0,4) . '/' . substr($latest[1]['date'],5,2) . '/' . $latest[1]['slug'])}}"><img src="{{asset('storage/' . $latest[1]['image'])}}" alt="" class="img-fluid"></a>
                                <div class="post-meta">
                                    @foreach ($latest[1]['tags'] as $tag)
                                        <a href="{{url('/subjects/' . $tag->slug)}}"><span class="date">{{$tag['name']}}</span></a> <span class="mx-1">•</span> 
                                    @endforeach
                                    <span>{{date("j M 'y",strtotime($latest[1]['date']))}}</span>    
                                </div>
                                <h2><a href="{{url('/' . $latest[1]['url'] . '/' . substr($latest[1]['date'],0,4) . '/' . substr($latest[1]['date'],5,2) . '/' . $latest[1]['slug'])}}">{{$latest[1]['title']}}</a></h2>
                                {!!$latest[1]['excerpt']!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row g-5">
                            @foreach ($latest as $recent)
                                @if ($loop->index>1)
                                    @if ($loop->index==2)
                                        <div class="col-lg-4 border-start custom-border">
                                    @endif
                                    @if ($loop->index==5)
                                        </div><div class="col-lg-4 border-start custom-border">
                                    @endif
                                    <div class="post-entry">
                                        <a href="{{url('/' . $recent['url'] . '/' . substr($recent['date'],0,4) . '/' . substr($recent['date'],5,2) . '/' . $recent['slug'])}}"><img src="{{asset('storage/' . $recent['image'])}}" alt="" class="img-fluid"></a>
                                        <div class="post-meta">
                                            @foreach ($recent['tags'] as $tag)
                                                <a href="{{url('/subjects/' . $tag->slug)}}"><span class="date">{{$tag['name']}}</span></a> <span class="mx-1">•</span> 
                                            @endforeach
                                            <span>{{date("j M 'y",strtotime($recent['date']))}}</span>    
                                        </div>
                                        <h2><a href="{{url('/' . $recent['url'] . '/' . substr($recent['date'],0,4) . '/' . substr($recent['date'],5,2) . '/' . $recent['slug'])}}">{{$recent['title']}}</a></h2>
                                        {!!$recent['excerpt']!!}
                                    </div>
                                    @if ($loop->last)
                                        </div>
                                    @endif
                                    @if ($loop->last and $loop->index < 5)
                                        <div class="col-lg-4 border-start custom-border">
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                    <!-- Utilities - search and tags -->
                    <div class="col-lg-4">
                        <div>
                            @livewire('search')
                            <h3 class="mt-5">Tag cloud</h3>
                            Tags go here
                            <h3>About us</h3>
                        </div>
                    </div> <!-- End Utils Section -->
                </div>

                </div> <!-- End .row -->
            </div>
        </div>
    </section><!-- /Recent Section -->

    <!-- Content Section -->
    @foreach ($items as $name=>$type)
        <section id="lifestyle-category" class="lifestyle-category section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <div class="section-title-container d-flex align-items-center justify-content-between">
                    <h2>{{strtoupper(str_replace('_',' ',$name))}}</h2>
                    <p><a href="{{url('/' . $type[0]['url'])}}">See All {{strtoupper(str_replace('_',' ',$name))}}</a></p>
                </div>
            </div><!-- End Section Title -->
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-5">
                    @foreach ($type as $item)
                        @if ($loop->first)
                            <div class="col-lg-4">
                                <div class="post-list lg">
                                    <a href="{{url('/' . $item['url'] . '/' . substr($item['date'],0,4) . '/' . substr($item['date'],5,2) . '/' . $item['slug'])}}"><img src="{{asset('storage/' . $item['image'])}}" alt="" class="img-fluid"></a>
                                    <div class="post-meta">
                                        @foreach ($item['tags'] as $tag)
                                            <a href="{{url('/subjects/' . $tag->slug)}}"><span class="date">{{$tag['name']}}</span></a> <span class="mx-1">•</span> 
                                        @endforeach
                                        <span>{{date("j M 'y",strtotime($item['date']))}}</span>    
                                    </div>
                                    <h2><a href="{{url('/' . $item['url'] . '/' . substr($item['date'],0,4) . '/' . substr($item['date'],5,2) . '/' . $item['slug'])}}">{{$item['title']}}</a></h2>
                                    <p class="mb-4 d-block">{!!$item['excerpt']!!}</p>
                                </div>
                        @elseif ($loop->index < 3)
                            <div class="post-list border-bottom">
                                <div class="post-meta">
                                    @foreach ($item['tags'] as $tag)
                                        <a href="{{url('/subjects/' . $tag->slug)}}"><span class="date">{{$tag['name']}}</span></a> <span class="mx-1">•</span> 
                                    @endforeach
                                    <span>{{date("j M 'y",strtotime($item['date']))}}</span>    
                                </div>    
                                <h2><a href="{{url('/' . $item['url'] . '/' . substr($item['date'],0,4) . '/' . substr($item['date'],5,2) . '/' . $item['slug'])}}">{{$item['title']}}</a></h2>
                            </div>
                            @if ($loop->index==2)
                                </div>
                            @endif
                        @elseif ($loop->index > 2)
                            @if ($loop->index ==3 or $loop->index == 8)
                                @if ($loop->index==3)
                                    <div class="col-lg-8">
                                        <div class="row g-5">
                                @endif
                                <div class="col-lg-6 border-start custom-border">
                            @endif
                            <div class="post-list">
                                <a href="{{url('/' . $item['url'] . '/' . substr($item['date'],0,4) . '/' . substr($item['date'],5,2) . '/' . $item['slug'])}}"><img src="assets/img/post-landscape-6.jpg" alt="" class="img-fluid"></a>
                                <div class="post-meta">
                                    @foreach ($item['tags'] as $tag)
                                        <a href="{{url('/subjects/' . $tag->slug)}}"><span class="date">{{$tag['name']}}</span></a> <span class="mx-1">•</span> 
                                    @endforeach
                                    <span>{{date("j M 'y",strtotime($item['date']))}}</span>      
                                </div>
                                <h2><a href="{{url('/' . $item['url'] . '/' . substr($item['date'],0,4) . '/' . substr($item['date'],5,2) . '/' . $item['slug'])}}">{{$item['title']}}</a></h2>
                            </div>
                            @if ($loop->index==7 or $loop->index==12)
                                @if ($loop->index==7)
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        </section><!-- /Content Section -->
    @endforeach
</x-hgrh::layouts.web>