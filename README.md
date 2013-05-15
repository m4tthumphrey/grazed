Just found out graze.com have somewhat of an API. Love data so these are some scripts that will just grab all product info so you can do what you like with them... charts, graphs etc...

Note this is just a proof of concept, coding style and standards should be ignored ;) A hosted and styled version will be available <s>soon</s> eventually.

To use with your own account simply clone or fork this repo, create a file in the `app/` directory called `auth.php` and add the following, obviously setting your own username (email address) and password:

```php
<?php
define('GRAZE_EMAIL', '<your username>');
define('GRAZE_PASSWORD', '<your password>');
```

I use [Composer](http://getcomposer.org) to manage the following dependencies:

* [Guzzle](http://github.com/guzzle/guzzle)

Run `composer install` in the project root to install them.

Then navigate to the `bin/` directory in your terminal and run the following:

```shell
php get_product_info.php
php get_boxes.php
```

Then whenever you get a new box, simply run the last command again. Run index.php in your web browser and you should see your stats. You should run the first command every now and again to make sure you have the latest product information. Play around with index.php to change what and how data is displayed.