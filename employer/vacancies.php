<?php

require_once("./include/initialize.php");

$employerID = $_SESSION['EMPLOYER_ID'];
$search = isset($_GET['search']) ? "%" . trim($_GET['search']) . "%" : null;

if ($search) {
  $query = "SELECT * FROM tbljob WHERE COMPANYID = ? AND (OCCUPATIONTITLE LIKE ? OR CATEGORY LIKE ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("iss", $employerID, $search, $search);
} else {
  $query = "SELECT * FROM tbljob WHERE COMPANYID = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $employerID);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container-fluid px-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary">Job Vacancies</h3>
    <a href="index.php?view=add_vacancy" class="btn btn-success">
      <i class="bi bi-plus-circle me-1"></i> Add New Vacancy
    </a>
  </div>
  <div class="d-flex justify-content-end mb-4">
    <form class="d-flex align-items-center w-auto" method="get" action="">
      <input type="hidden" name="view" value="vacancies">
      <input type="text" name="search" class="form-control form-control-sm me-2" 
            style="max-width: 250px;" 
            placeholder="Search job or category..." 
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
      <button class="btn btn-sm btn-outline-primary" type="submit">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>


  <?php if ($result->num_rows > 0): ?>
    <div class="row">
      <?php while ($job = $result->fetch_assoc()): ?>
        <div class="col-md-6 mb-4">
          <div class="card border-0 h-100" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.boxShadow='0 6px 24px rgba(0,0,0,0.12)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'; this.style.transform='none'">
            <div class="card-body">
              <h5 class="card-title text-primary"><?php echo htmlspecialchars($job['OCCUPATIONTITLE']); ?></h5>
              <p class="mb-2"><strong>Category:</strong> <?php echo htmlspecialchars($job['CATEGORY']); ?></p>
              <p class="mb-2"><strong>Required Employees:</strong> <?php echo $job['REQ_NO_EMPLOYEES']; ?></p>
              <p class="mb-2"><strong>Salary:</strong> ₱<?php echo number_format($job['SALARIES']); ?></p>
              <p class="mb-2"><strong>Duration:</strong> <?php echo htmlspecialchars($job['DURATION_EMPLOYEMENT']); ?></p>
              <p class="mb-2"><strong>Preferred Sex:</strong> <?php echo htmlspecialchars($job['PREFEREDSEX']); ?></p>
              <p class="mb-2"><strong>Status:</strong>
                <span class="badge bg-<?php echo $job['JOBSTATUS'] === 'Open' ? 'success' : 'secondary'; ?>">
                  <?php echo $job['JOBSTATUS']; ?>
                </span>
              </p>
              <p class="small text-muted">Posted: <?php echo date("M d, Y", strtotime($job['DATEPOSTED'])); ?> | Closing: <?php echo date("M d, Y", strtotime($job['CLOSINGDATE'])); ?></p>
              <div class="d-flex gap-2 mt-3">
                <!-- <a href="index.php?view=view_job&id=<?php echo $job['JOBID']; ?>" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye"></i> View
                </a> -->
                <a href="index.php?view=edit_job&id=<?php echo $job['JOBID']; ?>" class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="index.php?view=delete_job&id=<?php echo $job['JOBID']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job?');">
                  <i class="bi bi-trash"></i> Remove
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">
      <i class="bi bi-info-circle me-1"></i> No job vacancies found.
    </div>
  <?php endif; ?>
</div>
