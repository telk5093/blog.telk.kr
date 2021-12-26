@extends('layout')
@section('title', 'Tags')
@section('content')
<ul class="tags">
@foreach ($tagData as $_tagName => $_tags)
    <li>
        <span class="tag">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tag" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="8.5" cy="8.5" r="1" fill="currentColor" />
                <path d="M4 7v3.859c0 .537 .213 1.052 .593 1.432l8.116 8.116a2.025 2.025 0 0 0 2.864 0l4.834 -4.834a2.025 2.025 0 0 0 0 -2.864l-8.117 -8.116a2.025 2.025 0 0 0 -1.431 -.593h-3.859a3 3 0 0 0 -3 3z" />
            </svg>
            {{$_tagName}}
        </span>
        <br />
    @foreach ($_tags as $_uname)
        @php
        list($_date, $_uid) = explode('-', $_uname, 2);
        $_date = date('Y/m/d', strtotime($_date));
        $_title = $postList[$_uname]['title'];
        @endphp
        <a href="/{{$_date}}/{{$_uid}}">{{$_title}}</a><br />
    @endforeach
    </li>
@endforeach
</ul>
@endsection
