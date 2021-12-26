@extends('layout')
@section('title', $postData['title'])
@section('content')
<h2 class="post-title">{{$postData['title']}}</h2>
<div class="author">Written by {{\Config::get('blog.author')}} on {{date('Y.m.d', $postData['dateTime'])}}</div>
{!!$postData['content']!!}

<!-- Start utterances comments -->
<script src="https://utteranc.es/client.js" repo="telk5093/blog.telk.kr" issue-term="pathname" theme="preferred-color-scheme" crossorigin="anonymous" async></script>

@include('postlist', $list)
@endsection


@if (isset($postData['meta']['use_math']))
@push('scripts')
<!-- MathJax -->
<script type="text/x-mathjax-config">
MathJax.Hub.Config({
	tex2jax: {inlineMath: [['$', '$'], ['\\(', '\\)']]}
});
</script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3.0.1/es5/tex-mml-chtml.js"></script>
@endpush
@endif