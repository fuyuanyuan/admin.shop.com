<?php
namespace Goods\Model;
use Think\Model;
class BrandModel extends Model 
{
	protected $_validate = array(
		array('brand_name', 'require', '品牌名称不能为空！', 1),
	);
	
	public function search()
	{
		// 取出admin表总的记录数
		$totalRecord = $this->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		$data = $this->limit($page->firstRow, $page->listRows)->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
	
	protected function _before_insert(&$data, $option)
	{
		/** 如果有图片就上传图片 **/
		if($_FILES['logo']['tmp_name'])
		{
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize = 3145728 ;// 图片最大3M
		    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->savePath = 'Brand/'; // 设置附件上传（子）目录
		    // 上传文件 
		    $info = $upload->upload();
		    // 把上传之后图片的路径存到数据库中
		    $data['logo'] = $info['logo']['savepath'] . $info['logo']['savename'];
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
			// 先取出要删除的品牌的logo
			$logo = $this->field('logo')->where("id IN({$data['where']['id'][1]}) AND logo != ''")->select();
			// 循环每一个LOGO图片并删除
			foreach ($logo as $k => $v)
			{
				// @：错误抵制符：如果函数出错，那么就忽略掉
				@unlink(IMG_URL_HD . $v['logo']);	
			}
		}
		else 
		{
			$logo = $this->field('logo')->where("id = {$data['where']['id']} AND logo != ''")->select();
			@unlink(IMG_URL_HD . $logo[0]['logo']);	
		}
	}
	protected function _before_update(&$data, $option)
	{
		/** 如果上传了新图片 **/
		if($_FILES['logo']['tmp_name'])
		{
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize = 3145728 ;// 图片最大3M
		    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->savePath = 'Brand/'; // 设置附件上传（子）目录
		    // 上传文件 
		    $info = $upload->upload();
		    // 如果有原图就删除
		    if($data['logo'])
		    {
		    	@unlink(IMG_URL_HD .  $data['logo']);
		    }
		    // 用新图的地址覆盖原图
		    $data['logo'] = $info['logo']['savepath'] . $info['logo']['savename'];
		}
	}
}