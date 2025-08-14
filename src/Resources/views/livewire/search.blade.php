<div>
    <input wire:model.live="query" type="text" placeholder="Search..." class="form-control">
    <ul style="position:absolute; background-color:'#dfdfdf'; z-index:100;" class="list-unstyled">
        @foreach ($results['blogs'] as $blog)
            <li>
                <a href="{{url('/blog/' . substr($blog->published_at,0,4) . '/' . substr($blog->published_at,5,2) . '/' . $blog->slug)}}">
                    {{$blog->title}}
                </a>
            </li>
        @endforeach
        @foreach ($results['liturgies'] as $liturgy)
            <li>
                <a href="{{url('/liturgy/' . substr($liturgy->created_at,0,4) . '/' . substr($liturgy->created_at,5,2) . '/' . $liturgy->slug)}}">
                    {{$liturgy->title}}
                </a>
            </li>
        @endforeach
        @foreach ($results['videos'] as $video)
            <li>
                <a href="{{url('/videos/' . substr($video->published_at,0,4) . '/' . substr($video->published_at,5,2) . '/' . $video->slug)}}">
                    {{$video->title}}
                </a>
            </li>
        @endforeach
    </ul>
</div>