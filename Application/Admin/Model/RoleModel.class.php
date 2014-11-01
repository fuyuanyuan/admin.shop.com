<?php
namespace Admin\Model;
use Think\Model;
class RoleModel extends Model 
{
	protected $_validate = array(
		array('role_name', 'require', '角色名称不能为空！', 1),
	);
	
	public function search()
	{
		// 取出admin表总的记录数
		$totalRecord = $this->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		$data = $this->field('a.*,GROUP_CONCAT(b.pri_name) pri_name')->alias('a')->join('LEFT JOIN sh_privilege b ON FIND_IN_SET(b.id,a.privilege_id)')->group('id')->limit($page->firstRow, $page->listRows)->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
	
	protected function _before_insert(&$data, $option)
	{
		if(isset($data['privilege_id']))
			$data['privilege_id'] = implode(',', $data['privilege_id']);
	}
	
	protected function _before_update(&$data, $option)
	{
		if(isset($data['privilege_id']))
			$data['privilege_id'] = implode(',', $data['privilege_id']);
		else 
			$data['privilege_id'] = '';
	}
}