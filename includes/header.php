<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?=$config['siteTitle']?> | <?=$title?></title>
    <meta name="author" content="<?=$config['author']?>" />
    <meta name="description" content="<?=$config['description']?>" />
    <meta property="og:description" content="<?=$config['description']?>" />
    <meta property="og:title" content="<?=$title?>" />
    <meta property="og:locale" content="ko_KR" />
    <link rel="canonical" href="<?=$config['baseurl']?>" />
    <meta property="og:url" content="<?=$config['baseurl']?>" />
    <meta property="og:site_name" content="TELKblog" />
    <link rel="stylesheet" type="text/css" href="/assets/common.css" />
    <link rel="stylesheet" href="/assets/prism.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
</head>
<body>
<header>
    <nav>
        <div id="siteTitle">
            <a href="/"><?=$config['siteTitle']?></a>
        </div>
        <ul>
            <li><a href="/">Posts</a></li>
            <!-- <li><a href="/tags">Tags</a></li> -->
            <li><a href="/about">About</a></li>
        </ul>
    </nav>
</header>

<div id="content">
