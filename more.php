<?php 
	/**
	* 
	*/
	define("PASS", "admin");
	define("TYPE","d");
	define("NUM", 1);
	class Aria2{
    	protected $ch;
    	function __construct($server='http://127.0.0.1:6800/jsonrpc'){
        	$this->ch = curl_init($server);
        	curl_setopt_array($this->ch, [
            	CURLOPT_POST=>true,
            	CURLOPT_RETURNTRANSFER=>true,
            	CURLOPT_HEADER=>false
        	]);
    	}
    	function __destruct(){
        	curl_close($this->ch);
    	}
    	protected function req($data){
        	curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);        
        	return curl_exec($this->ch);
    	}
    	function __call($name, $arg){
        	$data = [
            	'jsonrpc'=>'2.0',
            	'id'=>'1',
            	'method'=>'aria2.'.$name,
            	'params'=>$arg
        	];
        	$data = json_encode($data);
        	$response = $this->req($data);
        	if($response===false) {
            	trigger_error(curl_error($this->ch));
        	}
        	return json_decode($response, 1);
    	}
	}
	class dir{
		public $dir;
		public $file;
		public $dirdir;
		public $notex;
		public $notdir;
		function __construct(){
			$this->notex=array("php","js","tgz");//不允许显示的后缀名文件
			$this->notdir=array("a","phpmyadmin");//不允许显示的文件夹
			if ($_GET['dir']) {
				foreach ($this->notdir as $key => $value) {
					if(strtolower($_GET['dir'])==$value){
						$_GET['dir']=".";
					}
				}
				$tom=trim($_GET['dir']);
				$tam=str_replace("..", ".", $tom);
				$this->dir="./".$tam;
			}else{
				$this->dir=".";
			}
		}
		function open_dir(){
			if(is_dir($this->dir)){
				if($dh=opendir($this->dir)){
					while(($file=readdir($dh))!==false){
						$this->jugg($file);
					}
					sort($this->file);
					sort($this->dirdir);
					closedir($dh);
				}
			}else{
				echo "error";
			}
		}
		function jugg($jugg){
			if($jugg!="."&&$jugg!=".."){
				if (is_dir($this->dir."/".$jugg)) {
					if(!in_array(strtolower($this->filename($jugg)), $this->notdir)){
						$this->dirdir[]=$this->dir."/".$jugg;
					}	
				}else{
					$ex=array_pop(explode(".", $jugg));
					if(!in_array(strtolower($ex), $this->notex)){
						$this->file[]=$this->dir."/".$jugg;
					}
				}
			}
		}
		function dirurl($dir){
			$urf=substr($dir,2 );
			return "?dir=".$urf;
		}
		function type($file){
			$ex=strtolower(array_pop(explode(".", $file)));
			switch ($ex) {
				case 'png':
				case 'jpg':
				case 'gif':
				case 'bmp':
				case 'jpeg':
					return "img";
					break;
				case 'torrent':
					return "torrent";
					break;
				case 'mp3':
					return "mp3";
					break;
				case 'mp4':
				case 'ogg':
				case 'webm':
					return "video";
					break;
				case 'xls':
				case 'xlsx':
				case 'doc':
				case 'docx':
				case 'ppt':
				case 'pptx':
					return "other";
					break;
				case 'pdf':
					return "pdf";
					break;
				case 'txt':
				case 'json':
				case 'xml':
				case 'html':
				case 'md':
					return "text";
					break;
				default:
					return "other";
					break;
			}
		}
		function download($file){
			return "<a href=\"".$file."\" ><span class=\"glyphicon glyphicon-download-alt\"></span></a>";
		}
		function other($file){


		}
		function img($img){

		}
		function pdf($pdf){

		}
		function video($video){

		}
		function mp3($mp3){

		}
		function torrent($torrent){

		}
		function filename($file){
			return array_pop(explode("/", $file));
		}
		function text($file){

		}
		function size($file){
			$fz=filesize($file);
			if ($fz>(1024*1024*1024)) {
				return sprintf("%.2f",$fz/(1024*1024*1024))."GB";
			}elseif ($fz>(1024*1024)) {
				return sprintf("%.2f",$fz/(1024*1024))."MB";
			}elseif($fz>1024){
				return sprintf("%.2f",$fz/1024)."KB";
			}else{
				return $fz."B";
			}
		}
		function mtime($file){
			return date("Y-m-d H:i:s",filemtime($file));
		}
		function atime($file){
			return date("Y-m-d H:i:s",fileatime($file));
		}
		function ctime($file){
			return date("Y-m-d H:i:s",filectime($file));
			
		}
		function icon($file){
			$ex=strtolower(array_pop(explode(".", $file)));
			switch ($ex) {
				case 'png':
				case 'jpg':
				case 'gif':
				case 'bmp':
				case 'jpeg':
					return "glyphicon glyphicon-picture";
					break;
				case 'torrent':
					return "glyphicon glyphicon-magnet";
					break;
				case 'mp3':
					return "glyphicon glyphicon-music";
					break;
				case 'mp4':
				case 'ogg':
				case 'webm':
					return "glyphicon glyphicon-film";
					break;
				case 'xls':
				case 'xlsx':
				case 'doc':
				case 'docx':
				case 'ppt':
				case 'pptx':
					return "glyphicon glyphicon-pencil";
					break;
				case 'pdf':
					return "glyphicon glyphicon-book";
					break;
				case 'txt':
				case 'md':
					return "glyphicon glyphicon-file";
					break;
				default:
					return "glyphicon glyphicon-stop";
					break;
			}
		}
		function pre(){
			$dir_array=explode("/", $this->dir);
			$num=count($dir_array);
			if($num>=2){
				@array_shift($dir_array);
				$url="<a class=\"text-success\" href=?>/.</a>";
				foreach ($dir_array as $key => $value) {
					$step=$step.$value."/";
					$url=$url."<a class=\"text-success\" href=\"?dir=".$step."\">/".$value."</a>";
				}
				return $url;
			}

		}
	}
	/**
	* 
	*/
	class json{
		public $info;
		function __construct(){
			if(is_file("./info.json")){
				$swap=file_get_contents("./info.json");
				$this->info=json_decode($swap,true);
			}else{
				$this->info=array("data"=>"json","gid"=>"","gid_info"=>"","this_num"=>"","this_time"=>"","hash"=>"","hash_info"=>"");//infoHash
				$aj=json_encode($this->info);
				@file_put_contents("./info.json", $aj);
			}
		}
		function gid($gid){
			return in_array($gid, $this->info['gid']);
		}
		function hash($hash){
			return in_array($hash, $this->info['hash']);
		}
		function add_hash($info){
			if($info["result"]['infoHash']){
				$this->info['hash'][]=$info["result"]['infoHash'];
				$iof['time']=time();
				$iof['gid']=$info["result"]['gid'];
				$iof['hash']=$info["result"]['infoHash'];
				$this->info['hash_info'][]=$iof;
				$this->save(); 
			}

		}
		function add($gid){
			$this->add_num();
			$this->add_gid($gid);
			$this->save();
		}
		function save(){
			$save=json_encode($this->info);
			@file_put_contents("./info.json", $save);

		}
		function add_num(){
			$this->info['this_time']=time();
			$this->info['this_num']+=1;
		}
		function add_gid($gid){
			$ar[$gid]['time']=time();
			$ar[$gid]['gid']=$gid;
			$this->info['gid_info'][]=$ar;
			$this->info['gid'][]=$gid;
		}
		function jugg(){
			$today=date("Ymd");
			$month=date("Ym");
			if(strtolower(TYPE)=="d"){
				if($today==date("Ymd",$this->info['this_time'])){
					if(NUM>$this->info['this_num']){
						return true;
					}else{
						return false;
					}
				}else{
					$this->info['this_num']=0;
					return true;
				}
			}elseif (strtolower(TYPE)=="m") {
				if($month==date("Ym",$this->info['this_time'])){
					if(NUM>$this->info['this_num']){
						return true;
					}else{
						return false;
					}
				}else{
					$this->info['this_num']=0;
					return true;
				}
				
			}else{
				if($today==date("Ymd",$this->info['this_time'])){
					if(NUM<$this->info['this_num']){
						return true;
					}else{
						return false;
					}
				}else{
					$this->info['this_num']=0;
					return true;
				}
			}
		}
	}
	function size($fz){
			if ($fz>(1024*1024*1024)) {
				return sprintf("%.2f",$fz/(1024*1024*1024))."GB";
			}elseif ($fz>(1024*1024)) {
				return sprintf("%.2f",$fz/(1024*1024))."MB";
			}elseif($fz>1024){
				return sprintf("%.2f",$fz/1024)."KB";
			}else{
				return $fz."B";
			}
		}
	
