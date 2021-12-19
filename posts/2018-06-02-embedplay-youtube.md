---
layout: post
title: "embedplay 유튜브 자동 재생이 막힐 경우"
tags: [tip]
permalink: /113
comments: true
published: true
use_math: false
---

크롬에서 [embedplayer](https://github.com/panzi/embedplayer)의 유튜브 동영상이 자동재생되지 않는다면 ...
1. chrome://flags/#autoplay-policy 설정 페이지로 들어감
2. Autoplay policy 설정의 값을 **No user gesture is required** 로 값을 변경
3. 크롬 재실행
(주의) 나만 작동하게 되는 것임. 다른 사람에게는 영향이 없음.

* * *

If youtube autoplay is not working on embedplayer in Chrome ...

1. Go to chrome://flags/#autoplay-policy
2. Set Autoplay policy to **No user gesture is required**
3. Relaunch Chrome
(note) Only works for ME, not being affected other people