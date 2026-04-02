
<?php
require_once ("../../include/initialize.php");
 	 if (!isset($_SESSION['ADMIN_USERID'])){
      redirect(web_root."admin/index.php");
     }


$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
	doInsert();
	break;
	
	case 'edit' :
	doEdit();
	break;
	
	case 'disable' :
	doDisable();
	break;

 
	}
   
	function doInsert(){
		if(isset($_POST['save'])){

 // `COMPANYNAME`, `COMPANYADDRESS`, `COMPANYCONTACTNO`
		if ( $_POST['COMPANYNAME'] == "" || $_POST['COMPANYADDRESS'] == "" || $_POST['COMPANYCONTACTNO'] == "" ) {
			$messageStats = false;
			message("All field is required!","error");
			redirect('index.php?view=add');
		}else{	
			$company = New Company();
			$company->COMPANYNAME		= $_POST['COMPANYNAME'];
			$company->COMPANYADDRESS	= $_POST['COMPANYADDRESS'];
			$company->COMPANYCONTACTNO	= $_POST['COMPANYCONTACTNO'];
			// $company->COMPANYMISSION	= $_POST['COMPANYMISSION'];
			$company->create();

			message("New company created successfully!", "success");
			redirect("index.php");
			
		}
		}

	}

	function doEdit(){
		if(isset($_POST['save'])){

			$company = New Company();
			$company->COMPANYNAME		= $_POST['COMPANYNAME'];
			$company->COMPANYADDRESS	= $_POST['COMPANYADDRESS'];
			$company->COMPANYCONTACTNO	= $_POST['COMPANYCONTACTNO'];
			// $company->COMPANYMISSION	= $_POST['COMPANYMISSION'];
			$company->update($_POST['COMPANYID']);

			message("Company has been updated!", "success");
			redirect("index.php");
		}

	}


	function doDisable() {
    global $mydb;

    if (!isset($_GET['id'])) {
        $_SESSION['message'] = "Missing employer ID.";
        redirect("index.php");
        exit;
    }

    $id = $_GET['id'];

    $sql = "UPDATE tblemployers SET IS_ACTIVE = '0' WHERE EMPLOYERID = '{$id}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();

    $_SESSION['message'] = "Company has been disabled successfully.";
    redirect("index.php");
}
?>