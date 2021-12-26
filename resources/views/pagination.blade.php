@php
	$page = $data->currentPage();
	$pageNum = \Config::get('blog.page_num');
@endphp
<ul class="pagination">
@if ($page > 1)
    <li class="prev"><a href="/?page={{($page - 1)}}">&lt;</a></li>
@endif
@for ($p=max(1, $page - $pageNum); $p<=min($page + $pageNum, $lastPageNum); $p++)
    <li{!!($page == $p ? ' class="current"' : '')!!}>
        <a href="/?page={{$p}}">{{$p}}</a>
    </li>
@endfor
@if ($page < $lastPageNum)
    <li class="next"><a href="/?page={{($page + 1)}}">&gt;</a></li>
@endif
</ul>
