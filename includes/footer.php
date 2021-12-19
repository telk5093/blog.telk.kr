</div>

<footer>
    <div id="copyright">
        © <?=date('Y')?> <?=$config['author']?>
    </div>
</footer>

<script src="/assets/prism.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?=$configs['google_analytics']?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{{ site.google_analytics }}');
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id=<?=$configs['google_analytics_v4']?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{{ site.google_analytics_v4 }}');
</script>

</body>
</html>
