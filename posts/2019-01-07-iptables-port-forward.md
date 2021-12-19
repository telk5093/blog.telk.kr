---
layout: post
title: "iptables 특정 포트를 다른 포트로 포워딩"
tags: [linux,iptables]
permalink: /116
comments: true
published: true
use_math: false
---
80번 포트를 8080번 포트로 포워딩하는 방법이다.

``sudo iptables -A PREROUTING -t nat -i eth0 -p tcp --dport 80 -j REDIRECT --to-port 8080``
<br />
<br />
  
**(추가)**  
eth0는 이더넷 이름으로, ``ifconfig`` 명령어로 이더넷의 이름을 먼저 확인해둘 필요가 있다.
```
ens3: flags=4163<UP,BROADCAST,RUNNING,MULTICAST>  mtu 9000
        inet 10.0.0.4  netmask 255.255.255.0  broadcast 10.0.0.255
        inet6 fe80::17ff:fe00:3c46  prefixlen 64  scopeid 0x20<link>
        ether 02:00:17:00:3c:46  txqueuelen 1000  (Ethernet)
        RX packets 143716  bytes 197647297 (197.6 MB)
        RX errors 0  dropped 0  overruns 0  frame 0
        TX packets 107925  bytes 72441697 (72.4 MB)
        TX errors 0  dropped 0 overruns 0  carrier 0  collisions 0

lo: flags=73<UP,LOOPBACK,RUNNING>  mtu 65536
        inet 127.0.0.1  netmask 255.0.0.0
        inet6 ::1  prefixlen 128  scopeid 0x10<host>
        loop  txqueuelen 1000  (Local Loopback)
        RX packets 7392  bytes 707152 (707.1 KB)
        RX errors 0  dropped 0  overruns 0  frame 0
        TX packets 7392  bytes 707152 (707.1 KB)
        TX errors 0  dropped 0 overruns 0  carrier 0  collisions 0
```