session_start();
$download_info=new json();
if ($_SESSION['user']) {
	if (strlen($_GET['url'])>5) {//验证session才能添加操作
		$download_jugg=$download_info->jugg();
		if($download_jugg){
			$url=$_GET['url'];
			$dir=dirname(__FILE__);
			$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');
			$json=$aria2->addUri(array($url),array('dir'=>$dir,));
			$download_info->add($json['result']);
			echo json_encode($json);
			$gh=$aria2->tellStatus($json['result']);
			$download_info->add_hash($gh);
			return;
		}else{
			$ar['error']['message']="今日配额已经使用完";
			echo json_encode($ar);
			return;
		}
	}
}


$x=new dir();
$x->open_dir();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="renderer" content="webkit">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">
	<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
	<script src="//cdn.bootcss.com/messenger/1.5.0/js/messenger.min.js"></script>
	<link href="//cdn.bootcss.com/messenger/1.5.0/css/messenger.min.css" rel="stylesheet">
	<link href="//cdn.bootcss.com/messenger/1.5.0/css/messenger-theme-future.min.css" rel="stylesheet">
	<title>Webdir</title>
	<style type="text/css">
		body{
			background-color:#F1F1FA;
		}
		.container{
			margin-top: 100px;
			border-radius:15px;
			background-color:#FFFFFF;
		}
	</style>
