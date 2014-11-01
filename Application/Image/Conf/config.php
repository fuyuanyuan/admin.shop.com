<?php
return array(
	'UPLOAD_TYPE' => 'Local',  // Local | FTP
	'TEMP_DIR' => './Temp/',
);

// 如果使用FTP，那么必须导入数据库：
/**
 * SET NAMES utf8;

DROP TABLE IF EXISTS sh_image_server;
CREATE TABLE sh_image_server(
	id tinyint unsigned not null auto_increment comment 'id',
	image_domain varchar(50) not null comment '域名',
	image_count int unsigned not null default '0' comment '当前图片的数量',
	max_image_count int unsigned not null comment '服务器上最大的图片数量',
	ftpport char(5) not null default '21' comment 'ftp端口号',
	ftpuser varchar(30) not null comment 'ftp账号',
	ftppassword varchar(50) not null comment 'ftp密码',
	primary key (id)
)engine=MyISAM default charset=utf8 comment '文章分类表';
 */