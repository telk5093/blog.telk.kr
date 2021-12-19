<?php
    include_once __DIR__.'/config.php';
    include_once __DIR__.'/vendor/autoload.php';

    // https://github.com/steampixel/simplePHPRouter
    use Steampixel\Route;

    error_reporting(0);

    // Base path
    define('_PATH', __DIR__);
    define('_BASEDIR', '/');

    // Parse meta data
    function parseMetaData($source) {
        $metaData = [];
        $lines = explode("\n", trim($source));
        for($l=0, $llen=count($lines); $l<$llen; $l++) {
            $_line = trim($lines[$l]);
            if (!$_line) {
                continue;
            }

            list($_key, $_val) = explode(':', $_line, 2);
            $_val = trim($_val);

            // String
            if (substr($_val, 0, 1) === '"' && substr($_val, -1, 1) === '"') {
                $_val = substr($_val, 1, -1);
            
            // Array
            } elseif (substr($_val, 0, 1) === '[' && substr($_val, -1, 1) === ']') {
                $items = explode(',', substr($_val, 1, -1));
                $items = array_map('trim', $items);
                $_val = $items;
            }
            $metaData[$_key] = $_val;
        }
        return $metaData;
    }

    // Get posts
    $permalinks = [];
    $posts = [];
    if (is_dir(_PATH.'/posts/')) {
        $dir = opendir(_PATH.'/posts/');
        while (($_file = readdir($dir)) !== false) {
            if ($_file === '.' || $_file === '..') {
                continue;
            }

            $_source = file_get_contents(_PATH.'/posts/'.$_file);
            list($_y, $_m, $_d, $_uname) = explode('-', pathinfo($_file, PATHINFO_FILENAME), 4);
            $_date = $_y.'-'.$_m.'-'.$_d;    //eg) 2021-01-01
            $_uid  = $_date.'-'.$_uname;     //eg) 2021-01-01-page-unique-name

            // Parse meta data if it exists
            if (substr($_source, 0, 3) === '---' && ($_metaEndPos = strpos($_source, '---', 3)) !== false) {
                $_metaData = parseMetaData(trim(substr($_source, 3, $_metaEndPos - 3)));
                $_content = trim(substr($_source, $_metaEndPos + 3));
            } else {
                $_metaData = [
                    'title'     => $_uname,
                    'published' => 'true',
                ];
                $_content = trim(substr($_source, $_metaEndPos + 3));
            }

            if ($_metaData['published'] === 'false') {
                continue;
            }

            if (isset($_metaData['permalink'])) {
                $permalinks[$_metaData['permalink']] = $_date.'/'.$_uname;
            }

            $posts[$_uid] = [
                'date'    => $_date,
                'uname'   => $_uname,
                'meta'    => $_metaData,
                'title'   => $_metaData['title'],
                'content' => $_content,
            ];
        }

        // Sort by date desc
        uasort($posts, function($a, $b) {
            return $a['date'] < $b['date'];
        });
    } else {
        $posts = [];
    }

    $Route = new Route();

    // List
    Route::add('/', function() {
        global $config, $posts;
        
        $title = 'Home';
        include_once _PATH.'/includes/header.php';

        echo '<ul class="post-list">'.PHP_EOL;
        foreach ($posts as $_uid => $_postData) {
            $_postTime = $_postData['date'];
            if ($_postData['meta']['date']) {
                $_postTime = $_postData['meta']['date'];
            }

            $_listContent = $_postData['content'];
            $Parsedown = new \Parsedown();
            $_listContent = $Parsedown->text($_listContent);
            $_listContent = strip_tags($_listContent);
            if (iconv_strlen($_listContent) >= $config['content_limit']) {
                $_listContent = iconv_substr($_listContent, 0, $config['content_limit']).' ...';
            } else {
            }

            echo "\t".'<li><a href="'.$_postData['date'].'/'.$_postData['uname'].'">';
            echo '<time datetime="'.$_postTime.'">'.$_postData['date'].'</time>';
            echo '<h2>'.$_postData['meta']['title'].'</h2>';
            echo '<p>'.$_listContent.'</p>';
            echo '</a></li>'.PHP_EOL;
        }
        echo '</ul>'.PHP_EOL;

        include_once _PATH.'/includes/footer.php';
    });

    // View
    Route::add('/(\d{4,4})-(\d{2,2})-(\d{2,2})/([a-zA-Z0-9\-]+)', function($y, $m, $d, $name) {
        global $config, $posts;
        $_uid = $y.'-'.$m.'-'.$d.'-'.$name;
        
        $title = $posts[$_uid]['title'];
        $postDate = date('Y/m/d', strtotime($posts[$_uid]['date']));
        include_once _PATH.'/includes/header.php';

        echo '<h2 class="post-title">'.$posts[$_uid]['title'].'</h2>'.PHP_EOL;
        echo '<div class="author">Written by '.$config['author'].' on '.$postDate.'</div>'.PHP_EOL;

        $Parsedown = new \Parsedown();
        echo $Parsedown->text($posts[$_uid]['content']);

        // MathJax
        if ($posts[$_uid]['meta']['use_math']) {
            echo '<script type="text/x-mathjax-config">
            MathJax.Hub.Config({
              tex2jax: {inlineMath: [[\'$\',\'$\'], [\'\\(\',\'\\)\']]}
            });
            </script><script async src="https://cdn.jsdelivr.net/npm/mathjax@2/MathJax.js?config=TeX-AMS-MML_CHTML"></script>'.PHP_EOL;
        }

        include_once _PATH.'/includes/utterances_comments.html';
        include_once _PATH.'/includes/footer.php';
    });

    // Permalink
    foreach ($permalinks as $_permalink => $_redirect) {
        Route::add($_permalink, function() use ($_redirect) {
            http_response_code(301);
            header('location: /'.$_redirect.'');
            exit;
        });
    }

    // About
    Route::add('/(\w+)', function($page) {
        global $config, $posts;
        
        $title = 'Home';
        include_once _PATH.'/includes/header.php';
        if (file_exists(_PATH.'/pages/'.$page.'.md')) {
            $Parsedown = new Parsedown();
            echo $Parsedown->text(file_get_contents(_PATH.'/pages/'.$page.'.md'));
        } else {
            error404($page);
        }
        include_once _PATH.'/includes/footer.php';
    });

    // 404
    Route::pathNotFound('error404');
    function error404($path) {
        global $config, $posts;
        http_response_code(404);
        
        $title = 'Error';
        include_once _PATH.'/includes/header.php';
        include_once _PATH.'/includes/404.php';
        include_once _PATH.'/includes/footer.php';
    }

    // Register
    Route::run('/');
