API Reference
================

Authentification and General Info
---------------------------------
First thing to know is that service works over HTTPS only
and every request should contain special headers.

These headers are:

 * X-Tinderbox-API-Version -- version of the API we're expect
  service to provide. Currently supported value is *1.0*
 * X-Tinderbox-User -- name of the tinderbox use, account
  is the same as the one with tinderbox webui access
 * X-Tinderbox-Token -- authentication token generated
  for the user, should be calculated this way:
	md5(username + md5(userpasswd))
  where "+" means string concatenation operation

Example call:

	curl -H "X-Tinderbox-Api-Version: 1.0" \
		-H "X-Tinderbox-User: username" \
		-H "X-Tinderbox-Token: f7403c8161d3901030bbf678452a3eeb" \
		"https://192.168.86.35/api/build"

Where token can be generated using this command:

	md5 -qs "username`md5 -qs userpasswd`"

*Note:* current service implementation is still in alpha stage. We don't force
you setting content-type and accept headers. We always talking JSON.

Builds Listing
--------------

Request:

	GET https://tindy/api/build

Response:

	{
	    "builds": [
		{
		    "build": {
			"currentport": null, 
			"description": "8.x with FreeBSD ports tree.", 
			"id": "1", 
			"jail": {
			    "id": "1"
			}, 
			"name": "8.x-FreeBSD", 
			"portstree": {
			    "id": "1"
			}, 
			"remakecount": "0", 
			"status": "IDLE", 
			"updated": "2011-01-29 21:51:40"
		    }
		}
	    ], 
	    "summary": {
		"status": "ok"
	    }
	}

Build details
-------------

Request:

	GET https://tindy/api/build/$build_id

Response:

	{
	    "builds": [
		{
		    "build": {
			"currentport": null, 
			"description": "8.x with FreeBSD ports tree.", 
			"id": "1", 
			"jail": {
			    "id": "1"
			}, 
			"name": "8.x-FreeBSD", 
			"portstree": {
			    "id": "1"
			}, 
			"remakecount": "0", 
			"status": "IDLE", 
			"updated": "2011-01-29 21:51:40"
		    }
		}
	    ], 
	    "summary": {
		"status": "ok"
	    }
	}

Listing Queue Entries
---------------------

Request:

	GET https://tindy/api/queue

Response:

	{
	    "entries": [
		{
		    "entry": {
			"build": {
			    "id": "1"
			}, 
			"buildname": "8.x-FreeBSD", 
			"completed": "2011-01-29 15:25:36", 
			"enqueued": "2011-01-29 15:15:31", 
			"id": "5", 
			"portdirectory": "security/gnutls-devel", 
			"priority": "10", 
			"status": "SUCCESS", 
			"user": {
			    "id": "1"
			}, 
			"username": "novel"
		    }
		}, 
		{
		    "entry": {
			"build": {
			    "id": "1"
			}, 
			"buildname": "8.x-FreeBSD", 
			"completed": "2011-01-29 15:33:05", 
			"enqueued": "2011-01-29 15:16:06", 
			"id": "6", 
			"portdirectory": "security/gnutls", 
			"priority": "10", 
			"status": "SUCCESS", 
			"user": {
			    "id": "1"
			}, 
			"username": "novel"
		    }
		}, 
		{
		    "entry": {
			"build": {
			    "id": "1"
			}, 
			"buildname": "8.x-FreeBSD", 
			"completed": "2011-01-29 21:51:41", 
			"enqueued": "2011-01-29 15:43:13", 
			"id": "9", 
			"portdirectory": "editors/vim", 
			"priority": "10", 
			"status": "SUCCESS", 
			"user": {
			    "id": "1"
			}, 
			"username": "novel"
		    }
		}
	    ], 
	    "summary": {
		"status": "ok"
	    }
	}

Queue Entry Details
-------------------

Request:

	GET https://tindy/api/queue/$entry_id

Response:

	{
	    "entries": [
		{
		    "entry": {
			"build": {
			    "id": "1"
			}, 
			"buildname": null, 
			"completed": "2011-01-29 21:51:41", 
			"enqueued": "2011-01-29 15:43:13", 
			"id": "9", 
			"portdirectory": "editors/vim", 
			"priority": "10", 
			"status": "SUCCESS", 
			"user": {
			    "id": "1"
			}, 
			"username": null
		    }
		}
	    ], 
	    "summary": {
		"status": "ok"
	    }
	}

Add Queue Entry
---------------

Request:

	PUT https://tindy/api/queue

Request Body:

	{"entry": {"priority": 10, "portdirectory": "security/gnutls", "build": {"id": "1"}, "email_on_completion": false}}

Response:

	{
	    "summary": {
		"status": "ok"
	    }
	}

Listing Buildports
------------------

Request:

	GET https://tindy/api/buildport

Response:

	{
	    "buildports": [
		{
		    "buildport": {
			"build": {
			    "build": {
				"currentport": "gnutls-2.8.6_2", 
				"description": "8.x with FreeBSD ports tree.", 
				"id": "1", 
				"jail": {
				    "id": "1"
				}, 
				"name": "8.x-FreeBSD", 
				"portstree": {
				    "id": "1"
				}, 
				"remakecount": "1", 
				"status": "PORTBUILD", 
				"updated": "2011-01-30 14:01:12"
			    }
			}, 
			"buildport": {
			    "buildport": {
				"comment": "GNU Transport Layer Security library", 
				"directory": "security/gnutls", 
				"id": "1", 
				"last_built": "2011-01-29 15:32:44", 
				"last_built_version": null, 
				"last_fail_reason": null, 
				"last_failed_dependency": null, 
				"last_run_duration": "248", 
				"last_status": "SUCCESS", 
				"last_successful_built": "2011-01-29 15:32:44", 
				"maintainer": "novel@freebsd.org", 
				"name": "gnutls"
			    }
			}, 
			"port_current_version": "gnutls-2.8.6_2", 
			"target_port": "security/gnutls"
		    }
		}
	    ], 
	    "summary": {
		"status": "ok"
	    }
	}
