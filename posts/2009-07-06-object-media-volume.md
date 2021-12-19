---
layout: post
title: "object로 삽입한 미디어의 퍼센트 단위 볼륨 조정"
tags: [javascript]
permalink: /8
comments: true
published: true
use_math: true
---

아래와 같이 object 태그를 이용해서 미디어(특히 음악)를 삽입했다고 하자.   
```
<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" id="bgm">
    <param name="FileName" value="음원파일" />
    <param name="Volume" value="-600" />
</object>
```
이때, param 태그로 음량(Volume)을 조절할 수 있다.

classid 속성값을 clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95 으로 준 경우에는   
Volume의 값은 -10000 ~ 0을 가질 수 있다. (-10000은 muted, 0은 full volume)

classid 속성값이 CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6 인 경우에는 Volume의 값은 0 ~ 100을 가진다.


Volume이 0~ 100의 값을 가지는 후자의 경우에는 기본값이 50이고 단위가 퍼센트(%)이기 때문에 직관적인 이해가 쉽다.   
그래서 Javascript에서 다음과 같이 접근하면 손쉽게 볼륨 조정이 가능하다.

```
<script type="text/javascript">
<!--
function setVolume1(percent) {
    var bgm = document.getElementById("bgm");
    bgm.settings.volume = percent; //50을 인수로 주면 그대로 50% 볼륨으로 설정
}
-->
</script>
```
그러나 Volume이 -10000 ~ 0의 값을 가지는 전자의 경우, 기본값인 50% 볼륨에 해당하는 Volume 값은 -602이다.   
다시 말해서, param 태그의 Volume에 들어갈 값이 퍼센트 단위와 직관적으로 연결되지 않아 난감하다는 것이다.   

결론부터 말하자면, 사실 저 값은 밀리벨[mB] (=100분의 1 dB)로 얼마나 음량을 줄일 것인지를 말한다.

param태그에서 Volume 값을 -602로 주면, (ie. ``<param name="Volume" value="-602" />``)
그 단위가 mB이므로 단위를 데시벨(dB)로 고쳐보면 미디어의 음량을 -6.02dB을 줄이는 효과를 얻을 수 있다.

[이 참고 포스트](https://blog.naver.com/mith80/60019103622)에서 중간의 표를 보라. 이 팁에서는 왼쪽 표보다는 오른쪽 표가 중요하다.   
표를 보면 -6dB은 음량을 0.5배, 즉 반으로 줄이고 -20dB은 음량을 0.1배, 즉 10%로 만든다는 것을 알 수 있다.

 

이 점을 이용해서, -10000 ~ 0의 값을 가지는 classid object의 경우에는   
퍼센트 단위로 볼륨을 조정하기 위해 다음과 같은 공식을 사용하게 된다.

\$\$ Vol = 20 \times \log_{10}{x} \$\$
(Vol : 데시벨(dB) 단위, x : 음량 배율 (x배))

즉, 우리가 음량을 50%로 만들고 싶다면 x = 50% = 50/100 = 0.5 로 두면 되는 것이고,  
음량을 30%로 만들고 싶다면 x = 30% = 30/100 = 0.3 으로 두면 되는 것이다.  
그리고 param 태그에 들어갈 Volume 값은 데시벨(dB) 단위가 아니라 그 100분의 1인 밀리벨(mB) 단위이므로  
param 태그의 Volume 값에는 100×Vol의 값을 넣어주면 된다.

이제 이를 자바스크립트로 표현하면 다음과 같아진다.
```
<script type="text/javascript">
<!--
function setVolume2(percent) {
    if(percent>0) {
        var x = percent/100;
        var Vol = 20*(Math.log(x)/Math.log(10));    // 설명 #1
    } else {
        var Vol = -100;                             // 설명 #2
    }

    var bgm = document.getElementById("bgm");
    bgm.volume = Vol*100;
}
-->
</script>
```

이제까지 이론을 설명했는데,   
실제 적용시에는 몇 가지 오류를 피하기 위해 조금 다르게 적용해야 한다.

 

설명 #1은 자바스크립트의 Math.log 때문에 저렇게 작성하였다.   
공식은 Vol = 20×log10(x) 으로 로그의 밑이 10인 상용로그다.   
하지만 자바스크립트의 Math.log(x)는 밑이 e인 자연로그이다.   
따라서 아래와 같은 로그에 대한 수학 공식을 이용해서 밑을 10으로 바꿔줘야 한다.

$$ \log_{a}{b} = \frac{\log_{c}{b}} {\log_{c}{a}} $$

 

설명 #2는 수학적인 오류를 피하기 위함이다.
만약에 볼륨을 0%로 조정한다고 하자. (소스에서 percent = 0 인 경우)
그럼 우리는 저 소스가 최종적으로 Vol = -100을 뱉어내도록 해서
-100×100=-10000을 bgm.volume에 대입할 수 있도록 해야한다.

하지만 0을 공식에 대입하면 중간에 log(0)이 나타난다.
log(0)은 -∞이기 때문에 자바스크립트는 이를 -Infinity로 처리한다.
결국 문자와 숫자를 계산할 수 없기 때문에 스크립트 오류를 뱉고 만다.

따라서 이 경우는 예외적인 처리를 해서
percent = 0으로 준 경우에만 억지로 Vol = -100 으로 주어
우리가 원하는 -10000을 Volume에 넣을 수 있게 만드는 것이다.