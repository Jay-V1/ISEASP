<?php 
require_once("./include/initialize.php");  

// if (!isset($_SESSION['EMPLOYER_ID'])) {
//     redirect(web_root.'employer/login.php');
// }
if(!isset($_SESSION['EMPLOYER_ID'])){
    redirect(web_root."employer/login.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';

switch ($view) { 
    case 'companies':
        $title = "Companies";    
        $content = 'companies.php';
        break;

    case 'vacancies':
        $title = "Vacancies";    
        $content = 'vacancies.php';
        break;

    case 'applicants':
        $title = "Applicants";    
        $content = 'applicants.php';
        break;

    case 'view_applicant':
        $title = "View Applicant";    
        $content = 'view_applicant.php';
        break;

    case 'view_employee':
        $title = "Employee";    
        $content = 'view_employee.php';
        break;

    case 'employees':
        $title = "Employees";    
        $content = 'employees.php';
        break;

    case 'add_vacancy':
        $title = "Vacancies";    
        $content = 'add_vacancy.php';
        break;

    case 'edit_job':
        $title = "Update Vacancies";    
        $content = 'edit_vacancy.php';
        break;

    case 'profile':
        $title = "Profile";    
        $content = 'profile.php'; 
        break;

    default:
        $title = "Dashboard";    
        $view = 'dashboard'; // <-- set default view name for active class
        $content = 'dashboard.php';
}

// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $title; ?> - Employer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      position: fixed;
      width: 250px;
      background-color: #183B4E;
      padding: 1rem 0;
      color: white;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    .sidebar a {
      color: #dcdcdc;
    }
    .sidebar .nav-link:hover {
      background-color:rgba(221, 168, 83, 0.13);
      color: white;
    }
    .sidebar .nav-link.active {
      background-color: #DDA853 !important;
      color: white !important;
      font-weight: bold;
    }
    .content {
      margin-left: 250px;
      padding: 2rem;
    }
    .navbar {
      margin-left: 250px;
      background-color: #F2F2F2;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.19);
      padding: .5rem 2rem;
    }
    .navbar .nav-item {
      margin-left: 1rem;
    }
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        position: relative;
      }
      .content, .navbar {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column p-3">
    <div class="d-flex align-items-center justify-content-center mb-4">
      <!-- <i class="bi bi-speedometer2 fs-3 me-2"></i> -->
      <span class="fs-4">PANGGEDAN</span>
    </div>
    <hr class="text-light">
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="<?php echo web_root.'employer/index.php'; ?>" 
          class="nav-link <?php echo $view == 'dashboard' ? 'active' : ''; ?>">
          <i class="bi bi-house-door me-2"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="<?php echo web_root.'employer/index.php?view=profile'; ?>" 
          class="nav-link <?php echo $view == 'profile' ? 'active' : ''; ?>">
          <i class="bi bi-person-circle me-2"></i> Profile
        </a>
      </li>

      <!-- <li>
        <a href="<?php echo web_root.'employer/index.php?view=companies'; ?>" 
          class="nav-link <?php echo $view == 'companies' ? 'active' : ''; ?>">
          <i class="bi bi-buildings me-2"></i> Companies
        </a>
      </li> -->
      <li>
        <a href="<?php echo web_root.'employer/index.php?view=vacancies'; ?>" 
          class="nav-link <?php echo $view == 'vacancies' ? 'active' : ''; ?>">
          <i class="bi bi-briefcase me-2"></i> Vacancies
        </a>
      </li>
      <li>
        <a href="<?php echo web_root.'employer/index.php?view=applicants'; ?>" 
          class="nav-link <?php echo $view == 'applicants' ? 'active' : ''; ?>">
          <i class="bi bi-people me-2"></i> Applicants
        </a>
      </li>
      <li>
        <a href="<?php echo web_root.'employer/index.php?view=employees'; ?>" 
          class="nav-link <?php echo $view == 'employees' ? 'active' : ''; ?>">
          <i class="bi bi-person-badge me-2"></i> Employees
        </a>
      </li>
    </ul>
    <hr class="text-light">
    <!-- <div class="px-3">
      <a href="<?php echo web_root.'employer/logout.php'; ?>" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
      </a>
    </div> -->
  </div>

  <!-- Top Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h4"><?php echo $title; ?></span>
      <ul class="navbar-nav ms-auto align-items-center">
        <!-- <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-bell fs-5"></i></a>
        </li> -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-5 me-1"></i><?php echo $_SESSION['EMPLOYER_NAME']; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?php echo web_root.'employer/index.php?view=profile'; ?>">Profile</a></li>
            <!-- <li><a class="dropdown-item" href="#">Settings</a></li> -->
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?php echo web_root.'employer/logout.php'; ?>">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="content">
    <?php require_once($content); ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
