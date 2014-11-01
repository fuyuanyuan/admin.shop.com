<?php
namespace Image\Controller;
use Adminmember\Controller\AdminController;
class ImageServerController extends AdminController 
{
	public function add()
	{
		if(IS_POST)
		{
			$model = D('ImageServer');
			if($model->create())
			{
				if($model->add())
				{
					$this->success('操作成功！', U('lst'));
					exit;
				}
				else 
				{
					if(APP_DEBUG)
						die($model->getLastSql());
					else
						$this->error('发生未错误，请重试！');
				}
			}
			else 
				$this->error($model->getError());
		}
		$this->display();
	}
	public function save($id)
	{
		if(IS_POST)
		{
			$model = D('ImageServer');
			if($model->create())
			{
				if($model->save() !== FALSE)
				{
					$this->success('操作成功！', U('lst'));
					exit;
				}
				else 
				{
					if(APP_DEBUG)
						die($model->getLastSql());
					else
						$this->error('发生未错误，请重试！');
				}
			}
			else 
				$this->error($model->getError());
		}
		// 取出要修改的记录
		$model = M('ImageServer');
		$data = $model->find($id);
		$this->assign('data', $data);
		$this->display();
	}
	// $id :get方式提交过来的id
	public function del($id)
	{
		$model = D('ImageServer');
		$model->delete($id);
		$this->success('操作成功！');
		exit;
	}
	// 批量删除
	public function bdel()
	{
		if(IS_POST && isset($_POST['delid']) && $_POST['delid'])
		{
			$model = D('ImageServer');
			$ids = implode(',', $_POST['delid']);
			$model->where("id IN($ids)")->delete();
		}
		$this->success('操作成功！');
	}
	// 列表页
	public function lst()
	{
		$model = D('ImageServer');
		// 获取带翻页数据
		$data = $model->search();
		$this->assign('data', $data['data']);
		$this->assign('page', $data['page']);
		$this->display();
	}
}