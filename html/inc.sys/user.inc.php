<?php

function getUserRight($sectionName)
{
    $res = smart_mysql_query($sql = "
		SELECT st.value as type 

		from sysUserSection s 
		LEFT JOIN sysUser u on u.id = s.sysUserid 
		LEFT JOIN sysSection t on s.sysSectionId = t.id 
		LEFT JOIN sysUserSectionType st on st.id = s.sysUserSectionTypeid 

		WHERE u.id = '" . $_COOKIE['cookieUsername'] . "' and t.id='$sectionName'");

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return $row['type'];
    } else {
        return 0;
    }
}

function sysCanUserWrite($sectionName)
{
    return getUserRight($sectionName) == 2;
}

function sysCanUserRead($sectionName)
{
    return getUserRight($sectionName) > 0;
}

function doLogin($user, $pass, $forward = true)
{
    $message = 'Sign in to start your session';
    if (strlen($user) > 0 && strlen($pass) > 0) {
        $minutes = 30;
        $allowedLogins = getAllowedLogins($user, $minutes);
        if ($allowedLogins <= 0) {
            sysLogAccessGo($user);
            $message = 'Your account is locked out for ' . $minutes . ' minutes';
        } else {
            $result = smart_mysql_query($sql = "SELECT * FROM sysUser where id='" . $user . "' and UPPER(password)='" . strtoupper(openssl_digest($pass, 'sha512')) . "'");

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                setcookie('cookieUsername', $user, time() + 60 * 60 * 24 * 10, '/');
                sysUpdateOnlineUser();
                if ($forward) {
                    header('Location: ./?a=sysHome');
                    die();
                }

            } else {
                sysLogAccessGo($user);
                $message = 'Try it again please. (' . $allowedLogins . ' more times)';
            }
        }
    }

    return $message;
}

function getAllowedLogins($user, $minutes)
{
    $result = smart_mysql_query($sql = "SELECT count(*) as count FROM sysLogAccess where user='" . $user . "' and timestamp > '" . date('Y-m-d H:i:s', (time() - $minutes * 60)) . "'");
    if ($result->num_rows > 0) {
        $count = $result->fetch_assoc();
    }
    return 5 - $count['count'];
}

function logout()
{
    setcookie('cookieUsername', '', time() - 3600);
    $_COOKIE['cookieUsername'] = '';

    //logging out modules
    foreach ($_SESSION as $key => $item) {
        if (substr($key, 0, 7) == 'module-') {
            unset($_SESSION[$key]);
        }
    }
    exit;
    header('Location: ?a=pageLogin');
}

function checkUserLoggedIn()
{

    //login in case user sending username and password right away
    /*if (
        (strlen($_REQUEST['user']) > 0 && strlen($_REQUEST['pass']) > 0)
        &&
        (!(strlen($_COOKIE['cookieUsername']) > 0))
    ) {
        doLogin(escapeQuotes($_REQUEST['user']), escapeQuotes($_REQUEST['pass']), false);
        //sleep(3);
    }*/

    $result = smart_mysql_query("SELECT * FROM sysUser");
    while ($row = $result->fetch_assoc()) {
        if (array_key_exists('cookieUsername', $_COOKIE) && ($row['id'] == $_COOKIE['cookieUsername'])) {
            return true;
        }
    }
    sysLogAccessGo('incorrectUser: ' . (array_key_exists('cookieUsername', $_COOKIE) ? $_COOKIE['cookieUsername'] : ' no user'));
    pageLogin();
    return false;
}

function sysUserGet($userId)
{
    $result = smart_mysql_query("SELECT * FROM sysUser where id='" . $userId . "'");
    return ($result->fetch_assoc());
}

function sysUpdateOnlineUser()
{
    if (isset($_COOKIE['cookieUsername'])) {
        $username = substr($_COOKIE['cookieUsername'], 0, 32);
        $username = str_ireplace("'", "", $username);
        $username = str_ireplace('"', "", $username);
        smart_mysql_query("UPDATE `sysUser` set timestamp = '" . date('Y-m-d H:i:s') . "' where id = '$username'");
    }
}

function sysCountOnlineUsers()
{
    $count = sqlGetRow("SELECT count(*) as count FROM sysUser where timestamp > '" . date('Y-m-d H:i:s', (time() - 300)) . "'");
    return $count['count'];
}

function sysUserGetSetting($nr)
{
    $res = smart_mysql_query($sql = "SELECT `setting-" . $nr . "` as setting FROM sysUser u WHERE u.id = '" . $_COOKIE['cookieUsername'] . "'");
    $row = $res->fetch_assoc();
    return $row['setting'];
}

function sysUserSetSetting()
{
    $nr = escapeQuotes($_REQUEST['nr']);
    $value = escapeQuotes($_REQUEST['value']);

    smart_mysql_query($sql = "UPDATE sysUser SET `setting-" . $nr . "` = '" . $value . "' WHERE id = '" . $_COOKIE['cookieUsername'] . "'");
    echo $sql;
}
