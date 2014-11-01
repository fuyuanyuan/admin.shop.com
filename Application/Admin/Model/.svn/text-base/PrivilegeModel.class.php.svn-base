<?php
namespace Admin\Model;
use Think\Model;
class PrivilegeModel extends Model 
{
	protected $_validate = array(
		array('pri_name', 'require', '权限名称不能为空！', 1),
		array('module_name', 'require', '模块名称不能为空！', 1),
		array('controller_name', 'require', '控制器名称不能为空！', 1),
		array('action_name', 'require', '方法名称不能为空！', 1),
	);
	public function getPriTree($level = 2)
	{
		return $this->where('pri_level<'.$level)->order('CONCAT(pri_path,"-",id) ASC')->select();
	}
	protected function _before_insert(&$data, $option)
	{
		// 如果是了个权限的子权限,那么就计算子权限的path是多少
		if($data['parent_id'])
		{
			// 取出上级权限的id和path
			$path = $this->field('id,pri_path,pri_level')->find($data['parent_id']);
			// 设置子权限的pri_path字段
			$data['pri_path'] = $path['pri_path'].'-'.$path['id'];
			// 设置当前是第几级的
			$data['pri_level'] = $path['pri_level']+1;
		}
	}
	protected function _before_update(&$data, $option)
	{
		// 如果上级权限不等于0就重新计算新的pri_path字段
		if($data['parent_id'])
		{
			// 取出上级权限的id和path
			$path = $this->field('id,pri_path,pri_level')->find($data['parent_id']);
			// 设置子权限的pri_path字段
			$data['pri_path'] = $path['pri_path'].'-'.$path['id'];
			// 设置当前是第几级的
			$data['pri_level'] = $path['pri_level']+1;
		}
		else 
		{
			$data['pri_path'] = '0';
			// 设置当前是第几级的
			$data['pri_level'] = 0;
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
			$_attr = explode(',', $data['where']['id'][1]);
			// 循环每一个要删除的权限，找出子权限并删除
			foreach ($_attr as $k => $v)
			{
				// 删除所有的子权限
				$this->execute("DELETE FROM {$data['table']} WHERE CONCAT('-',pri_path,'-') LIKE '%-{$v}-%'");
			}
		}
		else 
		{
			// 删除所有的子权限
			$this->execute("DELETE FROM {$data['table']} WHERE CONCAT('-',pri_path,'-') LIKE '%-{$data['where']['id']}-%'");
		}
	}
}