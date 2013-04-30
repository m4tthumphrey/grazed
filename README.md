Just found out graze.com have somewhat of an API. Love data so these are some scripts that will just grab all product info so you can do what you like with them... charts, graphs etc...

Note this is just a proof of concept, coding style and standards should be ignored ;) A hosted and styled version will be available <s>soon</s> eventually.

To use with your own account simply clone or fork this repo, create a file in the root called `config.php` and add the following, obviously setting your own username (email address) and password:

    <?php
    define('GRAZE_EMAIL', '<your username>');
    define('GRAZE_PASSWORD', '<your password>');

Then navigate to the `/bin` directory in your terminal and run the following:

    php get_product_ids.php
    php get_product_info.php
    php get_boxes.php

Then whenever you get a new box, simply run the last command again. Run index.php in your browser and you should see your stats!