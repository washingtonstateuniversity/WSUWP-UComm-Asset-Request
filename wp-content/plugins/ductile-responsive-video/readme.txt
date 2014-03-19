=== Ductile Responsive Video ===
Contributors: numeeja
Donate link: http://cubecolour.co.uk/wp
Tags: responsive, video, embed, elastic, ductile, simple, youtube, 
Requires at least: 3.7
Tested up to: 3.8
Stable tag: 1.0.0
License: GPLv2

A very simple lightweight plugin which makes YouTube and other default WordPress video embeds responsive

== Description ==

* Filters default WordPress video embeds to make them responsive

= Usage: =

Just add the url of the YouTube video you want to embed on its own line as you normally would for default WordPress video embed.

Then you can play with your browser width and if you have a responsive theme you should be able to watch your video resize to fit the width and retain the correct aspect ratio.
		
== Installation ==

1. Upload the plugin folder to your '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Where is the plugin's admin page? =

There isn't one. This is a lightweight plugin with no options, so there is no need for an admin page.

= How can I make it even more lightweight =

You can copy the css rules from the plugin's stylesheet into your child theme's stylesheet and prevent the built-in styles from loading by adding the following line to your child theme's functions.php or a custom functionality plugin:

`<?php remove_action('wp_print_styles', 'cc_ductile_embed_css', 30); ?>`

= Does the WordPress universe really need another responsive video embeds plugin? =
There are already several plugins which achieve a similar result as this one. Some provide extra functionality like shortcodes, and some are fairly slimline but use client-side javascript to modify the default embed rather than a server-side function. I like plugins to be as lightweight as possible, so I think there's room for this one.

= I am using the plugin and love it, how can I show my appreciation? =

You can donate via [my donation page](http://cubecolour.co.uk/wp/ "cubecolour donation page")

If you find the plugin useful I would also appreciate a review on the [plugin review page](http://wordpress.org/support/view/plugin-reviews/ductile-responsive-video/ "Ductile Responsive Video plugin reviews")

If it isn't working for you, please read the documentation carefully. If this does not address your issue, please post a question on the [plugin support forum](http://wordpress.org/support/plugin/ductile-responsive-video "Ductile Responsive Video plugin support forum") so we can at least have an opportunity to try to get it working before you leave a review.

== Screenshots ==

1. The video fits the width of the browser and retains its aspect ratio.

1. After resizing the browser window, or viewing on a mobile device, the video still fits the width of the browser and retains its aspect ratio.

== Changelog ==

= 1.0.0 =

* Initial Version

== Upgrade Notice ==

= 1.0.0 =

* Initial Version