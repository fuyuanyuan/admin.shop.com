<?php
namespace Goods\Model;
use Think\Model;
class GoodsModel extends Model 
{
	protected $_validate = array(
		array('goods_name', 'require', '商品名称不能为空！', 1),
		array('market_price', 'require', '市场价不能为空！', 1),
		array('shop_price', 'require', '本店价不能为空！', 1),
		array('cat_id', 'require', '商品分类不能为空！', 1),
		array('is_on_sale', 'require', '是否上架@radio|是-否不能为空！', 1),
	);
	
	public function search()
	{
		// 取出admin表总的记录数
		$totalRecord = $this->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		/**
		 * 取商品以及库存量总和的SQL是：
		 * SELECT a.goods_name,IFNULL(SUM(b.goods_number),0) goods_number FROM sh_go
ods a LEFT JOIN sh_product b ON a.id=b.goods_id GROUP BY a.id;
		 */
		$data = $this->field('a.*,IFNULL(SUM(b.goods_number),0) goods_number')->alias('a')->join('LEFT JOIN sh_product b ON a.id=b.goods_id')->limit($page->firstRow, $page->listRows)->order('a.id DESC')->group('a.id')->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
	
	protected function _before_insert(&$data, $option)
	{
		/** 如果有图片就上传图片 **/
		if($_FILES['logo']['tmp_name'])
		{
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize = 3145728 ;// 图片最大3M
		    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->savePath = 'Goods/'; // 设置附件上传（子）目录
		    // 上传文件 
		    $info = $upload->upload(array('logo'=>$_FILES['logo']));
		    $image = new \Think\Image();
		    $image->open(IMG_URL_HD . $info['logo']['savepath'] . $info['logo']['savename']);
		    // 把缩略图和原图放到同一个目录下，但名字不同，在原图名字前加缀
		    // 注意：在TP中，必须先生成大的再生成小的
		    $image->thumb(350, 350)->save(IMG_URL_HD . $info['logo']['savepath'] .'big_'.$info['logo']['savename']);
		    $image->thumb(130, 130)->save(IMG_URL_HD . $info['logo']['savepath'] .'mid_'.$info['logo']['savename']);
		    $image->thumb(50, 50)->save(IMG_URL_HD . $info['logo']['savepath'] .'sm_'.$info['logo']['savename']);
		    // 把上传之后图片的路径存到数据库中
		    $data['logo'] = $info['logo']['savepath'] . $info['logo']['savename'];
		    $data['sm_logo'] = $info['logo']['savepath'] . 'sm_'.$info['logo']['savename'];
		    $data['mid_logo'] = $info['logo']['savepath'] . 'mid_'.$info['logo']['savename'];
		    $data['big_logo'] = $info['logo']['savepath'] . 'big_'.$info['logo']['savename'];
		}
		$data['goods_sn'] = '###';
		$data['addtime'] = date('Y-m-d H:i:s');
	}
	
	protected function _after_insert($data)
	{
		// 计算商品编号并更新
		$this->execute('UPDATE sh_goods SET goods_sn="'.date('Ymd').$data['id'].'" WHERE id = '.$data['id']);
		/*************** 处理会员价格的表单 **********************/
		/**
		 * 表单中数组结构：
		 * 'LevelPrice' => 
			    array
			      1 => string '34' (length=2)
			      2 => string '4' (length=1)
			      3 => string '23' (length=2)
		 */
		if($_POST['LevelPrice'])
		{
			$lpModel = M('LevelPrice');
			// 如果价格不为空那么就插入到价格表中
			foreach ($_POST['LevelPrice'] as $k => $v)
			{
				if(!$v)
					continue ;
				$lpModel->add(array(
					'price' => $v,
					'goods_id' => $data['id'],
					'level_id' => $k,
				));
			}
		}
		/*************** 处理商品属性的表单 **********************/
		/**
		 * 表单中数组结构：3,4,5是属性id
		 *   'GoodsAttr' => 
			    array
			      3 => string '白色' (length=6)
			      4 => 
			        array
			          0 => string '38' (length=2)
			          1 => string '39' (length=2)
			          2 => string '40' (length=2)
			          3 => string '41' (length=2)
			      5 => string '3' (length=1)
		 */
		if($_POST['GoodsAttr'])
		{
			$gaModel = M('GoodsAttr');
			// 如果价格不为空那么就插入到价格表中
			foreach ($_POST['GoodsAttr'] as $k => $v)
			{
				if(is_array($v))
				{
					foreach ($v as $k1 => $v1)
					{
						$gaModel->add(array(
							'attr_id' => $k,
							'attr_value' => $v1,
							'goods_id' => $data['id'],
						));
					}
				}
				else 
				{
					$gaModel->add(array(
						'attr_id' => $k,
						'attr_value' => $v,
						'goods_id' => $data['id'],
					));
				}
			}
		}
		/*************** 处理商品相册的表单 **********************/
		// 最多能上传几张？有什么限制？
		// php.ini中有限制
		// 1.脚本执行时间，一个PHP脚本默认执行30秒
		// 解决办法：set_time_time(0) " 设置脚本执行时间。0：代表一直执行，单位是秒
		// 2. php.ini中post_max_size 这一顶代表：一个表单最大的尺寸
		// 3. php.ini中upload_max_filesize这一项是限制单个文件最大的尺寸
		if($this->_hasImg($_FILES['GoodsPics']))
		{
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize = 3145728 ;// 图片最大3M
		    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->savePath = 'Goods/'; // 设置附件上传（子）目录
		    // 上传文件 
		    $info = $upload->upload(array('GoodsPics'=>$_FILES['GoodsPics']));
		   	// 循环每一张图片生成缩略图
		   	if($info)
		   	{
		   		$image = new \Think\Image();
		   		$gpModel = M('GoodsPics');
		   		foreach ($info as $k => $v)
		   		{
				    $image->open(IMG_URL_HD . $v['savepath'] . $v['savename']);
				    // 把缩略图和原图放到同一个目录下，但名字不同，在原图名字前加缀
				    // 注意：在TP中，必须先生成大的再生成小的
				    $image->thumb(350, 350)->save(IMG_URL_HD . $v['savepath'] .'big_'.$v['savename']);
				    $image->thumb(130, 130)->save(IMG_URL_HD . $v['savepath'] .'mid_'.$v['savename']);
				    $image->thumb(50, 50)->save(IMG_URL_HD . $v['savepath'] .'sm_'.$v['savename']);
				    // 把上传之后图片的路径存到数据库中
				    $gpModel->add(array(
				    	'goods_id' => $data['id'],
				    	'sm_logo' => $v['savepath'] . 'sm_'.$v['savename'],
				    	'mid_logo' => $v['savepath'] . 'mid_'.$v['savename'],
				    	'big_logo' => $v['savepath'] . 'big_'.$v['savename'],
				    	'logo' => $v['savepath'] . $v['savename'],
				    ));
		   		}
		   	}
		}
		/************************* 推荐到的数据处理 *******************************/
		// 取出所有的推荐位都循环一遍
		$recModel = M('Recommend');
		$recData = $recModel->select();
		foreach ($recData as $k => $v)
		{
			// 判断是否推荐到了这个位置上
			if(in_array($v['id'], $_POST['recto']))
			{
				$v['goods_id'] .= ','.$data['id'];
				$v['goods_id'] = ltrim($v['goods_id'], ',');
				$recModel->where('id='.$v['id'])->save(array(
					'goods_id' => $v['goods_id'],
				));
			}
		}
		/************************ 处理相关新闻 ********************************/
		if(isset($_POST['News']) && $_POST['News'])
		{
			$gn = M('GoodsNews');
			foreach ($_POST['News'] as $v)
			{
				$gn->add(array(
					'goods_id' => $data['id'],
					'news_id' => $v,
				));
			}
		}
	}
	// 判断表单中有没有图片
	private function _hasImg($data)
	{
		// 循环数据中每一项只要有一项不为空就代表有图片
		foreach ($data['tmp_name'] as $k => $v)
		{
			if($v)
				return TRUE;
		}
		return FALSE;
	}
	protected function _before_delete($data)
	{
		/**
		 * 当前批量删除时$data的结构：
		 *   'where' => 
			    array
			      'id' => 
			        array
			          0 => string 'IN' (length=2)
			          1 => string '1,2,3,12,4,5,6,7,9,11,8,10' (length=26)
			  'table' => string 'sh_category' (length=11)
			  'model' => string 'Category' (length=8)
  
		  当删除一个时$data的结构是：
		  array
		  'where' => 
		    array
		      'id' => int 1
		  'table' => string 'sh_category' (length=11)
		  'model' => string 'Category' (length=8)
		 */
		if(is_array($data['where']['id']))
		{
			/***************** 会员价格表**************/
			$model = M('LevelPrice');
			$model->where("goods_id IN({$data['where']['id'][1]})")->delete();
			/***************** 商品属性表**************/
			$model = M('GoodsAttr');
			$model->where("goods_id IN({$data['where']['id'][1]})")->delete();
			/***************** 货品表**************/
			$model = M('Product');
			$model->where("goods_id IN({$data['where']['id'][1]})")->delete();
			/***************** 相册表**************/
			$model = M('GoodsPics');
			$img = $model->where("goods_id IN({$data['where']['id'][1]})")->select();
			foreach ($img as $k => $v)
			{
				@unlink(IMG_URL_HD . $v['sm_logo']);
				@unlink(IMG_URL_HD . $v['mid_logo']);
				@unlink(IMG_URL_HD . $v['big_logo']);
				@unlink(IMG_URL_HD . $v['logo']);
			}
			$model->where("goods_id IN({$data['where']['id'][1]})")->delete();
			/***************** 商品的logo图片 *******************/
			$img = $this->field('logo,sm_logo,big_logo,mid_logo')->where("id IN({$data['where']['id'][1]})")->select();
			foreach ($img as $k => $v)
			{
				@unlink(IMG_URL_HD . $v['sm_logo']);
				@unlink(IMG_URL_HD . $v['mid_logo']);
				@unlink(IMG_URL_HD . $v['big_logo']);
				@unlink(IMG_URL_HD . $v['logo']);
			}
			/****************** 删除文章　＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊***/
			$model = M('GoodsNews');
			$model->where("goods_id IN({$data['where']['id'][1]})")->delete();
		}
		else 
		{
			/***************** 会员价格表**************/
			$model = M('LevelPrice');
			$model->where('goods_id='.$data['where']['id'])->delete();
			/***************** 商品属性表**************/
			$model = M('GoodsAttr');
			$model->where('goods_id='.$data['where']['id'])->delete();
			/***************** 货品表**************/
			$model = M('Product');
			$model->where('goods_id='.$data['where']['id'])->delete();
			/***************** 相册表**************/
			$model = M('GoodsPics');
			$img = $model->where('goods_id='.$data['where']['id'])->select();
			foreach ($img as $k => $v)
			{
				@unlink(IMG_URL_HD . $v['sm_logo']);
				@unlink(IMG_URL_HD . $v['mid_logo']);
				@unlink(IMG_URL_HD . $v['big_logo']);
				@unlink(IMG_URL_HD . $v['logo']);
			}
			$model->where('goods_id='.$data['where']['id'])->delete();
			/***************** 商品的logo图片 *******************/
			$img = $this->field('logo,sm_logo,big_logo,mid_logo')->find($data['where']['id']);
			@unlink(IMG_URL_HD . $img['sm_logo']);
			@unlink(IMG_URL_HD . $img['mid_logo']);
			@unlink(IMG_URL_HD . $img['big_logo']);
			@unlink(IMG_URL_HD . $img['logo']);
			/***************** 删除相应的新闻  &******************/
			$model = M('GoodsNews');
			$model->where('goods_id='.$data['where']['id'])->delete();
		}
	}
	public function _before_update(&$data, $option)
	{
		$goods_id = $option['where']['id'];
		/** 如果有图片就上传图片 **/
		if($_FILES['logo']['tmp_name'])
		{
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize = 3145728 ;// 图片最大3M
		    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->savePath = 'Goods/'; // 设置附件上传（子）目录
		    // 上传文件 
		    $info = $upload->upload(array('logo'=>$_FILES['logo']));
		    $image = new \Think\Image();
		    $image->open(IMG_URL_HD . $info['logo']['savepath'] . $info['logo']['savename']);
		    // 把缩略图和原图放到同一个目录下，但名字不同，在原图名字前加缀
		    // 注意：在TP中，必须先生成大的再生成小的
		    $image->thumb(350, 350)->save(IMG_URL_HD . $info['logo']['savepath'] .'big_'.$info['logo']['savename']);
		    $image->thumb(130, 130)->save(IMG_URL_HD . $info['logo']['savepath'] .'mid_'.$info['logo']['savename']);
		    $image->thumb(50, 50)->save(IMG_URL_HD . $info['logo']['savepath'] .'sm_'.$info['logo']['savename']);
		    // 先把原来的图片删除掉
		    if($data['logo'])
		    {
		    	@unlink(IMG_URL_HD . $data['logo']);
		    	@unlink(IMG_URL_HD . $data['sm_logo']);
			    @unlink(IMG_URL_HD . $data['mid_logo']);
			    @unlink(IMG_URL_HD . $data['big_logo']);
		    }
		    // 把上传之后图片的路径存到数据库中
		    $data['logo'] = $info['logo']['savepath'] . $info['logo']['savename'];
		    $data['sm_logo'] = $info['logo']['savepath'] . 'sm_'.$info['logo']['savename'];
		    $data['mid_logo'] = $info['logo']['savepath'] . 'mid_'.$info['logo']['savename'];
		    $data['big_logo'] = $info['logo']['savepath'] . 'big_'.$info['logo']['savename'];
		}
		/*************** 处理会员价格的表单 **********************/
		/**
		 * 表单中数组结构：
		 * 'LevelPrice' => 
			    array
			      1 => string '34' (length=2)
			      2 => string '4' (length=1)
			      3 => string '23' (length=2)
		 */
		$lpModel = M('LevelPrice');
		// 先删除数据库中原数据
		$lpModel->where('goods_id='.$goods_id)->delete();
		if($_POST['LevelPrice'])
		{
			// 如果价格不为空那么就插入到价格表中
			foreach ($_POST['LevelPrice'] as $k => $v)
			{
				if(!$v)
					continue ;
				$lpModel->add(array(
					'price' => $v,
					'goods_id' => $goods_id,
					'level_id' => $k,
				));
			}
		}
		/*************** 处理商品相册的表单 **********************/
		if($this->_hasImg($_FILES['GoodsPics']))
		{
			$upload = new \Think\Upload();// 实例化上传类
		    $upload->maxSize = 3145728 ;// 图片最大3M
		    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->savePath = 'Goods/'; // 设置附件上传（子）目录
		    // 上传文件 
		    $info = $upload->upload(array('GoodsPics'=>$_FILES['GoodsPics']));
		   	// 循环每一张图片生成缩略图
		   	if($info)
		   	{
		   		$image = new \Think\Image();
		   		$gpModel = M('GoodsPics');
		   		foreach ($info as $k => $v)
		   		{
				    $image->open(IMG_URL_HD . $v['savepath'] . $v['savename']);
				    // 把缩略图和原图放到同一个目录下，但名字不同，在原图名字前加缀
				    // 注意：在TP中，必须先生成大的再生成小的
				    $image->thumb(350, 350)->save(IMG_URL_HD . $v['savepath'] .'big_'.$v['savename']);
				    $image->thumb(130, 130)->save(IMG_URL_HD . $v['savepath'] .'mid_'.$v['savename']);
				    $image->thumb(50, 50)->save(IMG_URL_HD . $v['savepath'] .'sm_'.$v['savename']);
				    // 把上传之后图片的路径存到数据库中
				    $gpModel->add(array(
				    	'goods_id' => $goods_id,
				    	'sm_logo' => $v['savepath'] . 'sm_'.$v['savename'],
				    	'mid_logo' => $v['savepath'] . 'mid_'.$v['savename'],
				    	'big_logo' => $v['savepath'] . 'big_'.$v['savename'],
				    	'logo' => $v['savepath'] . $v['savename'],
				    ));
		   		}
		   	}
		}
		/************** 处理商品属性 ****************/
		if($_POST['GoodsAttr'])
		{
			// 定义数组用来保存表单中单选属性的ID
			$_attr = array();
			$gaModel = M('GoodsAttr');
			// 循环表单中每一个属性
			foreach ($_POST['GoodsAttr'] as $k => $v)
			{
				// 判断是否是单选的属性
				if(is_array($v))
				{
					// 如果属性是单选的那么就把属性的ID保存到数组中,后面要单独处理表单中的单选属性,所以要把单选属性先提取出来
					$_attr[] = $k;
					// 循环单选属性的每一个值
					foreach ($v as $k1 => $v1)
					{
						// 判断数据库中是否有这个单选属性的值，如果没有就添加
						$_c = $gaModel->where('goods_id='.$goods_id.' AND attr_id='.$k.' AND attr_value="'.$v1.'"')->count();
						if($_c == 0)
						{
							$gaModel->add(array(
								'goods_id' => $goods_id,
								'attr_id' => $k,
								'attr_value' => $v1,
							));
						}
					}
				}
				else 
				{
					// 如果不是单选属性那么直接更新数据库
					$gaModel->where('goods_id='.$goods_id.' AND attr_id='.$k)->save(array(
						'attr_value' => $v,
					));
				}
			}
			// 如果表单中有单选的属性，那么就要再处理单选属性中被删除的属性值
			if($_attr)
			{
				// 循环表单中每一个单选属性
				foreach ($_attr as $k => $v)
				{
					// 从数据库中取出这个单选属性所有的值
					$_data = $gaModel->where('attr_id='.$v.' AND goods_id='.$goods_id)->select();
					// 判断每一个单选属性的值是否在表单中,如果不在表单中说明应该删除
					foreach ($_data as $k => $v1)
					{
						if(!in_array($v1['attr_value'], $_POST['GoodsAttr'][$v]))
						{
							$gaModel->where('id='.$v1['id'])->delete();
						}
					}
				}
			}
		}
		/************************* 推荐到的数据处理 *******************************/
		// 取出所有的推荐位都循环一遍
		$recModel = M('Recommend');
		$recData = $recModel->select();
		foreach ($recData as $k => $v)
		{
			// 判断是否推荐到了这个位置上
			if(in_array($v['id'], $_POST['recto']))
			{
				// 先判断当前这个商品是否已经推荐了
				if(strpos(','.$v['goods_id'].',', ','.$goods_id.',') === FALSE)
				{
					$v['goods_id'] .= ','.$goods_id;
					$v['goods_id'] = ltrim($v['goods_id'], ',');
					$recModel->where('id='.$v['id'])->save(array(
						'goods_id' => $v['goods_id'],
					));
				}
			}
			else 
			{
				// 如果没有勾引这个位置但是数据库中有这个数字那么就应该把这个数字从字符串中去掉
				if(strpos(','.$v['goods_id'].',', ','.$goods_id.',') !== FALSE)
				{
					$v['goods_id'] = trim(str_replace(','.$goods_id.',', ',', ','.$v['goods_id'].','), ',');
					$recModel->where('id='.$v['id'])->save(array(
						'goods_id' => $v['goods_id'],
					));
				}
			}
		}
		/************************ 处理相关新闻 ********************************/
		$gn = M('GoodsNews');
		$gn->where('goods_id='.$goods_id)->delete();
		if(isset($_POST['News']) && $_POST['News'])
		{
			foreach ($_POST['News'] as $v)
			{
				$gn->add(array(
					'goods_id' => $goods_id,
					'news_id' => $v,
				));
			}
		}
	}
}