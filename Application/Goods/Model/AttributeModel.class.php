<?php
namespace Goods\Model;
use Think\Model;
class AttributeModel extends Model 
{
	protected $_validate = array(
		array('attr_name', 'require', '属性名称不能为空！', 1),
		array('attr_type', 'require', '属性类型不能为空！', 1),
		array('type_id', 'require', '类型的id不能为空！', 1),
		);
	
	public function search($type_id)
	{
		// 取出admin表总的记录数
		$totalRecord = $this->where('type_id='.$type_id)->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		$data = $this->where('type_id='.$type_id)->limit($page->firstRow, $page->listRows)->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
	protected function _before_insert(&$data, $option)
	{
		$data['attr_value'] = str_replace('，', ',', $data['attr_value']);
	}
	protected function _before_update(&$data, $option)
	{
		$data['attr_value'] = str_replace('，', ',', $data['attr_value']);
	}
}