</head>
<body>
<?php
	if ($_POST['password']==PASS) {
		$_SESSION['user']="666";
	}
	if($_SESSION['user']){
	}else{
?>
	<div  class="container">
		<div class="row " style="margin:20px ">
			<div class="row">
					<center><h2 class="text-primary">Webdir</h2></center>	
			</div>
			<form class="form" action="" method="post">
				<div class="row">
					<div class="input-group col-md-4 col-md-offset-4">
						<input type="password" name="password" class="form-control">
						<span class="input-group-btn">
							<input type="submit" name="sub" class="btn btn-success" value="登入">
						</span>
					</div>
				</div>
			</form>
		</div>
		
	</div>
<?php
	return;
	}
	$free=@disk_free_space(".");//disk 
	$total=@disk_total_space(".");
	$used=$total-$free;
	$usp=round($used/$total*100,2);//used %
if(strlen($_GET['pause'])>5){
	$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');
	$aria2->pause($_GET['pause']);
	header("Location:".$_SERVER['HTTP_REFERER']);
	return;
}
if(strlen($_GET['unpause'])>5){
	$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');
	$aria2->unpause($_GET['unpause']);
	header("Location:".$_SERVER['HTTP_REFERER']);
	return;
}	
?>
	<div class="container">
		<div class="row">
			<div class="col-md-1" style="margin-bottom:10px; ">
				<a  href="
<?php echo $_SERVER['HTTP_REFERER'] ?>
				"
				><h2 class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left " id="back"></span></h2></a>
			</div>
			<div class="col-md-6">
				<h3>
<?php 
echo $x->pre() ;
?>
				</h3>
			</div>
			<div class="col-md-5">
				<div class="input-group" style="margin-top:10px ">
					<input type="text" name="magnet" id="magnet" class="form-control">
					<span class="input-group-btn">
						<span class="btn btn-success" id="btn-magnet">Magnet</span>
						<span class="btn btn-info" id="xzz">下载列表</span>
					</span>
				</div>
			</div>
		</div>
		<table class="table table-striped ">
			<tr>
				<th>文件名</th>
				<th>大小</th>
				<th>时间</th>
				<th>下载</th>
			</tr>
<?php
	foreach ($x->dirdir as $key => $value) {
		echo "<tr>";
			echo "<td><a href=\"".$x->dirurl($value)."\"><span class=\"glyphicon glyphicon-list\"> ".$x->filename($value)."</span></a></td>";
				echo "<td>目录</td>";
				echo "<td>".$x->mtime($value)."</td>";
				echo "<td></td>";
		echo "</tr>";
	}
	foreach ($x->file as $key => $value) {
		echo "<tr>";
			echo "<td><span class=\" click_onload ".$x->icon($value)." fileshow\" type=\"".$x->type($value)."\" value=\"".rawurlencode($value)."\"> ".$x->filename($value)."</span></td>";
			echo "<td>".$x->size($value)."</td>";
			echo "<td>".$x->mtime($value)."</td>";
			echo "<td>".$x->download($value)."</td>";
		echo "</tr>";
	}
?>
		</table>
			<div class="row">
				<span class="col-md-2">Powered by <a href="https://github.com/maysrp/webdir">webdir</a></span>
				<div class="col-md-6 ">
					<div class="row">
						<span class="col-md-2 text-right"><b>磁盘信息:</b></span>
						<div class="col-md-10">
							<div class="progress">
								<div class="progress-bar 
								<?php
									if ($usp<30) {
										echo "progress-bar-success";
									}elseif($usp<60){
										echo "progress-bar-primary";
									}elseif($usp<90){
										echo "progress-bar-warning";
									}else{
										echo "progress-bar-danger";
									}

								?>" role="progressbar" aria-valuenow="<?php echo $usp ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $usp ?>%;">
							<?php echo $usp ?>%
								</div>
							</div>
						</div>
					</div>
				</div>
				<span class="col-md-4">
					<b><span class="text-danger">USED:<?php echo size($used)?></span> / <span class="text-success">FREE:<?php echo size($free)?></span> / <span class="text-primary">TOTAL:<?php echo size($total)?></span></b>
				</span>
			</div>

	</div>
	<div>
		<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="modal">
  			<div class="modal-dialog modal-lg">
    			 <div class="modal-content ">
    			  	<div class="modal-header">
        				<h4 class="modal-title" id="myModalLabel"></h4>
      				</div>
      				<div class="modal-body text-center">
      				</div>
      				<div class="modal-footer">
        				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      				</div>
    			 </div>
  			</div>
		</div>
	</div>
	<div class="row hide" id="showdownload">
		<span class="text-success"><b>下载中的任务</b></span>
		<table class="table">
			<tr>
				<th class="text-left">文件名</th>
				<th>下载速度</th>
				<th>完成率</th>
				<th>操作</th>
			</tr>
