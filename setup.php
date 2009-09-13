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

/* Article Admin Handler */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_openid'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_openid: Adding <b>OpenID handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='OpenID', pg_link='Jojo_Plugin_Jojo_openid', pg_url='openid', pg_parent=?, pg_mainnav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_followto='no', pg_footernav='no', pg_breadcrumbnav='yes'", $_NOT_ON_MENU_ID);
}