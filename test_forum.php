<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ForumPost;

echo "=== Forum Posts in Database ===\n";
$posts = ForumPost::with('user')->get();
echo "Total posts: " . $posts->count() . "\n\n";

foreach ($posts as $post) {
    echo "ID: {$post->id}, Title: {$post->title}\n";
    echo "User: {$post->user?->name}\n";
    echo "---\n";
}

echo "\n=== Testing API Endpoint ===\n";
if ($posts->count() > 0) {
    $firstPost = $posts->first();
    echo "Trying GET /api/forum/posts/{$firstPost->id}\n";
}
