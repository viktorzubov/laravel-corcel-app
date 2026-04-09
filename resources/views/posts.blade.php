<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Posts</title>
</head>
<body>
    <h1>Posts</h1>

    @forelse ($posts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <p>{{ $post->excerpt }}</p>
        </article>
    @empty
        <p>No published posts found.</p>
    @endforelse
</body>
</html>
