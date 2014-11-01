<?php
namespace Image\Model;
class ImageModel
{
	private $_config;
	private $_ftp_timeout = 15;
	private $_ftp_conn = null;
	private $_image_server_info = array();
	private $_image_server_model = null;
	private $_use_ftp = FALSE;
	private static $_self = NULL;
	private $_thumb_file = '';
	private $_thumb_width = '';
	private $_thumb_height = '';
	
	public static function getInstance()
	{
		if(self::$_self === NULL)
			self::$_self = new self();
		return self::$_self;
	}
	private function __construct()
	{
		$this->_config = include(dirname(__FILE__).'/../Conf/config.php');
		if(isset($this->_config['UPLOAD_TYPE']) && strtoupper($this->_config['UPLOAD_TYPE']) == 'FTP')
			$this->_use_ftp = TRUE;
	}
	private function __clone(){}
	private function _ftp_login()
	{
		if($this->_use_ftp && $this->_ftp_conn === NULL)
		{
			$this->_image_server_model = new \Image\Model\ImageServerModel();
			$this->_image_server_model->image_domain = rtrim($this->_image_server_model->image_domain, '/');
			$this->_image_server_info = $this->_image_server_model->pickAImageServerInfo();
			$this->_ftp_conn = @ftp_connect($this->_image_server_model->image_domain, $this->_image_server_model->ftpport, $this->_ftp_timeout);
			if(!$this->_ftp_conn)
				die('图片服务器连接失败！');
			if(!ftp_login($this->_ftp_conn, $this->_image_server_model->ftpuser, $this->_image_server_model->ftppassword))
				die('图片服务器登录失败！');
		}
	}
	/**
	 * 第三个参数：
	 *	一张图片：
	 *  	array(
	 * 			'pre_img' => '',  // 用来显示图片的图片地址
	 * 			'img_url' => '',  // 放在表单的隐藏域中的值
	 *  	)
	 *  多张图片
	 * 		array(
	 * 			array(
	 * 				'pre_img' => '',  // 用来显示图片的图片地址
	 * 				'img_url' => '',  // 放在表单的隐藏域中的值
	 * 				'img_text' => '',  // 图片对应的文本
	 * 			),
	 *			array(
	 * 				'pre_img' => '',  // 用来显示图片的图片地址
	 * 				'img_url' => '',  // 放在表单的隐藏域中的值
	 * 				'img_text' => '',  // 图片对应的文本
	 * 			),
	 * 		)
	 */
	public function show_btn($name, $isMulti = FALSE, $value = array(), $withTxt = TRUE)
	{
		if($isMulti)
		{
			$html = '';
			// 构造初始图片的字符串
			if($value)
			{
				$html = '<ul class="_image_container_li_">';
				foreach ($value as $k => $v)
				{
					$html .= '<li>';
					$html .= '<img width="100px" src="'.$v['pre_img'].'" /><br />';
					if($withTxt)
						$html .= '<input type="text" value="'.$v['img_text'].'" name="text_'.$name.'" />';
					$html .= '<a onclick="this.parentNode.parentNode.removeChild(this.parentNode);if(typeof(_image_after_delete) == \'function\'){_image_after_delete(\''.$v['img_url'].'\');}" href="#">[-]</a><input name="'.$name.'" type="hidden" value="'.$v['img_url'].'">';
					$html .= '</li>';
				}
				$html .= '</ul>';
			}
			echo "<input type='button' onclick='_image_show_upload_form_(this, \"$name\", \"$withTxt\");' value='上传' />$html";
		}
		else 
		{
			$html = '';
			// 有没有初始化的值
			if($value)
				$html = "<ul class='_image_container_li_'><li><img width='100px' src='{$value['pre_img']}' /><br /><a onclick='this.parentNode.parentNode.removeChild(this.parentNode)' href='#'>[-]</a><input name='$name' type='hidden' value='{$value['img_url']}'></li></ul>";
			echo "<input type='button' onclick='_image_show_upload_one_form_(this, \"$name\");' value='上传一张' /><div>$html</div>";
		}
	}
	/**
	 * 这个函数必须在表单的外面调用，建议放到html的最后调用 
	 *
	 */
	public function image_init()
	{
		$root = _PHP_FILE_;
		$html = <<<HTML
	<div id="_image_div_container_" style="width:50%;display:none;position:absolute;border:1px solid #CCC;background:#FFF;";>
	<form id="_image_form_" target="_image_upload_" method="POST" action="$root/Image/Index/index" enctype="multipart/form-data">
		<input type="hidden" id="_image_with_txt_" name="_image_with_txt_" value="1" />
		<input type="hidden" id="_image_name_" name="_image_name_" />
		<input type="hidden" id="_image_is_multi_" name="_image_is_multi_" value="1" />
		<input onchange="this.parentNode.submit();" type="file" name="_image_img_" />
		<input type="button" onclick="_image_hide_form_();" value="关闭" />
		<input type="button" onclick="_image_put_to_form_();" value="确定" />
	</form>
	<div>
	<ul id="_image_container_"></ul>	
	<iframe style="display:none;" name="_image_upload_" width="500" height="500"></iframe>
	<script>
	var _image_current_btn = null;
	function _image_show_upload_form_(o, name, withTxt)
	{
		if(withTxt)
			$("#_image_with_txt_").val(1);
		else
			$("#_image_with_txt_").val(0);
		$("#_image_name_").val(name);
		// 把元素转化JQ的对象
		var obj = $(o);
		// 点击按钮时，把这个按钮变到这个变量中
		_image_current_btn = obj;
		// 获取按钮的位置
		var p = obj.position();
		var form = $("#_image_div_container_");
		form.css({
			"left":p.left+"px",
			"top":p.top+"px",
		});
		form.show();
		$("#_image_is_multi_").val(1);
	}
	function _image_put_to_form_()
	{
		var newu = $("#_image_container_").clone();
		newu.removeAttr("id");
		newu.addClass("_image_container_li_");
		// 把所有的图片放到点击的按钮的后面
		if($("#_image_is_multi_").val() == "1")
			_image_current_btn.after(newu);
		else
			_image_current_btn.next("div").html(newu);
		_image_hide_form_();
		// 清空表单
		_image_clear_form_();
	}
	function _image_clear_form_()
	{
		$("#_image_container_").html("");
		document.getElementById("_image_form_").reset();
	}
	function _image_hide_form_()
	{
		_image_clear_form_();
		$('#_image_div_container_').hide();
	}
	// 上传一张图片的表单
	function _image_show_upload_one_form_(o, name)
	{
		// 设置图片的名字
		$("#_image_name_").val(name);
		// 把元素转化JQ的对象
		var obj = $(o);
		// 点击按钮时，把这个按钮变到这个变量中
		_image_current_btn = obj;
		// 获取按钮的位置
		var p = obj.position();
		var form = $("#_image_div_container_");
		form.css({
			"left":p.left+"px",
			"top":p.top+"px",
		});
		form.show();
		$("#_image_is_multi_").val(0);
	}
	</script>
	<style>
	#_image_container_ li{list-style-type:none;float:left;margin:5px;}
	._image_container_li_ li{list-style-type:none;float:left;margin:5px;}
	</style>
HTML;
		echo $html;
	}
	// 返回值是移动之后的图片地址
	public function moveImgFromTempToImageDir($tmpImgUrl, $dirName)
	{
		$_dirName = $dirName = trim($dirName, '/');
		if(strpos($dirName, './Uploads/') !== 0)
			$dirName = './Uploads/' . $dirName;
			
		if(strpos($tmpImgUrl, './Uploads') !== 0)
			$tmpImgUrl = './Uploads' . $tmpImgUrl;
			
		$imgName = strrchr($tmpImgUrl, '/');  // /53378f24b562e.jpg
		$date = date('Y-m-d');
		if($this->_use_ftp)
		{
			$this->_ftp_login();
			if(!$this->_ftp_mkDir($dirName.'/'.$date))
				return FALSE;
			
			if(!ftp_put($this->_ftp_conn, $dirName.'/'.$date.$imgName, $tmpImgUrl, FTP_BINARY))
				return FALSE;
				
			$this->_image_server_model->execute('UPDATE sh_image_server SET image_count = image_count + 1 WHERE id = '.$this->_image_server_model->id);
			
			$image_domain = $this->_image_server_model->image_domain;
			strpos($image_domain, 'http://') !== 0 && $image_domain = 'http://'.$image_domain;
			return $image_domain.$dirName.'/'.$date.$imgName;
		}
		else 
		{
			if(!is_dir($dirName.'/'.$date))
				mkdir($dirName.'/'.$date, 0777, TRUE);
			if(copy($tmpImgUrl, $dirName.'/'.$date.$imgName))
				return '/'.$_dirName.'/'.$date.$imgName;
			else 
				return FALSE;
		}
	}
	public function unlink($url)
	{
		if($this->_use_ftp)
		{
			$this->_ftp_login();
			if(strpos(strtolower($url), 'http://') === 0)
			{
				$url = parse_url($url);
				$url = $url['path'];
			}
			ftp_delete($this->_ftp_conn, $url);
		}
		else 
			@unlink('./Uploads'.$url);
	}
	public function open($url)
	{
		if(file_exists($url))
		{
			$this->_thumb_file = $url;
			return TRUE;
		}
		else 
			return FALSE;
	}
	public function thumb($thumb_width, $thumb_height)
	{
		$this->_thumb_width = $thumb_width;
		$this->_thumb_height = $thumb_height;
		return $this;
	}
	public function save($save_path)
	{
		static $think_image_model = null;
		
		if($think_image_model === null)
			$think_image_model = new \Think\Image();
			
		$think_image_model->open($this->_thumb_file);
		
		$path = pathinfo($save_path);
		$dir = $path['dirname'];
		$basename = $path['basename'];
		
		if($this->_use_ftp)
		{
			$_tmp_thumb_file = './Uploads/'.$this->_config['TEMP_DIR']. date('Y-m-d') . '/' . $basename;
			$think_image_model->thumb($this->_thumb_width, $this->_thumb_height)->save($_tmp_thumb_file);
			$this->_ftp_mkDir($dir);
			ftp_put($this->_ftp_conn, $save_path, $_tmp_thumb_file, FTP_BINARY);
			$image_domain = $this->_image_server_model->image_domain;
			strpos($image_domain, 'http://') !== 0 && $image_domain = 'http://'.$image_domain;
			return $image_domain.ltrim($save_path, '.');
		}
		else 
		{
			if(!is_dir($dir))
				mkdir($dir, 0777, TRUE);
			$think_image_model->thumb($this->_thumb_width, $this->_thumb_height)->save($save_path);
			return str_replace('./Uploads', '', $save_path);
		}
	}
	private function _ftp_mkDir($path) 
	{
		$this->_ftp_login();
		$dir = explode('/', $path); 
   		$ret = true; 
		for ($i=0; $i<count($dir); $i++) 
		{ 
			if($dir[$i] == '.')
				continue ;
			$path = './' . $dir[$i]; 
			if(!@ftp_chdir($this->_ftp_conn, $path))
			{ 
				if(!@ftp_mkdir($this->_ftp_conn, $path))
				{ 
					$ret = false;
					break;
				}
				ftp_chdir($this->_ftp_conn, $path);	
			}
		}
		ftp_chdir($this->_ftp_conn, '/');
		return $ret; 
	}
	public function saveToTemp($data, $imageName)
	{
		$tmpdir = './Uploads/'.$this->_config['TEMP_DIR'].date('Y-m-d').'/';
		$imgPath = $tmpdir . ltrim($imageName, '/');
		if(!is_dir($tmpdir))
			mkdir($tmpdir, 0777, TRUE);
		file_put_contents($imgPath, $data);
		return $imgPath; 
	}
}