<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("Base_AdminController.php");
require_once("Base_AppController.php");
require_once("Base_MobileController.php");
require_once("Base_WechatPay.php");

// table names
$TABLE['admin']				= 'admin';
$TABLE['admin_role']		= 'admin_role';
$TABLE['admin_log']			= 'admin_log';
$TABLE['auth_code']			= 'auth_code';
$TABLE['app_version']		= 'app_version';
$TABLE['area']				= 'area';
$TABLE['area_limit']		= 'area_limit';
$TABLE['audit']				= 'audit';
$TABLE['challenge']			= 'challenge';
$TABLE['audit_config']		= 'audit_config';
$TABLE['baby']				= 'baby';
$TABLE['banner']			= 'banner';
$TABLE['category'] 			= 'category';
$TABLE['club']				= 'club';
$TABLE['club_image']		= 'club_image';
$TABLE['comment'] 			= 'comment';
$TABLE['config']			= 'config';
$TABLE['content']			= 'content';
$TABLE['content_article']	= 'content_article';
$TABLE['content_advert']	= 'content_advert';
$TABLE['content_gallery']	= 'content_gallery';
$TABLE['gallery_image']		= 'content_gallery_image';
$TABLE['content_live']		= 'content_live';
$TABLE['content_video']		= 'content_video';
$TABLE['event']				= 'event';
$TABLE['event_content']		= 'event_content';
$TABLE['event_counterpart']	= 'event_counterpart';
$TABLE['ticket_price']		= 'event_ticket_price';
$TABLE['feedback']			= 'feedback';
$TABLE['gift']				= 'gift';
$TABLE['like']				= 'like';
$TABLE['link']				= 'link';
$TABLE['link_content']		= 'link_content';
$TABLE['member']			= 'member';
$TABLE['member_content']	= 'member_content';
$TABLE['message']			= 'message';
$TABLE['order']				= 'order';
$TABLE['organization']		= 'organization';
$TABLE['restrict_vocab']	= 'restrict_vocab';
$TABLE['session']			= 'session';
$TABLE['splash']			= 'splash';
$TABLE['bgimage']			= 'bgimage';
$TABLE['user']				= 'user';
$TABLE['user_rank']			= 'user_rank';
$TABLE['visit']				= 'visit';
$TABLE['ranking']			= 'ranking';



// device type
define('DEVICE_TYPE_ALL', 				0);
define('DEVICE_TYPE_IPHONE', 			1);
define('DEVICE_TYPE_ANDROID',			2);

// standard page size
define('PAGE_SIZE', 					10);

// area type
define('AREA_TYPE_COUNTRY', 			0);
define('AREA_TYPE_PROVINCE', 			1);
define('AREA_TYPE_CITY', 				2);
define('AREA_TYPE_DISTRICT', 			3);

// area limit type
define('AREA_LIMIT_BLACKLIST', 			0);
define('AREA_LIMIT_WHITELIST', 			1);

// area limit item kind
define('AREA_LIMIT_KIND_BANNER',		1);
define('AREA_LIMIT_KIND_SPLASH', 		2);

// gender
define('GENDER_MALE', 					1);
define('GENDER_FEMALE', 				2);

// Order Status
define('MEMBER_KIND_PLAYER',			1);
define('MEMBER_KIND_REFEREE',			2);
define('MEMBER_KIND_COACH',				3);

// audit
define('AUDIT_KIND_PLAYER',				MEMBER_KIND_PLAYER);
define('AUDIT_KIND_REFEREE',			MEMBER_KIND_REFEREE);
define('AUDIT_KIND_COACH',				MEMBER_KIND_COACH);
define('AUDIT_KIND_CLUB',				MEMBER_KIND_COACH + 1);
define('AUDIT_KIND_CHALLENGE',			AUDIT_KIND_CLUB + 1);

define('AUDIT_STATUS_REQUESTED',		0);
define('AUDIT_STATUS_PASSED',			1);
define('AUDIT_STATUS_REJECTED',			2);

// Comment
define('COMMENT_STATUS_REQUESTED',		0);
define('COMMENT_STATUS_PASSED',			1);
define('COMMENT_STATUS_REJECTED',		2);

