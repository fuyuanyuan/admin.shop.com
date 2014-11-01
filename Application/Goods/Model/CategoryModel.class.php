<?php
namespace Goods\Model;
use Think\Model;
class CategoryModel extends Model 
{
	protected $_validate = array(
		array('cat_name', 'require', '分类名称不能为空！', 1),
		array('parent_id', 'require', '上级分类不能为空！', 1),
	);
	
	public function getCatTree()
	{
		$data = $this->select();
		return $this->_reSort($data);
	}
	
	private function _reSort($data, $parent_id=0, $level=0)
	{
		static $ret = array();
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				$v['level'] = $level;
				$ret[] = $v;
				// 找子级
				$this->_reSort($data, $v['id'], $level+1);
			}
		}
		return $ret;
	}
	
	public function getChildrenId($catId)
	{
		$data = $this->select();
		return $this->_findChildrenId($data, $catId);
	}
	
	private function _findChildrenId($data, $parent_id=0, $isClear = TRUE)
	{
		static $ret = array();
		// 如果是第一次访问先清空数组在递归时不清空
		if($isClear)
			$ret = array();
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				$ret[] = $v['id'];
				// 找子级
				$this->_findChildrenId($data, $v['id'], FALSE);
			}
		}
		return $ret;
	}
	
	// 在删除一个分类之前这个函数会自动调用
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
			// 所有子分类的ID
			$children = array();
			$id = explode(',', $data['where']['id'][1]);
			// 找出每个分类的子分类
			foreach ($id as $k => $v)
			{
				// 找子分类的ID
				$_children = $this->getChildrenId($v);
				if($_children)
					$children = array_merge($children, $_children);
			}
			// 去重
			$children = array_unique($children);
		}
		else 
			// 取出所有的子分类的ID
			$children = $this->getChildrenId($data['where']['id']);

		if($children)
		{
			$children = implode(',', $children);
			$this->execute("DELETE FROM {$data['table']} WHERE id IN($children)");
		}
	}
}

























