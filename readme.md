# Programmes Plant

The Programmes Plant is part of the [XCRI-CAP project](http://www.kent.ac.uk/is/projects/xcri/) at the University of Kent, and is a replacement for the existing Programmes Factory.

It is written in [Laravel](http://laravel.com) and using [Twitter Bootstrap](http://twitter.github.com/bootstrap/).

## Build Status

[![Build Status - master](https://travis-ci.org/unikent/programmes-plant.png?branch=master)](https://travis-ci.org/unikent/programmes-plant) `master` 

[![Build Status - develop](https://travis-ci.org/unikent/programmes-plant.png?branch=develop)](https://travis-ci.org/unikent/programmes-plant) `develop`

![Programmes plant homepage screenshot](https://github.com/unikent/programmes-plant/blob/master/screenshot.png "Programmes plant homepage screenshot")

## Development Setup

The following are instructions on running the Programmes Plant on a local machine for development.

### Method 1

Install this in one line? Why of course! Should work on any sensible Unix like system with Git installed.

```shell
curl -s https://raw.github.com/unikent/programmes-plant/setup/setup.sh | sh
```

### Method 2

1. Clone this repository. Change into the directory.

2. Obtain Laravel as a submodule by running `git submodule init` then `git submodule update`, unless you have done a recursive clone. Note that this app uses the [University of Kent Laravel fork](https://github.com/unikent/laravel.git), because this contains important performance enhancements as yet not pulled into the Laravel core. This fork is generally kept up to date with the Laravel master branch, so including it as a submodule should give you the latest version of Laravel.

3. Install [Composer](http://getcomposer.org/) and run `composer.phar install --dev` to install dependencies.

4. You need to setup the application by editing all the sample files in `config/` and moving them to `config/local/` filling in as appropriate. To move the files in one command, run:
```shell
mkdir application/config/local && cp application/config/*.sample application/config/local && ls application/config/local/*.sample | while read file; do mv $file  `echo $file | sed s/.sample//`; done
```

5. You will need to create a MySQL database if you plan to use MySQL. Create this database and add the credentials to `application/config/local/database.php`. If you want to get this running using SQLite then change line 45 of `application/config/local/database.php` to be `sqlite` not `mysql`. Should just be good to go.

6. The application requires an authentication driver to be used. The details of this can be setup in `application/config/local/auth.php`. We use our own LDAP driver. If you decide to use our bundled LDAP driver, the server settings can go in `application/config/local/ldap.php`.

7. Run `php artisan migrate:install --env=local` to setup the migtations table. Then run `php artisan migrate --env=local` to run all the migrations to setup your database.

6. Point a browser at the `public/` folder.

## Testing

Unit tests are written in PHPUnit. To run the tests run `php artisan test`. The tests use an in memory SQLite database to make them significantly faster.

## Other Projects

Want to consume the data from a Programmes Plant API? Consider using our [PHP library for this](https://github.com/unikent/of-course).

Want to see what a front-end to this data might look like using this library? See [Of Course](https://github.com/unikent/of-course).

## Licensing

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/.
