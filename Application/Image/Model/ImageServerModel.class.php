<?php
namespace Image\Model;
use Think\Model;
class ImageServerModel extends Model 
{
	protected $_validate = array(
		array('image_domain', 'require', '域名不能为空!', 1),
		array('image_count', '/^\d+$/', '当前图片的数量必须是一个数字!', 1),
		array('max_image_count', '/^\d+$/', '服务器上最大的图片数量必须是一个数字!', 1),
		array('ftpuser', 'require', 'ftp账号不能为空!', 1),
		array('ftppassword', 'require', 'ftp密码不能为空!', 1),
	);
	
	public function search()
	{
		$where = 1;
		// 在这里再写要搜索的字段
		// 取出总记录数
		$totalCount = $this->where($where)->count();
		// 生成翻页类的对象
		// 第二个参数：每页的条数
		$page = new \Think\Page($totalCount, 15);
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		// 生成翻页的字符串
		$str = $page->show();
		// 取出当前页的数据
		$data = $this->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		return array(
			'data' => $data,
			'page' => $str,
		);
	}
	protected function _before_insert(&$data, $option)
	{
		$data['image_domain'] = rtrim($data['image_domain'], '/');
	}
	public function pickAImageServerInfo()
	{
		return $this->where('image_count < max_image_count')->order('RAND()')->find();
	}
}