<?php
	$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');//修改
	$info=$aria2->tellActive();
	foreach ($info['result'] as $key => $value) {
		if ((!$download_info->gid($value['gid'])) && (!$download_info->hash($value['infoHash']))) {	
			continue;
		}
		echo "<td class=\"text-left\">".$value['bittorrent']['info']['name']."</td>";
		echo "<td>".size($value['downloadSpeed'])."/S</td>";
		echo "<td>".round($value['completedLength']/$value['totalLength']*100,2)."%</td>";
		echo "<td><a href=\"?pause=".$value['gid']."\" class=\" btn btn-danger btn-sm \" >关闭</a></td></tr>";
	}
?>
		</table>
		<hr>
		<span class="text-warning"><b>等待中的任务</b></span>
		<table class="table">
			<tr>
				<th class="text-left">文件名</th>
				<th>下载速度</th>
				<th>完成率</th>
				<th>操作</th>
			</tr>
<?php
	$info=$aria2->tellWaiting(0,1000);
	foreach ($info['result'] as $key => $value) {
		if ((!$download_info->gid($value['gid'])) && (!$download_info->hash($value['infoHash']))) {	
			continue;
		}
		//echo "<tr><td>".$value['gid']."</td>";
		echo "<td class=\"text-left\">".$value['bittorrent']['info']['name']."</td>";
		echo "<td>".size($value['downloadSpeed'])."/S</td>";
		echo "<td>".round($value['completedLength']/$value['totalLength']*100,2)."%</td>";
		echo "<td><a href=\"?unpause=".$value['gid']."\" class=\" btn btn-success btn-sm \" >开始</a></td></tr>";
	}
?>
		</table>
	</div>


<script type="text/javascript">
	$(".fileshow").click(function(){
		var type=$(this).attr("type");
		var name=$(this).text();
		var value=$(this).attr("value");
		switch(type){
			case "img":
				$(".modal-title").html(name);
				$(".modal-body").html("<a href=\""+value+"\"><img style=\"max-width:80%;\" src=\""+value+"\"></a>");
				$("#modal").modal();
			break;
			case "video":
				$(".modal-title").html(name);
				$(".modal-body").html("<video width=\"80%\" autoplay controls id=\"play\" src=\""+value+"\"></video>");
				$("#modal").modal();
			break;
			case "mp3":
				$(".modal-title").html(name);
				$(".modal-body").html("<audio src=\""+value+"\" id=\"play\" autoplay controls>您的浏览器不支持 audio 标签。</audio>");
				$("#modal").modal();
			break;
			case "text":
				$(".modal-title").html(name);
				$(".modal-body").html("<iframe width=\"80%\" height=\"600px\" src="+value+">");
				$("#modal").modal();
			break;
			case "pdf":
				$(".modal-title").html(name);
				$(".modal-body").html("<iframe width=\"80%\" height=\"800px\" src="+value+">");
				$("#modal").modal();
			default:
		}
	})
	$('#modal').on('hidden.bs.modal', function (e) {
  		var play=$("#play")[0];
  			play.pause();
	})
	$(".click_onload").mouseover(function(){
		$(this).addClass("text-primary");
	})
	$(".click_onload").mouseout(function(){
		$(this).removeClass("text-primary");
	})	
	$("#btn-magnet").click(function(){
		var magnet=$("#magnet").val();
		$.get("?url="+magnet,function(data){
			var re=eval("("+data+")");
			if(typeof(mx) != 'undefined' ){
				mx.hide();
			}
			if(re.result){
				mx=Messenger().post("你已经添加一个离线任务！");
			}else{
				mx=Messenger().post("添加失败:"+re.error.message);
			}
		});
		$("#magnet").val("");
	})
	$("#xzz").click(function(){
		var show=$("#showdownload").clone();
		show.removeClass("hide");
		$(".modal-title").html("下载列表");
		$(".modal-body").html(show);
		$("#modal").modal();
	})
	

</script>
</body>
</html>
