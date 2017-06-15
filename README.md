#GenyBundle Demo

##Goal of GenyBundle is to provide :

A user interface to create forms

A user interface to render and validate those forms

##Goal GenyBundle Demo is to show :


How to implement a form builder

How to implement buitl form, persit and retreive data (only on text input and without validation for now )

#WARNING: under development! POCs, etc.

##What is it?

There are tons of ideas where admin may need to draw a form, set a template (not necessarily a view) and then final user can give the context by filling the form. This way, user's context is mixed to admin's template and, according to a website's goal, do some stuff without any programming needs.

Some websites examples:

a highly-dynamic back end: admin define sql query/linux command templates and the context form: and users can run those query/commands after filling that form.
a code generator: user define templates and forms to complete the context, and then just need to fill it as much times as he want.
...

##Warning

From the Symfony documentation:

A bundle should not embed third-party libraries written in JavaScript, CSS or any other language.
As this bundle contains a complex UI, it was too challenging for me to do it without jQuery and Twitter Bootstrap.

They are not included in the bundle.

#Installation

## Step 1

Download this repo.

Put the folder in the "www" directory like "C:\wamp\www\" on a wamp.

Rename it if you wish

## Step 2

Access it throught command line.

    cd C:\wamp\www\GenyBundleDemo

Type the command : 

    php composer.phar install
    
or something like

    php ../composer.phar install

It depends of your installation...

Be patient.

Personnaly, I have an memry issue. So I have to run this command :

    php -d memory_limit=2G ../composer.phar install
    
More info here : https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors

Answer the questions...

You'll get this error : Fatal error: Class 'GenyBundle\GenyBundle' not found in C:\wamp\www\GenyBundleDemo\app\AppKernel.php on line 29... Don't mind it. We will correct this.

## Step 3

Download my actual GenyBundle : https://github.com/Gilles-Lengy/GenyBundle/archive/master.zip

Put it in "C:\wamp\www\GenyBundleDemo\src" under the name : "GenyBundle"

## Step 4

Create the base :

    php app/console doctrine:database:create
    
Create the tables

    php app/console doctrine:schema:update --force
    
## Step 5

Make assets accessible

     php app/console assetic:dump
     
     php app/console assets:install --symlink
     
## Step 6

Access it  !

For example : http://localhost/GenyBundleDemo/web/app_dev.php/

Enjoy it !!!! or not... 

Be aware only the input text can be persisted and updated !
