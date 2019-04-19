<title>db handlers</title>
<p>
This is PHP <?php echo phpversion(); ?>
</p>
<?php

echo "Available DBA handlers:\n";
foreach (dba_handlers(true) as $handler_name => $handler_version) {
  // clean the versions
  $handler_version = str_replace('$', '', $handler_version);
  echo " - $handler_name: $handler_version\n";
}

phpinfo()
?>
