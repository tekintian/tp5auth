# thinkphp5 权限认证 RBAC 加 行为日志
这个插件主要有一整套RBAC  行为日志 视图 只需要 composer安装即可和你的系统融为一体

## 安装
~~~
> composer require tekintian/tp5auth
~~~
## v1.1更新
* 1.加入了行为日志
* 2.加入样式文件路由定义,

## v1.1.1新加入方法
~~~
is_login()                              判断是否登录
login($uid 用户ID,$nickname 用户昵称)    用户登录
logout()                               用户退出
checkPath($path 路由,$param 参数)       检查路由是否有权限 ，可以做按钮权限判断（ 已经加入助手函数可以直接使用）
注 更新废弃函数输入用户ID 和 用户昵称 ，加入 按钮的路由权限判断，

## 配置 v1.1
~~~
'tp5auth' =>[
        'style_directory' => '/static/admin/',
        'session_prefix'  => 'abc_',
  ]
~~~


可以不配置  配置以后Js css文件需要放到配置的目录里

## 手动加入日志  v1.1
~~~
    $auth = new Auth();
    $auth->admin = $list['user_name'];
    $auth->createLog('管理员<spen style=\'color: #1dd2af;\'>[ {name} ]</spen>偷偷的进入后台了,','后台登录');
~~~

## 视图调用
~~~
     public function _empty($name)
        {
            $auth =  new \tp5auth\auth\Auth();
            $auth = $auth->autoload($name);
            if($auth){
                if(isset($auth['code'])){
                    return json($auth);
                }elseif(isset($auth['file'])){
                    return $auth['file'];
                }
                $this->view->engine->layout(false);
                return $this->fetch($auth[0],$auth[1]);
            }
            return abort(404,'页面不存在');
        }
~~~
在模块中创建一个Auth控制器，把_empty方法复制上去，这样就可以访问以下视图

* /auth/role.html           角色列表
* /auth/roleAdd.html        角色添加
* /auth/roleEdit.html       角色修改
* /auth/authorize/id/2.html 权限设置
* /auth/menu.html           菜单列表
* /auth/menuAdd.html        菜单增加
* /auth/menuEdit.html       菜单修改
* /auth/log.html            行为日志    新v1.1
* /auth/viewLog.html        查看日志    新v1.1
* /auth/clear.html          清空日志    新v1.1
* /auth/adminAuthorize.html 独立权限    新v1.1.2

## 权限认证
~~~
     public function __construct()
        {
            parent::__construct();
            $auth                   = new Auth();
            $auth->noNeedCheckRules = ['index/index/index','index/index/home'];
            $auth->log              = true;                 // v1.1版本  日志开关默认true
            $user                   = $auth::is_login();

            if($user){//用户登录状态
                $this->uid = $user['uid'];
                if(!$auth->auth()){
                    return $this->error("你没有权限访问！");
                }
            }else{
                return $this->error("您还没有登录！",url("publics/login"));
            }
        }
~~~
这里在公共控制器上加入验证即可

##管理员独立权限
~~~
 url('auth/adminAuthorize',['id' => '用户ID','name'=>'用户昵称'])
~~~
## 授权菜单
~~~
 Auth::menuCheck();
~~~
这个方法返回授权及非隐藏的所有菜单，这样我们后台的菜单就可以根据管理员的权限来来展示授权的目录 




代码案例
 public function _empty($name)
    {
        $auth =  new \tp5auth\auth\Auth();
        $auth = $auth->autoload($name);
        if($auth){
            if(isset($auth['code'])){
                return json($auth);
            }elseif(isset($auth['file'])){
                return $auth['file'];
            }
            $this->view->engine->layout(false);
            return $this->fetch($auth[0],$auth[1]);
        }
     return abort(404,'页面不存在');
    }

创建一个控制器把这个_empty方法加进去会渲染rbac视图，这个我就不多讲了待会看图 

