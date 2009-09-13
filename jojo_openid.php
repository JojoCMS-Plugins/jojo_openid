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

class JOJO_Plugin_Jojo_openid extends JOJO_Plugin
{

    function getUserId($openid)
    {
        $openid = JOJO_Plugin_Jojo_openid::canonicalizeUrl($openid);
        $data = Jojo::selectQuery("SELECT `userid` FROM {user_openid} WHERE openid=?", $openid);
        return count($data) ? $data[0]['userid'] : false;
    }

    function getOpenIDsByUser($userid)
    {
        $data = Jojo::selectQuery("SELECT `openid` FROM {user_openid} WHERE userid=?", $userid);
        $openids = array();
        foreach ($data as $k => $v) {
            $openids[] = $v['openid'];
        }
        return $openids;
    }

    function attachOpenID($openid, $userid)
    {
        $openid = JOJO_Plugin_Jojo_openid::canonicalizeUrl($openid);
        return Jojo::insertQuery("INSERT INTO {user_openid} SET openid=?, userid=?", array($openid, $userid));
    }

    function detachOpenID($openid, $userid)
    {
        $openid = JOJO_Plugin_Jojo_openid::canonicalizeUrl($openid);
        Jojo::deleteQuery("DELETE FROM {user_openid} WHERE openid=? AND userid=?", array($openid, $userid));
        return true;
    }

    function detachOpenIDsByUser($userid)
    {
        Jojo::deleteQuery("DELETE FROM {user_openid} WHERE userid=?", array($userid));
        return true;
    }

    function register_before_form()
    {
        global $smarty;
        if (!empty($_SESSION['openid_url'])) $smarty->assign('openid_url', $_SESSION['openid_url']); //the authenticated OpenID URL
        return $smarty->fetch('jojo_openid_register_before_form.tpl');
    }

    function login_before_form()
    {
        global $smarty;
        return $smarty->fetch('jojo_openid_login_before_form.tpl');
    }

    function register_top()
    {
        global $smarty;
        return $smarty->fetch('jojo_openid_register_top.tpl');
    }

    function canonicalizeUrl($url)
    {
        //todo: lowercase the protocol and domain but not the uri eg "WWW.AOL.COM/myOpenID" should be stored as "http://www.aol.com/myOpenID"
        return Jojo::addHttp($url);
    }

    /* after registration is complete, if user has authenticated using OpenID then attach the OpenID to their user account */
    function register_complete()
    {
        global $_USERID;
        $openid_url = $_SESSION['openid_url'];
        if (!empty($openid_url) && !empty($_USERID)) {
            JOJO_Plugin_Jojo_openid::attachOpenID($openid_url, $_USERID);
            return true;
        }
        return false;
    }



