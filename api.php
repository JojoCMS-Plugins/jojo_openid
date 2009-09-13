<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

Jojo::addHook('register_before_form', 'register_before_form', 'jojo_openid');
Jojo::addHook('login_before_form', 'login_before_form', 'jojo_openid');
Jojo::addHook('register_top', 'register_top', 'jojo_openid');
Jojo::addHook('register_complete', 'register_complete', 'jojo_openid');

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_openid' => 'OpenID - Handler pages'
        );

Jojo::registerURI("openid/[action:string]", 'Jojo_Plugin_Jojo_openid'); // "openid/login/"