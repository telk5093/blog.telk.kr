<ul class="post-list">
@foreach ($postList as $uid => $postData)
	<li>
		<a href="{{ url($postData['postLink']) }}">
            <time datetime="{{date('Y/m/d H:i:s', $postData['dateTime'])}}">{{date('Y/m/d', $postData['dateTime'])}}</time>
            <h2>{{$postData['meta']['title']}}</h2>
            <p>{!!$postData['contentSummary']!!}</p>
        </a>
	</li>
@endforeach
</ul>

{{$paginate->links('pagination', ['data' => $paginate, 'lastPageNum' => $lastPageNum])}}
