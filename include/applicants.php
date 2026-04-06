<?php
require_once(LIB_PATH . DS . 'database.php');

class Applicants {
    protected static $tblname = "tbl_applicants";

    function dbfields() {
        global $mydb;
        return $mydb->getFieldsOnOneTable(self::$tblname);
    }

    function single_applicant($id = "") {
        global $mydb;
        $mydb->setQuery("SELECT * FROM " . self::$tblname . " 
            WHERE APPLICANTID = '{$id}' LIMIT 1");
        $cur = $mydb->loadSingleResult();
        return $cur;
    }

    function get_all_applicants() {
        global $mydb;
        $mydb->setQuery("SELECT * FROM " . self::$tblname . " ORDER BY DATECREATED DESC");
        $mydb->executeQuery();
        return $mydb->loadResultList();
    }

    function update_status($id = "", $status = "") {
        global $mydb;
        $mydb->setQuery("UPDATE " . self::$tblname . " 
            SET STATUS = '{$status}', LAST_UPDATED = NOW() 
            WHERE APPLICANTID = '{$id}'");
        $mydb->executeQuery();
    }
}
?>