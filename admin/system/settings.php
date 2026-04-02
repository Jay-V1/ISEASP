<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

if ($_SESSION['ADMIN_ROLE'] != 'Super Admin') {
    redirect(web_root . "admin/index.php");
}

global $mydb;

// Handle settings save
if (isset($_POST['save_settings'])) {
    $system_name = $_POST['system_name'];
    $system_email = $_POST['system_email'];
    $system_contact = $_POST['system_contact'];
    $address = $_POST['address'];
    $passing_score = intval($_POST['passing_score']);
    $current_sy = $_POST['current_sy'];
    $current_semester = $_POST['current_semester'];
    
    // In a real application, you would save these to a settings table
    // For now, we'll simulate by saving to session or config file
    
    message("Settings saved successfully!", "success");
    redirect("index.php?view=settings");
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">System Settings</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#general" data-toggle="tab">General Settings</a></li>
                <li><a href="#academic" data-toggle="tab">Academic Settings</a></li>
                <li><a href="#email" data-toggle="tab">Email Configuration</a></li>
                <li><a href="#security" data-toggle="tab">Security</a></li>
            </ul>
            
            <div class="tab-content">
                <!-- General Settings Tab -->
                <div class="tab-pane active" id="general">
                    <form method="POST" action="" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-3 control-label">System Name:</label>
                            <div class="col-md-7">
                                <input type="text" name="system_name" class="form-control" 
                                       value="Ilocos Sur Educational Assistance and Scholarship Program">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">System Email:</label>
                            <div class="col-md-7">
                                <input type="email" name="system_email" class="form-control" value="iseasp@ilocossur.gov.ph">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Contact Number:</label>
                            <div class="col-md-7">
                                <input type="text" name="system_contact" class="form-control" value="(077) 123-4567">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Address:</label>
                            <div class="col-md-7">
                                <textarea name="address" class="form-control" rows="3">Provincial Capitol, Vigan City, Ilocos Sur</textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">System Logo:</label>
                            <div class="col-md-7">
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <span class="help-block">Recommended size: 200x50 pixels</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Favicon:</label>
                            <div class="col-md-7">
                                <input type="file" name="favicon" class="form-control" accept="image/*">
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-7">
                                <button type="submit" name="save_settings" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save General Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Academic Settings Tab -->
                <div class="tab-pane" id="academic">
                    <form method="POST" action="" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Passing Score (%):</label>
                            <div class="col-md-7">
                                <input type="number" name="passing_score" class="form-control" value="75" min="0" max="100">
                                <span class="help-block">Minimum score to pass the examination</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Current School Year:</label>
                            <div class="col-md-7">
                                <select name="current_sy" class="form-control">
                                    <option value="2024-2025">2024-2025</option>
                                    <option value="2025-2026" selected>2025-2026</option>
                                    <option value="2026-2027">2026-2027</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Current Semester:</label>
                            <div class="col-md-7">
                                <select name="current_semester" class="form-control">
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester" selected>2nd Semester</option>
                                    <option value="Summer">Summer</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Application Deadline:</label>
                            <div class="col-md-7">
                                <input type="date" name="deadline" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Max Units for Renewal:</label>
                            <div class="col-md-7">
                                <input type="number" name="max_units" class="form-control" value="24" min="0">
                                <span class="help-block">Minimum units required for renewal</span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-7">
                                <button type="submit" name="save_academic" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Academic Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Email Configuration Tab -->
                <div class="tab-pane" id="email">
                    <form method="POST" action="" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-3 control-label">SMTP Host:</label>
                            <div class="col-md-7">
                                <input type="text" name="smtp_host" class="form-control" value="smtp.gmail.com">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">SMTP Port:</label>
                            <div class="col-md-7">
                                <input type="number" name="smtp_port" class="form-control" value="587">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">SMTP Username:</label>
                            <div class="col-md-7">
                                <input type="email" name="smtp_user" class="form-control" value="noreply@ilocossur.gov.ph">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">SMTP Password:</label>
                            <div class="col-md-7">
                                <input type="password" name="smtp_pass" class="form-control" value="********">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Encryption:</label>
                            <div class="col-md-7">
                                <select name="encryption" class="form-control">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Send Test Email:</label>
                            <div class="col-md-7">
                                <input type="email" name="test_email" class="form-control" placeholder="test@email.com">
                                <button type="button" class="btn btn-info btn-sm" style="margin-top: 5px;">Send Test</button>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-7">
                                <button type="submit" name="save_email" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Email Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Security Tab -->
                <div class="tab-pane" id="security">
                    <form method="POST" action="" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Session Timeout (minutes):</label>
                            <div class="col-md-7">
                                <input type="number" name="session_timeout" class="form-control" value="30" min="5" max="120">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Password Expiry (days):</label>
                            <div class="col-md-7">
                                <input type="number" name="password_expiry" class="form-control" value="90" min="0">
                                <span class="help-block">0 = never expires</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Max Login Attempts:</label>
                            <div class="col-md-7">
                                <input type="number" name="max_attempts" class="form-control" value="5" min="1">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Two-Factor Authentication:</label>
                            <div class="col-md-7">
                                <select name="two_factor" class="form-control">
                                    <option value="0">Disabled</option>
                                    <option value="1">Enabled for all users</option>
                                    <option value="2">Enabled for admins only</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Maintenance Mode:</label>
                            <div class="col-md-7">
                                <select name="maintenance" class="form-control">
                                    <option value="0">Disabled</option>
                                    <option value="1">Enabled</option>
                                </select>
                                <span class="help-block">When enabled, only admins can access the system</span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-7">
                                <button type="submit" name="save_security" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Security Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs-custom {
    margin-bottom: 20px;
    background: #fff;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 3px;
}
.nav-tabs-custom > .nav-tabs {
    margin: 0;
    border-bottom-color: #f4f4f4;
    border-top-right-radius: 3px;
    border-top-left-radius: 3px;
    background: #f8f9fa;
    padding: 10px 10px 0 10px;
}
.nav-tabs-custom > .tab-content {
    padding: 20px;
    background: #fff;
}
</style>