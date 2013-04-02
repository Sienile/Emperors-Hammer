<a href="/security.php">Security Home</a> | 
<?php if(has_access($_SESSION['EHID'], "security_ip")){ ?>
<a href="/security_ip.php">IP Tracking</a> | 
<?php } if(has_access($_SESSION['EHID'], "security_docs")){ ?>
<a href="/security_docs.php">Documents</a> | 
<?php } if(has_access($_SESSION['EHID'], "security_access")){ ?>
<a href="/security_access.php">Access Control</a>
<?php } ?>