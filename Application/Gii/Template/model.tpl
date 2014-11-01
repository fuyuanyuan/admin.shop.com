namespace <?php echo $moduleName; ?>\Model;
use Think\Model;
class <?php echo $mvcName; ?>Model extends Model 
{
	protected $_validate = array(
		<?php foreach ($fields as $k => $v):
			if($v['Field'] == 'id')
				continue ;
			if($v['Null'] == 'NO'):
		?>
array('<?php echo $v['Field']; ?>', 'require', '<?php echo $v['Comment']; ?>不能为空！', 1),
		<?php 
			endif;
		endforeach; ?>
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