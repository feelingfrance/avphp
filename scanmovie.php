<html>
<head>
<meta charset="utf-8" />
<title>电影扫描</title>
</head>
<body>
<p style=text-align:center>输入密码才能访问这个页面</p>
</body>
</html>


<?php
include 'var.php';//根据输入的目录,通过file.php文件,扫描目录下的电影文件,添加到数据库中



session_start([
                  'read_and_close'  => true,

              ]);
//$webaddr = "https://jemmi.gicp.net:$port/sdf.php";
if (isset($_POST['password']) && $_POST['password'] == 'superball') {
    $_SESSION['ok'] = 1;
    // header("location: $webaddr");

}
if (!isset($_SESSION['ok'])) {
    exit('
         <form method="post">
         <p style=text-align:center>密码：<input type="password" name="password" />
         <input type="submit" value="登陆" />
         </p>
         </form>
         ');
}

?>


	扫描电影目录: <input type="text" id="field">
    <script type="text/javascript" language="javascript" src="jquery.min.js"></script>
	<script type="text/javascript" language="javascript">
		function fun(n) {
		
			
			var tt=$("#field").val();

			$.ajax({
				url:"file.php", 			
				type: "POST", 				
				data:{dir:tt},
				beforeSend: function(){
					$("#view").html("");
					$("#btn1").attr({ disabled: "disabled" });
					$("#loading").show();
			
				},
				success:function(result){
					$("#view").html(result);
					$("#field").val("").focus();
				},
				complete: function(){
					$("#btn1").removeAttr("disabled");
					$("#loading").hide();
					
				}
			});
		}

	</script>
	<div>
		<button type="button" class="btn" id="btn1" onclick="fun(this)">提交</button>
	</div>
	<p id="view"></p>
	<p id="loading" style="display:none">加载中,请等待...</p>