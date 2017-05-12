Nagios WordPress Updates Checker
===============

### TL;DR
This is a Wordpress Plugin that does the same thing as https://github.com/fredbradley/Nagios-WordPress-Update/. The difference being it's installable as a Plugin and has a setting where can set your Nagios IP Address

### The Original Readme
A Nagios plugin to check for WordPress version updates on a remote server without the use of NRPE.

### How to use:

- Install the Wordpress plugin as you would usually for any other Wordpress plugin.
- When installed and activated go to `Settings -> Nagios` and enter the IP Address of your Nagios Server
- Copy `check_wp_update` to your Nagios plugins folder (on your Nagios Server). For me, it's on `/usr/lib64/nagios/plugins`
- Create a service command template
- Create a service check on your host

__Command Template__

	define command{
	        command_name    check_wp_update
	        command_line    $USER1$/check_wp_update $ARG1$
	        }

__Service Check__

	define service{
	        use                     generic-service
	        host_name               example.com
	        service_description     My WordPress Install
	        check_command           check_wp_update!http://example.com/wp-content/plugins/nagios-wordpress-updates-checker/check-version.php
	        }

### Set the Settings
Find the settings page "Nagios Checker" under the main "Settings" menu in Wordpress.

There are two settings to set: 
#### Nagios Server IP Address
In here, put the IP address (IPv4) of your Nagios Server (from where the checks will be coming from). This measure makes sure that only checks from your Nagios server are allowed and all other attempts are failed.

#### Ignored Plugins
I have found that sometimes you might not be able to update a particularly plugin (perhaps you don't have the license for updates anymore). For example you might have bought a theme from ThemeForest which comes with Visual Composer bundled in. But you can't update Visual Composer yourself. For these plugins, just check them on this checklist and they will be ignored from the Nagios Check. 

**NB: They will still show up on the Wordpress Updates page when you log in, but will no longer affect your Nagios Checks**

### Credit Nods
Inspired by check\_wp\_version by @hteske. Original [here](http://exchange.nagios.org/directory/Plugins/CMS-and-Blog-Software/Wordpress/check_wp_version/details)
