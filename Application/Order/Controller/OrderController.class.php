<?php
namespace Order\Controller;
use Admin\Controller\AdminController;
class OrderController extends AdminController 
{
	// 列表页
	public function lst()
	{
		$model = D('Order');
		// 调用我们自己写的search方法，方法会返回数据以及翻页的字符串
		$data = $model->search();
		$this->assign('data', $data['data']);
		$this->assign('page', $data['page']);
		// 显示页面
		$this->display();
	}
	public function view($id)
	{
		// 取出定单的基本信息
		$orderModel = M('Order');
		$info = $orderModel->find($id);
		// 取出定单中所有的商品
		$og = M('OrderGoods');
		$goods = $og->where('order_id='.$id)->select();
		$this->assign(array(
			'info' => $info,
			'goods' => $goods,
		));
		$this->display();
	}
	public function setPaid($id)
	{
		$orderModel = M('Order');
		$orderModel->where('id='.$id)->save(array(
			'pay_status' => '已支付',
		));
		$this->success('操作成功');
		exit;
	}
	public function setPosted($id)
	{
		$orderModel = M('Order');
		$orderModel->where('id='.$id)->save(array(
			'post_status' => '已发送',
			'post_time' => time(),
		));
		$this->success('操作成功');
		exit;
	}
	public function setRefund($id)
	{
		$orderModel = M('Order');
		$orderModel->field('member_id,total_price')->find($id);
		$orderModel->where('id='.$id)->save(array(
			'post_status' => '已退货',
		));
		// 并退钱
		$orderModel->execute("UPDATE sh_member SET money=money+{$orderModel->total_price} WHERE id={$orderModel->member_id}");
		$this->success('操作成功');
		exit;
	}
}