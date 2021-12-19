---
layout: post
title: "[Linux/Shell script] EUC-KR to UTF-8"
tags: [linux, shell script]
permalink: /110
comments: true
published: true
use_math: false
---
conv2utf8.sh 파일이 있는 폴더 및 그 하위 폴더 안에 있는 모든 *.php 파일과 *.html 파일의 인코딩 형식을 EUC-KR에서 UTF-8로 변경하는 쉘 스크립트

1. 아래의 파일을 _**conv2utf8.sh**_ 파일로 저장
```
#!/bin/bash
for f in $(find -type f -name "*.php" -or -name "*.html")
do
    encoding=`file -i $f | cut -f 2 -d";" | cut -f 2 -d=`
    case $encoding in
    iso-8859-1)
        echo "$f was $encoding"
        iconv -c -f euc-kr -t utf-8 $f > $f.tmp
        mv $f.tmp $f
        ;;
    esac
done
```

2. ``chmod +x conv2utf8.sh`` 명령어로 실행 권한을 줌
3. ``./conv2utf8.sh`` 명령어로 실행