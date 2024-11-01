=== VdoCipher: Secure Video Player and Hosting ===
Contributors: vibhavsinha, milangupta4
Tags: video, DRM, video plugin, e-learning
Requires at least: 3.5.1
Tested up to: 6.5.3
Stable tag: 1.29
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple wordpress plugin which enables you to embed VdoCipher videos inside a WordPress website with security from piracy.

== Description ==

VdoCipher provides video hosting, video playback, and piracy protection solutions for WordPress websites in the e-learning & media field to help them serve content most securely and smoothly. A combination of Hollywood standard DRMs and viewer-specific watermarking ensures that videos can't be downloaded or shared illegally from your platform. A combination of a custom smart player, analytics, and video management suite enables the best viewer and video management experience. We serve 3000+ businesses and 10,000+ content creators' platforms across 120+ countries.

== VdoCipher’s DRM & Watermark Security Features: ==

* Video DRM Encryption to prevent illegal video downloads
* Dynamic Watermarking based on user ID/email ID/IP address to discourage screen capture. In some cases, screen capture is also blocked on browsers, for the rest of the cases, watermark is a good discouragement.
* Easily customize watermark to change color, transparency, and speed of movement.
* Domain Restriction. Use a single VdoCipher account to integrate with multiple WordPress websites.
* Geo, Time, Domain, Authentication Restrictions

== VdoCipher’s Custom Smart Video Player & Video Hosting Suite ==

* Adaptive Streaming based on user internet speed. User can also switch between qualities as per their choice. Quality optimized to ensure playback on slow internet connections,
* Customizable Video Player: Change color, controls on/off, speed change options.
* Have different themes for different videos/courses.
* Add Chapters over the video.
* Add multilingual subtitles.
* Video Analytics
* Amazon AWS Server & CDN

== Compatible with Global Popular WordPress LMS & Themes ==
* Playback and security work well with all popular WordPress LMS like Learndash, LifterLMS, TutorLMS , Sensei, etc. The plugin works at the core backend of WordPress, thus compatible with all LMS.
* Works well with woocommerce and all membership plugins.

== Easy 15-minute integration ==

* Register for VdoCipher trial or paid account.
* Upload a video and wait for it to complete processing and get ready with encryption.
* Set up domain restriction from VdoCipher security settings.
* Install the VdoCipher WordPress plugin. Fill in the API key and other settings like watermark parameters.
* Use shortcodes to embed in any WordPress page/post/course.


== Additional Resources ==

