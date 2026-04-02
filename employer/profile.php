<?php
require_once('./include/initialize.php');

if (!isset($_SESSION['EMPLOYER_ID'])) {
  redirect(web_root . 'employer/login.php');
}

$employerID = $_SESSION['EMPLOYER_ID'];
$sql = "SELECT * FROM tblemployers WHERE EMPLOYERID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employerID);
$stmt->execute();
$result = $stmt->get_result();
$employer = $result->fetch_assoc();

// Define your project root relative to DOCUMENT_ROOT and web root URL
$projectRoot = '/panggedan/employer';

// Clean the logo path from DB (remove leading dot and slash if present)
$logoPathFromDB = ltrim($employer['LOGO'], './\\');

// Build full server path to check if file exists
$logoFilePath = $_SERVER['DOCUMENT_ROOT'] . $projectRoot . '/' . $logoPathFromDB;

// Default logo web path if logo missing
$defaultLogoWeb = $projectRoot . '/applicant/photos/default.png';

// Determine final logo URL for <img> src
if (!empty($employer['LOGO']) && file_exists($logoFilePath)) {
    $finalLogoPath = $projectRoot . '/' . $logoPathFromDB;
} else {
    $finalLogoPath = $defaultLogoWeb;
}
?>

<!-- Put this meta in your <head> section -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* Fix image size to prevent layout shift */
  #logoPreview {
    width: 150px;
    height: 150px;
    object-fit: cover;
    transition: all 0.3s ease-in-out;
    will-change: transform, opacity;
    backface-visibility: hidden;
  }

  /* Container around logo to fix layout size */
  .logo-container {
    width: 150px;
    height: 150px;
    overflow: hidden;
  }

  /* Reduce heavy shadow on card for smoother scrolling */
  .card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    transform: translateZ(0); /* GPU hack */
    will-change: transform;
  }

  /* Prevent layout shift from margin/padding during scrolling */
  body, html {
    scroll-behavior: smooth;
  }
</style>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-xl-10 col-lg-12">
      <div class="card border-0 rounded-4">
        <div class="card-body p-5">
          <h2 class="mb-4 text-center text-primary">
            <i class="bi bi-building-fill-gear me-2"></i>Employer Profile
          </h2>

          <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="employer_id" value="<?php echo $employerID; ?>">

            <div class="row g-4">
              <!-- Company Info -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">Company Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-building"></i></span>
                  <input type="text" class="form-control" name="companyname" value="<?php echo htmlspecialchars($employer['COMPANYNAME']); ?>" required>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Person</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                  <input type="text" class="form-control" name="contactperson" value="<?php echo htmlspecialchars($employer['CONTACTPERSON']); ?>" required>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                  <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($employer['EMAIL']); ?>" required>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Phone</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                  <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($employer['PHONE']); ?>" required>
                </div>
              </div>

              <div class="col-md-12">
                <label class="form-label fw-semibold">Address</label>
                <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($employer['ADDRESS']); ?></textarea>
              </div>

              <!-- Logo Upload -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">Company Logo</label><br>
                <div class="logo-container mb-2 rounded shadow-sm border">
                  <img id="logoPreview" 
                       src="<?php echo htmlspecialchars($finalLogoPath); ?>" 
                       alt="Logo" 
                       class="img-thumbnail rounded" 
                       loading="lazy" 
                       style="width:150px; height:150px; object-fit:cover;">
                </div>
                <input type="file" class="form-control" name="logo" accept="image/*" onchange="previewLogo(event)">
              </div>
            </div>

            <div class="d-grid mt-5">
              <button type="submit" name="updateProfile" class="btn btn-primary btn-lg">
                <i class="bi bi-save2 me-2"></i>Update Profile
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function previewLogo(event) {
    const input = event.target;
    const preview = document.getElementById('logoPreview');

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
