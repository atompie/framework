AtomPie
========

AtomPie is a component based, controller-less framework. What it means?
We are trying to establish new paradigm for web application architecture and 
get rid or MVC as we know it. 

Use composer to install sample project
======================================

To install sample project type:

> php composer.phar create-project --stability=dev --prefer-dist atompie/project-scaffold

Framework for framework agnostic programmers
============================================

AtomPie is a framework for framework agnostic programmers. If you do not 
want to be enslaved by any framework use AtomPie. It will not bind you code
with itself. You will be able to move quickly to any other framework if 
you wish to do so.  

Idea behind the framework
=========================

I have been programming in PHP over 15 years and I was lacking framework that
would be very simplistic and allow me to separate application code from 
the framework. If that was possible I would like to use framework only for the
purpose of exposing the application to the web without it being coupled with the
framework internal functions/helpers. 

One day I wrote down how I see micro-framework responsibilities. 
I came up with the following list.

Micro-framework should:

 * be separated from application core
 * expose application methods to the web.
 * allow communication in different forms such as: JSON, XML, CLI params, Web request params 
   without major change in code.
 * inject dependent objects as parameters.
 * handle different configuration files.

Any other functionality should be handled by framework-agnostic composer 
packages.

# Manual index

* [Application separation](ApplicationSeparation)
* [Bootstrapping framework](Bootstrapping)
* [EndPoints](EndPoints)
* [EndPoint annotations](EndPoint-annotations)
* [Dependency injection](Dependency-injection)
* [Parameter validation](ParameterValidation)

