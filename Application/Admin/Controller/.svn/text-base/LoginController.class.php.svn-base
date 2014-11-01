<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller 
{
	public function login()
	{
		// 如果提交就处理表单
		if(IS_POST)
		{
			// 处理表单
			//1.生成管理员模型
			// 生成模型的方法：
			// 1. $model = M('Admin'); :只调用TP自带的方法时用M
			// 2. $model = D('Admin'); : 如果要用到自己在模型中写的代码时就要使用D来生成
			// 3. $model = new \Admin\Model\AdminModel();: 如果要调用其他模块中的模型时使用这种方法
			$model = D('Admin');
			// 接收表单并且验证验证并且过滤
			if($model->create($_POST, 4))
			{
				// 执行登录方法
				$ret = $model->login();
				if($ret === TRUE)
				{
					$this->success('登录成功！', U('Index/index'));
					exit;
				}
				else 
				{
					$error = $ret == 1 ? '用户名不存在' : '密码错误';
					$this->error($error);
				}
			}
			else 
				// 如果验证失败，取出失败的原因
				$this->error($model->getError());
		}
		// 显示表单
		$this->display();
	}
	public function chkCodeImg()
	{
		$Verify = new \Think\Verify();
		$Verify->entry();
	}
	public function logout()
	{
		$model = D('Admin');
		$model->logout();
		redirect(U('Admin/Login/login'));
	}
}