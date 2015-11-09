# Encode
#### INTRODUCTION TO ENCODE
Encode is *quite easy* to use, but we'd love to tell you about some stuff before you start coding.
If you are an experienced coder, you'll be through this in a second.
If not, sit down, grab a beer/coffee and read quickly through this part of the docs.

Just..one..rule: Every Encode-file is prefixed with an underscore
```php
'_Require.php', '_Model.php', ...
```
If you don't want things to get dirty, keep your hand of them.

#### THE INSTALLATION
Installing Encode should be, as promised, a piece of cake. 
First of all, clone this repository, and copy it to a very safe location.
After that, you can just unzip it, and upload it to your server, either local or cloudy.
Now redirect your browser to `path/to/Encode`, take a good look at the settings-page, fill everything in correctly, and press submit!

Great, everything should be ready to use, click the link called 'landing page' to see your website in action!
To get coding, there is not much more than 1 rule: **use the application-folder for everything**. 
Leave the system-folder alone, he doesn't like to get bothered.

Great! You did it!
Now for the settings you just entered, they can easily be adjusted in the application/config folder, anytime!
If you ignored the [Mandrill](www.mandrilapp.com "Mandrill") API key, no worries, you can still enter him in those files later on.

#### THE MAGIC THIS
Every world has his own definition of magic.
Within Encode, that piece of magic is called `$this`.
With `$this`, you can call out to pretty much everything out there, in the Encode-universe. Using it is childsplay, just figure out the controller (Encode's or your own) you want to reach out to, and call him with this short line:

```php
$this->CONTROLLER_NAME->FUNCTION_NAME(VARIABLES);
```
Ta.Daa. That's pretty much it. For example, ask Encode to load the view called 'myView':
```php
$this->load->view('myView');
```
Yep. You got it all figured out now.

#### MVC-PATTERN
Encode is an MVC-framework, a Model-View-Controller pattern ermerges within.
If you have no idea what an MVC is, allow us to explain *(very briefly)*.
When a user accesses your webpage, the following steps occur:

1. The Controller is called, based on the url:
`www.yourdomain.com/Controller`
   * This controller is placed within `application/Controllers`. Use the correct namespace "Controller" and extend your controller from `\Encode\Controller` to make full use of Encode. Also, name your file after your controller (without the namespace) for lazyloading.
2. In the Controller, the correct method is located:
`www.yourdomain.com/Controller/Method`
   * If the method requires parameters, they are located after the methods name, between '/':
`www.yourdomain.com/Controller/Method/parameter_one/parameter_two/.../parameter_thousand`
   * In this method, some Model may be called, to deal with parameters, grab some variables and make a connection with a database if needed.
   * When all the data is collected, the controller will load a view, which contains plain HTML (and some basic logic-statements, like if or switch cases). The view-call can be loaded with some variables as well, so you can access them in the view itself, and output them wherever you want.

#### MODULES USAGE
Since version `2.3`, it is possible to write custom, reusable modules to expand Encode.
The structure of these modules are very much like the basic application-folder. They both have assets, controllers, models, views, config files, and others.
If you are going to write or edit modules, best have a look at these guidelines:

1. Use the structure from the application-folder
   * `.php`-files in controllers and config-folders are loaded automatically
   * `.php`-files in models and helpers-folders are loaded as normal, and automatically found inside your module-folder
2. If you need to install tables, use an `install.php` script in the root of your module
3. Please document everything good enough so any user can use your module properly.

#### Improvements
We would very much like to hear your thoughts about the system, so if you made some improvements on the framework, just send a PR! (If you found a bug, leave an issue ;) )

#### Done
So, that's about it. If you have any trouble/problems/questions with the controller-usage, visit the [Manual](http://www.ebro.me/Encode/Manual) or the [FAQ](http://www.ebro.me/Encode/FAQ).
Have fun!

