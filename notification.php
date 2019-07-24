<?php
$ndata = shownotification();
?>
<?php if (!empty($ndata)) { ?>
    
    <?php if($ndata['type'] == 'success'){ ?>
        <div class="alert alert-success">
        <strong>Success!</strong> <?php echo $ndata['msg']; ?>
        </div>
    <?php } ?>

    <?php if($ndata['type'] == 'error'){ ?>
        <div class="alert alert-danger">
        <strong>Fail!</strong> <?php echo $ndata['msg']; ?>
        </div>
    <?php } ?>

<?php } ?>