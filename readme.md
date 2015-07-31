Single File Application
========================
A very simple skeleton for creating single file PHP applications out of multiple
PHP classes. Also contains a basic templating engine.

Debugging can be done using the uncompiled source code. When building the
project, the compiler can automatically build the single php file. The compiler
can also be run from the command line or even within a browser.

## Developing an application
The application can be debugged from the src directory. This directory contains
an index.php file which has an autoloader and Template class.
**You must not edit the index.php file!**

Your application always starts from the same (bootstrap) file. In this example
application we use the Script class for this. This can be any filename and any
class name you want, as long as it is located in the root directory.

## Guidelines & tricks
* Classes that you want to use must be placed in the *code* directory.
* Templates reside in the template directory
* The layout is called layout.php and is wrapped around the template.
* Do not use the PHP closing tag ?>
* Include your JS and CSS inside the layout.php

## Why use this?
You can create simple applications that run from one file, so no external files
are required. You could use this to create a filebrowser that can be run from a
single file, for example.
