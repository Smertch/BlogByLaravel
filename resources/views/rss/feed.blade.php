{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $siteName }}</title>
        <link>{{ $siteUrl }}/</link>
        <description>Latest posts from {{ $siteName }}</description>
        <language>en</language>
        <atom:link href="{{ $siteUrl }}/feed" rel="self" type="application/rss+xml"/>
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ $siteUrl }}/#post-{{ $post->id }}</link>
                <guid isPermaLink="false">{{ $siteUrl }}/posts/{{ $post->id }}</guid>
                <pubDate>{{ $post->updated_at?->toRssString() }}</pubDate>
                <author>{{ $post->user?->email }}</author>
                <description><![CDATA[{!! nl2br(e($post->content !== '' ? $post->content : $post->title)) !!}]]></description>
            </item>
        @endforeach
    </channel>
</rss>
