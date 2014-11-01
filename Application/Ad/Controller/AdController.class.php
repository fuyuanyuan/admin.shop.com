<?php
namespace Ad\Controller;
use Admin\Controller\AdminController;
class AdController extends AdminController 
{
	// 添加
	public function add()
	{
		if(IS_POST)
		{
			$model = D('Ad');
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
				$this->error($model->getError(), U('add'));
		}
		// 取出所有的广告位
		$apModel = M('AdPos');
		$apData = $apModel->select();
		$this->assign('apData', $apData);
		// 显示表单
		$this->display();
	}
	public function save($id)
	{
		$model = D('Ad');
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
				$this->error($model->getError(), U('save', array('id'=>$id)));
		}
		// 取出所有的广告位
		$apModel = M('AdPos');
		$apData = $apModel->select();
		$this->assign('apData', $apData);
		// 先取出原数据
		$data = $model->find($id);
		$this->assign('data', $data);
		// 如果广告类型是jq那么需要再取出JQ的信息
		if($data['ad_type'] == 'jq')
		{
			$jqModel = M('JqInfo');
			$jqData = $jqModel->where('ad_id='.$id)->select();
			$this->assign('jqData', $jqData);
		}
		// 显示表单
		$this->display();
	}
	public function del($id)
	{
		$model = D('Ad');
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
			$model = D('Ad');
			$model->delete($id);
		}
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 列表页
	public function lst()
	{
		$model = D('Ad');
		// 调用我们自己写的search方法，方法会返回数据以及翻页的字符串
		$data = $model->search();
		$this->assign('data', $data['data']);
		$this->assign('page', $data['page']);
		// 显示页面
		$this->display();
	}
	public function ajaxDelImg($id)
	{
		$model = M('JqInfo');
		$model->field('img')->find($id);
		@unlink(IMG_URL_HD . $model->img);
		$model->delete($id);
	}
}