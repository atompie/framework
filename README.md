AtomPie
========

AtomPie is a component based, controller-less framework. What it means?
We are trying to establish new paradigm for web application architecture and 
get rid or MVC as we know it. 

Use composer to install sample project
======================================

To install sample project type:

> php composer.phar create-project --stability=dev --prefer-dist atompie/project

Framework for framework agnostic programmers
============================================

AtomPie is a framework for framework agnostic programmers. If you do not 
want to be enslaved by any framework use AtomPie. It will not bind you code
with itself. You will be able to move quickly to any other framework if 
you wish todo so.  

Idea behind the framework
=========================

I have been programming in PHP over 15 years and I was lacking framework that
would be very simplistic and allow me to separate application code from 
the framework. If that was possible I would like to use framework only for the
purpose of exposing the application to the web without it being coupled with the
framework internal functions/helpers. Any other, not framework specific packages, 
I would like to reference via composer.

I could not find framework that would meet the following requirements:
 
 * Separate the application from framework with no reference to it at all, if possible.
 * Be simple as plain PHP objects.
 * Use dependency injection as a part of the application not framework or/and 
   use factory methods as dependency injection builder.
 * Framework should not allow the use of global dependency injection container as it 
   becomes the center of the application. Type hints should be used instead as better
   way of handling dependency injection.
 * Dependency injection should be simple.
 * Framework should understand that it does not matter how the data is provided to 
   the application, whether it is JSON, url param, CLI param or XML it should always 
   be declared the same way. No need for other configuration for different interfaces.
 * Globals such as request, config, env variables should not be mutable.
 * Session should not be accessible inside the application, only some variables should 
   be declared as stateful and saved into session automatically. 
 * Data layer, view layer should not be part of framework it should be referenced as
   composer package. 
 
 Therefore I decided I write my type of framework.

AtomPie example of use
======================

Imagine that you write your code as Plain PHP Object. 

```
class Calculator {
   public function divide($number1, $number2) {
       return $number1 / $number2;
   }
}
```

Now you would like to expose your method to the web. To do so, just 
annotate the method with @EndPoint and you are ready to go.

```
class Calculator {
    /**
     * @EndPoint()
     */
    public function divide($number1, $number2) {
       return $number1 / $number2;
    }
}
```

Type http://your.server/Calculator.divide?numbre1=1&number2=2 and you'll
get 3 as a result.

How about making it an API EndPoint and post data as JSON. Try this. 

```
class Calculator {
    /**
     * @EndPoint(Method="POST", ContentType="application/json")
     */
    public function divide($number1, $number2) {
       return $number1 / $number2;
    }
}
```

POSTING to http://your.server/Calculator.divide

```
{
    "number1": 1,
    "number2": 2
}
```

will result as 0.5 in a json response (STATUS: 200 OK).

If you want to be sure that client (e.g. browser) accepts json content annotate 
method with @Client

```
class Calculator {
    /**
     * @Client(Accept="application/json")
     * @EndPoint(Method="POST", ContentType="application/json")
     */
    public function divide($number1, $number2) {
       return $number1 / $number2;
    }
}
```

In case you pass wrong parameters, e.g.

```
{
    "number1": 1,
    "number2": 0
}
```

Exception wil be thrown and yu get STATUS: INTERNAL SERVER ERROR (500) and json serialized 
exception will be sent. 

Dependency Injection
====================

If you method is dependent on some other infrastructure as database connection 
you will need a database repository provider, for example UserRepositoryProvider. 
See that it depends on some configuration. It will be injected automatically during
dependency configuration.

```
class UserRepositoryProvider
{
    public function provide(IAmApplicationConfig $oConfig)
    {
        return new UserRepository(
            $oConfig->mysqlHost,
            $oConfig->mysqlDatabase,
            $oConfig->mysqlUser,
            $oConfig->mysqlPassword);
    }
}
```

Now create dependency trait. It describes where the provider should be injected.
For example UserDependency defines that for method load if you add
parameter UserRepository it will run UserRepositoryProvider that provides
class UserRepository. Notice that $oConfig if injected by the framework
as it is a global variable that no provider in the application can deliver without
referencing the global dependency injection container. This way application is
still not touching the framework.

```
    trait UserDependency
    {
        public function __dependency()
        {

            $oProvider = new UserRepositoryProvider();

            return [
                'load' => [  // Method name
                    UserRepository::class => // Type hint
                        /**
                         * @singleton
                         */
                        function (IAmApplicationConfig $oConfig) use ($oProvider) {
                            return $oProvider->provide($oConfig); // Dependency provider
                        },
                ]
            ];
        }
    }
```

Finally apply trait to your application class.

```
class User {

    use UserDependency
    
    /**
     * @Client(ContentType="application/json")
     * @EndPoint(Method="POST", ContentType="application/json")
     */
    public function load(UserRepository $userRepo) {
        // TODO use $userRepo here
    }
}
```

