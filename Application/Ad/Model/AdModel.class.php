<?php
namespace Ad\Model;
use Think\Model;
class AdModel extends Model 
{
	protected $_validate = array(
		array('pos_id', 'require', '广告位不能为空！', 1),
		array('ad_name', 'require', '广告名称不能为空！', 1),
		array('ad_type', 'require', '广告类型不能为空！', 1),
		array('is_on', 'require', '是否启用不能为空！', 1),
	);
	
	public function search()
	{
		// 取出admin表总的记录数
		$totalRecord = $this->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		$data = $this->field('a.*,b.pos_name')->alias('a')->join('LEFT JOIN sh_ad_pos b ON a.pos_id=b.id')->limit($page->firstRow, $page->listRows)->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
	
	protected function _before_insert(&$data, $option)
	{
		if ($data['ad_type'] == 'img')
		{
			if($_FILES['ad_img']['tmp_name'])
			{
				$upload = new \Think\Upload();// 实例化上传类
			    $upload->maxSize = 3145728 ;// 图片最大3M
			    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->savePath = 'Ad/'; // 设置附件上传（子）目录
			    // 上传文件 
			    $info = $upload->upload(array('ad_img'=>$_FILES['ad_img']));
			    $data['ad_img'] = $info['ad_img']['savepath'] . $info['ad_img']['savename'];
			}
		}
		// 如果新添加的这个是启用的状态，那么需要先把之前的广告设置为非启用
		if($data['is_on'] == '是')
		{
			$this->where('pos_id='.$data['pos_id'].' AND is_on = "是"')->save(array(
				'is_on' => '否',
			));
		}
	}
	
	// 判断表单中有没有图片
	private function _hasImg($data)
	{
		// 循环数据中每一项只要有一项不为空就代表有图片
		foreach ($data['tmp_name'] as $k => $v)
		{
			if($v)
				return TRUE;
		}
		return FALSE;
	}
	
	protected function _after_insert($data)
	{
		if ($data['ad_type'] == 'jq')
		{
			if($this->_hasImg($_FILES['jq_img']))
			{
				$upload = new \Think\Upload();// 实例化上传类
			    $upload->maxSize = 3145728 ;// 图片最大3M
			    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->savePath = 'Ad/'; // 设置附件上传（子）目录
			    // 上传文件 
			    $info = $upload->upload(array('jq_img'=>$_FILES['jq_img']));
			   	// 循环每一张图片生成缩略图
			   	if($info)
			   	{
			   		$gpModel = M('JqInfo');
			   		// 循环每一张图片插入到jqinfo表
			   		foreach ($info as $k => $v)
			   		{
					    // 把上传之后图片的路径存到数据库中
					    $gpModel->add(array(
					    	'ad_id' => $data['id'],
					    	'ad_link' => $_POST['jq_link'][$k],
					    	'img' => $v['savepath'] . $v['savename'],
					    ));
			   		}
			   	}
			}
		}
	}
	
	public function _before_update(&$data, $option)
	{
		// 如果上传了新图
		if ($data['ad_type'] == 'img')
		{
			if($_FILES['ad_img']['tmp_name'])
			{
				$upload = new \Think\Upload();// 实例化上传类
			    $upload->maxSize = 3145728 ;// 图片最大3M
			    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->savePath = 'Ad/'; // 设置附件上传（子）目录
			    // 上传文件 
			    $info = $upload->upload(array('ad_img'=>$_FILES['ad_img']));
			    // 删除原图'
			    if($data['ad_img'])
			   		@unlink(IMG_URL_HD . $data['ad_img']);
			   	// 设置新图的地址
			    $data['ad_img'] = $info['ad_img']['savepath'] . $info['ad_img']['savename'];
			}
		}
		/******************** 如果 是JQ**********************/
		if ($data['ad_type'] == 'jq')
		{
			$gpModel = M('JqInfo');
			// // 上传了新图片
			if($this->_hasImg($_FILES['jq_img']))
			{
				$upload = new \Think\Upload();// 实例化上传类
			    $upload->maxSize = 3145728 ;// 图片最大3M
			    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->savePath = 'Ad/'; // 设置附件上传（子）目录
			    // 上传文件 
			    $info = $upload->upload(array('jq_img'=>$_FILES['jq_img']));
			   	// 循环每一张图片生成缩略图
			   	if($info)
			   	{
			   		// 循环每一张图片插入到jqinfo表
			   		foreach ($info as $k => $v)
			   		{
					    // 把上传之后图片的路径存到数据库中
					    $gpModel->add(array(
					    	'ad_id' => $option['where']['id'],
					    	'ad_link' => $_POST['jq_link'][$k],
					    	'img' => $v['savepath'] . $v['savename'],
					    ));
			   		}
			   	}
			}
			/************************** 修改原数据 ***********************************/
			
			// 先上传修改的图片
			if($this->_hasImg($_FILES['jq_img_e']))
			{
				$upload = new \Think\Upload();// 实例化上传类
			    $upload->maxSize = 3145728 ;// 图片最大3M
			    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->savePath = 'Ad/'; // 设置附件上传（子）目录
			    // 上传文件 
			    $info = $upload->upload(array('jq_img_e'=>$_FILES['jq_img_e']));
			}
			if($_POST['jq_link_e'])
			{
				$_k = 0;
				foreach ($_POST['jq_link_e'] as $k => $v)
				{
					// 判断有没有对应上传新的图片
					if(isset($info[$_k]))
					{
						// 如果上传了新图就把旧图删除然后更新
						$gpModel->field('img')->find($k);
						@unlink(IMG_URL_HD . $gpModel->img);
						// 修改
						$gpModel->where('id='.$k)->save(array(
							'ad_link' => $v,
							'img' => $info[$_k]['savepath'] . $info[$_k]['savename'],
						));
					}
					else 
					{
						// 如果没有上传新图就不修改图片
						$gpModel->where('id='.$k)->save(array(
							'ad_link' => $v,
						));
					}
					$_k++;
				}
			}
			
		}
		// 如果新添加的这个是启用的状态，那么需要先把之前的广告设置为非启用
		if($data['is_on'] == '是')
		{
			$this->where('pos_id='.$data['pos_id'].' AND is_on = "是"')->save(array(
				'is_on' => '否',
			));
		}
	}
	protected function _before_delete($data)
	{
		/**
		 * 当前批量删除时$data的结构：
		 *   'where' => 
			    array
			      'id' => 
			        array
			          0 => string 'IN' (length=2)
			          1 => string '1,2,3,12,4,5,6,7,9,11,8,10' (length=26)
			  'table' => string 'sh_category' (length=11)
			  'model' => string 'Category' (length=8)
  
		  当删除一个时$data的结构是：
		  array
		  'where' => 
		    array
		      'id' => int 1
		  'table' => string 'sh_category' (length=11)
		  'model' => string 'Category' (length=8)
		 */
		if(is_array($data['where']['id']))
		{
			// 把所有要删除的记录取出来
			$data = $this->where("id IN({$data['where']['id'][1]})")->select();
			$jqModel = M('JqInfo');
			// 循环每一个要删除的广告一个一个判断是否有图片
			foreach ($data as $k => $v)
			{
				if($v['ad_type'] == 'img' && $v['ad_img'])
				{
					@unlink(IMG_URL_HD . $v['ad_img']);
				}
				if($v['ad_type'] == 'jq')
				{
					// 取出相应图片删除掉
					$img = $jqModel->where('ad_id='.$v['id'])->select();
					foreach ($img as $k1 => $v1)
					{
						@unlink(IMG_URL_HD . $v1['img']);
					}
					// 从数据库中把JQ的记录删除掉
					$jqModel->where('ad_id='.$v['id'])->delete();
				}
			}
		}
		else 
		{
			$this->find($data['where']['id']);
			if($this->ad_type == 'img' && $this->ad_img)
			{
				@unlink(IMG_URL_HD . $this->ad_img);
			}
			if($this->ad_type == 'jq')
			{
				$jqModel = M('JqInfo');
				// 取出相应图片删除掉
				$img = $jqModel->where('ad_id='.$data['where']['id'])->select();
				foreach ($img as $k => $v)
				{
					@unlink(IMG_URL_HD . $v['img']);
				}
				// 从数据库中把JQ的记录删除掉
				$jqModel->where('ad_id='.$data['where']['id'])->delete();
			}
		}
	}
}