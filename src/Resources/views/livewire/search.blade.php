<div>
    <input wire:model.live="query" type="text" placeholder="Search..." class="form-control mb-2">
    <ul style="position:absolute; background-color:'#dfdfdf'; z-index:100;" class="list-unstyled">
        @foreach ($results['documents'] as $doc)
            <li>
                <a style="text-decoration: none; color:blue" href="{{url('/storage/' . $doc->file)}}">
                    {{$doc->document}}
                </a>
                @foreach ($doc->tags as $tag)
                    &nbsp;<span class="badge bg-primary">{{$tag->name}}</span>
                @endforeach
            </li>
        @endforeach
    </ul>
</div>