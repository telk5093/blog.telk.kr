<ul class="pagination">
<?php
    if ($page > 1) {
?>
    <li class="prev"><a href="?page=<?=($page - 1)?>">&lt;</a></li>
<?php
    }
    for ($p=max(1, $page - $config['page_num']); $p<=min($page + $config['page_num'], $lastPageNum); $p++) {
?>
    <li<?=($page == $p ? ' class="current"' : '')?>>
        <a href="?page=<?=$p?>"><?=$p?></a>
    </li>
<?php
    }
    if ($page < $lastPageNum) {
?>
    <li class="next"><a href="?page=<?=($page + 1)?>">&gt;</a></li>
<?php
    }
?>
</ul>
