#GenyBundle

##Goal of this bundle is to provide:

a user interface to create forms
a user interface to render and validate those forms

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
