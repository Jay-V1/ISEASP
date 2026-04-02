<table class="table table-hover table-striped">
  <thead class="table-primary">
    <tr>
      <th>Title</th>
      <th>Date Posted</th>
      <th>Date Closing</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $sql = "SELECT * FROM `tbljob` j, `tblemployers` c WHERE j.`COMPANYID`=c.`EMPLOYERID` AND j.`JOBSTATUS` = 'open' ORDER BY DATEPOSTED DESC LIMIT 10";
      // $sql = "SELECT * 
      //   FROM `tblemployers` c, `tbljob` j 
      //   WHERE c.`EMPLOYERID` = j.`COMPANYID` 
      //     AND j.`JOBSTATUS` = 'open'
      //     AND c.`COMPANYNAME` LIKE '%" . $COMPANYNAME . "%' 
      //   ORDER BY j.`DATEPOSTED` DESC LIMIT 10";
      $mydb->setQuery($sql);
      $cur = $mydb->loadResultList();
      foreach ($cur as $result) {
        echo '<tr>';
        echo '<td class="mailbox-name">' . htmlspecialchars($result->OCCUPATIONTITLE) . '</td>';
        echo '<td class="mailbox-date">' . date("M d, Y", strtotime($result->DATEPOSTED)) . '</td>';
        echo '<td class="mailbox-date">' . date("M d, Y", strtotime($result->CLOSINGDATE)) . '</td>';
        echo '<td>
                <a href="'.web_root.'index.php?q=viewjob&search='.$result->JOBID. '" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-eye"></i> View
                </a>
              </td>';
        echo '</tr>';
      }
    ?> 
  </tbody>
</table>