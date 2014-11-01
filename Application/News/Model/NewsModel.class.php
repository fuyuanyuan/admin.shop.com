<?php
namespace News\Model;
use Think\Model;
class NewsModel extends Model 
{
	protected $_validate = array(
		array('cat_id', 'require', '所在分类不能为空！', 1),
		array('title', 'require', '标题不能为空！', 1),
		array('content', 'require', '内容不能为空！', 1),
		array('isshow', 'require', '是否显示@radio|是-否不能为空！', 1),
		array('click', 'require', '浏览量不能为空！', 1),
	);
	
	public function search()
	{
		// 取出admin表总的记录数
		$totalRecord = $this->count();
		// 第二个参数：每页显示的条数
		$page = new \Think\Page($totalRecord, 15);
		// 生成翻页的字符串
		// 取出当前页的数据
		$data = $this->field('a.*,b.cat_name')->alias('a')->join('LEFT JOIN __NEWS_CAT__ b ON a.cat_id=b.id')->limit($page->firstRow, $page->listRows)->select();
		return array(
			'page' => $page->show(),
			'data' => $data,
		);
	}
	
	protected function _before_insert(&$data, $option)
	{
		$data['addtime'] = date('Y-m-d H:i:s');
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
		$gnModel = M('GoodsNews');
		if(is_array($data['where']['id']))
			$gnModel->where("news_id IN({$data['where']['id'][1]})")->delete();
		else 
			$gnModel->where('news_id='.$data['where']['id'])->delete();
	}
}