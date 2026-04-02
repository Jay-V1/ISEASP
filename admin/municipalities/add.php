<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

global $mydb;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add New Municipality</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-plus"></i> Municipality Information
            </div>
            <div class="panel-body">
                <form method="POST" action="controller.php?action=add" class="form-horizontal">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Municipality Name:</label>
                        <div class="col-md-7">
                            <input type="text" name="MUNICIPALITY_NAME" class="form-control" 
                                   required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">District:</label>
                        <div class="col-md-7">
                            <select name="DISTRICT" class="form-control" required>
                                <option value="">-- Select District --</option>
                                <option value="1st District">1st District</option>
                                <option value="2nd District">2nd District</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Status:</label>
                        <div class="col-md-7">
                            <select name="IS_ACTIVE" class="form-control" required>
                                <option value="Yes">Active</option>
                                <option value="No">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-7">
                            <button type="submit" name="save" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Municipality
                            </button>
                            <a href="index.php" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add List -->
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Common Municipalities of Ilocos Sur
            </div>
            <div class="panel-body">
                <p>Click to quickly add common municipalities:</p>
                <div class="row">
                    <div class="col-md-6">
                        <h5>1st District:</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" onclick="quickAdd('Vigan City', '1st District')">Vigan City</a></li>
                            <li><a href="#" onclick="quickAdd('Santa Catalina', '1st District')">Santa Catalina</a></li>
                            <li><a href="#" onclick="quickAdd('Bantay', '1st District')">Bantay</a></li>
                            <li><a href="#" onclick="quickAdd('Caoayan', '1st District')">Caoayan</a></li>
                            <li><a href="#" onclick="quickAdd('Santa', '1st District')">Santa</a></li>
                            <li><a href="#" onclick="quickAdd('Narvacan', '1st District')">Narvacan</a></li>
                            <li><a href="#" onclick="quickAdd('Santa Maria', '1st District')">Santa Maria</a></li>
                            <li><a href="#" onclick="quickAdd('San Esteban', '1st District')">San Esteban</a></li>
                            <li><a href="#" onclick="quickAdd('Santiago', '1st District')">Santiago</a></li>
                            <li><a href="#" onclick="quickAdd('Candon City', '1st District')">Candon City</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>2nd District:</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" onclick="quickAdd('Tagudin', '2nd District')">Tagudin</a></li>
                            <li><a href="#" onclick="quickAdd('Suyo', '2nd District')">Suyo</a></li>
                            <li><a href="#" onclick="quickAdd('Alilem', '2nd District')">Alilem</a></li>
                            <li><a href="#" onclick="quickAdd('Sigay', '2nd District')">Sigay</a></li>
                            <li><a href="#" onclick="quickAdd('Gregorio Del Pilar', '2nd District')">Gregorio Del Pilar</a></li>
                            <li><a href="#" onclick="quickAdd('Cervantes', '2nd District')">Cervantes</a></li>
                            <li><a href="#" onclick="quickAdd('Quirino', '2nd District')">Quirino</a></li>
                            <li><a href="#" onclick="quickAdd('Santa Cruz', '2nd District')">Santa Cruz</a></li>
                            <li><a href="#" onclick="quickAdd('Santa Lucia', '2nd District')">Santa Lucia</a></li>
                            <li><a href="#" onclick="quickAdd('Salcedo', '2nd District')">Salcedo</a></li>
                            <li><a href="#" onclick="quickAdd('San Vicente', '2nd District')">San Vicente</a></li>
                            <li><a href="#" onclick="quickAdd('Galimuyod', '2nd District')">Galimuyod</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function quickAdd(name, district) {
    document.querySelector('input[name="MUNICIPALITY_NAME"]').value = name;
    document.querySelector('select[name="DISTRICT"]').value = district;
    document.querySelector('select[name="IS_ACTIVE"]').value = 'Yes';
}
</script>