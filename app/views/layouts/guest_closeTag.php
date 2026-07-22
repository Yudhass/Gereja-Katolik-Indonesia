<script src="<?= BASEURL; ?>assets/js/jquery.min.js"></script>
<script src="<?= BASEURL; ?>assets/js/bootstrap.bundle.min.js"></script>
<?php if (isset($js)): foreach ($js as $jss): ?>
    <script src="<?= (strpos($jss, 'http://') === 0 || strpos($jss, 'https://') === 0) ? $jss : (BASEURL . $jss); ?>"></script>
<?php endforeach; endif; ?>
<?php if (isset($scripts)): echo $scripts; endif; ?>
</body>
</html>