// Comment Item Kind
define('COMMENT_ITEM_KIND_CONTENT',		1);
define('COMMENT_ITEM_KIND_LINKS',		2);
 
// Feedback
define('FEEDBACK_STATUS_REQUESTED',		0);
define('FEEDBACK_STATUS_PROCEED',		1);

// Message Receiver Type
define('RECEIVER_TYPE_SINGLE',			0);
define('RECEIVER_TYPE_ALL',				1);

// Content Kind
define('CONTENT_KIND_ARTICLE',			1);
define('CONTENT_KIND_GALLERY',			2);
define('CONTENT_KIND_VIDEO',			3);
define('CONTENT_KIND_LIVE',				4);
define('CONTENT_KIND_ADVERT',			8);

// Link Content Kind
define('LINK_CONTENT_KIND_ARTICLE',		1);
define('LINK_CONTENT_KIND_GALLERY',		2);
define('LINK_CONTENT_KIND_VIDEO',		3);
define('LINK_CONTENT_KIND_URL',			4);

// Live Status 
define('LIVE_STATUS_COUNTDOWN',			1);
define('LIVE_STATUS_LIVE',				2);
define('LIVE_STATUS_RETURN',			3);

// Event Kind
define('EVENT_KIND_COMPETITION',		1);
define('EVENT_KIND_MATCH',				2);

// Event Competition Type 
define('EVENT_COMPETITION_TYPE1',		1);
define('EVENT_COMPETITION_TYPE2',		2);

// Like Item Kind
define('LIKE_ITEM_KIND_COMMENT',		1);
define('LIKE_ITEM_KIND_BABY',			2);

// BANNER Kind
define('BANNER_MAIN',					0);
define('BANNER_NEARBY',					1);

// BANNER Item Kind
define('BANNER_KIND_ARTICLE',			CONTENT_KIND_ARTICLE);
define('BANNER_KIND_GALLERY',			CONTENT_KIND_GALLERY);
define('BANNER_KIND_VIDEO',				CONTENT_KIND_VIDEO);
define('BANNER_KIND_LIVE',				CONTENT_KIND_LIVE);
define('BANNER_KIND_MEMBER',			5);
define('BANNER_KIND_EVENT',				6);
define('BANNER_KIND_URL',				7);


// Portal Kind
define('PORTAL_KIND_CONTENT',			1);
define('PORTAL_KIND_LINK',				2);
define('PORTAL_KIND_EVENT',				3);
define('PORTAL_KIND_MEMBER',			4);
define('PORTAL_KIND_CLUB',				5);
define('PORTAL_KIND_ORGAN',				6);

// Order Kind
define('ORDER_KIND_YUNJIFEN',			1);
define('ORDER_KIND_TICKET',				2);
define('ORDER_KIND_GIFT',				3);
define('ORDER_KIND_BUYPOINT',			4);
define('ORDER_KIND_MANUALPOINT',		5);

// Order Status
define('ORDER_STATUS_PROCESSING',		0);
define('ORDER_STATUS_SUCCEED',			1);
define('ORDER_STATUS_FAILED',			2);

// PAY Status
define('PAY_STATUS_UNPAID',				0);
define('PAY_STATUS_PAID',				1);

// PAY Status
define('SHIP_STATUS_UNSHIPPED',			0);
define('SHIP_STATUS_SHIPPED',			1);

// labels

// gender
$Genders[GENDER_MALE]			= '男';
$Genders[GENDER_FEMALE]			= '女';

// content kinds
$ContentKinds[CONTENT_KIND_ARTICLE]	= '文章';
$ContentKinds[CONTENT_KIND_VIDEO]	= '视频';
$ContentKinds[CONTENT_KIND_GALLERY]	= '图集';
$ContentKinds[CONTENT_KIND_LIVE]	= '直播';
$ContentKinds[CONTENT_KIND_ADVERT]	= '广告';

// event kinds
$EventKinds[EVENT_KIND_COMPETITION]	= '赛事';
$EventKinds[EVENT_KIND_MATCH]		= '比赛';

