<?php
$cookie_file="./tmp.cookie";
session_start();
if(isset($_SERVER['HTTP_REFERER'])){
	$refer = $_SERVER['HTTP_REFERER'];
	if($refer){
   	 	$url = parse_url($refer);
    	$t = explode('.',$url['host']);
    	$l = count($t);
   		// $domainStr = $t[$l-2].'.'.$t[$l-1];
    	// if ($domainStr != 'jessicxin.top'){
     // 		exit('拒绝访问！');
    	//  }
    		 if(isset($_POST['token'])){
				if($_POST['token'] == $_SESSION['token']){
		    		unset($_SESSION['token']);
   					imitateLogin();
				}else{
					exit('bad request<br>');
				}
		}else{
				exit('bad request sorry<br>');
		}
    }
}
function imitateLogin()
{

	//提交参数到教务处,模拟登陆
 		if(strtolower($_SESSION['code'])!=strtolower($_POST['code'])){
  			echo 0;
  		}else{
  			getCode();
  			$res = login();
//判断登录状态
			if(strripos($res,iconv("utf-8","gb2312","密码不正确"))){
      			echo 1;
      		}else if(strripos($res,iconv("utf-8","gb2312","用户不存在或者学籍状态为空！请查证后再试！"))){
				echo -1;
      			}else{
					getScore($_POST['stuNum']);
      		}
      	}
}
	function getCode()
	{
//获取验证码并缓存cookie包
//方法1
		global $cookie_file;

// 		$ch = curl_init();
// 		curl_setopt($ch, CURLOPT_URL,"http://10.50.17.1/default3.aspx");
// 		curl_setopt($ch, CURLOPT_HEADER, 1);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 		$content = curl_exec($ch);
// 		curl_close($ch);
// // 解析HTTP数据流
// 		list($header, $body) = explode("\r\n\r\n", $content);
// // 解析COOKIE
// 		preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
// // 后面用CURL提交的时候可以直接使用
// 		$cookie_file = $matches[1];
//加载cookie包取出验证码图片
		// $curl = curl_init();
		// curl_setopt($curl, CURLOPT_URL,"http://10.50.17.1/CheckCode.aspx");
		// curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
		// curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		// $img = curl_exec($curl);
		// curl_close($curl);
		
	//方法2
		$timeout = 5;
		$curl 	 = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://10.50.17.1/default3.aspx");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($curl,CURLOPT_COOKIEJAR,$cookie_file);
		curl_exec($curl);
		curl_close($curl);
}
//抓取教务处里的成绩表
	function getScore($stuNum)
	{
		global $cookie_file;
		$ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL,"http://10.50.17.1/xstop.aspx");
    	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    	curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    	$return = curl_exec($ch);
    	curl_close($ch);
    	$userInfo = explode('>',mb_convert_encoding($return, "UTF-8","GBK"));
    	$final=['姓名'=>str_replace("</span","",$userInfo[24]),'班级'=>str_replace("</span","",$userInfo[28])];
		$ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL,"http://10.50.17.1/xscj.aspx?xh=".$stuNum);  //这里是我想要信息的网址
    	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    	curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    	$return = curl_exec($ch);
    	curl_close($ch);
    	$pdo=new PDO('mysql:host=localhost;dbname=finalGrade;charset=utf8','root','',[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    	$stmt= $pdo->prepare("INSERT log VALUES (?,now(),null)");
    	if($stmt->execute([$stuNum])){
    		showScore($return,$final);
    	}
	}

	//切割后输出成绩
	function showScore($score,$final)
	{
		$pos = strpos(strip_tags(mb_convert_encoding($score, "UTF-8","GBK")),"2017-20181");
		$score = substr(strip_tags(mb_convert_encoding($score, "UTF-8","GBK")),$pos);
		$pos = strpos($score,"2017-20182");
		if($pos!=false)
			$score = substr($score,0,$pos);
		$grade = explode("2017-20181",$score);
		foreach ($grade as $key => $value) {
			$pos = stripos($value,"必修课");
			if(!$pos){
				$pos = stripos($value,"公共选修课");
			}
			$subject = substr($value, 0,$pos);
			$pos = stripos($value,"&nbsp;&nbsp;");
			$tmp = str_replace("&nbsp;","",substr($value,0,$pos));
			if(is_numeric(substr($tmp,strlen($tmp)-2,strlen($tmp))))
			{
				$final[$subject] = substr($tmp,strlen($tmp)-2,strlen($tmp));
			}else if(is_numeric(substr($tmp,strlen($tmp)-1,strlen($tmp))))
			{
				$final[$subject] = substr($tmp,strlen($tmp)-1,strlen($tmp));
			}
		}
			echo json_encode($final,JSON_UNESCAPED_UNICODE);
	}
	//模拟登录
	function login()
	{
		global $cookie_file;
		$data 		= [
		'__VIEWSTATE'		=>'dDwtMTE5NDgzNTU2MTt0PDtsPGk8MT47PjtsPHQ8O2w8aTwzPjtpPDE0PjtpPDE3Pjs+O2w8dDxwPGw8VmlzaWJsZTs+O2w8bzxmPjs+Pjs7Pjt0PHA8O3A8bDxvbmNsaWNrOz47bDx3aW5kb3cuY2xvc2UoKVw7Oz4+Pjs7Pjt0PHA8bDxWaXNpYmxlOz47bDxvPGY+Oz4+Ozs+Oz4+Oz4+O2w8aW1nREw7Pj6e0r8XxWaLW1epPm5xLYC1H1N9Ug==',
		'tbYHM'				=>$_POST['stuNum'],
		'tbPSW'				=>$_POST['password'],
		'TextBox3'			=>$_POST['code'],
		'RadioButtonList1'	=>'学生',
		'imgDL.x'			=>128,
		'imgDL.y'			=>24
		];
 		$ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL,'http://10.50.17.1/default3.aspx');
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
       	curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
	}
?>
