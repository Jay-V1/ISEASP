<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper"> 
  <!-- Main content -->
  <section class="content">
    <div class="row"> 
      <?php if (!isset($_GET['p'])) { ?>
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Applied Jobs</h3> 
          </div>
          <!-- /.box-header -->
          <div class="box-body no-padding">
            <div class="table-responsive mailbox-messages">
              <table id="dash-table" class="table table-hover table-striped">
                <thead> 
                  <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $sql = "SELECT * FROM `tblemployers` c, `tbljobregistration` r, `tbljob` j 
                            WHERE c.`EMPLOYERID` = r.`COMPANYID` 
                            AND r.`JOBID` = j.`JOBID` 
                            AND r.`APPLICANTID` = {$_SESSION['APPLICANTID']}";
                    $mydb->setQuery($sql);
                    $cur = $mydb->loadResultList();  

                    foreach ($cur as $result) {
                      echo '<tr>';
                      echo '<td class="mailbox-star"><a href="index.php?view=appliedjobs&p=job&id='.$result->REGISTRATIONID.'"><i class="fa fa-pencil-o text-yellow"></i> '.$result->OCCUPATIONTITLE.'</a></td>';
                      echo '<td class="mailbox-attachment">'.$result->COMPANYNAME.'</td>';
                      echo '<td class="mailbox-attachment">'.$result->ADDRESS.'</td>';

                      // Status badge
                      $status = strtolower($result->IS_ACCEPTED);
                      $badge = '';

                      if ($status == 'yes') {
                        $badge = '<span class="label label-success">Yes</span>';
                      } elseif ($status == 'rejected') {
                        $badge = '<span class="label label-danger">Rejected</span>';
                      } elseif ($status == 'no') {
                        $badge = '<span class="label label-default">No</span>';
                      } else {
                        $badge = '<span class="label label-warning">'.htmlspecialchars($result->IS_ACCEPTED).'</span>';
                      }

                      echo '<td class="mailbox-attachment">'.$badge.'</td>';
                      echo '</tr>';
                    } 
                  ?>
                </tbody>
              </table>
              <!-- /.table -->
            </div>
            <!-- /.mail-box-messages -->
          </div> 
        </div>
        <!-- /. box -->
      </div>
      <!-- /.col -->
      <?php } else {
        require_once("viewjob.php");          
      } ?>
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
