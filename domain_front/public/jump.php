{__NOLAYOUT__}
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<title>跳转提示</title>
<script type="text/javascript" src="/lib/utils/jquery.min-2.2.1.js"></script>
<script type="text/javascript" src="/lib/components/layer/layer.js"></script>
</head>

<body>
<a id="href" href="<?php echo($url); ?>" style='display: none'>跳转</a>
<script type="text/javascript">
$(function() {
	var title   = "页面自动跳转中... 等待时间:<b id='wait'><?php echo($wait); ?></b>秒";
	<?php switch ($code) {?>
		<?php case 1:?>
			var icon = 1;
		<?php break;?>
        <?php case 0:?>
        	var icon = 2;
        <?php break;?>
	<?php } ?>
	layer.alert("<?php echo(strip_tags($msg)); ?>", {
		icon: icon,
		title: title,
		success: function(layer) {
			var wait = document.getElementById('wait');
			var	href = document.getElementById('href').href;
			var interval = setInterval(function() {
				var time = --wait.innerHTML;
				if (time <= 0) {
					location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
       }
	})
})
</script>
</body>

</html>

