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
    function parseMetaData($source)
    {
        $metaData = [];
        $lines = explode("\n", trim($source));
        for ($l=0, $llen=count($lines); $l<$llen; $l++) {
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
    $posts      = [];
    $tags       = [];
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

            // Tags
            foreach ($_metaData['tags'] as $_tag) {
                $tags[$_tag][] = $_uid;
            }
        }

        // Sort by date desc
        uasort($posts, function ($a, $b) {
            return $a['date'] < $b['date'];
        });

        // Sort by tag's name asc
        ksort($tags, SORT_STRING);
    } else {
        $posts = [];
    }

    // Route
    $Route = new Route();

    // List
    Route::add('/', function () {
        global $config, $posts;

        // Page
        $page = abs((int) $_GET['page']);
        if (!$page || $page < 1) {
            $page = 1;
        }
        $totalPostCount = count($posts);
        $lastPageNum    = (int) ceil($totalPostCount / $config['item_per_page']);
        if ($page > $lastPageNum) {
            $page = $lastPageNum;
        }
        $startNum       = (int) (($page - 1) * $config['item_per_page']);

        // Slice array
        $posts = array_slice($posts, $startNum, $config['item_per_page']);
        
        $title = 'Home';
        include_once _PATH.'/includes/header.php';

        echo '<ul class="post-list">'.PHP_EOL;
        foreach ($posts as $_uid => $_postData) {
            $_postTime = $_postData['date'];
            
            // Custom date
            if ($_postData['meta']['date']) {
                $_postTime = $_postData['meta']['date'];
            }

            // Cut contents
            $_listContent = $_postData['content'];
            $Parsedown = new \Parsedown();
            $_listContent = $Parsedown->text($_listContent);
            $_listContent = strip_tags($_listContent);
            if (iconv_strlen($_listContent) >= $config['content_limit']) {
                $_listContent = iconv_substr($_listContent, 0, $config['content_limit']).' ...';
            }

            include _PATH.'/includes/list.php';
        }
        echo '</ul>'.PHP_EOL;

        include_once _PATH.'/includes/pagination.php';
        include_once _PATH.'/includes/footer.php';
    });

    // View
    Route::add('/(\d{4,4})-(\d{2,2})-(\d{2,2})/([a-zA-Z0-9\-]+)', function ($y, $m, $d, $name) {
        global $config, $posts;
        $_uid = $y.'-'.$m.'-'.$d.'-'.$name;

        if (!array_key_exists($_uid, $posts)) {
            error404($_SERVER['REQUEST_URI']);
        }
        
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
        Route::add($_permalink, function () use ($_redirect) {
            http_response_code(301);
            header('location: /'.$_redirect.'');
            exit;
        });
    }

    // Tags
    Route::add('/tags', function () {
        global $config, $posts, $tags;
        
        $title = 'Home';
        include_once _PATH.'/includes/header.php';
        
        echo '<ul class="tags">';
        foreach ($tags as $_tagName => $_tags) {
            echo '<li>';
            echo '<span class="tag">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tag" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">';
            echo '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>';
            echo '<circle cx="8.5" cy="8.5" r="1" fill="currentColor" />';
            echo '<path d="M4 7v3.859c0 .537 .213 1.052 .593 1.432l8.116 8.116a2.025 2.025 0 0 0 2.864 0l4.834 -4.834a2.025 2.025 0 0 0 0 -2.864l-8.117 -8.116a2.025 2.025 0 0 0 -1.431 -.593h-3.859a3 3 0 0 0 -3 3z" />';
            echo '</svg>';
            echo ' '.$_tagName.'</span><br />';
            foreach ($_tags as $_uname) {
                list($_y, $_m, $_d, $_uid) = explode('-', $_uname, 4);
                $_date = $_y.'-'.$_m.'-'.$_d;
                $_title = $posts[$_uname]['title'];
                echo '<a href="/'.$_date.'/'.$_uid.'">'.$_title.'</a><br />';
            }
            echo '</li>'.PHP_EOL;
        }
        echo '</ul>';

        include_once _PATH.'/includes/footer.php';
    });

    // About
    Route::add('/(\w+)', function ($page) {
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
    function error404($path)
    {
        global $config, $posts;
        http_response_code(404);
        
        $title = 'Error';
        include_once _PATH.'/includes/header.php';
        include_once _PATH.'/includes/404.php';
        include_once _PATH.'/includes/footer.php';
    }

    // Register
    Route::run('/');
