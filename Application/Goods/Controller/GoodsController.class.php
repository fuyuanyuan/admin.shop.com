<?php
namespace Goods\Controller;
use Admin\Controller\AdminController;
class GoodsController extends AdminController 
{
	// 添加
	public function add()
	{
		if(IS_POST)
		{
			//var_dump($_POST);die;
			set_time_limit(0);
			$model = D('Goods');
			if($model->create($_POST['Goods']))
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
		// 取出所有的商品分类
		$catModel = D('Category');
		$catData = $catModel->getCatTree();
		$this->assign('catData', $catData);
		// 取出所有的品牌
		$brandModel = M('Brand');
		$brandData = $brandModel->select();
		$this->assign('brandData', $brandData);
		// 取出所有的会员级别
		$mpModel = M('MemberLevel');
		$mpData = $mpModel->select();
		$this->assign('mpData', $mpData);
		// 取出所有的商品类型
		$typeModel = M('Type');
		$typeData = $typeModel->select();
		$this->assign('typeData', $typeData);
		// 取出所有的推荐位
		$recModel = M('Recommend');
		$recData = $recModel->select();
		$this->assign('recData', $recData);
		// 显示表单
		$this->display();
	}
	public function save($id)
	{
		$model = D('Goods');
		if(IS_POST)
		{
			if($model->create($_POST['Goods']))
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
		// 取出所有的商品分类
		$catModel = D('Category');
		$catData = $catModel->getCatTree();
		$this->assign('catData', $catData);
		// 取出所有的品牌
		$brandModel = M('Brand');
		$brandData = $brandModel->select();
		$this->assign('brandData', $brandData);
		// 取出所有的会员级别
		$mpModel = M('MemberLevel');
		$mpData = $mpModel->select();
		$this->assign('mpData', $mpData);
		// 取出所有的商品类型
		$typeModel = M('Type');
		$typeData = $typeModel->select();
		$this->assign('typeData', $typeData);
		// 取出所有的推荐位
		$recModel = M('Recommend');
		$recData = $recModel->select();
		$this->assign('recData', $recData);
		// 先取出原数据
		$data = $model->find($id);
		$this->assign('data', $data);
		// 取出要修改的商品的会员价格
		$lpModel = M('LevelPrice');
		$lpData = $lpModel->where('goods_id='.$id)->select();
		$this->assign('lpData', $lpData);
		// 取出要修改的商品的商品属性
		$gaModel = M('GoodsAttr');
		$gaData = $gaModel->where('goods_id='.$id)->select();
		$this->assign('gaData', $gaData);
		// 取出要修改的商品的商品相册
		$gpModel = M('GoodsPics');
		$gpData = $gpModel->where('goods_id='.$id)->select();
		$this->assign('gpData', $gpData);
		// 如果商品已经有类型了，那么取出当前商品类型的属性
		if($data['type_id'])
		{
			$attrModel = M('Attribute');
			$attrData = $attrModel->where('type_id='.$data['type_id'])->select();
			$this->assign('attrData', $attrData);
		}
		/*** 取出商品相关的文章 ***********/
		$gnModel = M('GoodsNews');
		$news = $gnModel->field('b.id,b.title')->alias('a')->join('LEFT JOIN sh_news b ON a.news_id=b.id')->where('a.goods_id='.$id)->select();
		$this->assign('news', $news);
		// 显示表单
		$this->display();
	}
	public function del($id)
	{
		$model = D('Goods');
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
			$model = D('Goods');
			$model->delete($id);
		}
		$this->success('操作成功！', U('lst'));
		exit;
	}
	// 列表页
	public function lst()
	{
		$model = D('Goods');
		// 调用我们自己写的search方法，方法会返回数据以及翻页的字符串
		$data = $model->search();
		$this->assign('data', $data['data']);
		$this->assign('page', $data['page']);
		// 显示页面
		$this->display();
	}
	public function ajaxGetAttr($type_id)
	{
		// 根据type_id取出属性
		$model = M('Attribute');
		$data = $model->where('type_id='.(int)$type_id)->select();
		echo json_encode($data);
	}
	public function ajaxDelImg($id)
	{
		// 取出图片的地址然后删除
		$gpModel = M('GoodsPics');
		$gpModel->find($id);
		@unlink(IMG_URL_HD . $gpModel->logo);
		@unlink(IMG_URL_HD . $gpModel->sm_logo);
		@unlink(IMG_URL_HD . $gpModel->mid_logo);
		@unlink(IMG_URL_HD . $gpModel->big_logo);
		$gpModel->delete();
	}
	public function product($id)
	{
		$id = (int)$id;
		$proModel = M('Product');
		if(IS_POST)
		{
			/**
			 * 货品表结构：
			 * mysql> SELECT * FROM sh_product;
				+--------------+----------+---------------+
				| goods_number | goods_id | goods_attr_id |
				+--------------+----------+---------------+
				|          321 |       25 | 17,24         |
				|           65 |       25 | 17,32         |
				|           34 |       25 | 24,30         |
				|           54 |       25 | 24,31         |
				|           56 |       25 | 30,32         |
				|           54 |       25 | 31,32         |
				+--------------+----------+---------------+
			 */
			// 先删除原数据
			$proModel->where('goods_id='.$id)->delete();
			// 计算属性ID与库存量数字的对应比例
			$rate = count($_POST['goods_attr_id']) / count($_POST['goods_number']);
			// 循环每一个库存量的数字
			foreach ($_POST['goods_number'] as $k => $v)
			{
				// 转化成整型
				$v = intval($v);
				// 如果库存量为空就跳过
				if(!$v)
					continue ;
				/*********** 取出数字对应的属性id并构造属性ID字符串 ****************/
				// 从第几个属性开始取
				$start = $k * $rate;
				// 定义一个数组把取出的属性先放这个数组中
				$_attr = array();
				// 一个库存量数字对应$rate个属性值，所以要循环取出$rate个属性值
				for ($i=0; $i<$rate; $i++)
				{
					$_start = $start;
					// 如果这个属性值为空就跳过
					if(!$_POST['goods_attr_id'][$start++])
						continue 2;
					$_attr[] = $_POST['goods_attr_id'][$_start];
					
				}
				// 把对应的多个属性ID升序排列
				sort($_attr);
				// 把属性ID转化成字符串
				$_attr = implode(',', $_attr);
				// 存入数据库
				$proModel->add(array(
					'goods_id' => $id,
					'goods_number' => $v,
					'goods_attr_id' => $_attr,
				));
			}
			$this->success('保存成功');
			exit;
		}
		// 根据商品的ID取出这件商品所有单选的属性
		$gaModel = M('GoodsAttr');
		/**
		 * SELECT a.*,b.attr_name
		 * 	FROM sh_goods_attr a 
		 *   LEFT JOIN sh_attribute b ON a.attr_id=b.id
		 *    WHERE a.goods_id=$id AND b.attr_type='单选'
		 */
		/**
		 * array
			  0 => 
			    array
			      'id' => string '17' (length=2)
			      'attr_id' => string '3' (length=1)
			      'attr_value' => string '黑色' (length=6)
			      'goods_id' => string '25' (length=2)
			      'attr_name' => string '颜色' (length=6)
			  1 => 
			    array
			      'id' => string '30' (length=2)
			      'attr_id' => string '3' (length=1)
			      'attr_value' => string '白色' (length=6)
			      'goods_id' => string '25' (length=2)
			      'attr_name' => string '颜色' (length=6)
			  2 => 
			    array
			      'id' => string '31' (length=2)
			      'attr_id' => string '3' (length=1)
			      'attr_value' => string '蓝色' (length=6)
			      'goods_id' => string '25' (length=2)
			      'attr_name' => string '颜色' (length=6)
			  3 => 
			    array
			      'id' => string '24' (length=2)
			      'attr_id' => string '4' (length=1)
			      'attr_value' => string '41' (length=2)
			      'goods_id' => string '25' (length=2)
			      'attr_name' => string '尺码' (length=6)
			  4 => 
			    array
			      'id' => string '26' (length=2)
			      'attr_id' => string '4' (length=1)
			      'attr_value' => string '40' (length=2)
			      'goods_id' => string '25' (length=2)
			      'attr_name' => string '尺码' (length=6)
		 */
		$_attrData = $gaModel->field('a.*,b.attr_name')->alias('a')->join('LEFT JOIN sh_attribute b ON a.attr_id=b.id')->where('a.goods_id='.$id.' AND b.attr_type="单选"')->select();
		// 现在这个数据的数组不合适，所以需要重新整理一下
		$attrData = array();
		foreach ($_attrData as $k => $v)
		{
			$attrData[$v['attr_name']][] = $v;
		}
		/** 处理完之后的数组结构
		 * array
		  '颜色' => 
		    array
		      0 => 
		        array
		          'id' => string '17' (length=2)
		          'attr_id' => string '3' (length=1)
		          'attr_value' => string '黑色' (length=6)
		          'goods_id' => string '25' (length=2)
		          'attr_name' => string '颜色' (length=6)
		      1 => 
		        array
		          'id' => string '30' (length=2)
		          'attr_id' => string '3' (length=1)
		          'attr_value' => string '白色' (length=6)
		          'goods_id' => string '25' (length=2)
		          'attr_name' => string '颜色' (length=6)
		      2 => 
		        array
		          'id' => string '31' (length=2)
		          'attr_id' => string '3' (length=1)
		          'attr_value' => string '蓝色' (length=6)
		          'goods_id' => string '25' (length=2)
		          'attr_name' => string '颜色' (length=6)
		  '尺码' => 
		    array
		      0 => 
		        array
		          'id' => string '24' (length=2)
		          'attr_id' => string '4' (length=1)
		          'attr_value' => string '41' (length=2)
		          'goods_id' => string '25' (length=2)
		          'attr_name' => string '尺码' (length=6)
		      1 => 
		        array
		          'id' => string '26' (length=2)
		          'attr_id' => string '4' (length=1)
		          'attr_value' => string '40' (length=2)
		          'goods_id' => string '25' (length=2)
		          'attr_name' => string '尺码' (length=6)
		 * having 和where的区别：
		 * having是在聚合函数（COUNT,SUM,AVG...）之后执行的
		 * where是在聚合函数之前执行的
		 * 所以这里需要先count算出数据之后再过滤所以要使用having。
		 * 或者可以通过SQL只取出有多于一个值单选属性：
		 * SELECT a.*,b.attr_name,
		 * (
		 *    SELECT COUNT(*) 
		 *        FROM sh_goods_attr c 
		 *           WHERE c.attr_id=a.attr_id AND c.goods_id=a.goods_id
			) num 
			 FROM sh_goods_attr a LEFT JOIN sh_attribute b ON a.attr_id=b.id 
			  WHERE a.goods_id=25 AND b.attr_type='单选 ' HAVING num>1;
		 */
		// 再处理单选的属性，如果属性只有一个值就去掉
		foreach ($attrData as $k => $v)
		{
			if(count($v) == 1)
				unset($attrData[$k]);
		}
		$this->assign('attrData', $attrData);
		// 先取出货品表中的数据
		$proData = $proModel->where('goods_id='.$id)->select();
		// 重新处理数据取出attr_id对应的值
		foreach ($proData as $k => $v)
		{
			$_attr = array();
			$_data = $gaModel->field('id,attr_value')->where("id IN({$v['goods_attr_id']})")->select();
			foreach ($_data as $k1 => $v1)
			{
				$_attr[$v1['id']] = $v1['attr_value'];
			}
			$proData[$k]['gaData'] = $_attr;
		}
		$this->assign('proData', $proData);
		$this->display();
	}
	public function ajaxSearchArticles($title)
	{
		$articleModel = M('News');
		$data = $articleModel->field('id,title')->where("title LIKE '%$title%'")->select();
		echo json_encode($data);
	}
}