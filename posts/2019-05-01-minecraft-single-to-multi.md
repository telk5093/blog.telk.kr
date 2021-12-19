---
layout: post
title: "마인크래프트 멀티플레이 맵을 싱글플레이 맵으로 변환"
tags: [minecraft]
permalink: /117
comments: true
published: true
use_math: false
---
1. 멀티플레이 서버에 있는 ``world``, ``world_nether``, ``world_the_end`` 폴더를 백업
2. 새 싱글플레이 맵을 하나 생성하고 바로 나옴.
3. ``%APPDATA%/.minecraft/save`` 폴더 안에 해당 맵 이름으로 된 폴더(이하 "싱글 폴더") 안으로 들어감
4. 멀티의 world 폴더 **안**의 내용물을 모두 복사 → 싱글 폴더 안에 붙여넣기
5. 멀티의 world_nether 폴더 안에 있는 DIM-1 **폴더 자체**를 복사 → 싱글 폴더 안에 있는 DIM-1 폴더에 덮어쓰기
6. 멀티의 world_the_end 폴더 안에 있는 DIM1 **폴더 자체**를 복사 → 싱글 폴더 안에 있는 DIM1 폴더에 덮어쓰기