    function _getContent()
    {
        global $smarty, $_USERID;
        $content = array();
        require_once(_PLUGINDIR . '/jojo_openid/external/simple_openid/class.openid.v2.php');
        $action = Util::getFormData('action', false); //login, complete, attach, list, clear, and delete
        $openid_url = Util::getFormData('openid_url', false);

        $required = Util::getFormData('openid_required_fields', false);
        $required = explode(',', $required);
        $optional = Util::getFormData('openid_optional_fields', false);
        $optional = explode(',', $optional);


        if ($openid_url) $openid_url = JOJO_Plugin_Jojo_openid::canonicalizeUrl($openid_url);
        $userid = JOJO_Plugin_Jojo_openid::getUserId($openid_url);

        /* clears session variables relating to OpenID */
        if ($action == 'clear') {
            if (isset($_SESSION['requested_openid_url'])) unset($_SESSION['requested_openid_url']);
            if (isset($_SESSION['openid_url'])) unset($_SESSION['openid_url']);
            Jojo::redirectBack();
        }

        if ($action == 'attach') {
            if (!empty($openid_url)) {

                $openid = new OpenIDService;
                $openid->SetIdentity($openid_url);
                $openid->SetApprovedURL(_SITEURL.'/openid/complete/'); // Script which handles a response from OpenID Server
                $openid->SetTrustRoot(_SITEURL);
                $serverurl = $openid->GetOpenIDServer(); // Returns false if server is not found
                if (!$serverurl) {
                    echo 'OpenID error';
                    exit();
                } else {
                    //Jojo::redirect($serverurl);
                    $_SESSION['requested_openid_url'] = $openid_url;
                    $_SESSION['openid_redirect'] = _SITEURL.'/openid/list/';
                    $openid->Redirect();
                }

            }
        }

        if ($action == 'login') {
            if (!empty($openid_url)) {

                $openid = new OpenIDService;
                $openid->SetIdentity($openid_url);
                $openid->SetApprovedURL(_SITEURL.'/openid/complete/'); // Script which handles a response from OpenID Server
                $openid->SetTrustRoot(_SITEURL);
                $openid->SetRequiredFields($required);
                //$openid->SetOptionalFields($optional);
                //$openid->SetRequiredFields(array('fullname','email'));
                //$openid->SetOptionalFields(array('country','language','timezone'));
                $serverurl = $openid->GetOpenIDServer(); // Returns false if server is not found
                if (!$serverurl) {
                    echo 'OpenID error';
                    exit();
                } else {
                    //Jojo::redirect($serverurl);
                    $_SESSION['requested_openid_url'] = $openid_url;
                    $_SESSION['openid_redirect'] = '';
                    $openid->Redirect();
                }

            }
        }

        if ($action == 'complete') {

            $openid = new OpenIDService;
            $openid->SetIdentity($_SESSION['requested_openid_url']);
            $authenticated = $openid->ValidateWithServer();
            if (!$authenticated) {
                echo 'There was an error authenticating your OpenID.';
                exit();
            }
            $openid_url = $_SESSION['requested_openid_url'];
            $userid = JOJO_Plugin_Jojo_openid::getUserId($openid_url);
            /* if OpenID is not attached to a user account, and user is logged in, attach now */
            if (empty($userid) && !empty($_USERID)) {
                JOJO_Plugin_Jojo_openid::attachOpenID($openid_url, $_USERID);
                $redirect = !empty($_SESSION['openid_redirect']) ? $_SESSION['openid_redirect'] : _SITEURL;
                unset($_SESSION['openid_redirect']);
                Jojo::redirect($redirect);
            }
            /* already attached, and user is not signed in to this account */
            elseif (!empty($userid) && empty($_USERID)) {
                $_SESSION['userid'] = $userid;
                $_USERID = $userid;
                $redirect = !empty($_SESSION['openid_redirect']) ? $_SESSION['openid_redirect'] : _SITEURL;
                unset($_SESSION['openid_redirect']);
                Jojo::redirect($redirect);
            }
            /* not signed in, not attached */
            else {
                $_SESSION['openid_url'] = $openid_url;
                Jojo::redirect(_SITEURL.'/register/');
            }
        }

        if ($action == 'list') {
            if (!empty($_USERID)) {
                $openids = JOJO_Plugin_Jojo_openid::GetOpenIDsByUser($_USERID);
                $smarty->assign('openids', $openids);
                $content['title'] = 'Your OpenIDs';
                $content['seotitle'] = 'Your OpenIDs';
            }
            $content['content'] = $smarty->fetch('jojo_openid_list.tpl');
        }

        if ($action == 'delete') {
            if (false) {
                //todo - if user does not have a regular password, check to make sure the user isn't deleting their last OpenID (ie their only way of signing into the site)
            } else {
                JOJO_Plugin_Jojo_openid::detachOpenID($openid_url, $_USERID);
                Jojo::redirect(_SITEURL.'/openid/list/');
            }
        }

        return $content;
    }

    function getCorrectUrl()
    {
        //Assume the URL is correct
        return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }


}