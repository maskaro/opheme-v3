## Super Important

If Twitter application keys ever need to be updated, remember to update both config files
/config/packages/thujohn/twitter/config.php and /config/auth.php

## Required Local System Configuration

Requires /etc/hosts (Windows hosts file equivalent) to have:

127.0.0.1 portalv3.opheme.com

127.0.0.1 portalv3.twadar.net

## Required Vagrant Changes

update mysql (This needs to be added to vagrant provisioner)
sudo apt-get -y install mysql-server-5.6
sudo apt-get install sendmail
pico /etc/php5/fpm/pool.d/www.conf
=> make sure that this line is the same: (found at the end of the file)
php_admin_value[sendmail_path] = /usr/sbin/sendmail -t -i -f noreply@opheme.com

## Run Migrations and seed

Create Mysql Schema "opheme30"
vagrant ssh
cd Code

bash src/oPheme/db_migration_seed_script.sh

OR Manually 
php artisan migrate --path=src/oPheme/Database/Migrations
php artisan db:seed --class=DevelopmentSeeder


Before rollback the below command may need to be run:
composer dumpautoload


Authorise link example:
<a href="https://backend.opheme.com/oauth/twitter/authorise?callback=example.com&user_id=ca0d6e42-6b84-4008-b592-f17c26bcd90d&authorise_code=ItNYE7kllDK7IjAGB2rzd9goUY2WbsO4XbKoxc7s" target="_blank"> Linky </a>

## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/downloads.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, and caching.

Laravel aims to make the development process a pleasing one for the developer without sacrificing application functionality. Happy developers make the best code. To this end, we've attempted to combine the very best of what we have seen in other web frameworks, including frameworks implemented in other languages, such as Ruby on Rails, ASP.NET MVC, and Sinatra.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the entire framework can be found on the [Laravel website](http://laravel.com/docs).

### Contributing To Laravel

**All issues and pull requests should be filed on the [laravel/framework](http://github.com/laravel/framework) repository.**

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
