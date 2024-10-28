=== Plugin Name ===
Contributors: AntonShevchuk
Donate link: http://donate.hohli.com/
Tags: rss, feed
Requires at least: 3.0.0
Tested up to: 3.0.0
Stable tag: 0.0.2

This is a plugin that allows you to additionally export RSS with the full text of the articles. Your reader can now select what RSS he wants to read himself.

== Description ==
Additional - you can use <!--more--> second time (for cut full article in full RSS).
Options available on Settings/Reading page


== Installation ==

1. Upload `a-rss-more` directory  to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If you theme is support 'automatic-feed-links' - all done
4. Another - add link to new RSS with param `?announce=1`:

    `<link rel="alternate" type="application/rss+xml" title="Blog » Announce Feed" href="http://domain.com/feed/?announce=1" />`
    or

    `<link rel="alternate" type="application/rss+xml" title="Blog » Announce Feed" href="http://domain.com/?feed=rss2&announce=1" />`

== Upgrade Notice ==

Reactivate plugin.

== Frequently Asked Questions ==

= Are you sure? It's best practice? =
Yes, see on votes results http://habrahabr.ru/blogs/blogosphere/105394/ (it's russian hi-tech community blog):

    Question: Full RSS or announce only
    1: Always full
    2: When article don't consist some source code
    3: Announce only

== Screenshots ==

1. Settings on Admin page
2. "Announce Feed" in browser

== Changelog ==

= 0.0.2 =
* Added options of plugin

= 0.0.1 =
* Initial version