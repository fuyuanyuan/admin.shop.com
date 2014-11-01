<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
class PrivilegeController extends AdminController 
{
	// 添加
	public function add()
	{
		if(IS_POST)
		{
			$model = D('Privilege');
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
		// 取出所有的极限
		$priModel = D('Privilege');
		$priData = $priModel->getPriTree();
		$this->assign('priData', $priData);
		// 显示表单
		$this->display();
	}
	public function save($id)
	{
		$model = D('Privilege');
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
		// 取出所有的极限
		$priModel = D('Privilege');
		$priData = $priModel->getPriTree();
		$this->assign('priData', $priData);
		// 先取出原数据
		$data = $model->find($id);
		$this->assign('data', $data);
		// 显示表单
		$this->display();
	}
	public function del($id)
	{
		$model = D('Privilege');
		$model->delete($id);
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 批量删除
	public function bdel()
	{
		if(isset($_POST['did']))
		{
			// 先把ID的数组转化成一个字符串 格式：1,2,3,4,5,56,7
			$id = implode(',', $_POST['did']);
			$model = D('Privilege');
			$model->delete($id);
		}
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 列表页
	public function lst()
	{
		$model = D('Privilege');
		$data = $model->getPriTree(3);
		$this->assign('data', $data);
		// 显示页面
		$this->display();
	}
	
}