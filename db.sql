SET NAMES utf8;
########### 以下三张表用来实现 RBAC ################
# 有了pri_path字段实现无限级可以不用递归
DROP TABLE IF EXISTS sh_privilege;
CREATE TABLE sh_privilege(
	id mediumint unsigned not null auto_increment comment 'id',
	pri_name varchar(30) not null comment '权限名称',
	module_name varchar(50) not null comment '模块名称',
	controller_name varchar(50) not null comment '控制器名称',
	action_name varchar(50) not null comment '方法名称',
	parent_id mediumint unsigned not null default '0' comment '上级权限',
	pri_level tinyint unsigned not null default '0' comment '级别,0:顶级 1：第二级 2：第三级',
	pri_path varchar(150) not null default '0' comment '所有的上级的ID',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '权限';

# 如何删除ID=1的权限以及所有子权限？
# 所有path中有1的就是子权限
# DELETE FROM xxx WHERE CONCAT('-',pri_path,'-') LIKE "%-1-%"
# 继承法 （不用递归实现无限级）
#id  pri_name    pri_path           
#------------------------------------------
#1     电脑          0                  
#2     书            0                
#3     笔记本        0-1             
#4     PHP          0-2               
#5     java         0-2
#6     台式          0-1
#7     苹果          0-1-3
#8     华硕          0-1-3
#9     php5.3       0-2-4
#10    j2ee         0-2-5  
#11    android      0-2-5
#12    android-1    0-2-5-11
#13    android-2    0-2-5-11-12
# 取树形数据的SQL语句（无须递归）
#SELECT * FROM xxx ORDER BY CONCAT(pri_path,'-',id) ASC
#id  pri_name    pri_path        CONCAT(pri_path,'-',id)
#----------------------------------------------------------
#1     电脑          0                  0-1
	#3     笔记本        0-1                0-1-3
		#7     苹果          0-1-3             0-1-3-7
		#8     华硕          0-1-3             0-1-3-8
	#6     台式          0-1               0-1-6
#2     书            0                 0-2
	#4     PHP          0-2               0-2-4
		#9     php5.3       0-2-4             0-2-4-9
	#5     java         0-2               0-2-5
		#10    j2ee         0-2-5             0-2-5-10
		#11    android      0-2-5             0-2-5-11

DROP TABLE IF EXISTS sh_role;
CREATE TABLE sh_role(
	id mediumint unsigned not null auto_increment comment 'id',
	role_name varchar(30) not null comment '角色名称',
	privilege_id varchar(150) not null default '' comment '权限,多个权限用,隔开如：1,2,3, *代表拥有所有的权限',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '角色';
INSERT INTO sh_role VALUES(1,'超级管理员','*');

# 密码算法： md5(md5(password) . salt)
# 密钥：是用程序随机生成的一个随机字符串
DROP TABLE IF EXISTS sh_admin;
CREATE TABLE sh_admin(
	id tinyint unsigned not null auto_increment comment 'id',
	username varchar(15) not null comment '用户名',
	password char(32) not null comment '密码',
	salt char(6) not null comment '密钥',
	role_id mediumint unsigned not null comment '角色',
	primary key (id)
)engine=MyISAM default charset=utf8 comment '管理员';
INSERT INTO sh_admin VALUES(1,'admin','c8d8ac54ddff192bff97aee8ee082726','e^2#3R',1);

# 字段建索引的原则：
#1. 将来会不会根据这个字段进行查询where username='ab';
#2. 虽然会被查询，但如果是一个大文本就不应该建索引如text,如新闻表content text,char,varchar,SELECT #* F#RO3. M news WHERE content LIKE "%php%"; 这时要根据content字段进行查询，但以%开头的like查询是不会使用#索引的，所以content字段不应该建索引

# 索引类型：
# 普通索引 : 加快查询的速度
# 唯一索引 : 在加快查询的基础上，又限制了字段中的值必须唯一
# 主键    : 在加快查询的基础上，又限制了字段中的值必须唯一并且非空
# 全文索引 : 一般建在 text,varchar,char这种大文本字段类型

# int : 0 ~ 40多亿 : 4个字节
# mediumint : 0 ~ 1600多万  3个字节 
# smallint : 0 ~ 65535      2 个字节
# tinyint  : 0 ~ 255        1个字节

# MyISAM 和 InnoDB 的区别？
#MyISAM
#	表级锁定
#	插入数据和读取时非常快
#	支持全文索引（对中文的支持不太好，所以一般情况下不用，一般使用sphinx）
#InnoDB
#	行级锁定
#	占用更多的硬盘空间
#	支持事务、外键
#选择引擎的原则：
#1.	是否使用事务或者外键，如果用就只能是innodb

# 数据库的存储优化：
# 原则：
# 1. 数据库越小越好，选择最小最合适的字段类型。
# 2. 所有的字段都加上not null,这样速度会更快
# 3. 为表创建合适的索引

DROP TABLE IF EXISTS sh_news_cat;
CREATE TABLE sh_news_cat(
	id mediumint unsigned not null auto_increment comment 'id',
	cat_name varchar(50) not null comment '分类名称',
	is_help enum('是','否') not null comment '是否帮助@radio|否-是',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '新闻分类';

DROP TABLE IF EXISTS sh_news;
CREATE TABLE sh_news(
	id mediumint unsigned not null auto_increment comment 'id',
	title varchar(50) not null comment '标题',
	content text not null comment '内容',
	isshow enum('是','否') not null comment '是否显示@radio|是-否',
	addtime datetime not null comment '添加时间',
	click int unsigned not null default '0' comment '浏览量',
	cat_id mediumint unsigned not null comment '所在分类',
	primary key (id),
	foreign key (cat_id) references sh_news_cat(id) on delete cascade
)engine=InnoDB default charset=utf8 comment '新闻';

# 外键的语法：前提：必须是InnoDB引擎
# foreign key (cat_id) references sh_news_cat(id) on delete|update cascade|restrict|set null|no action
# cascade : 主表删除，从表数据也删除
# restrict : 如果从表有对应数据，那么主表中的数据不允许删除
# set null: 如果主表数据删除了就把从表对应的字段设置为NULL
# no action : 主表删除，从表什么也不做

# 问题一、列出所有帮助的分类以及分类下文章的数量
# 写法一、SELECT a.*,(SELECT COUNT(*) FROM sh_news b WHERE a.id=b.cat_id) news_count
#	FROM sh_news_cat a	
#	 WHERE a.is_help = '是'
# 写法二
#	SELECT a.*,COUNT(b.*) news_count
#	 FROM sh_news_cat a	LEFT JOIN sh_news b ON a.id=b.cat_id
#	  GROUP BY a.id
#	   WHERE a.is_help = '是'
# 写法三
#	SELECT a.*,COUNT(b.*) news_count
#	 FROM sh_news_cat a,sh_news b
#	  GROUP BY a.id
#	   WHERE a.is_help = '是' AND a.id=b.cat_id
# 问题二、列出所有的文章以及文章所在分类的名字
# SELECT a.*,b.cat_name
#	FROM sh_news a
#	 LEFT JOIN sh_news_cat b ON a.cat_id=b.id

# 外键和用,号连接区别是什么？
cat
----------------------------
id    cat_name
1       体育
2       教育

news
----------------------------
id    title      cat_id
1     世界杯        1
2     php          2
3     xxxx         3

# 取出所有的文章以及分类的名称
# SELECT a.*,b.cat_name FROM news a LEFT JOIN cat b ON a.cat_id=b.id
# 结果：
#  id    title      cat_id    cat_name
#  1     世界杯        1          体育 
#  2     php          2          教育
#  3     xxxx         3          null

# SELECT a.*,b.cat_name FROM news a,cat b WHERE a.cat_id=b.id
# 结果：没有第三条记录
# id    title      cat_id    cat_name
# 1     世界杯        1          体育 
# 2     php          2          教育

DROP TABLE IF EXISTS sh_category;
CREATE TABLE sh_category(
	id mediumint unsigned not null auto_increment comment 'id',
	cat_name varchar(50) not null comment '分类名称',
	parent_id mediumint unsigned not null default '0' comment '上级分类',
	is_rec enum("是","否") not null comment '是否推荐@radio|否-是',
	pos_id1 mediumint unsigned not null default '0' comment '广告位1',
	pos_id2 mediumint unsigned not null default '0' comment '广告位2',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '商品分类';

DROP TABLE IF EXISTS sh_brand;
CREATE TABLE sh_brand(
	id mediumint unsigned not null auto_increment comment 'id',
	brand_name varchar(30) not null comment '品牌名称',
	logo varchar(150) not null default '' comment '品牌logo',
	site varchar(150) not null default '' comment '品牌的官方网站',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '品牌';

DROP TABLE IF EXISTS sh_type;
CREATE TABLE sh_type(
	id mediumint unsigned not null auto_increment comment 'id',
	type_name varchar(30) not null comment '类型名称',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '类型';

DROP TABLE IF EXISTS sh_attribute;
CREATE TABLE sh_attribute(
	id mediumint unsigned not null auto_increment comment 'id',
	attr_name varchar(30) not null comment '属性名称',
	attr_type enum("单选","唯一") not null default '唯一' comment '属性类型@radio|唯一-单选',
	attr_value varchar(300) not null default '' comment '属性可选值',
	type_id mediumint unsigned not null comment '类型的id',
	primary key (id),
	foreign key (type_id) references sh_type(id) on delete cascade
)engine=InnoDB default charset=utf8 comment '属性';

# char 和 varchar 区别？
# char(100)    123 -> 存到数据库是 123   --> 占硬盘100个字符
# varchar(100) 123 -> 存到数据库是 123\0 --> 占硬盘4个字符
#
# varhcar 最大多少？
# char 0~255个字符       a-> 一个字符   中 -> 一个字符
# varchar 0 ~65535个字节 65535个a   不能存65535个“中”，一个汉字占字节 gbk/2字节 utf8/3个字节
# text 0~65535个字符

DROP TABLE IF EXISTS sh_member_level;
CREATE TABLE sh_member_level(
	id mediumint unsigned not null auto_increment comment 'id',
	level_name varchar(30) not null comment '级别名称',
	num_bottom int unsigned not null comment '积分下限',
	num_top int unsigned not null comment '积分上限',
	rate tinyint unsigned not null default '100' comment '折扣率',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '会员级别';


DROP TABLE IF EXISTS sh_member;
CREATE TABLE sh_member(
	id mediumint unsigned not null auto_increment comment 'id',
	username varchar(20) not null comment '用户名',
	password char(32) not null comment '密码',
	salt char(6) not null comment '密钥',
	email varchar(60) not null comment 'Email',
	reg_time datetime not null comment '注册时间',
	jifen int unsigned not null default '0' comment '积分',
	jyz int unsigned not null default '0' comment '经验值',
	money decimal(10,2) not null default '0.00' comment '余额',
	mobile char(11) not null default '' comment '手机号',
	sm_logo varchar(150) not null default '' comment '小头像',
	logo varchar(150) not null default '' comment '大头像',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '会员';

#商品编号算法：2014010900000001 -> 日期+商品id
DROP TABLE IF EXISTS sh_goods;
CREATE TABLE sh_goods(
	id mediumint unsigned not null auto_increment comment 'id',
	goods_sn char(16) not null comment '商品编号',
	goods_name varchar(60) not null comment '商品名称',
	sm_logo varchar(150) not null comment '小图',
	mid_logo varchar(150) not null comment '中图',
	big_logo varchar(150) not null comment '大图',
	logo varchar(150) not null comment '原图',
	market_price decimal(10,2) not null comment '市场价',
	shop_price decimal(10,2) not null comment '本店价',
	cat_id mediumint unsigned not null comment '商品分类',
	brand_id mediumint unsigned not null default '0' comment '品牌',
	is_on_sale enum("是","否") not null comment '是否上架@radio|是-否',
	addtime datetime not null comment '添加时间',
	goods_desc text comment '商品描述',
	type_id mediumint unsigned not null comment '商品的类型',
	primary key (id),
	key cat_id(cat_id),
	key brand_id(brand_id),
	key shop_price(shop_price),
	key is_on_sale(is_on_sale)
)engine=InnoDB default charset=utf8 comment '商品';

DROP TABLE IF EXISTS sh_recommend;
CREATE TABLE sh_recommend(
	id mediumint unsigned not null auto_increment comment 'id',
	rec_name varchar(30) not null comment '推荐位名称',
	goods_id varchar(150) not null default '' comment '商品id，如果这个位置上有多个商品就用,隔开',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '推荐位';

DROP TABLE IF EXISTS sh_level_price;
CREATE TABLE sh_level_price(
	price decimal(10,2) not null comment '价格',
	goods_id mediumint unsigned not null comment '商品id',
	level_id mediumint unsigned not null comment '级别id',
	primary key (goods_id,level_id)
)engine=InnoDB default charset=utf8 comment '会员价格';

# 查看1这件商品在1这个级别是多少钱
#SELECT price FROM sh_level_price WHERE goods_id=1 AND level_id=1  --> 会使用索引
#SELECT price FROM sh_level_price WHERE goods_id=1                 --> 会使用索引
#SELECT price FROM sh_level_price WHERE level_id=1                 --> 不会使用索引

DROP TABLE IF EXISTS sh_goods_pics;
CREATE TABLE sh_goods_pics(
	id mediumint unsigned not null auto_increment comment 'id',
	sm_logo varchar(150) not null comment '小图',
	mid_logo varchar(150) not null comment '中图',
	big_logo varchar(150) not null comment '大图',
	logo varchar(150) not null comment '原图',
	goods_id mediumint unsigned not null comment '商品的id',
	primary key (id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '商品相册';

DROP TABLE IF EXISTS sh_goods_news;
CREATE TABLE sh_goods_news(
	goods_id mediumint unsigned not null comment '商品id',
	news_id mediumint unsigned not null comment '新闻id',
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '商品新闻';

DROP TABLE IF EXISTS sh_goods_attr;
CREATE TABLE sh_goods_attr(
	id mediumint unsigned not null auto_increment comment 'id',
	attr_id mediumint unsigned not null comment '属性id',
	attr_value varchar(150) not null default '' comment '属性值',
	goods_id mediumint unsigned not null comment '商品的id',
	primary key (id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '商品属性';

# 一件商品有几件货品不一定要看这件商品有几个单选的属性和值
DROP TABLE IF EXISTS sh_product;
CREATE TABLE sh_product(
	goods_number int unsigned not null comment '库存量',
	goods_id mediumint unsigned not null comment '商品的id',
	goods_attr_id varchar(150) not null comment '属性的id如果有多个属性先排序用|隔开，如：1|3,1和3是指sh_goods_attr表中的id',
	primary key (goods_id,goods_attr_id)
)engine=InnoDB default charset=utf8 comment '货品';

DROP TABLE IF EXISTS sh_ad_pos;
CREATE TABLE sh_ad_pos(
	id smallint unsigned not null auto_increment comment 'id',
	pos_name varchar(30) not null comment '广告位名称',
	pos_width smallint unsigned not null comment '广告位宽',
	pos_height smallint unsigned not null comment '广告位高',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '广告位';

DROP TABLE IF EXISTS sh_ad;
CREATE TABLE sh_ad(
	id mediumint unsigned not null auto_increment comment 'id',
	ad_name varchar(30) not null comment '广告名称',
	ad_type enum("jq","img","text","code") not null comment '广告类型@radio|jq-img-text-code',
	is_on enum("是","否") not null comment '是否启用@radio|是-否',
	ad_link varchar(150) not null default '' comment '链接地址',
	ad_img varchar(150) not null default '' comment '图片',
	ad_text varchar(600) not null default '' comment '文字/代码',
	pos_id smallint unsigned not null comment '广告位id',
	primary key (id),
	key pos_id (pos_id)
)engine=InnoDB default charset=utf8 comment '广告';

DROP TABLE IF EXISTS sh_jq_info;
CREATE TABLE sh_jq_info(
	id mediumint unsigned not null auto_increment comment 'id',
	img varchar(150) not null comment '图片地址',
	ad_link varchar(150) not null comment '链接地址',
	ad_id mediumint unsigned not null comment '广告的id',
	primary key (id),
	key ad_id (ad_id)
)engine=InnoDB default charset=utf8 comment '广告';

DROP TABLE IF EXISTS sh_button;
CREATE TABLE sh_button(
	id mediumint unsigned not null auto_increment comment 'id',
	btn_name varchar(30) not null comment '按钮名称',
	btn_pos enum('top','mid','bottom') not null comment '位置@radio|top-mid-bottom',
	btn_link varchar(150) not null comment '跳转地址',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '按钮';

DROP TABLE IF EXISTS sh_goods_remark;
CREATE TABLE sh_goods_remark(
	id mediumint unsigned not null auto_increment comment 'id',
	content varchar(300) not null comment '内容',
	goods_id mediumint unsigned not null comment '商品id',
	member_id mediumint unsigned not null default '0' comment '会员id',
	addtime datetime not null comment '评论时间',
	star tinyint unsigned not null default '5' comment '打分',
	primary key (id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '商品评论';

DROP TABLE IF EXISTS sh_goods_impression;
CREATE TABLE sh_goods_impression(
	id mediumint unsigned not null auto_increment comment 'id',
	title varchar(10) not null comment '印象名称',
	num smallint unsigned not null default '1' comment '印象的次数',
	goods_id mediumint unsigned not null comment '商品id',
	primary key (id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '商品印象';

# order_sn算法：2014010412 -> 当前日期+定单的ID
DROP TABLE IF EXISTS sh_order;
CREATE TABLE sh_order(
	id mediumint unsigned not null auto_increment comment 'id',
	order_sn varchar(16) not null comment '定单编号',
	addtime datetime not null comment '下单时间',
	pay_status enum("未支付","已支付") not null default '未支付' comment '支付状态@radio|未支付-已支付',
	post_status enum("未发货","已发送","已收货","退货中","已退货") not null default '未发货' comment '送货状态@radio|未发货-已发送-已收货-退货中-已退货',
	total_price decimal(10,2) not null comment '定单总价',
	shr_username varchar(30) not null comment '收货人姓名',
	shr_province varchar(30) not null comment '收货人所在省',
	shr_city varchar(30) not null comment '收货人所在城市',
	shr_area varchar(30) not null comment '收货人所在地区',
	shr_address varchar(30) not null comment '收货人详细地址',
	shr_phone varchar(30) not null comment '收货人电话',
	pay_method tinyint unsigned not null comment '支付方式：1.支付宝 2.余额支付',
	member_id mediumint unsigned not null comment '会员id',
	post_time int unsigned not null default '0' comment '发货时间',
	primary key (id),
	key member_id(member_id),
	key order_sn(order_sn),
	key pay_status(pay_status),
	key post_status(post_status),
	key addtime(addtime)
)engine=InnoDB default charset=utf8 comment '定单';

DROP TABLE IF EXISTS sh_order_goods;
CREATE TABLE sh_order_goods(
	order_id mediumint unsigned not null comment '定单id',
	goods_id mediumint unsigned not null comment '商品id',
	goods_logo varchar(150) not null comment '商品logo',
	goods_name varchar(60) not null comment '商品名称',
	goods_price decimal(10,2) not null comment '商品价格',
	goods_number int unsigned not null comment '购买的数量',
	goods_attr_id varchar(150) not null default '' comment '商品属性的id',
	goods_attr_str varchar(150) not null default '' comment '商品属性字符串',
	key order_id(order_id)
)engine=InnoDB default charset=utf8 comment '定单商品';

#sh_order
#------------------
#id     post_status
#1        未发货
#2        已收货
#3        已收货
#4        已发货

#sh_order_goods
#------------------------
#order_id          goods_id         goods_number
#    1                2                32
#    1                3                23
#    1                1                33
#    2                4                21
#    2                2                21
#    3                3                3
#    3                2                3
#    4                3                4
#    4                1                5

#select a.*,b.* 
# from sh_order a
# left join sh_order_goods as b on a.id = b.order_id 
#  where a.post_status ='已到货'
                                  goods_id        goods_number
#2        已收货    2                4                21
#2        已收货    2                2                21
#3        已收货    3                3                3
#3        已收货    3                2                3
#select a.*,b.*,SUM(b.goods_number) num
# from sh_order a
# left join sh_order_goods as b on a.id = b.order_id 
#  where a.post_status ='已到货'
#  	group by b.goods_id
#  	 order by num DESC
#2        已收货    2                2                25
#2        已收货    2                4                21
#3        已收货    3                3                3


# 取出销售最高的10件商品？1.先取出所有已收货的定单 2.这些定单中的商品就是已经销售出去的，从这些商品取出商品数量并降序排列取前10个
#select b.*,SUM(b.goods_number) num
# from sh_order a
# left join sh_order_goods as b on a.id = b.order_id 
#  where a.post_status ='已到货'
#  	group by b.goods_id
#  	 order by num DESC
#	  LIMIT 10

DROP TABLE IF EXISTS sh_history;
CREATE TABLE sh_history(
	id mediumint unsigned not null auto_increment,
	member_id mediumint unsigned not null comment '会员id',
	goods_id mediumint unsigned not null comment '商品的id',
	addtime int unsigned not null comment '浏览时间',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '浏览历史';

# 取出浏览了1这件商品的用户还浏览了哪些商品（根据浏览时间排序取出5件：最近其他用户浏览过的）？
# 提示：流程：1.先取出浏览了这件商品的会员ID 2.再取出这些人浏览的其他的商品
#SELECT goods_id FROM member_id IN(SELECT member_id FROM goods_id=1) AND goods_id <> 1 ORDER BY ad#dtime DESC

# 如何随机取数据？
# 1. 普通方法是 select * from xxx order by rand(),但这个方法在数据量大时比较
# 2. 可以这样优化：1.在表中再加一个字段，在插入记录时存一个随机数 2. 为这个字段创建一个索引 3.取数据时根据这个字段排序，这样取的便是随机的，因为建了索引，所以排序会很快

DROP TABLE IF EXISTS sh_cart;
CREATE TABLE sh_cart(
	member_id mediumint unsigned not null comment '会员id',
	goods_id mediumint unsigned not null comment '商品的id',
	goods_attr_id varchar(150) not null default '' comment '商品属性id',
	goods_attr_str varchar(300) not null default '' comment '商品属性字符串',
	goods_number int unsigned not null default '1' comment '购买的数量',
	key member_id(member_id)
)engine=InnoDB default charset=utf8 comment '购物车';

DROP TABLE IF EXISTS sh_address;
CREATE TABLE sh_address(
	id mediumint unsigned not null auto_increment,
	shr_username varchar(30) not null comment '收货人姓名',
	shr_province varchar(30) not null comment '收货人所在省',
	shr_city varchar(30) not null comment '收货人所在城市',
	shr_area varchar(30) not null comment '收货人所在地区',
	shr_address varchar(30) not null comment '收货人详细地址',
	shr_phone varchar(30) not null comment '收货人电话',
	member_id mediumint unsigned not null comment '会员id',
	primary key (id),
	key member_id(member_id)
)engine=InnoDB default charset=utf8 comment '收货人信息';


















