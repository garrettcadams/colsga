=== Upload Larger Plugins ===
Contributors: DavidAnderson
Tags: upload larger plugins, upload file limit, plupload, plugins installation
Requires at least: 3.3
Tested up to: 5.3
Stable tag: 1.4.1
Donate link: https://david.dw-perspective.org.uk/donate
License: MIT

Install plugins of any size (i.e. work around web hosting limits)

== Description ==

This plugin replaces the built-in WordPress plugin uploader with one that allows you to upload a plugin of any size.

This is something you might need if your web hosting has a low maximum file upload limit (some default PHP installs have 2MB), and the plugin you wish to install is too large for this.

It accomplishes this task by replacing WordPress's default plugin uploader with a more sophisticated component that can upload larger files in chunks. You just install this plugin, activate it, and that's it - you can now get back to the real work.

Want to see some more quality plugins and products?

* <a title="WordPress backup, clone and restoration" href="https://updraftplus.com/">UpdraftPlus - best WordPress backup, clone and restore plugin</a>
* <a title="WooCommerce extensions for WordPress" href="https://www.simbahosting.co.uk/s3/shop/">Some other premium WordPress plugins (mostly WooCommerce extensions)</a>
* <a href="https://profiles.wordpress.org/davidanderson#content-plugins">Other free plugins on my WordPress profile page</a>
* <a href="https://www.simbahosting.co.uk/s3/shop/">WooCommerce extensions</a>

== Screenshots ==

1. New plugin uploading widget - accepting plugin zips of any size

== Installation ==

Standard WordPress plugin installation:

1. Search for "Upload Larger Plugins" in the WordPress plugin installer
2. Click 'Install'

== Frequently Asked Questions ==

= Where are the configuration settings? =

There are none. If the plugin is active, then it will replace the upload widget on the Plugins -> Upload configuration page in your WordPress dashboard. That is all.

= Does this plugin also enable the upload of larger themes? =

No. It would not be a huge job to extend it to do so; but I have no reasons for doing that presently.

== Changelog ==

= 1.4.1 - 17-Sep-2016 =
- Compatibility: Make the plugin work again after WP 4.6.1 introduced an incompatibility

= 1.4 - 01-Aug-2016 =
- Compatibility: Compatible with the forthcoming WP 4.6

= 1.3 - 28-Jul-2016 =
- Compatibility: Preparations for compatibility with the forthcoming WP 4.6

= 1.2 - 11-Feb-2015 =
- Fixed: Now works on WP 3.6 (and probably earlier)
- Tweak: Add some extra permissions checks (believed to be redundant - no security issue known)

= 1.1 - 30-May-2014 =
- Only load this plugin's JavaScript on the relevant page

= 1.0 - 21-May-2014 =
- First version

== License ==

Copyright 2014- David Anderson

MIT License:

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

== Upgrade Notice ==
* Make the plugin work again after WP 4.6.1 introduced an incompatibility
