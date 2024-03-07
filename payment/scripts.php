<?php $subd = ($_SERVER["HTTP_HOST"] == 'www.misaludonline.cl' or $_SERVER["HTTP_HOST"] == 'misaludonline.cl') ? '..' : '' ?>
<script src="<?php echo $subd ?>/node_modules/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $subd ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $subd ?>/dist/js/adminlte.js"></script>
<script src="<?php echo $subd ?>/dist/js/functions.js?v=<?php echo time() ?>"></script>
<script src="<?php echo $subd ?>/build/js/system.js?v=<?php echo time() ?>"></script>