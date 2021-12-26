<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;

class post extends Controller
{
    public $tags = [];
    public $permalinks = [];

    /**
     * Get post data
     * @param string $postFileName              File name of the post   (eg) "2022-01-23-post-unique-name.md"
     * @return array|false                      Data of the post
     */
    private function getPostData(string $postFileName)
    {
        if (!is_dir(base_path().'/posts/')) {
            return false;
        }
        $postPath = base_path().'/posts/'.$postFileName;
        if (!file_exists($postPath)) {
            return false;
        }

        $uid = pathinfo($postFileName, PATHINFO_FILENAME);
        $_source = file_get_contents($postPath);
        $_temp = explode('-', $uid, 2);
        $_tempLen = count($_temp);

        // If there is both date string and post name id
        if ($_tempLen === 2) {
            $dateString = $_temp[0];
            $postName   = $_temp[1];
        } else {
            // If the file name is only consist of date
            if (preg_match('/^([0-9]+)$/is', $_temp[0]) && in_array(strlen($_temp[0]), [6, 8, 12, 14])) {
                $postName = 'unnamed';
                $dateString = $_temp[0];

            // If the file name is only consist of string
            } else {
                $postName = $_temp[0];
                $dateString = date('Ymd', filemtime($postPath));
            }
        }

        // Get post date
        switch (strlen($dateString)) {
            case 6:   // yymmdd
                $_y = substr($dateString, 0, 2);
                $dateTime = mktime(0, 0, 0, substr($dateString, 2, 2), substr($dateString, 4, 2), $_y + ($_y < 50 ? 2000 : 1900));
                break;
            case 8:   // YYYYmmdd
                $dateTime = mktime(0, 0, 0, substr($dateString, 4, 2), substr($dateString, 6, 2), substr($dateString, 0, 4));
                break;
            case 12:   // YYYYmmddHHii
                $dateTime = mktime(substr($dateString, 8, 2), substr($dateString, 10, 2), 0, substr($dateString, 4, 2), substr($dateString, 6, 2), substr($dateString, 0, 4));
                break;
            case 14:   // YYYYmmddHHiiss
                $dateTime = mktime(substr($dateString, 8, 2), substr($dateString, 10, 2), substr($dateString, 12, 2), substr($dateString, 4, 2), substr($dateString, 6, 2), substr($dateString, 0, 4));
                break;
            default:
                $dateTime = strtotime($dateString);
        }

        // Parse meta data if it exists
        if (substr($_source, 0, 3) === '---' && ($_metaEndPos = strpos($_source, '---', 3)) !== false) {
            $_metaData = $this->parseMetaData(trim(substr($_source, 3, $_metaEndPos - 3)));
            $contentRaw = trim(substr($_source, $_metaEndPos + 3));
        } else {
            $_metaData = [
                'title'     => $postName,
                'published' => 'true',
            ];
            $contentRaw = trim(substr($_source, $_metaEndPos + 3));
        }

        // Cut contents
        $Parsedown = new \Parsedown();
        $content = $Parsedown->text($contentRaw);
        
        $contentSummary = strip_tags($content);
        if (iconv_strlen($contentSummary) >= \Config::get('blog.content_limit')) {
            $contentSummary = iconv_substr($contentSummary, 0, \Config::get('blog.content_limit')).' ...';
        }

        if ($_metaData['published'] === 'false') {
            return false;
        }

        // if (isset($_metaData['permalink'])) {
        //     $permalinks[$_metaData['permalink']] = $_date.'/'.$postName;
        // }

		$params = request()->all();

        return [
            'date'           => date('Ymd', $dateTime),
            'dateTime'       => $dateTime,
            'name'           => $postName,
            'filename'       => $postFileName,
            'postLink'       => date('Y/m/d', $dateTime).'/'.$postName.(count($params) > 0 ? '?'.http_build_query($params) : ''),
            'meta'           => $_metaData,
            'title'          => $_metaData['title'],
            'content'        => $content,
            'contentRaw'     => $contentRaw,
            'contentSummary' => $contentSummary,
        ];
    }

    /**
     * Get the list of posts
     * @param bool $isAll						Whether ignore pagination
	 * @return array
     */
    public function getList(bool $isAll=false): array
    {
        // Get all posts
        $postList = [];
        $fileList = [];
        if (is_dir(base_path().'/posts/')) {
            $dir = opendir(base_path().'/posts/');
            while (($_file = readdir($dir)) !== false) {
                if ($_file === '.' || $_file === '..') {
                    continue;
                }

                if (substr($_file, 0, 1) !== '#') {
                    $fileList[] = $_file;
                }
            }

            // Sort by date desc
            rsort($fileList, SORT_NATURAL);

            // Pagination
			$page = (int) abs($_GET['page'] ?? 1);
			$totalCount = count($fileList);
			$itemPerPage = \Config::get('blog.item_per_page');
			$lastPageNum = (int) ceil($totalCount / $itemPerPage);
            if ($isAll) {
				$fileListSliced = $fileList;
				$paginate = new Paginator($fileListSliced, $totalCount, $page);
            } else {
                $fileListSliced = array_slice($fileList, ($page - 1) * $itemPerPage, $itemPerPage);
				$paginate = new Paginator($fileListSliced, $itemPerPage, $page);
			}
			if ($isAll === false && $page < $lastPageNum) {
				$paginate->hasMorePagesWhen(true);
			}

            foreach ($paginate->items() as $_file) {
                // Get post data
                $_uid = pathinfo($_file, PATHINFO_FILENAME);
                $postData = $this->getPostData($_file);
                if ($postData === false) {
                    continue;
                }
                $postList[$_uid] = $postData;

                if (isset($postList[$_uid]['meta']['permalink'])) {
                    $this->permalinks[$postList[$_uid]['meta']['permalink']] = $postList[$_uid]['date'].'/'.$postList[$_uid]['name'];
                }

                // Tags
                foreach ($postList[$_uid]['meta']['tags'] ?? [] as $_tag) {
                    $this->tags[$_tag][] = $_uid;
                }
            }

            // Sort by tag's name asc
            ksort($this->tags, SORT_STRING);
        } else {
            $postList = [];
        }

        return [
            'postList'    => $postList,
            'paginate'    => $paginate,
            'lastPageNum' => $lastPageNum,
        ];
    }

	/**
	 * List posts
	 */
	public function list()
	{
		$data = $this->getList();
		return view('list', $data);
	}

    /**
     * Parse a post's meta data
     * @param string $source                    Raw source of a post
     * @return array                            Meta data
     */
    private function parseMetaData(string $source): array
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

    /**
     * Read a post
     * @param string $y                         Year               (eg) 2022
     * @param string $m                         Month              (eg) 01
     * @param string $d                         Date               (eg) 23
     * @param string $postName                  Unique page name   (eg) "page-unique-name"
     */
    public function read(string $y, string $m, string $d, string $postName)
    {
        $postData = $this->getPostData($y.$m.$d.'-'.$postName.'.md');
        if ($postData === false) {
            abort(404);
        }

		$list = $this->getList();

        return view('postread', [
            'postData' => $postData,
			'list'     => $list,
        ]);
    }
}
