<?php
session_start();
$token = md5(mt_rand());
$_SESSION['token'] = $token;
?>
<!DOCTYPE html>
<html lang="en" id="pos">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>外网查分</title>
	<style type="text/css">
		* {
			font-size: 27px;
		}
		#pos {
			position: relative;
		}
		body {
			margin: 0px;
		}
		#login {
			width: 380px;
			height: 500px;
			margin: 50px auto 150px;
			border-style: solid;
			border-width: 1px;
			border-color: black;
		}
		#head {
			text-align: center;
			width: 100px;
			margin: 50px auto;
		}
		#username, #password {
			display: block;
			height: 30px;
			width: 300px;
			margin: 45px auto;
			padding: 5px;

			border-style: solid;
			border-width: 1px;
			border-color: black;
		}
		#ic {
			width: 312px;
			margin: 0px auto;
		}
		img {
			display: inline-block;
			height: 40px;
			width: 100px;
			margin: 0px auto;
			vertical-align: middle
		}
		#code {
			display: inline-block;
			height: 28px;
			width: 200px;
			padding: 5px;
			vertical-align: middle;

			border-style: solid;
			border-width: 1px;
			border-color: black;
		}
		input::-webkit-input-placeholder {
		    color: #000;  
		}  
		input:-moz-placeholder {
		    color: #000;  
		}  
		input::-moz-placeholder {
		    color: #000;  
		}  
		input:-ms-input-placeholder {
		    color: #000;   
		}
		#query {
			display: block;
			height: 50px;
			width: 120px;
			margin: 30px auto;
			background-color: #fff;
			border-style: solid;
			border-width: 1px;
			border-color: black;
		}
		#displayResult {
			width: 320px;
			height: 500px;
			margin: 50px auto 150px;
		}
		/*p {
			text-align: center;
		}*/
		span {
			font-size: 20px;
			display: inline-block;
			height: 30px;
			margin: 3px auto;
			text-align: center;
			overflow: hidden;
			text-overflow:ellipsis;
			white-space: nowrap;
		}
		.bujige {
			color: red;
		}
		.span1 {
			width: 60px;
			height: 36px;
		}
		.span2 {
			width: 260px;
			font-size: 16px;
			border-bottom: 1px black solid;
		}
		.span3 {
			width: 220px;
		}
		.span4 {
			width: 100px;
		}
		#power {
			font-size: 20px;
			position: absolute;
			bottom: 10px;
			right: 20px;
		}
	</style>
</head>
<body id="p">
	<div id="login">
		<div id="head">登录</div>
		<div>
			<input type="text" id="username" placeholder="学号" />
			<input type="password" id="password" placeholder="密码" />
			<div id="ic">
			<img src="captcha.php?" id="change" /><input type="text" id="code" placeholder="验证码" />
			</div>
			<input type="hidden" name="token" value=>
			<input type="submit" value="查分" id="query" />
		</div>
	</div>
</body>
<div id="power">Powered by 校团委网络中心</div>
<script src="./js/ajax.js"></script>
<script>
	var l = document.getElementById('login');
	var u = document.getElementById('username');
	var c = document.getElementById('code');
	var i = document.getElementById('change');
	var p = document.getElementById('password');
	var q = document.getElementById('query');

	i.onclick = function () {
		i.src="captcha.php?"+ new Date();
	}
	
	var f = function () {
		if ((isNaN(u.value)||u.value.length!=10)&&p.value!=""&&c.value.length==4) {
			alert("Error：非十位位数字的学号");
			return false;
		} else if ((!isNaN(u.value)&&u.value.length==3)&&p.value==""&&c.value.length==4) {
			alert("Error：密码为空");
			return false;
		}  else if ((!isNaN(u.value)&&u.value.length==3)&&p.value!=""&&c.value.length!=4) {
			alert("Error：非四位的验证码");
			return false;
		} else if ((!isNaN(u.value)&&u.value.length==3)&&p.value==""&&c.value.length!=4) {
			alert("Error：密码为空、非四位的验证码");
			return false;
		} else if ((isNaN(u.value)||u.value.length!=3)&&p.value!=""&&c.value.length!=4) {
			alert("Error：非十位位数字的学号、非四位的验证码");
			return false;
		} else if ((isNaN(u.value)||u.value.length!=3)&&p.value==""&&c.value.length==4) {
			alert("Error：非十位位数字的学号、密码为空");
			return false;
		} else if ((isNaN(u.value)||u.value.length!=3)&&p.value==""&&(c.value==""||c.value.length!=4)){
			alert("Error：非十位数字的学号、密码为空、非四位的验证码");
			return false;
		}
		
		$.ajax({
			"url": "getGrade.php",
			"data": {
				"stuNum":u.value,
				"password":p.value,
				"code":c.value,
				"token":"<?php echo $token?>"
			},
			"success": function (r) {
				if (r == 0) {
					alert("Error：验证码错误");
				} else if (r == -1) {
					alert("Error：用户不存在或者学籍状态为空，请查证后重试");
				} else if (r == 1) {
					alert("Error：密码错误");
				} else {
					document.getElementById('p').removeChild(l);
					document.body.innerHTML="<div id='displayResult'></div><div id='power'>Powered by 校团委网络中心</div>";
					var g = JSON.parse(r);
					var d = document.getElementById('displayResult');
					d.innerHTML="<p>查询结果</p>";
					var z = 0;
					for (var k in g) {
						z++;
						if (z==3) {
							d.innerHTML+="<span class='span3'>科目</span><span class='span4'>成绩</span>";
						}
						console.log(k + "：" + g[k]);
						if (g[k]<60) {
							d.innerHTML+="<span class='span3'>" + k + "</span><span class='span4 bujige'>" + g[k] + "</span>";
						} else if (g[k]>60) {
							d.innerHTML+="<span class='span3'>" + k + "</span><span class='span4'>" + g[k] + "</span>";
						} else {
							d.innerHTML+="<span class='span1'><b>" + k + "</b></span><span class='span2'>" + g[k] + "</span>";
						}
					}
				}
			}
		});
	};
	q.onclick = f;
	document.body.onkeydown = function (){
		if (event.keyCode==13)
			f();
	};
</script>
</html>