=== Captcha by Yandex for Contact Form 7 ===
Contributors: stasionok
Tags: contact form 7, yandex, captcha, яндекс капча, yandex captcha
Tested up to: 6.4.3
Stable tag: 1.0.2
License: GPLv3

Add antispam Yandex captcha for your forms with Contact Form 7

== Description ==

Yandex Captcha protects you against spam and other types of automated abuse. With Contact Form 7’s Yandex Captcha integration module, you can block abusive form submissions by spam bots.

= Using of a 3rd Party or external service =

This plugin uses [Yandex SmartCaptcha](https://yandex.cloud/ru/services/smartcaptcha) service for its main functionality. Please read [terms of use](https://yandex.ru/legal/cloud_terms_smartcaptcha/)

To implement the main plugin functionality, this plugin makes remote requests to yandex smart captcha service (https://smartcaptcha.yandexcloud.net) within three cases:
 - to check yandex captcha sitekey validity
 - to load captcha challenge
 - to check a solving result

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/captcha-by-yandex-for-contact-form-7` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Contact form 7 plugin integration page
4. Setup site key and server key from yandex cloud
5. Open the contact form and press Yandex captcha tag button to add captcha in your form

== Frequently Asked Questions ==

= Where I can get my site key and server key =

Open [console.cloud.yandex.ru](https://console.cloud.yandex.ru/) login, add Yandex SmartCaptcha service and generate your keys

= How to hide captcha from customers =

When you add yandex captcha into your form, please check `Use invisible captcha`

== Screenshots ==

1. There is a configuration page placed
2. What configuration block looks like
3. How to place captcha into contact form
4. Captcha settings
5. Captcha frontend view
6. When the system suspects you
7. Just captcha block
8. Just captcha block in russian


== Changelog ==

= 1.0.0 =
* Basic functionality released.

= 1.0.1 =
Reduce php version to 7.4
Fix verify captcha on some cases

= 1.0.2 =
Fix wp codex issue
Allows using few forms in one page

= 1.0.3 =
* Update code due deploy review
