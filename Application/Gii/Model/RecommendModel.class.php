<?php
namespace Gii\Model;
use Think\Model;
class RecommendModel extends Model 
{
	protected $_validate = array(
		array('rec_name', 'require', '推荐位名称不能为空！', 1),
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
}