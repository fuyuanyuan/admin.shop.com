<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model 
{
	protected $_validate = array(
		array('username', 'require', '用户名不能为空！', 1),
		array('password', 'require', '密码不能为空！', 1, 'regex', 1),
		array('password', 'require', '密码不能为空！', 1, 'regex', 4),
		// 第六个参数的意思：1：添加时验证 2：修改时验证 3：所有情况都验证
		array('rpassword', 'password', '两次密码不一致！', 1, 'confirm', 1),
		array('rpassword', 'password', '两次密码不一致！', 1, 'confirm', 2),
		// 第六个参数的意思：4：我们规定是在登录时验证
		array('chk_code', 'require', '验证码不能为空！', 1, 'regex', 4),
		// 第五个参数：callback是指用回调函数chk_code验证
		array('chk_code', 'chk_code', '验证码输入错误！', 1, 'callback', 4),
		array('username', '', '用户名已经存在！', 1, 'unique', 1),
		array('username', '', '用户名已经存在！', 1, 'unique', 2),
	);
	
	protected function chk_code($code)
	{
		$verify = new \Think\Verify();
		return $verify->check($code);
	}
	
	public function login()
	{
		// 先把表单中接收的密码存到一个变量中，因为调用find方法之后$this->password就不再是表单中的密码了。
		$password = $this->password;
		// TP中查询数据库的两个方法：
		// find  ： 返回一条记录（一维数组）
		// select ： 返回多条记录（二维数组）
		// 根据用户名查询数据库中
		// 注意：find方法之后，会用户数据库中取出的值覆盖当前模型中所有字段的值
		$info = $this->where("username='{$this->username}'")->find();
		// 判断用户名是否存在
		if($info)
		{
			if($info['password'] == md5(md5($password) . $info['salt']))
			{
				// 登录成功之后把用户的id和用户名存到session中
				session('id', $info['id']);
				session('username', $info['username']);
				// 取出管理员的权限并存到session中
				$this->_putPriToSession($info['role_id']);
				return TRUE;
			}
			else 
				return 2;
		}
		else 
			return 1;
	}
	// 根据角色ID取出所有权限
	private function _putPriToSession($role_id)
	{
		// 先取出这个角色的权限id
		$roleModel = M('Role');
		$priId = $roleModel->field('privilege_id')->find($role_id);	
		$priId = $priId['privilege_id'];
		/*************** 把用户可以访问的地址存到session中并且把用户可以显示的按钮放到session中 ***************/
		if($priId == '*')
		{
			// 如果是超级管理员，那么取出所有的权限当作按钮
			$priModel = D('Privilege');
			$menu = $priModel->getPriTree(2);
			session('privilege', '*');
		}
		elseif ($priId != '')
		{
			// 根据权限的ID取出对应的信息
			$priModel = D('Privilege');
			$data = $priModel->where("id IN($priId)")->order('CONCAT(pri_path,"-",id) ASC')->select();
			$menu = array();
			$_arr = array();
			// 数组中存的是当前管理员有权访问的地址
			foreach ($data as $k => $v)
			{
				// 抽取出前两级的权限做为按钮
				if($v['pri_level'] <= 1)
					$menu[] = $v;
				$_arr[] = $v['module_name'].'/'.$v['controller_name'].'/'.$v['action_name']; //  Admin/Admin/lst
			}
			session('privilege', $_arr);
		}
		else 
		{
			session('privilege', '');
		}
		// 重新处理$menu的二维数组
		if($menu)
		{
			/**
			 * 处理前：
			 * array
  0 => 
    array
      'id' => string '1' (length=1)
      'pri_name' => string '绠＄悊鍛樻ā鍧�' (length=15)
      'module_name' => string 'null' (length=4)
      'controller_name' => string 'null' (length=4)
      'action_name' => string 'null' (length=4)
      'parent_id' => string '0' (length=1)
      'pri_level' => string '0' (length=1)
      'pri_path' => string '0' (length=1)
  1 => 
    array
      'id' => string '3' (length=1)
      'pri_name' => string '鏉冮檺鍒楄〃' (length=12)
      'module_name' => string 'Admin' (length=5)
      'controller_name' => string 'Privilege' (length=9)
      'action_name' => string 'lst' (length=3)
      'parent_id' => string '1' (length=1)
      'pri_level' => string '1' (length=1)
      'pri_path' => string '0-1' (length=3)
  2 => 
    array
      'id' => string '8' (length=1)
      'pri_name' => string '绠＄悊鍛樺垪琛�' (length=15)
      'module_name' => string 'Admin' (length=5)
      'controller_name' => string 'Admin' (length=5)
      'action_name' => string 'lst' (length=3)
      'parent_id' => string '1' (length=1)
      'pri_level' => string '1' (length=1)
      'pri_path' => string '0-1' (length=3)
      处理后：
      array
  0 => 
    array
      'id' => string '1' (length=1)
      'pri_name' => string '绠＄悊鍛樻ā鍧�' (length=15)
      'module_name' => string 'null' (length=4)
      'controller_name' => string 'null' (length=4)
      'action_name' => string 'null' (length=4)
      'parent_id' => string '0' (length=1)
      'pri_level' => string '0' (length=1)
      'pri_path' => string '0' (length=1)
      'children' => 
        array
          0 => 
            array
              ...
          1 => 
            array
              ...
              后两个按钮放到了第一个按钮的children里现在就有层级关系
			 */
			$_menu = array();
			foreach ($menu as $k => $v)
			{
				if($v['parent_id'] == 0)
				{
					// 取出这个顶级权限的子权限
					foreach ($menu as $k1 => $v1)
					{
						if($v1['parent_id'] == $v['id'])
						{
							$menu[$k]['children'][] = $v1;
						}
					}
					$_menu[] = $menu[$k];
				}
			}
			session('menu', $_menu);
		}
	}
	
	public function logout()
	{
		session(null);
	}
	public function search()
	{
		// 取出admin表总的记录数
		$totalRecord = $this->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		$data = $this->alias('a')->field('a.*,b.role_name')->join('LEFT JOIN sh_role b ON a.role_id=b.id')->limit($page->firstRow, $page->listRows)->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
	// 在插入数据库之前这个函数会自动调用
	// $data ： 就是要插入到数据库中的数据的数组
	protected function _before_insert(&$data, $option)
	{
		// 先生成6位的密钥
		$data['salt'] = substr(uniqid(), -6);
		$data['password'] = md5(md5($data['password']) . $data['salt']);
	}
	// 在修改之前自动被调用
	protected function _before_update(&$data, $option)
	{
		// 如果是超级管理员那么不允许修改角色
		if($option['where']['id'] == 1)
			unset($data['role_id']);
		// 如果密码为空就不修改这个字段
		if(!$data['password'])
			unset($data['password']);
		else 
		{
			// 先生成6位的密钥
			$data['salt'] = substr(uniqid(), -6);
			$data['password'] = md5(md5($data['password']) . $data['salt']);
		}
	}
}