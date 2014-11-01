<?php
namespace News\Model;
use Think\Model;
class NewsCatModel extends Model 
{
	protected $_validate = array(
		array('cat_name', 'require', '分类名称不能为空！', 1),
		array('is_help', 'require', '是否帮助@radio|否-是不能为空！', 1),
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