<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller 
{
	public function __construct()
	{
		parent::__construct();
		// 验证登录
		if(!session('id'))
			$this->error('必须先登录！', U('Admin/Login/login'));
		// 验证权限
		$pri = session('privilege');
		// 后台首页无论有没有权限都可以访问
		if(strtolower(MODULE_NAME) == 'admin' && strtolower(CONTROLLER_NAME) == 'index' && in_array(strtolower(ACTION_NAME), array('index','left','top','right')))
			return TRUE;
		if($pri != '*' && ($pri == '' || !in_array(MODULE_NAME .'/'. CONTROLLER_NAME .'/'. ACTION_NAME, $pri)))
			$this->error('无权访问');
	}
	// 添加
	public function add()
	{
		if(IS_POST)
		{
			$model = D('Admin');
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
		// 取出所有的角色
		$roleModel = M('Role');
		$roleData = $roleModel->select();
		$this->assign('roleData', $roleData);
		// 显示表单
		$this->display();
	}
	public function save($id)
	{
		$model = D('Admin');
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
		// 取出所有的角色
		$roleModel = M('Role');
		$roleData = $roleModel->select();
		$this->assign('roleData', $roleData);
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
			$model = D('Admin');
			$model->delete($id);
		}
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 批量删除
	public function bdel()
	{
		// 先判断数组中有没有1，如果有就删除掉
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
				$model = D('Admin');
				$model->delete($id);
			}
		}
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 列表页
	public function lst()
	{
		$model = D('Admin');
		// 调用我们自己写的search方法，方法会返回数据以及翻页的字符串
		$data = $model->search();
		$this->assign('data', $data['data']);
		$this->assign('page', $data['page']);
		// 显示页面
		$this->display();
	}
	
}