<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
class RoleController extends AdminController 
{
	// 添加
	public function add()
	{
		if(IS_POST)
		{
			$model = D('Role');
			if($model->create())
			{
				if($model->add())
				{
					$this->success('添加成功！', U('lst'));
					exit;
				}
				else 
					$this->error('添加失败，请重试！');
			}
			else 
				$this->error($model->getError());
		}
		// 取出所有的权限
		$priModel = D('Privilege');
		$priData = $priModel->getPriTree(3);
		$this->assign('priData', $priData);
		// 显示表单
		$this->display();
	}
	public function save($id)
	{
		if($id == 1)
			$this->error('不允许修改超级管理员');
		$model = D('Role');
		if(IS_POST)
		{
			if($model->create())
			{
				if($model->save() !== FALSE)
				{
					$this->success('修改成功！', U('lst'));
					exit;
				}
				else 
					$this->error('修改失败，请重试！');
			}
			else 
				$this->error($model->getError());
		}
		// 取出所有的权限
		$priModel = D('Privilege');
		$priData = $priModel->getPriTree(3);
		$this->assign('priData', $priData);
		// 先取出原数据
		$data = $model->find($id);
		$this->assign('data', $data);
		// 显示表单
		$this->display();
	}
	public function del($id)
	{
		if($id > 1)
		{
			$model = D('Role');
			$model->delete($id);
		}
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 批量删除
	public function bdel()
	{
		if(isset($_POST['did']))
		{
			// 去重
			$_POST['did'] = array_unique($_POST['did']);
			// 找到有没有1
			$key = array_search(1, $_POST['did']);
			// 如果1存在就删除
			if($key !== FALSE)
				// 把1从数组中删除掉
				unset($_POST['did'][$key]);
			// 如果数组中还有元素
			if($_POST['did'])
			{
				// 先把ID的数组转化成一个字符串 格式：1,2,3,4,5,56,7
				$id = implode(',', $_POST['did']);
				$model = D('Role');
				$model->delete($id);
			}
		}
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 列表页
	public function lst()
	{
		$model = D('Role');
		// 调用我们自己写的search方法，方法会返回数据以及翻页的字符串
		$data = $model->search();
		$this->assign('data', $data['data']);
		$this->assign('page', $data['page']);
		// 显示页面
		$this->display();
	}
	
}