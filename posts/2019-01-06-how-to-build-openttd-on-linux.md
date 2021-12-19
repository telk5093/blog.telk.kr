---
layout: post
title: "Ubuntu에서 OpenTTD를 빌드하는 방법"
tags: [openttd, linux, build, ubuntu]
permalink: /115
comments: true
published: true
use_math: false
---

Ubuntu 20.04(16.04, 18.04 동일)에서 OpenTTD를 빌드하는 방법이다.
자세한 것은 [COMPILING.md 파일](https://github.com/OpenTTD/OpenTTD/blob/master/COMPILING.md)을 참고하기 바란다.

1. 빌드에 필요한 Dependency를 설치한다.
``cmake``와 ``make``, ``g++``는 기본적으로 있어야 하고, 그 외 추가적으로 빌드에 필요한 라이브러리와 그 상세 용도는 아래와 같다.
 - zlib: 오래된(0.3.0-1.0.5) 세이브 파일, 다운로드한 콘텐츠, 높이맵의 압축(해제) 용도
 - liblzo2: 오래된(0.3.0 이전) 세이브 파일 압축(해제) 용도
 - liblzma: 세이브 파일(1.1.0 이후) 압축(해제) 용도
 - libpng: 스크린 샷을 생성하고 높이맵을 생성하는 용도
 - libfreetype: 폰트를 불러오고 렌더링하기 위한 용도
 - libfontconfig: 폰트를 찾고 실제 폰트의 이름을 가져오는 용도
 - libicu: 오른쪽에서 왼쪽으로 쓰는 글(e.g. 아랍어, 페르시아어 등)이나 문자열의 자연 정렬(리눅스만 해당) 용도
 - libsdl2: 하드웨어 접근(비디오, 사운드, 마우스) (Windows나 MacOS에서는 불필요)   
 다른 건 몰라도 liblzma는 있어야 최근 사용되는 대부분의 세이브 파일을 읽을 수 있다.
```
sudo apt install -y cmake pkg-config g++ zlib1g-dev liblzo2-dev liblzma-dev libpng-dev libfreetype6-dev libfontconfig-dev libicu-dev libsdl2-dev fcitx-libs-dev
```

2. OpenTTD Github에서 저장소를 클론해온다
```
git clone https://github.com/OpenTTD/OpenTTD.git
```
 - JGR's Patch Pack을 가져오려면 ``git clone https://github.com/JGRenisson/OpenTTD-patches.git ./OpenTTD`` 를 위 명령어 대신 실행한다.<br /><br />


3. OpenTTD 폴더로 진입하고 build 폴더를 만든 다음, ``cmake ..``와 ``make`` 명령어를 실행한다
```
cd OpenTTD
mkdir build && cd build
cmake ..
make
```
 * cmake의 버전은 3.12.4 이상이 필요하므로 [https://kalten.tistory.com/267](https://kalten.tistory.com/267) 을 참고하여 최신 버전으로 업데이트