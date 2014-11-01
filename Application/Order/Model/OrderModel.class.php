<?php
namespace Order\Model;
use Think\Model;
class OrderModel extends Model 
{
	protected $_validate = array(
		array('order_sn', 'require', '定单编号不能为空！', 1),
		array('addtime', 'require', '下单时间不能为空！', 1),
		array('pay_status', 'require', '支付状态@radio|未支付-已支付不能为空！', 1),
		array('post_status', 'require', '送货状态@radio|未发货-已发送-已收货-退货中-已退货不能为空！', 1),
		array('total_price', 'require', '定单总价不能为空！', 1),
		array('shr_username', 'require', '收货人姓名不能为空！', 1),
		array('shr_province', 'require', '收货人所在省不能为空！', 1),
		array('shr_city', 'require', '收货人所在城市不能为空！', 1),
		array('shr_area', 'require', '收货人所在地区不能为空！', 1),
		array('shr_address', 'require', '收货人详细地址不能为空！', 1),
		array('shr_phone', 'require', '收货人电话不能为空！', 1),
		array('member_id', 'require', '会员id不能为空！', 1),
		array('pay_method', 'require', '支付方式：1.支付宝 2.余额支付不能为空！', 1),
		);
	
	public function search()
	{
		// 取出admin表总的记录数
		$totalRecord = $this->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		$data = $this->limit($page->firstRow, $page->listRows)->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
}