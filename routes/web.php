<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// List
Route::get('/', 'App\Http\Controllers\post@list');

// Read
Route::get('/{y}/{m}/{d}/{postName}', 'App\Http\Controllers\post@read')->where([
    'y'        => '([0-9]{4,4})',
    'm'        => '([0-9]{2,4})',
    'd'        => '([0-9]{2,4})',
    'postName' => '([a-zA-Z0-9\.\-_]+)',
])->name('toPost');

// About
Route::get('/about', function() {
    return view('about');
});

// Permalinks
$post = new App\Http\Controllers\post();
$postList = $post->getList(true);
foreach ($postList['postList'] as $_uname => $_postData) {
    if (!isset($_postData['meta']['permalink'])) {
        continue;
    }

    Route::get($_postData['meta']['permalink'], function() use ($_postData) {
        return redirect('/'.$_postData['postLink']);
    });
}

// Tags
Route::get('/tags', function() use ($post, $postList) {
    return view('tags', [
        'postList' => $postList['postList'],
        'tagData' => $post->tags,
    ]);
});
