<?php
require_once(LIB_PATH . DS . 'database.php');

class Scholars {
    protected static $tblname = "tbl_scholarship_awards";

    function single_scholar($id = "") {
        global $mydb;
        $mydb->setQuery("SELECT * FROM " . self::$tblname . " 
            WHERE AWARD_ID = '{$id}' LIMIT 1");
        $cur = $mydb->loadSingleResult();
        return $cur;
    }

    function get_active_scholars() {
        global $mydb;
        $mydb->setQuery("SELECT * FROM " . self::$tblname . " 
            WHERE STATUS = 'Active' ORDER BY CREATED_AT DESC");
        $mydb->executeQuery();
        return $mydb->loadResultList();
    }
}
?>