<?php
namespace Member\Model;
use Think\Model;
class MemberLevelModel extends Model 
{
	protected $_validate = array(
		array('level_name', 'require', '级别名称不能为空！', 1),
		array('num_bottom', 'require', '积分下限不能为空！', 1),
		array('num_top', 'require', '积分上限不能为空！', 1),
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