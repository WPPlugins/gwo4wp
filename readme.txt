===GWO4WP===
Contributors: andreasnrb
Tags: testing, gwo, website optimizer,google
Requires at least: 2.7
Stable tag: 11.2.2

This plugin integrates Google Website Optimizer with Wordpress.

== Description ==
This plugin integrates Google Website Optimizer with Wordpress.
You don't have to change anything in your Wordpress files or templates to get this plugin to work. 

You'll get a new section on the "Add new" page which is called GWO.
There you just check the boxes that corresponds to your testcase.

The plugin doesn't create a test for you. You need to do that yourself on www.google.com/websiteoptimizer 

**Requirements**

You must have a Google Website Optimizer account.
You must have PHP5


== Installation ==

1. Unzip the file 'gwo4wp.xxx.zip'
2. Upload the folder `gwo4wp` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Does this work with A/B experiments? =

Yes

= Does this work with Multivariate experiments? =

Yes

= If I don't control the conversionpage/goal what do I do? =
You can then make a link click count as a conversion. It is the green upside down arrow (funnel) in the texteditor menu.
Just make sure you check "Page uses link click as conversion(goal)". Otherwise no conversion will occur.

= Are you 100% sure everything works? =
No. It works for me but can't make such claims. So it is good if you let me know if it doesnt work for you so I can take a look at it and update the plugin if need be =).

== Changelog ==
** 11.2.2 **
Removed an unused hook
** 11.2.1 **
Updated to conform with new Google tracking scripts.
**10.12.2**
Fixed loading problem due to stupid file copy mixup.
**10.12.1**
Altered JavaScript due to the security problem reported by Google GWO (not plugin specific)
**9.09.1**

Altered the name on a file to reflect the plugin name change. Updated the readme.txt file. Changed some code.

`<?php code(); // goes in backticks ?>`
