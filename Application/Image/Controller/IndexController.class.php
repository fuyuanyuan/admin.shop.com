<?php
namespace Image\Controller;
use Think\Controller;
class IndexController extends Controller 
{
	private $_temp_dir;
	private $_domain;
	public function __construct()
	{
		$this->_temp_dir = C('TEMP_DIR');
		$this->_domain = 'http://'.$_SERVER['SERVER_NAME'] . '/Uploads';
	}
	public function index()
	{
		if(IS_POST)
		{
			$upload = new \Think\Upload();// 实例化上传类
			// 设置的值必须小于等于php.ini中的值upload_max_filesize的值
		    $upload->maxSize =     1024*1024 ;// 1M
		    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->savePath  =      $this->_temp_dir;
		    // 上传文件 
		    $info   =   $upload->upload();
		    if(!$info) {// 上传错误提示错误信息
		        $this->error($upload->getError());
		    }else{// 上传成功
		    	// 生成缩略图
		    	$image = new \Think\Image();
		    	$image->open('./Uploads/'.$info['_image_img_']['savepath'].$info['_image_img_']['savename']);
		    	$image->thumb(100, 100)->save('./Uploads/'.$info['_image_img_']['savepath'].'sm_'.$info['_image_img_']['savename']);
		    	// 多张图片时
		    	if($_POST['_image_is_multi_'] == 1)
		    	{
		    		// 预览图片是否带文本框
		    		$_txt_input = '';
		    		if($_POST['_image_with_txt_'] == 1)
		    			$_txt_input = "<input type='text' name='text_{$_POST['_image_name_']}' />";
			        $url = ltrim($info['_image_img_']['savepath'], '.') . $info['_image_img_']['savename'];
			        $pre_url = $this->_domain . ltrim($info['_image_img_']['savepath'], '.') . 'sm_'.$info['_image_img_']['savename'];
			       	$url_html = "<li><img src='$pre_url' /><br />{$_txt_input}<a onclick='this.parentNode.parentNode.removeChild(this.parentNode)' href='#'>[-]</a><input name='{$_POST['_image_name_']}' type='hidden' value='$url' /></li>";
			        echo "<script>
			        parent.document.getElementById('_image_container_').innerHTML += \"$url_html\";
			        </script>";
		    	}
			    else 
			    {
			    	$url = ltrim($info['_image_img_']['savepath'], '.') . $info['_image_img_']['savename'];
			        $pre_url = $this->_domain . ltrim($info['_image_img_']['savepath'], '.') . 'sm_'.$info['_image_img_']['savename'];
			       	$url_html = "<li><img src='$pre_url' /><br /><a onclick='this.parentNode.parentNode.removeChild(this.parentNode)' href='#'>[-]</a><input name='{$_POST['_image_name_']}' type='hidden' value='$url' /></li>";
			        echo "<script>
			        parent.document.getElementById('_image_container_').innerHTML = \"$url_html\";
			        </script>";
			    }
		        exit;
		    }
		}
		$this->display();
	}
}