<?php
require_once(LIB_PATH . DS . 'database.php');

class User {
    protected static $tblname = "tblusers";

    function dbfields() {
        global $mydb;
        return $mydb->getFieldsOnOneTable(self::$tblname);
    }

    function find_user($id = "", $user_name = "") {
        global $mydb;
        $mydb->setQuery("SELECT * FROM " . self::$tblname . " 
            WHERE USERID = {$id} OR USERNAME = '{$user_name}'");
        $cur = $mydb->executeQuery();
        $row_count = $mydb->num_rows($cur);
        return $row_count;
    }

    function userAuthentication($USERNAME, $h_pass) {
        global $mydb;
        
        $mydb->setQuery("SELECT * FROM `tblusers` WHERE `USERNAME` = '" . $USERNAME . "' and `PASS` = '" . $h_pass . "'");
        $cur = $mydb->executeQuery();
        
        if (!$cur) {
            return false;
        }
        
        $row_count = $mydb->num_rows($cur);
        if ($row_count == 1) {
            $user_found = $mydb->loadSingleResult();
            $_SESSION['USERID'] = $user_found->USERID;
            $_SESSION['ADMIN_USERID'] = $user_found->USERID;
            $_SESSION['FULLNAME'] = $user_found->FULLNAME;
            $_SESSION['USERNAME'] = $user_found->USERNAME;
            $_SESSION['ADMIN_USERNAME'] = $user_found->USERNAME;
            $_SESSION['ROLE'] = $user_found->ROLE;
            $_SESSION['ADMIN_ROLE'] = $user_found->ROLE;
            $_SESSION['PICLOCATION'] = $user_found->PICLOCATION;
            return true;
        } else {
            return false;
        }
    }

    function single_user($id = "") {
        global $mydb;
        $mydb->setQuery("SELECT * FROM " . self::$tblname . " 
            Where USERID = '{$id}' LIMIT 1");
        $cur = $mydb->loadSingleResult();
        return $cur;
    }

    function update_last_login($id = "") {
        global $mydb;
        $mydb->setQuery("UPDATE " . self::$tblname . " 
            SET LAST_LOGIN = NOW() WHERE USERID = '{$id}'");
        $mydb->executeQuery();
    }

    // Update user
    function update_user($id = "", $fullname = "", $username = "", $role = "", $email = "") {
        global $mydb;
        $mydb->setQuery("UPDATE " . self::$tblname . " 
            SET FULLNAME = '{$fullname}', 
                USERNAME = '{$username}', 
                ROLE = '{$role}', 
                EMAIL = '{$email}' 
            WHERE USERID = '{$id}'");
        $mydb->executeQuery();
    }

    // Update password
    function update_password($id = "", $new_password = "") {
        global $mydb;
        $mydb->setQuery("UPDATE " . self::$tblname . " 
            SET PASS = '{$new_password}' 
            WHERE USERID = '{$id}'");
        $mydb->executeQuery();
    }

    // Get all users
    function get_all_users() {
        global $mydb;
        $mydb->setQuery("SELECT * FROM " . self::$tblname . " ORDER BY USERID ASC");
        $mydb->executeQuery();
        return $mydb->loadResultList();
    }
}
?>