// member kinds
$MemberKinds[MEMBER_KIND_PLAYER]	= '选手';
$MemberKinds[MEMBER_KIND_REFEREE]	= '裁判';
$MemberKinds[MEMBER_KIND_COACH]		= '教练';

// banner kind
$BannerKinds[BANNER_KIND_ARTICLE]	= $ContentKinds[CONTENT_KIND_ARTICLE];
$BannerKinds[BANNER_KIND_GALLERY]	= $ContentKinds[CONTENT_KIND_GALLERY];
$BannerKinds[BANNER_KIND_VIDEO]		= $ContentKinds[CONTENT_KIND_VIDEO];
$BannerKinds[BANNER_KIND_LIVE]		= $ContentKinds[CONTENT_KIND_LIVE];
$BannerKinds[BANNER_KIND_MEMBER]	= '选手'; 
$BannerKinds[BANNER_KIND_EVENT]		= '比赛'; 
$BannerKinds[BANNER_KIND_URL]		= '链接'; 

// 选手体系
$PlayerLevels[] 					= '竞技';
$PlayerLevels[] 					= '品势';
$PlayerLevels[] 					= '纪录';
$PlayerLevels[] 					= '示范';

// player weight levels
$PlayerWeightLevels[24]				= '少儿组女子(25KG)';
$PlayerWeightLevels[25]				= '少儿组男子(25KG)';
$PlayerWeightLevels[30]				= '少年组女子(30KG)';
$PlayerWeightLevels[35]				= '少年组男子(35KG)';
$PlayerWeightLevels[40]				= '青年组女子(40KG)';
$PlayerWeightLevels[45]				= '青年组男子(45KG)';
$PlayerWeightLevels[55]				= '成人组女子(55KG)';
$PlayerWeightLevels[60]				= '成人组男子(60KG)';
$PlayerWeightLevels[70]				= '成人组男子(70KG)';
$PlayerWeightLevels[80]				= '成人组男子(80KG)';

// audit kinds
$AuditKinds[AUDIT_KIND_PLAYER]		= '零售店';
$AuditKinds[AUDIT_KIND_REFEREE]		= '裁判';
$AuditKinds[AUDIT_KIND_COACH]		= '教练';
$AuditKinds[AUDIT_KIND_CLUB]		= '道馆';
$AuditKinds[AUDIT_KIND_CHALLENGE]	= '联盟';

// audit status
$AuditStatus[AUDIT_STATUS_PASSED]		= '通过';
$AuditStatus[AUDIT_STATUS_REJECTED]		= '未通过';
$AuditStatus[AUDIT_STATUS_REQUESTED]	= '待审核';

// audit value types
$AuditValueTypes['string']			= '输入 - 文本';
$AuditValueTypes['integer']			= '输入 - 整数';
$AuditValueTypes['float']			= '输入 - 浮点数';
$AuditValueTypes['bool']			= '是否';
$AuditValueTypes['date']			= '日期';
$AuditValueTypes['datetime']		= '日期时间';
$AuditValueTypes['select']			= '选择';
$AuditValueTypes['file']			= '上传图片';


// feedback status
$FeedbackStatus[FEEDBACK_STATUS_PROCEED] 	= '已阅';
$FeedbackStatus[FEEDBACK_STATUS_REQUESTED] 	= '未阅';

// comment status
$CommentStatus[COMMENT_STATUS_PASSED] 		= '通过';
$CommentStatus[COMMENT_STATUS_REJECTED] 	= '未通过';
$CommentStatus[COMMENT_STATUS_REQUESTED] 	= '异常';

// order status
$OrderStatus[ORDER_STATUS_FAILED] 			= '取消';
$OrderStatus[ORDER_STATUS_SUCCEED] 			= '完成';
$OrderStatus[ORDER_STATUS_PROCESSING] 		= '处理中';


// Chat Related Infomation
define('CHAT_USERNAME_PREFIX',			'imtone_user_');
define('CHAT_GLOBAL_PASSWORD',			md5('imtone20170211'));

define('CHAT_GROUP_USERNAME_PREFIX',	'imtone_guser_');
define('CHAT_GROUP_NAME_PREFIX',		'imtone_group_');


class Base_Controller extends CI_Controller {

	function __construct()
	{
		
	}
}
