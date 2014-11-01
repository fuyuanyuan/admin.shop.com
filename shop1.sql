-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 07 月 01 日 10:15
-- 服务器版本: 5.5.20
-- PHP 版本: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `shop1`
--

-- --------------------------------------------------------

--
-- 表的结构 `sh_ad`
--

CREATE TABLE IF NOT EXISTS `sh_ad` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `ad_name` varchar(30) NOT NULL COMMENT '广告名称',
  `ad_type` enum('jq','img','text','code') NOT NULL COMMENT '广告类型@radio|jq-img-text-code',
  `is_on` enum('是','否') NOT NULL COMMENT '是否启用@radio|是-否',
  `ad_link` varchar(150) NOT NULL DEFAULT '' COMMENT '链接地址',
  `ad_img` varchar(150) NOT NULL DEFAULT '' COMMENT '图片',
  `ad_text` varchar(600) NOT NULL DEFAULT '' COMMENT '文字/代码',
  `pos_id` smallint(5) unsigned NOT NULL COMMENT '广告位id',
  PRIMARY KEY (`id`),
  KEY `pos_id` (`pos_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='广告' AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `sh_ad`
--

INSERT INTO `sh_ad` (`id`, `ad_name`, `ad_type`, `is_on`, `ad_link`, `ad_img`, `ad_text`, `pos_id`) VALUES
(15, '兑换中心图片', 'jq', '是', '', '', '', 1),
(16, 'jqf', 'img', '是', 'http://www.sina.com', 'Ad/2014-06-22/53a678126bf77.jpg', '', 2),
(17, '图片', 'jq', '否', '', '', '', 2),
(18, 're', 'img', '是', 'fdsafadsf', 'Ad/2014-06-24/53a8edeec48e8.jpg', '', 3),
(19, 'gfds', 'jq', '是', '', '', '', 4);

-- --------------------------------------------------------

--
-- 表的结构 `sh_address`
--

CREATE TABLE IF NOT EXISTS `sh_address` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `shr_username` varchar(30) NOT NULL COMMENT '收货人姓名',
  `shr_province` varchar(30) NOT NULL COMMENT '收货人所在省',
  `shr_city` varchar(30) NOT NULL COMMENT '收货人所在城市',
  `shr_area` varchar(30) NOT NULL COMMENT '收货人所在地区',
  `shr_address` varchar(30) NOT NULL COMMENT '收货人详细地址',
  `shr_phone` varchar(30) NOT NULL COMMENT '收货人电话',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='收货人信息' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `sh_address`
--

INSERT INTO `sh_address` (`id`, `shr_username`, `shr_province`, `shr_city`, `shr_area`, `shr_address`, `shr_phone`, `member_id`) VALUES
(2, '吴英雷', '河北省', '秦皇岛', '海港区', '西三旗', '13333223345', 1),
(3, '韩顺平', '江西省', '南昌市', '东湖区', '西三旗', '3131', 1),
(4, '韩忠康', '河南省', '平顶山市', '新华区', '西三旗', '3131', 1);

-- --------------------------------------------------------

--
-- 表的结构 `sh_admin`
--

CREATE TABLE IF NOT EXISTS `sh_admin` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(15) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `salt` char(6) NOT NULL COMMENT '密钥',
  `role_id` mediumint(8) unsigned NOT NULL COMMENT '角色',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `sh_admin`
--

INSERT INTO `sh_admin` (`id`, `username`, `password`, `salt`, `role_id`) VALUES
(1, 'admin', 'c8d8ac54ddff192bff97aee8ee082726', 'e^2#3R', 1),
(2, 'admin123', '49de31ca8daa328bdd99c7fc86ec462b', '3307cb', 4);

-- --------------------------------------------------------

--
-- 表的结构 `sh_ad_pos`
--

CREATE TABLE IF NOT EXISTS `sh_ad_pos` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pos_name` varchar(30) NOT NULL COMMENT '广告位名称',
  `pos_width` smallint(5) unsigned NOT NULL COMMENT '广告位宽',
  `pos_height` smallint(5) unsigned NOT NULL COMMENT '广告位高',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='广告位' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `sh_ad_pos`
--

INSERT INTO `sh_ad_pos` (`id`, `pos_name`, `pos_width`, `pos_height`) VALUES
(1, '首页中间轮换广告', 670, 400),
(2, '首页上面右侧图片', 310, 70),
(3, '首页php大类左侧广告位', 205, 170),
(4, 'php大类右侧广告位', 310, 105);

-- --------------------------------------------------------

--
-- 表的结构 `sh_attribute`
--

CREATE TABLE IF NOT EXISTS `sh_attribute` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `attr_name` varchar(30) NOT NULL COMMENT '属性名称',
  `attr_type` enum('单选','唯一') NOT NULL DEFAULT '唯一' COMMENT '属性类型@radio|唯一-单选',
  `attr_value` varchar(300) NOT NULL DEFAULT '' COMMENT '属性可选值',
  `type_id` mediumint(8) unsigned NOT NULL COMMENT '类型的id',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='属性' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `sh_attribute`
--

INSERT INTO `sh_attribute` (`id`, `attr_name`, `attr_type`, `attr_value`, `type_id`) VALUES
(3, '颜色', '单选', '白色,黑色,蓝色', 4),
(4, '尺码', '单选', '38,39,40,41', 4),
(5, '几块', '唯一', '', 4);

-- --------------------------------------------------------

--
-- 表的结构 `sh_brand`
--

CREATE TABLE IF NOT EXISTS `sh_brand` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `brand_name` varchar(30) NOT NULL COMMENT '品牌名称',
  `logo` varchar(150) NOT NULL DEFAULT '' COMMENT '品牌logo',
  `site` varchar(150) NOT NULL DEFAULT '' COMMENT '品牌的官方网站',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='品牌' AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `sh_brand`
--

INSERT INTO `sh_brand` (`id`, `brand_name`, `logo`, `site`) VALUES
(9, 'Dell', 'Brand/2014-06-17/539f9f68532c7.jpg', ''),
(10, '明基', 'Brand/2014-06-20/53a384a09d0cd.jpg', ''),
(11, '双星', 'Brand/2014-06-20/53a384a5e6b2d.jpg', '');

-- --------------------------------------------------------

--
-- 表的结构 `sh_button`
--

CREATE TABLE IF NOT EXISTS `sh_button` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `btn_name` varchar(30) NOT NULL COMMENT '按钮名称',
  `btn_pos` enum('top','mid','bottom') NOT NULL COMMENT '位置@radio|top-mid-bottom',
  `btn_link` varchar(150) NOT NULL COMMENT '跳转地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `sh_button`
--

INSERT INTO `sh_button` (`id`, `btn_name`, `btn_pos`, `btn_link`) VALUES
(1, '我的定单', 'top', 'http://www.shop.com/index.php/Member/Order/lst'),
(2, '积分兑换', 'mid', 'xxx'),
(3, '关于我们', 'bottom', 'xxxx'),
(4, '百度', 'bottom', 'http://www.baidu.com'),
(5, '收藏', 'top', 'dd');

-- --------------------------------------------------------

--
-- 表的结构 `sh_cart`
--

CREATE TABLE IF NOT EXISTS `sh_cart` (
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品的id',
  `goods_attr_id` varchar(150) NOT NULL DEFAULT '' COMMENT '商品属性id',
  `goods_attr_str` varchar(300) NOT NULL DEFAULT '' COMMENT '商品属性字符串',
  `goods_number` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '购买的数量',
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购物车';

-- --------------------------------------------------------

--
-- 表的结构 `sh_category`
--

CREATE TABLE IF NOT EXISTS `sh_category` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cat_name` varchar(50) NOT NULL COMMENT '分类名称',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类',
  `is_rec` enum('是','否') NOT NULL COMMENT '是否推荐@radio|否-是',
  `pos_id1` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '广告位1',
  `pos_id2` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '广告位2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品分类' AUTO_INCREMENT=28 ;

--
-- 转存表中的数据 `sh_category`
--

INSERT INTO `sh_category` (`id`, `cat_name`, `parent_id`, `is_rec`, `pos_id1`, `pos_id2`) VALUES
(13, 'php', 0, '是', 3, 4),
(14, 'oop', 13, '是', 0, 0),
(15, '__GET', 14, '否', 0, 0),
(16, '3d', 0, '是', 0, 0),
(17, 'directx', 16, '否', 0, 0),
(18, 'opengl', 16, '是', 0, 0),
(19, '渲染', 17, '否', 0, 0),
(20, '声音', 17, '否', 0, 0),
(21, '管道', 18, '否', 0, 0),
(22, '网格', 18, '否', 0, 0),
(23, 'xml', 13, '否', 0, 0),
(24, 'simple_xml_object', 23, '否', 0, 0),
(25, '函数', 13, '是', 0, 0),
(26, '数据类型', 13, '否', 0, 0),
(27, 'unity3d', 16, '否', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `sh_goods`
--

CREATE TABLE IF NOT EXISTS `sh_goods` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `goods_sn` char(16) NOT NULL COMMENT '商品编号',
  `goods_name` varchar(60) NOT NULL COMMENT '商品名称',
  `sm_logo` varchar(150) NOT NULL COMMENT '小图',
  `mid_logo` varchar(150) NOT NULL COMMENT '中图',
  `big_logo` varchar(150) NOT NULL COMMENT '大图',
  `logo` varchar(150) NOT NULL COMMENT '原图',
  `market_price` decimal(10,2) NOT NULL COMMENT '市场价',
  `shop_price` decimal(10,2) NOT NULL COMMENT '本店价',
  `cat_id` mediumint(8) unsigned NOT NULL COMMENT '商品分类',
  `brand_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '品牌',
  `is_on_sale` enum('是','否') NOT NULL COMMENT '是否上架@radio|是-否',
  `addtime` datetime NOT NULL COMMENT '添加时间',
  `goods_desc` text COMMENT '商品描述',
  `type_id` mediumint(8) unsigned NOT NULL COMMENT '商品的类型',
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `brand_id` (`brand_id`),
  KEY `shop_price` (`shop_price`),
  KEY `is_on_sale` (`is_on_sale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品' AUTO_INCREMENT=32 ;

--
-- 转存表中的数据 `sh_goods`
--

INSERT INTO `sh_goods` (`id`, `goods_sn`, `goods_name`, `sm_logo`, `mid_logo`, `big_logo`, `logo`, `market_price`, `shop_price`, `cat_id`, `brand_id`, `is_on_sale`, `addtime`, `goods_desc`, `type_id`) VALUES
(18, '2014061818', '20讲的OOP--声音', 'Goods/2014-06-18/sm_53a10bad6e80a.jpg', 'Goods/2014-06-18/mid_53a10bad6e80a.jpg', 'Goods/2014-06-18/big_53a10bad6e80a.jpg', 'Goods/2014-06-18/53a10bad6e80a.jpg', '123.00', '312.43', 23, 10, '是', '2014-06-18 11:46:53', '', 4),
(19, '2014061819', '20讲的OOP', 'Goods/2014-06-18/sm_53a10c4227f0a.jpg', 'Goods/2014-06-18/mid_53a10c4227f0a.jpg', 'Goods/2014-06-18/big_53a10c4227f0a.jpg', 'Goods/2014-06-18/53a10c4227f0a.jpg', '123.00', '312.00', 13, 0, '是', '2014-06-18 11:49:22', '', 0),
(20, '2014061820', '20讲的OOP-php大类', 'Goods/2014-06-18/sm_53a10c9c01c41.jpg', 'Goods/2014-06-18/mid_53a10c9c01c41.jpg', 'Goods/2014-06-18/big_53a10c9c01c41.jpg', 'Goods/2014-06-18/53a10c9c01c41.jpg', '123.00', '312.00', 13, 11, '是', '2014-06-18 11:50:52', '', 0),
(21, '2014061821', '20讲的OOP-333', 'Goods/2014-06-18/sm_53a10ca393a82.jpg', 'Goods/2014-06-18/mid_53a10ca393a82.jpg', 'Goods/2014-06-18/big_53a10ca393a82.jpg', 'Goods/2014-06-18/53a10ca393a82.jpg', '123.00', '312.00', 25, 0, '是', '2014-06-18 11:50:59', '', 0),
(22, '2014061822', '20讲的OOP', 'Goods/2014-06-18/sm_53a1453329f27.jpg', 'Goods/2014-06-18/mid_53a1453329f27.jpg', 'Goods/2014-06-18/big_53a1453329f27.jpg', 'Goods/2014-06-18/53a1453329f27.jpg', '123.00', '312.00', 13, 0, '是', '2014-06-18 14:13:11', '', 0),
(23, '2014061823', '20讲的OOP', 'Goods/2014-06-18/sm_53a145197d743.jpg', 'Goods/2014-06-18/mid_53a145197d743.jpg', 'Goods/2014-06-18/big_53a145197d743.jpg', 'Goods/2014-06-18/53a145197d743.jpg', '123.00', '312.00', 13, 0, '是', '2014-06-18 14:18:44', '', 4),
(24, '2014061824', '20讲的OOP', 'Goods/2014-06-18/sm_53a14512686eb.jpg', 'Goods/2014-06-18/mid_53a14512686eb.jpg', 'Goods/2014-06-18/big_53a14512686eb.jpg', 'Goods/2014-06-18/53a14512686eb.jpg', '123.00', '312.00', 14, 0, '是', '2014-06-18 14:23:21', '', 0),
(25, '2014061825', '20讲的OOP', 'Goods/2014-06-18/sm_53a138bf1c80c.jpg', 'Goods/2014-06-18/mid_53a138bf1c80c.jpg', 'Goods/2014-06-18/big_53a138bf1c80c.jpg', 'Goods/2014-06-18/53a138bf1c80c.jpg', '123.00', '312.00', 13, 0, '是', '2014-06-18 14:53:01', '<p>添加一个</p>', 4),
(27, '2014062227', '20讲的OOP', 'Goods/2014-06-22/sm_53a625d04a1d0.jpg', 'Goods/2014-06-22/mid_53a625d04a1d0.jpg', 'Goods/2014-06-22/big_53a625d04a1d0.jpg', 'Goods/2014-06-22/53a625d04a1d0.jpg', '123.00', '312.00', 13, 0, '是', '2014-06-22 08:39:44', '', 0),
(28, '2014062228', '20讲的OOP-123', 'Goods/2014-06-22/sm_53a625f640f2e.jpg', 'Goods/2014-06-22/mid_53a625f640f2e.jpg', 'Goods/2014-06-22/big_53a625f640f2e.jpg', 'Goods/2014-06-22/53a625f640f2e.jpg', '123.00', '312.00', 14, 0, '是', '2014-06-22 08:40:22', '', 0),
(29, '2014062229', '20讲的OOP', '', '', '', '', '123.00', '312.00', 15, 0, '是', '2014-06-22 16:46:04', '', 0),
(30, '2014062430', '20讲的OOP--__GET', 'Goods/2014-06-24/sm_53a8f63d0aad2.jpg', 'Goods/2014-06-24/mid_53a8f63d0aad2.jpg', 'Goods/2014-06-24/big_53a8f63d0aad2.jpg', 'Goods/2014-06-24/53a8f63d0aad2.jpg', '123.00', '312.00', 15, 10, '是', '2014-06-24 09:49:58', '', 0),
(31, '2014062431', '20讲的OOP', '', '', '', '', '123.00', '100.00', 14, 9, '是', '2014-06-24 10:14:53', '', 4);

-- --------------------------------------------------------

--
-- 表的结构 `sh_goods_attr`
--

CREATE TABLE IF NOT EXISTS `sh_goods_attr` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `attr_id` mediumint(8) unsigned NOT NULL COMMENT '属性id',
  `attr_value` varchar(150) NOT NULL DEFAULT '' COMMENT '属性值',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品的id',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品属性' AUTO_INCREMENT=37 ;

--
-- 转存表中的数据 `sh_goods_attr`
--

INSERT INTO `sh_goods_attr` (`id`, `attr_id`, `attr_value`, `goods_id`) VALUES
(12, 3, '白色', 23),
(13, 4, '38', 23),
(14, 4, '39', 23),
(15, 4, '40', 23),
(16, 5, '3', 23),
(17, 3, '黑色', 25),
(20, 5, '3', 25),
(24, 4, '41', 25),
(27, 4, '38', 18),
(28, 4, '39', 18),
(29, 4, '40', 18),
(30, 3, '白色', 25),
(31, 3, '蓝色', 25),
(32, 4, '40', 25),
(33, 3, '白色', 31),
(34, 3, '黑色', 31),
(35, 4, '38', 31),
(36, 4, '39', 31);

-- --------------------------------------------------------

--
-- 表的结构 `sh_goods_impression`
--

CREATE TABLE IF NOT EXISTS `sh_goods_impression` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(10) NOT NULL COMMENT '印象名称',
  `num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '印象的次数',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品印象' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `sh_goods_impression`
--

INSERT INTO `sh_goods_impression` (`id`, `title`, `num`, `goods_id`) VALUES
(1, '&lt;!--', 1, 18);

-- --------------------------------------------------------

--
-- 表的结构 `sh_goods_news`
--

CREATE TABLE IF NOT EXISTS `sh_goods_news` (
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `news_id` mediumint(8) unsigned NOT NULL COMMENT '新闻id',
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品新闻';

--
-- 转存表中的数据 `sh_goods_news`
--

INSERT INTO `sh_goods_news` (`goods_id`, `news_id`) VALUES
(31, 9);

-- --------------------------------------------------------

--
-- 表的结构 `sh_goods_pics`
--

CREATE TABLE IF NOT EXISTS `sh_goods_pics` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sm_logo` varchar(150) NOT NULL COMMENT '小图',
  `mid_logo` varchar(150) NOT NULL COMMENT '中图',
  `big_logo` varchar(150) NOT NULL COMMENT '大图',
  `logo` varchar(150) NOT NULL COMMENT '原图',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品的id',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品相册' AUTO_INCREMENT=23 ;

--
-- 转存表中的数据 `sh_goods_pics`
--

INSERT INTO `sh_goods_pics` (`id`, `sm_logo`, `mid_logo`, `big_logo`, `logo`, `goods_id`) VALUES
(14, 'Goods/2014-06-18/sm_53a13d9a29f22.jpg', 'Goods/2014-06-18/mid_53a13d9a29f22.jpg', 'Goods/2014-06-18/big_53a13d9a29f22.jpg', 'Goods/2014-06-18/53a13d9a29f22.jpg', 25),
(16, 'Goods/2014-06-18/sm_53a13da33d57e.jpg', 'Goods/2014-06-18/mid_53a13da33d57e.jpg', 'Goods/2014-06-18/big_53a13da33d57e.jpg', 'Goods/2014-06-18/53a13da33d57e.jpg', 25),
(17, 'Goods/2014-06-20/sm_53a384525a1a1.jpg', 'Goods/2014-06-20/mid_53a384525a1a1.jpg', 'Goods/2014-06-20/big_53a384525a1a1.jpg', 'Goods/2014-06-20/53a384525a1a1.jpg', 24),
(18, 'Goods/2014-06-20/sm_53a384525b613.jpg', 'Goods/2014-06-20/mid_53a384525b613.jpg', 'Goods/2014-06-20/big_53a384525b613.jpg', 'Goods/2014-06-20/53a384525b613.jpg', 24),
(19, 'Goods/2014-06-20/sm_53a384525cd45.jpg', 'Goods/2014-06-20/mid_53a384525cd45.jpg', 'Goods/2014-06-20/big_53a384525cd45.jpg', 'Goods/2014-06-20/53a384525cd45.jpg', 24),
(20, 'Goods/2014-06-20/sm_53a3847c04b52.jpg', 'Goods/2014-06-20/mid_53a3847c04b52.jpg', 'Goods/2014-06-20/big_53a3847c04b52.jpg', 'Goods/2014-06-20/53a3847c04b52.jpg', 18),
(22, 'Goods/2014-06-20/sm_53a3847c07523.jpg', 'Goods/2014-06-20/mid_53a3847c07523.jpg', 'Goods/2014-06-20/big_53a3847c07523.jpg', 'Goods/2014-06-20/53a3847c07523.jpg', 18);

-- --------------------------------------------------------

--
-- 表的结构 `sh_goods_remark`
--

CREATE TABLE IF NOT EXISTS `sh_goods_remark` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `content` varchar(300) NOT NULL COMMENT '内容',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `member_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `addtime` datetime NOT NULL COMMENT '评论时间',
  `star` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '打分',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品评论' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `sh_goods_remark`
--

INSERT INTO `sh_goods_remark` (`id`, `content`, `goods_id`, `member_id`, `addtime`, `star`) VALUES
(1, 'fdafdsa', 18, 0, '2014-06-25 16:29:00', 5),
(2, '测试一下', 20, 0, '2014-07-01 15:02:53', 5),
(3, '测试一下吧', 18, 0, '2014-07-01 15:23:53', 5),
(4, 'fdsafds', 21, 0, '2014-07-01 16:14:50', 5);

-- --------------------------------------------------------

--
-- 表的结构 `sh_history`
--

CREATE TABLE IF NOT EXISTS `sh_history` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品的id',
  `addtime` int(10) unsigned NOT NULL COMMENT '浏览时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='浏览历史' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `sh_history`
--

INSERT INTO `sh_history` (`id`, `member_id`, `goods_id`, `addtime`) VALUES
(1, 1, 18, 1404010940),
(2, 1, 21, 1404013539),
(3, 1, 25, 1404010942),
(4, 1, 28, 1404014121),
(5, 1, 20, 1403940809);

-- --------------------------------------------------------

--
-- 表的结构 `sh_jq_info`
--

CREATE TABLE IF NOT EXISTS `sh_jq_info` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `img` varchar(150) NOT NULL COMMENT '图片地址',
  `ad_link` varchar(150) NOT NULL COMMENT '链接地址',
  `ad_id` mediumint(8) unsigned NOT NULL COMMENT '广告的id',
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='广告' AUTO_INCREMENT=16 ;

--
-- 转存表中的数据 `sh_jq_info`
--

INSERT INTO `sh_jq_info` (`id`, `img`, `ad_link`, `ad_id`) VALUES
(5, 'Ad/2014-06-21/53a52b0d2ed00.jpg', 'http://www.baidu.com123123123', 15),
(6, 'Ad/2014-06-21/53a52b0d303ae.jpg', 'http://www.baidu.com123123123', 15),
(7, 'Ad/2014-06-21/53a52b0d31a06.jpg', 'http://www.baidu.com', 15),
(9, 'Ad/2014-06-22/53a67fa1ec3e0.jpg', '', 17),
(10, 'Ad/2014-06-22/53a67fa1edd21.jpg', '', 17),
(11, 'Ad/2014-06-24/53a8e6341f18b.jpg', '', 17),
(12, 'Ad/2014-06-22/53a67fa1f0228.jpg', '', 17),
(13, 'Ad/2014-06-24/53a8e558012b8.jpg', 'http://www.baidu.com', 17),
(14, 'Ad/2014-06-24/53a8edf9ddacc.jpg', 'http://www.baidu.com', 19),
(15, 'Ad/2014-06-24/53a8edf9df0bf.jpg', 'http://www.baidu.com123123123', 19);

-- --------------------------------------------------------

--
-- 表的结构 `sh_level_price`
--

CREATE TABLE IF NOT EXISTS `sh_level_price` (
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `level_id` mediumint(8) unsigned NOT NULL COMMENT '级别id',
  PRIMARY KEY (`goods_id`,`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员价格';

--
-- 转存表中的数据 `sh_level_price`
--

INSERT INTO `sh_level_price` (`price`, `goods_id`, `level_id`) VALUES
('100.00', 18, 1),
('123.00', 22, 1),
('23.00', 22, 3);

-- --------------------------------------------------------

--
-- 表的结构 `sh_member`
--

CREATE TABLE IF NOT EXISTS `sh_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `salt` char(6) NOT NULL COMMENT '密钥',
  `email` varchar(60) NOT NULL COMMENT 'Email',
  `reg_time` datetime NOT NULL COMMENT '注册时间',
  `jifen` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `sm_logo` varchar(150) NOT NULL DEFAULT '' COMMENT '小头像',
  `logo` varchar(150) NOT NULL DEFAULT '' COMMENT '大头像',
  `jyz` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='会员' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `sh_member`
--

INSERT INTO `sh_member` (`id`, `username`, `password`, `salt`, `email`, `reg_time`, `jifen`, `mobile`, `sm_logo`, `logo`, `jyz`, `money`) VALUES
(1, 'fortheday', 'ebbafb95a6f13c5c4b0c551f6414bece', '3d392f', 'fortheday@126.com', '0000-00-00 00:00:00', 5056, '', '', '', 5056, '281.97'),
(2, 'wylwyl', '9a1244c95a8c8bfc4fbce098a7a1b727', 'ce51b6', 'fortheday@126.com', '2014-06-24 17:08:44', 0, '', '', '', 0, '0.00'),
(3, 'fortheday1', '197af51dab23409a52e306e03b6786df', '171b67', 'forthed2ay@126.com', '2014-06-24 17:19:29', 0, '', '', '', 0, '0.00');

-- --------------------------------------------------------

--
-- 表的结构 `sh_member_level`
--

CREATE TABLE IF NOT EXISTS `sh_member_level` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `level_name` varchar(30) NOT NULL COMMENT '级别名称',
  `num_bottom` int(10) unsigned NOT NULL COMMENT '积分下限',
  `num_top` int(10) unsigned NOT NULL COMMENT '积分上限',
  `rate` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '折扣率',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='会员级别' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `sh_member_level`
--

INSERT INTO `sh_member_level` (`id`, `level_name`, `num_bottom`, `num_top`, `rate`) VALUES
(1, '注册会员', 0, 10000, 90),
(2, '中级会员', 10001, 40000, 100),
(3, '高级会员', 40001, 200000, 100);

-- --------------------------------------------------------

--
-- 表的结构 `sh_news`
--

CREATE TABLE IF NOT EXISTS `sh_news` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `isshow` enum('是','否') NOT NULL COMMENT '是否显示@radio|是-否',
  `addtime` datetime NOT NULL COMMENT '添加时间',
  `click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `cat_id` mediumint(8) unsigned NOT NULL COMMENT '所在分类',
  PRIMARY KEY (`id`),
  KEY `cat_id_k` (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='新闻' AUTO_INCREMENT=14 ;

--
-- 转存表中的数据 `sh_news`
--

INSERT INTO `sh_news` (`id`, `title`, `content`, `isshow`, `addtime`, `click`, `cat_id`) VALUES
(9, '天龙八部1', '&lt;p&gt;fdsafd&lt;/p&gt;', '是', '2014-06-24 14:41:49', 3, 2),
(10, '天龙八部12', '&lt;p&gt;厅2&lt;/p&gt;', '是', '2014-06-24 14:55:33', 2, 4),
(11, 'SDds', '&lt;p&gt;21122&lt;/p&gt;', '是', '2014-06-24 14:55:43', 122, 2),
(12, '靶标ff', '&lt;p&gt;土土土土&lt;/p&gt;', '是', '2014-06-24 14:56:20', 23, 2),
(13, '4432', '&lt;p&gt;2332&lt;/p&gt;', '是', '2014-06-24 14:56:28', 32, 4);

-- --------------------------------------------------------

--
-- 表的结构 `sh_news_cat`
--

CREATE TABLE IF NOT EXISTS `sh_news_cat` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cat_name` varchar(50) NOT NULL COMMENT '分类名称',
  `is_help` enum('是','否') NOT NULL COMMENT '是否帮助@radio|否-是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='新闻分类' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `sh_news_cat`
--

INSERT INTO `sh_news_cat` (`id`, `cat_name`, `is_help`) VALUES
(2, '配送方式', '是'),
(3, '购物指南', '是'),
(4, '站内快讯', '否');

-- --------------------------------------------------------

--
-- 表的结构 `sh_order`
--

CREATE TABLE IF NOT EXISTS `sh_order` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `order_sn` varchar(16) NOT NULL COMMENT '定单编号',
  `addtime` datetime NOT NULL COMMENT '下单时间',
  `pay_status` enum('未支付','已支付') NOT NULL DEFAULT '未支付' COMMENT '支付状态@radio|未支付-已支付',
  `post_status` enum('未发货','已发送','已收货','退货中','已退货') NOT NULL DEFAULT '未发货' COMMENT '送货状态@radio|未发货-已发送-已收货-退货中-已退货',
  `total_price` decimal(10,2) NOT NULL COMMENT '定单总价',
  `shr_username` varchar(30) NOT NULL COMMENT '收货人姓名',
  `shr_province` varchar(30) NOT NULL COMMENT '收货人所在省',
  `shr_city` varchar(30) NOT NULL COMMENT '收货人所在城市',
  `shr_area` varchar(30) NOT NULL COMMENT '收货人所在地区',
  `shr_address` varchar(30) NOT NULL COMMENT '收货人详细地址',
  `shr_phone` varchar(30) NOT NULL COMMENT '收货人电话',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `pay_method` tinyint(3) unsigned NOT NULL COMMENT '支付方式：1.支付宝 2.余额支付',
  `post_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `order_sn` (`order_sn`),
  KEY `pay_status` (`pay_status`),
  KEY `post_status` (`post_status`),
  KEY `addtime` (`addtime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='定单' AUTO_INCREMENT=16 ;

--
-- 转存表中的数据 `sh_order`
--

INSERT INTO `sh_order` (`id`, `order_sn`, `addtime`, `pay_status`, `post_status`, `total_price`, `shr_username`, `shr_province`, `shr_city`, `shr_area`, `shr_address`, `shr_phone`, `member_id`, `pay_method`, `post_time`) VALUES
(13, '2014062913', '2014-06-29 11:00:16', '已支付', '已收货', '1965.99', '吴英雷', '河北省', '秦皇岛', '海港区', '西三旗', '13333223345', 1, 1, 1404011587),
(15, '2014062915', '2014-06-29 11:52:43', '已支付', '已收货', '842.40', '韩顺平', '江西省', '南昌市', '东湖区', '西三旗', '3131', 1, 2, 1404197739);

-- --------------------------------------------------------

--
-- 表的结构 `sh_order_goods`
--

CREATE TABLE IF NOT EXISTS `sh_order_goods` (
  `order_id` mediumint(8) unsigned NOT NULL COMMENT '定单id',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品id',
  `goods_logo` varchar(150) NOT NULL COMMENT '商品logo',
  `goods_name` varchar(60) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_number` int(10) unsigned NOT NULL COMMENT '购买的数量',
  `goods_attr_id` varchar(150) NOT NULL DEFAULT '' COMMENT '商品属性的id',
  `goods_attr_str` varchar(150) NOT NULL DEFAULT '' COMMENT '商品属性字符串',
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='定单商品';

--
-- 转存表中的数据 `sh_order_goods`
--

INSERT INTO `sh_order_goods` (`order_id`, `goods_id`, `goods_logo`, `goods_name`, `goods_price`, `goods_number`, `goods_attr_id`, `goods_attr_str`) VALUES
(13, 18, 'Goods/2014-06-18/mid_53a10bad6e80a.jpg', '20讲的OOP--声音', '281.19', 3, '29', '尺码:40<br />'),
(13, 21, 'Goods/2014-06-18/mid_53a10ca393a82.jpg', '20讲的OOP-333', '280.80', 3, '', ''),
(13, 28, 'Goods/2014-06-22/mid_53a625f640f2e.jpg', '20讲的OOP-123', '280.80', 3, '', ''),
(15, 21, 'Goods/2014-06-18/mid_53a10ca393a82.jpg', '20讲的OOP-333', '280.80', 3, '', '');

-- --------------------------------------------------------

--
-- 表的结构 `sh_privilege`
--

CREATE TABLE IF NOT EXISTS `sh_privilege` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pri_name` varchar(30) NOT NULL COMMENT '权限名称',
  `module_name` varchar(50) NOT NULL COMMENT '模块名称',
  `controller_name` varchar(50) NOT NULL COMMENT '控制器名称',
  `action_name` varchar(50) NOT NULL COMMENT '方法名称',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级权限',
  `pri_level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '级别,0:顶级 1：第二级 2：第三级',
  `pri_path` varchar(150) NOT NULL DEFAULT '0' COMMENT '所有的上级的ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限' AUTO_INCREMENT=97 ;

--
-- 转存表中的数据 `sh_privilege`
--

INSERT INTO `sh_privilege` (`id`, `pri_name`, `module_name`, `controller_name`, `action_name`, `parent_id`, `pri_level`, `pri_path`) VALUES
(1, '管理员模块', 'null', 'null', 'null', 0, 0, '0'),
(3, '权限列表', 'Admin', 'Privilege', 'lst', 1, 1, '0-1'),
(6, '添加权限', 'Admin', 'Privilege', 'add', 3, 2, '0-1-3'),
(7, '修改权限', 'Admin', 'Privilege', 'save', 3, 2, '0-1-3'),
(8, '管理员列表', 'Admin', 'Admin', 'lst', 1, 1, '0-1'),
(9, '添加管理员', 'Admin', 'Admin', 'add', 8, 2, '0-1-8'),
(10, '修改管理员', 'Admin', 'Admin', 'save', 8, 2, '0-1-8'),
(11, '角色列表', 'Admin', 'Role', 'lst', 1, 1, '0-1'),
(12, '添加角色', 'Admin', 'Role', 'add', 11, 2, '0-1-11'),
(13, '修改角色', 'Admin', 'Role', 'save', 11, 2, '0-1-11'),
(14, '删除角色', 'Admin', 'Role', 'del', 11, 2, '0-1-11'),
(15, '批量删除角色', 'Admin', 'Role', 'bdel', 11, 2, '0-1-11'),
(16, '删除权限', 'Admin', 'Privilege', 'del', 3, 2, '0-1-3'),
(17, '批量删除权限', 'Admin', 'Privilege', 'bdel', 3, 2, '0-1-3'),
(18, '删除管理员', 'Admin', 'Admin', 'del', 8, 2, '0-1-8'),
(19, '批量删除管理员', 'Admin', 'Admin', 'bdel', 8, 2, '0-1-8'),
(20, '商品模块', 'null', 'null', 'null', 0, 0, '0'),
(21, '商品分类', 'Goods', 'Category', 'lst', 20, 1, '0-20'),
(22, '商品品牌', 'Goods', 'Brand', 'lst', 20, 1, '0-20'),
(23, '商品类型', 'Goods', 'Type', 'lst', 20, 1, '0-20'),
(24, '商品列表', 'Goods', 'Goods', 'lst', 20, 1, '0-20'),
(25, '新闻模块', 'null', 'null', 'null', 0, 0, '0'),
(26, '新闻分类', 'News', 'NewsCat', 'lst', 25, 1, '0-25'),
(27, '新闻列表', 'News', 'News', 'lst', 25, 1, '0-25'),
(28, '会员模块', 'null', 'null', 'null', 0, 0, '0'),
(29, '会员级别', 'Member', 'MemberLevel', 'lst', 28, 1, '0-28'),
(30, '系统模块', 'null', 'null', 'null', 0, 0, '0'),
(31, '代码生成器', 'Gii', 'Index', 'index', 30, 1, '0-30'),
(32, '添加分类', 'Goods', 'Category', 'add', 21, 2, '0-20-21'),
(33, '修改分类', 'Goods', 'Category', 'save', 21, 2, '0-20-21'),
(34, '删除分类', 'Goods', 'Category', 'del', 21, 2, '0-20-21'),
(35, '批量删除分类', 'Goods', 'Category', 'bdel', 21, 2, '0-20-21'),
(36, '添加品牌', 'Goods', 'Brand', 'add', 22, 2, '0-20-22'),
(37, '修改品牌', 'Goods', 'Brand', 'save', 22, 2, '0-20-22'),
(38, '删除品牌', 'Goods', 'Brand', 'del', 22, 2, '0-20-22'),
(39, '批量删除品牌', 'Goods', 'Brand', 'bdel', 22, 2, '0-20-22'),
(40, '添加类型', 'Goods', 'Type', 'add', 23, 2, '0-20-23'),
(41, '修改类型', 'Goods', 'Type', 'save', 23, 2, '0-20-23'),
(42, '删除类型', 'Goods', 'Type', 'del', 23, 2, '0-20-23'),
(43, '批量删除类型', 'Goods', 'Type', 'bdel', 23, 2, '0-20-23'),
(44, '属性列表', 'Goods', 'Attribute', 'lst', 23, 2, '0-20-23'),
(45, '添加属性', 'Goods', 'Attribute', 'add', 23, 2, '0-20-23'),
(46, '修改属性', 'Goods', 'Attribute', 'save', 23, 2, '0-20-23'),
(47, '删除属性', 'Goods', 'Attribute', 'del', 23, 2, '0-20-23'),
(48, '批量删除属性', 'Goods', 'Attribute', 'bdel', 23, 2, '0-20-23'),
(49, '添加商品', 'Goods', 'Goods', 'add', 24, 2, '0-20-24'),
(50, '修改商品', 'Goods', 'Goods', 'save', 24, 2, '0-20-24'),
(51, '删除商品', 'Goods', 'Goods', 'del', 24, 2, '0-20-24'),
(52, '批量删除商品', 'Goods', 'Goods', 'bdel', 24, 2, '0-20-24'),
(53, 'ajax获取属性', 'Goods', 'Goods', 'ajaxGetAttr', 24, 2, '0-20-24'),
(54, 'ajax删除图片', 'Goods', 'Goods', 'ajaxDelImg', 24, 2, '0-20-24'),
(55, '货品管理', 'Goods', 'Goods', 'product', 24, 2, '0-20-24'),
(56, '添加分类', 'News', 'NewsCat', 'add', 26, 2, '0-25-26'),
(57, '修改分类', 'News', 'NewsCat', 'save', 26, 2, '0-25-26'),
(58, '删除分类', 'News', 'NewsCat', 'del', 26, 2, '0-25-26'),
(59, '批量删除分类', 'News', 'NewsCat', 'bdel', 26, 2, '0-25-26'),
(60, '添加新闻', 'News', 'News', 'add', 27, 2, '0-25-27'),
(61, '修改新闻', 'News', 'News', 'save', 27, 2, '0-25-27'),
(62, '删除新闻', 'News', 'News', 'del', 27, 2, '0-25-27'),
(63, '批量删除', 'News', 'News', 'bdel', 27, 2, '0-25-27'),
(64, '添加级别', 'Member', 'MemberLevel', 'add', 29, 2, '0-28-29'),
(65, '修改级别', 'Member', 'MemberLevel', 'save', 29, 2, '0-28-29'),
(66, '删除级别', 'Member', 'MemberLevel', 'del', 29, 2, '0-28-29'),
(67, '批量删除级别', 'Member', 'MemberLevel', 'bdel', 29, 2, '0-28-29'),
(68, '广告模块', 'null', 'null', 'null', 0, 0, '0'),
(69, '广告位列表', 'Ad', 'AdPos', 'lst', 68, 1, '0-68'),
(70, '添加广告位', 'Ad', 'AdPos', 'add', 69, 2, '0-68-69'),
(71, '修改广告位', 'Ad', 'AdPos', 'save', 69, 2, '0-68-69'),
(72, '删除广告位', 'Ad', 'AdPos', 'del', 69, 2, '0-68-69'),
(73, '批量删除广告位', 'Ad', 'AdPos', 'bdel', 69, 2, '0-68-69'),
(74, '广告列表', 'Ad', 'Ad', 'lst', 68, 1, '0-68'),
(75, '添加广告', 'Ad', 'Ad', 'add', 74, 2, '0-68-74'),
(76, '修改广告', 'Ad', 'Ad', 'save', 74, 2, '0-68-74'),
(77, '删除广告', 'Ad', 'Ad', 'del', 74, 2, '0-68-74'),
(78, '批量删除广告', 'Ad', 'Ad', 'bdel', 74, 2, '0-68-74'),
(79, 'ajax删除图片', 'Ad', 'Ad', 'ajaxDelImg', 74, 2, '0-68-74'),
(80, '前台按钮列表', 'Gii', 'Button', 'lst', 30, 1, '0-30'),
(81, '添加按钮', 'Gii', 'Button', 'add', 80, 2, '0-30-80'),
(82, '修改按钮 ', 'Gii', 'Button', 'save', 80, 2, '0-30-80'),
(83, '删除按钮', 'Gii', 'Button', 'del', 80, 2, '0-30-80'),
(84, '批量删除按钮', 'Gii', 'Button', 'bdel', 80, 2, '0-30-80'),
(85, '推荐位列表', 'Gii', 'Recommend', 'lst', 30, 1, '0-30'),
(86, '添加推荐位', 'Gii', 'Recommend', 'add', 85, 2, '0-30-85'),
(87, '修改推荐位', 'Gii', 'Recommend', 'save', 85, 2, '0-30-85'),
(88, '删除推荐位', 'Gii', 'Recommend', 'del', 85, 2, '0-30-85'),
(89, '批量删除推荐位', 'Gii', 'Recommend', 'bdel', 85, 2, '0-30-85'),
(90, 'ajax搜索文章', 'Goods', 'Goods', 'ajaxSearchArticles', 24, 2, '0-20-24'),
(91, '定单模块', 'null', 'null', 'null', 0, 0, '0'),
(92, '定单列表', 'Order', 'Order', 'lst', 91, 1, '0-91'),
(93, '修改定单', 'Order', 'Order', 'save', 92, 2, '0-91-92'),
(94, '设置为已支付', 'Order', 'Order', 'setPaid', 92, 2, '0-91-92'),
(95, '设置为已发货', 'Order', 'Order', 'setPosted', 92, 2, '0-91-92'),
(96, '设置为已退货', 'Order', 'Order', 'setRefund', 92, 2, '0-91-92');

-- --------------------------------------------------------

--
-- 表的结构 `sh_product`
--

CREATE TABLE IF NOT EXISTS `sh_product` (
  `goods_number` int(10) unsigned NOT NULL COMMENT '库存量',
  `goods_id` mediumint(8) unsigned NOT NULL COMMENT '商品的id',
  `goods_attr_id` varchar(150) NOT NULL COMMENT '属性的id如果有多个属性先排序用|隔开，如：1|3,1和3是指sh_goods_attr表中的id',
  PRIMARY KEY (`goods_id`,`goods_attr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='货品';

--
-- 转存表中的数据 `sh_product`
--

INSERT INTO `sh_product` (`goods_number`, `goods_id`, `goods_attr_id`) VALUES
(373, 18, '27'),
(370, 18, '28'),
(369, 18, '29'),
(111, 19, ''),
(53, 20, ''),
(560, 21, ''),
(321, 22, ''),
(111, 23, '13'),
(444, 23, '14'),
(321, 23, '15'),
(34, 24, ''),
(123, 25, '17,32'),
(43242, 25, '24,30'),
(491, 27, ''),
(774, 28, ''),
(554, 29, ''),
(308, 30, ''),
(23, 31, '33,35'),
(43, 31, '33,36'),
(321, 31, '34,35'),
(234, 31, '34,36');

-- --------------------------------------------------------

--
-- 表的结构 `sh_recommend`
--

CREATE TABLE IF NOT EXISTS `sh_recommend` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `rec_name` varchar(30) NOT NULL COMMENT '推荐位名称',
  `goods_id` varchar(150) NOT NULL DEFAULT '' COMMENT '商品id，如果这个位置上有多个商品就用,隔开',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='推荐位' AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `sh_recommend`
--

INSERT INTO `sh_recommend` (`id`, `rec_name`, `goods_id`) VALUES
(1, '疯狂抢购', '25,21,18,28'),
(2, '热卖商品', '25,21,28'),
(3, '推荐商品', '29'),
(4, '新品上架', '29'),
(5, '猜您喜欢', '29'),
(6, '首页中间大类推荐', '28,21,30,20,18');

-- --------------------------------------------------------

--
-- 表的结构 `sh_role`
--

CREATE TABLE IF NOT EXISTS `sh_role` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `role_name` varchar(30) NOT NULL COMMENT '角色名称',
  `privilege_id` varchar(150) NOT NULL DEFAULT '' COMMENT '权限,多个权限用,隔开如：1,2,3, *代表拥有所有的权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `sh_role`
--

INSERT INTO `sh_role` (`id`, `role_name`, `privilege_id`) VALUES
(1, '超级管理员', '*'),
(3, '管理管理员', '1,8,10'),
(4, '编辑', '68,69,70,71,72,73,74,75,76,77,78');

-- --------------------------------------------------------

--
-- 表的结构 `sh_type`
--

CREATE TABLE IF NOT EXISTS `sh_type` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `type_name` varchar(30) NOT NULL COMMENT '类型名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='类型' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `sh_type`
--

INSERT INTO `sh_type` (`id`, `type_name`) VALUES
(3, '笔记本'),
(4, '足球');

--
-- 限制导出的表
--

--
-- 限制表 `sh_attribute`
--
ALTER TABLE `sh_attribute`
  ADD CONSTRAINT `sh_attribute_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `sh_type` (`id`) ON DELETE CASCADE;

--
-- 限制表 `sh_news`
--
ALTER TABLE `sh_news`
  ADD CONSTRAINT `sh_news_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `sh_news_cat` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
