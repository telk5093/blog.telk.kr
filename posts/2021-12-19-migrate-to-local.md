---
layout: post
title: "블로그 마이그레이션"
tags: []
comments: true
published: true
use_math: false
---

블로그를 jekyll에서 로컬 개발 환경(PHP)으로 마이그레이션했습니다.  
jekyll도 훌륭한 도구이지만 아무래도 자주 포스팅하는 입장이 아니다보니 포스트를 Markdown으로 작성한 뒤에 ``bundle exec jekyll build``를 입력하고 Github에 push 하는 것이 여간 귀찮은 일이 아니었습니다.  
<br />
  
어차피 저는 PHP가 가장 편하고, Markdown으로 가볍게 블로깅을 하는 것이 목적이었기 때문에 일요일 오전 시간을 활용해서 간단한 PHP+Markdown 블로그를 작성해봤습니다.  
Routing은 [simplePHPRouter](https://github.com/steampixel/simplePHPRouter)을 사용했고, Markdown parser로는 [Markdown Parser in PHP](https://parsedown.org/)를 이용해봤습니다.  
Syntax highlighting은 [prismjs](https://prismjs.com/)를 활용했습니다.  

`/posts/` 폴더 안에 있는 `*.md` 파일을 모두 읽어오는 방식이기 때문에 포스트 개수가 많아지면 느려질 수 있습니다만  
어차피 제 블로그는 포스트 양이 그렇게 많지 않으니까요 -_-a;;
시간이 된다면 Pagination도 추가해볼 생각입니다.  
<br />

만들어진 PHP 소스는 https://github.com/telk5093/blog.telk.kr 에 공개해두었습니다.