## 手动加入日志
    $auth = new Auth();
    $auth->admin = $list['user_name'];
    $auth->createLog('管理员<spen style=\'color: #1dd2af;\'>[ {name} ]</spen>偷偷的进入后台了,','后台登录');




路由目录
/auth/role.html 角色列表
/auth/roleAdd.html 角色添加
/auth/roleEdit.html 角色修改
/auth/authorize/id/2.html 权限设置
/auth/menu.html 菜单列表
/auth/menuAdd.html 菜单增加
/auth/menuEdit.html 菜单修改
/auth/log.html 行为日志 新v1.1
/auth/viewLog.html 查看日志 新v1.1
/auth/clear.html 清空日志 新v1.1
/auth/adminAuthorize.html 独立权限 新v1.1.2
    public function __construct()
        {
            parent::__construct();
            $auth                   = new Auth();
            $auth->noNeedCheckRules = ['index/index/index','index/index/home'];
            $auth->log              = true;                 // v1.1版本  日志开关默认true
            $user                   = $auth::is_login();
            if($user){//用户登录状态
                $this->uid = $user['uid'];
                if(!$auth->auth()){
                    return $this->error("你没有权限访问！");
                }
            }else{
                return $this->error("您还没有登录！",url("publics/login"));
            }
        }

这个是base控制器
$auth->noNeedCheckRules 为不需要权限认证的Url
$auth->auth 权限认证方法
到了这里你的权限rbac就搞定了
后台账户密码 admin admin
 Auth::menuCheck();
复制代码
这个方法返回授权及非隐藏的所有菜单，这样我们后台的菜单就可以根据管理员的权限来来展示授权的目录 


权限条件设置

假设我们需要index/extend/edit/id/875.html在这个rul上面设置权限只有＊角色能打开ID＝875 的应用编辑，那我们就可以在
［参数］ ＝ id=875 这里是菜单上的URL使用
［验证规则］ ＝ {id}==875 这里是权限认证条件判断 （{id}<=1 && {id} >=1 || in_array({id},[1,2,4,5] ) ）





## mysql文件
~~~
tp_action_log.sql
tp_auth_access.sql
tp_auth_role.sql
tp_auth_role_user.sql
tp_auth_rule.sql
tp_menu.sql
~~~

## 完整数据库脚本

