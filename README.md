Nagios-WordPress-Updates-Checker
===============

### TL;DR
This is a Wordpress Plugin that does the same thing as https://github.com/fredbradley/Nagios-WordPress-Update/. The difference being it's installable as a Plugin and has a setting where can set your Nagios IP Address

### The Original Readme
A Nagios plugin to check for WordPress version updates on a remote server without the use of NRPE.

### How to use:

- Install the Wordpress plugin as you would usually for any other Wordpress plugin.
- When installed and activated go to `Settings -> Nagios` and enter the IP Address of your Nagios Server
- Copy check\_wp\_update to your Nagios plugins folder. For me, it's on /usr/lib64/nagios/plugins
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

Inspired by check\_wp\_version by @hteske. Original [here](http://exchange.nagios.org/directory/Plugins/CMS-and-Blog-Software/Wordpress/check_wp_version/details)