* [Free Trial Signup](https://www.vdocipher.com)
* [All VdoCipher Features](https://www.vdocipher.com/page/features/)
* [DRM + Watermark video demo](https://www.vdocipher.com/blog/2014/12/add-text-to-videos-with-watermark/)
* [Full Upload + WordPress Embed Tutorial](https://www.vdocipher.com/blog/vdocipher-wordpress-plugin-embed-watermark-tutorial/)

= Installation =

1. Register for VdoCipher trial or paid account on vdocipher.com
2. Upload a video and wait for it to complete processing and get ready with encryption.
3. Set up domain restriction from VdoCipher "Security & Config" section in VdoCipher dashboard settings.
4. Install VdoCipher WordPress plugin. Fill in the API key and other settings like watermark parameters.
5. Click on the "embed" button below any video in the dashboard. Please choose the "Wordpress" section and follow the simple steps.
6. Use simple shortcode to embed in any WordPress page/post/course.
7. Inside a post or page you can write `[vdo id="id_of_video"]` to embed the video in a post or page.
8. To set width and height use, Example  `[vdo id="id_of_video" width="600" and height="400"]`
9. You can set custom video player themes from "Custom Player" section. It can be set as default theme from the settings page. It is also possible to modify shortcode to use specific theme for specific video embeds. `[vdo id="c1480d6f057b70578e7f9d33e" vdo_theme="uz6s6vivib"]`
10. Please contact support@vdocipher.com for more queries.

== Frequently Asked Questions ==
Please refer to the [FAQ page on VdoCipher](https://www.vdocipher.com/page/faq)

= Is there a free trial? =
On account creation, you shall be provided with 5GB of free trial bandwidth.

== Screenshots ==

1. The setting screen for the plugin
2. Attributes available for the shortcode
3. The video player
4. Customisation preview
5. Using the shortcode in the classic editor

== Changelog ==

= 1.29 =
* Added support for more attributes `controls`, `autoplay` and `cc_language`.
* Ability to override player design for each embed
* Fixed bug with applying player-id to v2 player.
* Improved documentation

= 1.28 =
* Added support for player v2 with customisation, light-weight and other features.
* Improved error messages
* Added option to arrange menu position
* Settings will be retained on deactivation. Only uninstall will remove it from DB

= 1.27 =
* Added speed change options
* Improved settings form
* Removed the legacy player themes and flash options from settings
    If you are on one of the old themes and flash settings, this will show you
    an option to update to new settings.

= 1.25 =
* Added gutenberg block support
* More themes
* auto upgrade of player version
* Fixed undefined notice message
* Fixed some more bugs
* Better handling of video aspect ratio
* Fairplay support in player
* detailed analytics support in wordpress

= 1.24 =
* Bug fixes

= 1.23 =
* Added player themes page

= 1.22 =
* Added vdo_theme attribute to vdo shortcode

= 1.21 =
* HTML5 watermark for custom version 1.6.4
* User can opt for Flash watermark globally
* User can add custom player version
* Height change to auto for player versions more than 1.5.0
* Tested for PHP version 5.6 and above

= 1.20 =
* default player version set to 1.5.0
* corrected bugs
* height auto available
* player tech over-ride enabled to play exclusively html5, flash, zen player

= 1.19 =
* add new player

= 1.17 =
* updated player theme

= 1.16 =
* more documentation
* updated player

= 1.15 =
* fixed bugs for older php versions

= 1.14 =
* add new player version 1.1.0

= 1.13 =
* New player with ability to choose player version
* Add custom themes from theplayer.io

= 1.8 =
Bug fixes

= 1.7 =
* set max height and width as default settings in 16:9 ratio
* use asynchronous code for rendering video player
* watermark date in wp timezone
* use wp transport apis instead of curl

= 1.6 =
* add filter hooks for annotation statement

= 1.3 =
* Compatible with PHP5.2

= 1.0 =
* Annotation can now be set from wordpress dashboard
* Better system for storing client key
* Clear options table of plugin related keys on deactivate
* Include options form to set default options for videos.

= 0.1 =
* A basic plugin which just makes it possible to embed vdocipher videos inside a wordpress plugin

== Upgrade Notice ==

= 1.29 =
* Added support for more player v2 attributes.
* Added better documentation.

= 1.28 =
* Added support to play version 2 of video player with more customisation

= 1.27 =
* Added the ability to set custom speed options on player

= 1.25 =
* Gutenberg block support
* New themes
* Detailed analytics support
* auto player version upgrade

= 1.24 =
* Bug fixes

= 1.23 =
* Added player themes page

= 1.22 =
* bug fixes and security update
* Added vdo_theme attribute to vdo shortcode

= 1.21 =
* HTML5 watermark for custom version 1.6.4
* User can opt for Flash watermark globally
* User can manually add player version
* Height change to auto for player versions more than 1.5.0
* Tested for PHP version 5.6 and above

= 1.20 =
* default player version set to 1.5.0
* corrected bugs
* height auto available
* player tech over-ride enabled to play exclusively html5, flash, zen player

= 1.17 =
* updated player theme

= 1.8 =
Bug fixes

= 1.7 =
* watermark date in wordpress timezone

= 1.6 =
* annotation pre and post process hooks to add content specific custom variables

= 1.5 =
* Multiple videos bug fix

= 1.3 =
* Compatible with PHP5.3

= 1.0 =
* This allows you to set annotation over video.
* No more editing files directly.