/*
Navicat MySQL Data Transfer

Source Server         : mysql-5.7_x64
Source Server Version : 50715
Source Host           : localhost:3306
Source Database       : tpauth

Target Server Type    : MYSQL
Target Server Version : 50715
File Encoding         : 65001

Date: 2017-01-18 22:41:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tp_action_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_action_log`;
CREATE TABLE `tp_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `log` longtext NOT NULL COMMENT '日志备注',
  `log_url` varchar(255) NOT NULL COMMENT '执行的URL',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  `username` varchar(255) NOT NULL COMMENT '执行者',
  `title` varchar(255) NOT NULL COMMENT '标题',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表';

-- ----------------------------
-- Records of tp_action_log
-- ----------------------------
INSERT INTO `tp_action_log` VALUES ('109', '1', '2130706433', '5', '/index/auth/menuedit.html', '1484745934', 'admin', '测试5');
INSERT INTO `tp_action_log` VALUES ('110', '27', '2130706433', '管理员<spen style=\'color: #1dd2af;\'>[ tekin ]</spen>偷偷的进入后台了,', '/index/publics/login.html', '1484746112', 'tekin', '后台登录');
INSERT INTO `tp_action_log` VALUES ('111', '1', '2130706433', '管理员<spen style=\'color: #1dd2af;\'>[ admin ]</spen>偷偷的进入后台了,', '/index/publics/login.html', '1484746195', 'admin', '后台登录');
INSERT INTO `tp_action_log` VALUES ('112', '27', '2130706433', '管理员<spen style=\'color: #1dd2af;\'>[ tekin ]</spen>偷偷的进入后台了,', '/index/publics/login.html', '1484746265', 'tekin', '后台登录');
INSERT INTO `tp_action_log` VALUES ('113', '1', '2130706433', '管理员<spen style=\'color: #1dd2af;\'>[ admin ]</spen>偷偷的进入后台了,', '/index/publics/login.html', '1484746296', 'admin', '后台登录');
INSERT INTO `tp_action_log` VALUES ('114', '27', '2130706433', '管理员<spen style=\'color: #1dd2af;\'>[ tekin ]</spen>偷偷的进入后台了,', '/index/publics/login.html', '1484746331', 'tekin', '后台登录');
INSERT INTO `tp_action_log` VALUES ('115', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746336', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('116', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746340', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('117', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746342', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('118', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746344', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('119', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746346', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('120', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746351', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('121', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746353', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('122', '27', '2130706433', '5', '/index/auth/menuedit.html', '1484746354', 'tekin', '测试5');
INSERT INTO `tp_action_log` VALUES ('123', '1', '2130706433', '管理员<spen style=\'color: #1dd2af;\'>[ admin ]</spen>偷偷的进入后台了,', '/index/publics/login.html', '1484746396', 'admin', '后台登录');

-- ----------------------------
-- Table structure for tp_admin
-- ----------------------------
DROP TABLE IF EXISTS `tp_admin`;
CREATE TABLE `tp_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员自增ID',
  `user_name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `user_password` varchar(255) DEFAULT NULL COMMENT '管理员的密码',
  `user_nicename` varchar(255) DEFAULT NULL COMMENT '管理员的简称',
  `user_status` int(11) DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；',
  `user_email` varchar(255) DEFAULT '' COMMENT '邮箱',
  `last_login_ip` varchar(16) DEFAULT NULL COMMENT '最后登录ip',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `create_time` datetime DEFAULT NULL COMMENT '注册时间',
  `role` varchar(255) DEFAULT NULL COMMENT '角色ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
-- Records of tp_admin
-- ----------------------------
INSERT INTO `tp_admin` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', null, '1', 'admin@qq.com', '114.88.197.96', '2016-10-26 12:06:43', '2016-06-07 17:04:05', null);
INSERT INTO `tp_admin` VALUES ('16', 'zou', '21232f297a57a5a743894a0e4a801fc3', null, '1', 'zou1@qq.com', '127.0.0.1', '2016-07-17 17:01:36', '2016-07-08 15:29:41', '2');
INSERT INTO `tp_admin` VALUES ('23', 'sdasd', '0aa1ea9a5a04b78d4581dd6d17742627', null, '1', 'asdas@qq.com', null, null, '2016-11-15 16:55:36', '2,3');
INSERT INTO `tp_admin` VALUES ('27', 'tekin', '21232f297a57a5a743894a0e4a801fc3', null, '1', 'tekin@qq.com', null, null, '2017-01-18 21:14:40', '2');

-- ----------------------------
-- Table structure for tp_auth_access
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_access`;
CREATE TABLE `tp_auth_access` (
  `role_id` mediumint(8) unsigned NOT NULL COMMENT '角色',
  `rule_name` varchar(255) NOT NULL COMMENT '规则唯一英文标识,全小写',
  `type` varchar(30) DEFAULT NULL COMMENT '权限规则分类，请加应用前缀,如admin_',
  `menu_id` int(11) DEFAULT NULL COMMENT '后台菜单ID',
  KEY `role_id` (`role_id`),
  KEY `rule_name` (`rule_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限授权表';

-- ----------------------------
-- Records of tp_auth_access
-- ----------------------------
INSERT INTO `tp_auth_access` VALUES ('2', 'index/auth/default', 'admin_url', '1');
INSERT INTO `tp_auth_access` VALUES ('2', 'index/auth/default', 'admin_url', '2');
INSERT INTO `tp_auth_access` VALUES ('2', 'index/auth/log', 'admin_url', '20');
INSERT INTO `tp_auth_access` VALUES ('2', 'index/auth/viewlog', 'admin_url', '21');
INSERT INTO `tp_auth_access` VALUES ('2', 'index/index/sasd', 'admin_url', '15');
INSERT INTO `tp_auth_access` VALUES ('2', 'index/index/asd', 'admin_url', '16');
INSERT INTO `tp_auth_access` VALUES ('2', 'index/auth/menuedit', 'admin_url', '19');

-- ----------------------------
-- Table structure for tp_auth_role
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_role`;
CREATE TABLE `tp_auth_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '角色名称',
  `pid` smallint(6) DEFAULT '0' COMMENT '父角色ID',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `listorder` int(3) NOT NULL DEFAULT '0' COMMENT '排序字段',
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of tp_auth_role
-- ----------------------------
INSERT INTO `tp_auth_role` VALUES ('1', '超级管理员', '0', '1', '拥有网站最高管理员权限！', '1329633709', '1329633709', '0');
INSERT INTO `tp_auth_role` VALUES ('2', '文章管理', '0', '1', 'SDAS', '0', '0', '0');
INSERT INTO `tp_auth_role` VALUES ('3', 'abc', '0', '1', '', '0', '0', '0');

-- ----------------------------
-- Table structure for tp_auth_role_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_role_user`;
CREATE TABLE `tp_auth_role_user` (
  `role_id` int(11) unsigned DEFAULT '0' COMMENT '角色 id',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户角色对应表';

-- ----------------------------
-- Records of tp_auth_role_user
-- ----------------------------
INSERT INTO `tp_auth_role_user` VALUES ('2', '16');
INSERT INTO `tp_auth_role_user` VALUES ('2', '27');

-- ----------------------------
-- Table structure for tp_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_rule`;
CREATE TABLE `tp_auth_rule` (
  `menu_id` int(11) NOT NULL COMMENT '后台菜单 ID',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` varchar(30) NOT NULL DEFAULT '1' COMMENT '权限规则分类，请加应用前缀,如admin_',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `url_param` varchar(255) DEFAULT NULL COMMENT '额外url参数',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `rule_param` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  `nav_id` int(11) DEFAULT '0' COMMENT 'nav id',
  PRIMARY KEY (`menu_id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限规则表';

-- ----------------------------
-- Records of tp_auth_rule
-- ----------------------------
INSERT INTO `tp_auth_rule` VALUES ('2', 'index', 'admin_url', 'index/auth/default', '', '权限管理', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('3', 'index', 'admin_url', 'index/auth/role', '', '角色管理', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('4', 'index', 'admin_url', 'index/auth/roleadd', '', '角色增加', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('5', 'index', 'admin_url', 'index/auth/roleedit', '', '角色编辑', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('6', 'index', 'admin_url', 'index/auth/roledelete', '', '角色删除', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('7', 'index', 'admin_url', 'index/auth/authorize', '', '角色授权', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('8', 'index', 'admin_url', 'index/auth/menu', '', '菜单管理', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('9', 'index', 'admin_url', 'index/auth/menu', '', '菜单列表', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('10', 'index', 'admin_url', 'index/auth/menuadd', '', '菜单增加', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('11', 'index', 'admin_url', 'index/auth/menuedit', '', '菜单修改', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('12', 'index', 'admin_url', 'index/auth/menudelete', '', '菜单删除', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('13', 'index', 'admin_url', 'index/auth/menuorder', '', '菜单排序', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('14', 'index', 'admin_url', 'index/admin/index', '', '用户管理', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('15', 'index', 'admin_url', 'index/index/sasd', '', '测试', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('16', 'index', 'admin_url', 'index/index/asd', 'asd', '测试', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('17', 'index', 'admin_url', 'index/auth/menuedit', 'dasd', '边缘', '1', 'in_array({id},[1,2,4,5] )', '0');
INSERT INTO `tp_auth_rule` VALUES ('19', 'index', 'admin_url', 'index/auth/menuedit', 'id=5', '测试5', '1', '{id}==5', '0');
INSERT INTO `tp_auth_rule` VALUES ('20', 'index', 'admin_url', 'index/auth/log', '', '行为日志', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('21', 'index', 'admin_url', 'index/auth/viewlog', '', '查看日志', '1', '', '0');
INSERT INTO `tp_auth_rule` VALUES ('22', 'index', 'admin_url', 'index/auth/clear', '', '清空日志', '1', '', '0');

-- ----------------------------
-- Table structure for tp_menu
-- ----------------------------
DROP TABLE IF EXISTS `tp_menu`;
CREATE TABLE `tp_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `parent_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `app` char(20) NOT NULL COMMENT '应用名称app',
  `model` char(20) NOT NULL COMMENT '控制器',
  `action` char(20) NOT NULL COMMENT '操作名称',
  `url_param` char(50) NOT NULL COMMENT 'url参数',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '菜单类型  1：权限认证+菜单；0：只作为菜单',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，1显示，0不显示',
  `name` varchar(50) NOT NULL COMMENT '菜单名称',
  `icon` varchar(50) NOT NULL COMMENT '菜单图标',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `list_order` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序ID',
  `rule_param` varchar(255) NOT NULL COMMENT '验证规则',
  `nav_id` int(11) DEFAULT '0' COMMENT 'nav ID ',
  `request` varchar(255) NOT NULL COMMENT '请求方式（日志生成）',
  `log_rule` varchar(255) NOT NULL COMMENT '日志规则',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `model` (`model`),
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
-- Records of tp_menu
-- ----------------------------
INSERT INTO `tp_menu` VALUES ('1', '0', 'index', 'auth', 'default', '', '0', '1', '系统管理', '', '', '10', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('2', '1', 'index', 'auth', 'default', '', '0', '1', '权限管理', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('3', '2', 'index', 'auth', 'role', '', '1', '1', '角色管理', '', '1', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('4', '3', 'index', 'auth', 'roleAdd', '', '1', '0', '角色增加', '', '', '0', '', '0', '', '{id}');
INSERT INTO `tp_menu` VALUES ('5', '3', 'index', 'auth', 'roleEdit', '', '1', '0', '角色编辑', '', 'asdas', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('6', '3', 'index', 'auth', 'roleDelete', '', '1', '0', '角色删除', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('7', '3', 'index', 'auth', 'authorize', '', '1', '0', '角色授权', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('8', '1', 'index', 'auth', 'default', '', '0', '1', '菜单管理', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('9', '8', 'index', 'auth', 'menu', '', '1', '1', '菜单列表', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('10', '9', 'index', 'auth', 'menuAdd', '', '1', '0', '菜单增加', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('11', '9', 'index', 'auth', 'menuEdit', '', '1', '0', '菜单修改', '', '', '0', '', '0', 'POST', '我的ID是{id} <br> 记入的目录{name}');
INSERT INTO `tp_menu` VALUES ('12', '9', 'index', 'auth', 'menuDelete', '', '1', '0', '菜单删除', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('13', '9', 'index', 'auth', 'menuOrder', '', '1', '0', '菜单排序', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('14', '2', 'index', 'admin', 'index', '', '1', '1', '用户管理', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('15', '0', 'index', 'index', 'sasd', '', '0', '1', '测试', '', '', '20', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('16', '15', 'index', 'index', 'asd', 'asd', '1', '1', '测试', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('17', '15', 'index', 'auth', 'menuEdit', 'dasd', '1', '1', '边缘', '', '11q1adas1adsasdfsdfdsd', '0', 'in_array({id},[1,2,4,5] )', '0', '', '');
INSERT INTO `tp_menu` VALUES ('20', '2', 'index', 'auth', 'log', '', '1', '1', '行为日志', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('19', '16', 'index', 'auth', 'menuEdit', 'id=5', '1', '1', '测试5', '', 'dasd', '0', '{id}==5', '0', 'GET', '{id}');
INSERT INTO `tp_menu` VALUES ('21', '20', 'index', 'auth', 'viewLog', '', '1', '0', '查看日志', '', '', '0', '', '0', '', '');
INSERT INTO `tp_menu` VALUES ('22', '20', 'index', 'auth', 'clear', '', '1', '0', '清空日志', '', '', '0', '', '0', '', '');

