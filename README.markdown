mcom-tinderbox-rest
===================

Overview
--------
mcom-tinderbox-rest is an addition to
[Marcuscom Tinderbox][] which
allows it to provide REST-like API for the services it
already provides.

For example, if you want to add a new entry to the ports building
queue, you might just perform specially crafted PUT request to an
URL like https://you.tindy/api/queue by either using something like
curl or a special client instead of pointing your browser to tinderbox
web ui and doing all the job by hand.

Installation
------------
Assume that you have [Marcuscom Tinderbox][] installed and configured
already (if not, please refer to [its documentation](http://tinderbox.marcuscom.com/README.html)
on how to do that).

When you're done with tinderbox configuration, you need only one additional
dependency:

	www/pecl-http

Once you're doing with installing need, all you need to do is to create an 'api'
subdirectory in the web root of your tinderbox installation, typically it
would be:

	mkdir /space/scripts/webui/api

and copy all the php files and .htaccess from the repo to this directory.

Web Server Configuration
------------------------
I've tested things with Apache 2.2 (www/apache22) and will use it as example.
Basically, only two things are importent:

 * URL Rewriting
 * SSL

URL rewriting is used to provide nice REST-like URLs like /api/build/1
instead of of build.php?id=1 for example. mcom-tinderbox-rest comes with
.htaccess which contains all the rewriting rules. So Apache should be
configured to include [mod_rewrite][] and [AllowOverride][] to enable .htaccess
parsing (other option would be to embed things into main configuration file).

And SSL so the scripts were served using HTTPS.

Client
------
You can grab client [here](https://github.com/novel/mcom-tinderbox-client).

Contacts
--------
 * Project page on github: https://github.com/novel/mcom-tinderbox-rest
 * Contact e-mail: [novel@FreeBSD.org](mailto:novel@FreeBSD.org), any feedback is welcome!

[Marcuscom Tinderbox]: http://tinderbox.marcuscom.com/
[mod_rewrite]: http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html
[AllowOverride]: http://httpd.apache.org/docs/2.2/mod/core.html#allowoverride
