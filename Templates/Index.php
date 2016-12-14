<?php
    $pjax = isset ($_SERVER['HTTP_X_PJAX']);
?>
<?php if ($pjax == false) require_once 'Header1.php' ?>
<title>后台</title>
<?php if ($pjax == false) require_once 'Header2.php' ?>
<?php if ($pjax == false) require_once 'Sidebar.php' ?>
<?php if ($pjax == false) echo '<div class="container" id="container">' ?>

<div class="row">
    <div class="col-xs-12" style="margin-top: 10px">
        This is a Page...
    </div>
</div>

<?php if ($pjax == false) echo '</div>' ?>
<?php if ($pjax == false) require_once 'Footer.php' ?>