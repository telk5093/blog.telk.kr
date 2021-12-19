---
layout: post
title: "[PHP] 참조(reference)를 이용해서 재귀함수 없이 row data를 계층형 배열로 가공"
tags: [php, code]
comments: true
published: true
use_math: false
---

카테고리나 답댓글이 있는 댓글 등과 같이 계층형 구조를 가져야 하는 경우, 보통 각 row data를 ``[no, name, parent, ...]`` 형식으로 저장하는데  
이를 참조(reference)를 이용해서 재귀함수를 사용하지 않고 계층형 배열로 가공하는 방법이다.

예를 들어 아래와 같은 데이터를:
```php
	//no  name    parent
	[1,   'A',    0],
	[2,   'Aa',   1],
	[3,   'Aa1',  2],
	[4,   'Aa2',  2],
	[5,   'Ab',   1],
	[6,   'Ab1',  5],
```
<br />

아래와 같은 배열로 가공하는 코드이다:
```
[
	A => [
		Aa => [
			Aa1 => [],
			Aa2 => [],
		],
		Ab => [
			Ab1 => [],
		]
	],
  ...
```
<br />
* * * * *
<br /><br />


**PHP code:**
```php

	$rows = [
		[
			'no' => 1, 
			'name' => 'A',
			'parent' => 0
		],
		[
			'no' => 5, 
			'name' => 'Aa',
			'parent' => 1
		],
		[
			'no' => 9, 
			'name' => 'Aa1',
			'parent' => 5
		],
		[
			'no' => 10,
			'name' => 'Aa2',
			'parent' => 5
		],
		[
			'no' => 6, 
			'name' => 'Ab',
			'parent' => 1
		],
		[
			'no' => 11,
			'name' => 'Ab1',
			'parent' => 6
		],
		[
			'no' => 7, 
			'name' => 'Ac',
			'parent' => 1
		],
		[
			'no' => 8, 
			'name' => 'Ad',
			'parent' => 1
		],
		[
			'no' => 2, 
			'name' => 'B',
			'parent' => 0
		],
		[
			'no' => 3, 
			'name' => 'C',
			'parent' => 0
		],
		[
			'no' => 4, 
			'name' => 'D',
			'parent' => 0
		],
	];

	$output   = [];
	$keyIndex = [];
	foreach($rows as &$_row) {
		$keyIndex[$_row['no']] = &$_row;

		$_row['child'] = [];
		if(!$_row['parent']) {
			$output[$_row['no']] = &$_row;
		} else {
			$keyIndex[$_row['parent']]['child'][$_row['no']] = &$_row;
		}
	}

	print_r($output);
```
  
  
**output:**
```
Array
(
    [1] => Array
        (
            [no] => 1
            [name] => A
            [parent] => 0
            [child] => Array
                (
                    [5] => Array
                        (
                            [no] => 5
                            [name] => Aa
                            [parent] => 1
                            [child] => Array
                                (
                                    [9] => Array
                                        (
                                            [no] => 9
                                            [name] => Aa1
                                            [parent] => 5
                                            [child] => Array
                                                (
                                                )

                                        )

                                    [10] => Array
                                        (
                                            [no] => 10
                                            [name] => Aa2
                                            [parent] => 5
                                            [child] => Array
                                                (
                                                )

                                        )

                                )

                        )

                    [6] => Array
                        (
                            [no] => 6
                            [name] => Ab
                            [parent] => 1
                            [child] => Array
                                (
                                    [11] => Array
                                        (
                                            [no] => 11
                                            [name] => Ab1
                                            [parent] => 6
                                            [child] => Array
                                                (
                                                )

                                        )

                                )

                        )

                    [7] => Array
                        (
                            [no] => 7
                            [name] => Ac
                            [parent] => 1
                            [child] => Array
                                (
                                )

                        )

                    [8] => Array
                        (
                            [no] => 8
                            [name] => Ad
                            [parent] => 1
                            [child] => Array
                                (
                                )

                        )

                )

        )

    [2] => Array
        (
            [no] => 2
            [name] => B
            [parent] => 0
            [child] => Array
                (
                )

        )

    [3] => Array
        (
            [no] => 3
            [name] => C
            [parent] => 0
            [child] => Array
                (
                )

        )

    [4] => Array
        (
            [no] => 4
            [name] => D
            [parent] => 0
            [child] => Array
                (
                )
        )
)
```