Nagios WordPress Updates Checker
===============

## TL;DR
This is a Wordpress Plugin that does the same thing as https://github.com/fredbradley/Nagios-WordPress-Update/. The difference being it's installable as a Wordpress Plugin.

## Requirements
 - Wordpress
 - Nagios Server (with permissions to edit checks and restart the service)
 - About 15mins
## Set up
### 1. Set up on Your Nagios Server
 - Take a copy of `check_wp_update` from the root of this repo.
 - Copy it to your Nagios plugins folder (on your Nagios Server). For me, it's on `/usr/lib64/nagios/plugins`
 - Add a new Command:
 
__Command Template__

	define command{
	        command_name    check_wp_update
	        command_line    $USER1$/check_wp_update $ARG1$
	        }

 - Then add a new Server Check
 
 __Service Check__

	define service{
	        use                     generic-service
	        host_name               example.com
	        service_description     My WordPress Install
	        check_command           check_wp_update!http://example.com/nagios/check
	        }
 
 **NB: Change `http://example.com` to whatever your site's URL is!**

### 2. Now, Set up on your Wordpress Site
- Take a zip download of the [latest release](https://github.com/fredbradley/nagios-wordpress-updates-checker/releases/tag/1.7.0)
- Upload that to your plugins directory (usually: `/wp-content/plugins`)

#### Set the Settings
Find the settings page "Nagios Checker" under the main "Settings" menu in Wordpress.

There are two settings to set: 
##### 1. Nagios Server IP Address
In here, put the IP address (IPv4) of your Nagios Server (from where the checks will be coming from). This measure makes sure that only checks from your Nagios server are allowed and all other attempts are failed.

##### 2. Ignored Plugins
I have found that sometimes you might not be able to update a particularly plugin (perhaps you don't have the license for updates anymore). For example you might have bought a theme from ThemeForest which comes with Visual Composer bundled in. But you can't update Visual Composer yourself. For these plugins, just check them on this checklist and they will be ignored from the Nagios Check. 

**NB: They will still show up on the Wordpress Updates page when you log in, but will no longer affect your Nagios Checks**

## Credit Nods
Inspired by check\_wp\_version by @hteske. Original [here](http://exchange.nagios.org/directory/Plugins/CMS-and-Blog-Software/Wordpress/check_wp_version/details)
