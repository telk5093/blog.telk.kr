---
layout: post
title: "OpenRCT2를 리눅스에 설치하는 방법"
tags: [openrct2]
comments: true
published: true
use_math: false
---
OpenRCT2를 Ubuntu 20.04 에 설치하는 방법이다.  

1. 설치를 원하는 폴더로 접근
1. [https://openrct2.org/downloads/develop/latest](https://openrct2.org/downloads/develop/latest)에서 먼저 Linux용 빌드 링크 주소를 복사
1. ``wget (복사한 주소)``  
   (eg. 0.3.3-develop-8eabdf8 버전의 경우 ``wget https://github.com/Limetric/OpenRCT2-binaries/releases/download/v0.3.3-8eabdf8/OpenRCT2-0.3.3-develop-8eabdf8-linux-x86_64.tar.gz``)
1. ``tar -zpxvf OpenRCT2*.tar.gz``

1. **기본 dependency 설치**
   ```
   sudo apt install -y curl libsdl2-dev fontconfig libzip-dev libpng-dev libfontconfig1-dev libfreetype6-dev libcrypto++-dev nlohmann-json3-dev openssl libicu-dev cmake
   ```

1. **구 버전의 dependency 수동 설치**
   libzip.so.4, libicu.so.60, libduktape.so.202 는 Ubuntu 20.04에서는 자동으로 설치되지 않으므로 수동 설치한다.
   ```
   cd /tmp
   wget http://archive.ubuntu.com/ubuntu/pool/universe/libz/libzip/libzip4_1.1.2-1.1_amd64.deb
   wget http://archive.ubuntu.com/ubuntu/pool/main/i/icu/libicu60_60.2-3ubuntu3.1_amd64.deb
   wget http://archive.ubuntu.com/ubuntu/pool/universe/d/duktape/libduktape202_2.2.0-3_amd64.deb
   sudo dpkg -i libzip4_1.1.2-1.1_amd64.deb
   sudo dpkg -i libicu60_60.2-3ubuntu3.1_amd64.deb
   sudo dpkg -i libduktape202_2.2.0-3_amd64.deb
   ```
1. 이제 ``./openrct2-cli`` 를 실행해서 확인한다.
