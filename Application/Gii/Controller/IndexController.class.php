<?php
namespace Gii\Controller;
use Think\Controller;
class IndexController extends Controller 
{
	public function index()
	{
		if(IS_POST)
		{
			/************ 1.先生成模块的目录结构 *******/
			$moduleName = ucfirst($_POST['moduleName']);
			$moduleDir = './Application/'.$moduleName;
			$controllerDir = './Application/'.$moduleName.'/Controller';
			$modelDir = './Application/'.$moduleName.'/Model';
			$viewDir = './Application/'.$moduleName.'/View';
			// 第二参数只有在linux系统下有效0777：可读可写可执行
			if(!is_dir($moduleDir))
				mkdir($moduleDir, 0777);
			if(!is_dir($controllerDir))
				mkdir($controllerDir, 0777);
			if(!is_dir($modelDir))
				mkdir($modelDir, 0777);
			if(!is_dir($viewDir))
				mkdir($viewDir, 0777);
			/********************* 2. 先生成控制器文件 ************************/
			// 0. 处理表名   sh_goods_info ->  GoodsInfo
			$mvcName = $this->_tableName2MVCName($_POST['tableName']);
			// 1. 控制器名字
			$controllerName = $mvcName . 'Controller';
			// 2. 控制器文件名
			$controllerFileName = $controllerName . '.class.php';
			// 3. 生成控制器中的代码
			ob_start();
			// 4. 模块文件中的代码到ob缓冲区
			include('./Application/Gii/Template/controller.tpl');
			// 5. 从缓冲区中取出代码并删除缓冲区
			$str = ob_get_clean();
			// 6. 加上php标记并把代码写到控制器文件中
			$str = "<?php\r\n".$str;
			file_put_contents($controllerDir . '/' . $controllerFileName, $str);
			/****************** 3. 生成模型文件 ***********************************/
			// 先获取表中所有字段的信息
			$db = M();
			$fields = $db->query('SHOW FULL FIELDS FROM '.$_POST['tableName']);
			ob_start();
			include('./Application/Gii/Template/model.tpl');
			$str = ob_get_clean();
			$str = "<?php\r\n".$str;
			file_put_contents($modelDir . '/' . $mvcName.'Model.class.php', $str);
			/****************** 4. 生成静态页 *************************************/
			// 获取数据库名
			$dbName = C('DB_NAME');
			// 获取表的注释
			$tableInfo = $db->query('SELECT TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA="'.$dbName.'" AND TABLE_NAME="'.$_POST['tableName'].'"');
			$table = $tableInfo[0]['TABLE_COMMENT'];
			// 0. 静态页所在的目录
			$htmlDir = $viewDir .'/'. $mvcName;
			// 1. 在View目录下生成控制器对应的目录
			if(!is_dir($htmlDir))
				mkdir($htmlDir);
			// 2. 先生成add.html
			ob_start();
			include('./Application/Gii/Template/add.html');
			$str = ob_get_clean();
			file_put_contents($htmlDir.'/add.html', $str);
			// 3. 生成save.html
			ob_start();
			include('./Application/Gii/Template/save.html');
			$str = ob_get_clean();
			file_put_contents($htmlDir.'/save.html', $str);
			// 4. 生成lst.html
			ob_start();
			include('./Application/Gii/Template/lst.html');
			$str = ob_get_clean();
			file_put_contents($htmlDir.'/lst.html', $str);
			$this->success('完成！');
			exit;
		}
		$this->display();
	}
	private function _tableName2MVCName($tableName)
	{
		// 1. 从配置文件中取出前缀
		$prefix = C('DB_PREFIX');
		// 2. 去掉前缀
		$tableName = ltrim($tableName, $prefix);
		// 3. 根据_转化成数组
		$tableName = explode('_', $tableName);
		// 4. 把数组中每一个单词的首字母大写
		$tableName = array_map('ucfirst', $tableName);
		// 5. 把数组中每个单词拼到一起成一个字符串
		return implode('', $tableName);
	}
}














