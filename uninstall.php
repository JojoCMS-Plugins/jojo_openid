<?php

/*

The uninstall script is run when the plugin is uninstalled via the admin interface.

The uninstall script may need to do several things, including...

-removing any pages added by the plugin
-removing any database tables added by the plugin (caution with this)
-Removing any options added by the plugin

*/

/* remove the hello page from the menu */
//Jojo::deleteQuery("DELETE FROM `page` WHERE pg_link='Jojo_Plugin_Empty_Plugin'");

//Remove any fields the plugin may have added
//Jojo::removeField($table, $field);

//Remove any options the plugin  may have added
//Jojo::removeOption('your option name');