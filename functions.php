<?php
define('kgm_version', '2019');
/**加载主题函数**/
require_once(get_template_directory().'/include/functions/custom-navwalker/custom-navwalker.php');
require_once(get_template_directory().'/include/functions/theme-setting/theme-setting.php');
kgm_theme_setting::backend_init();
require_once(get_template_directory().'/include/functions/view-post/view-post.php');
require_once(get_template_directory().'/include/widgets/widget-init.php');
/**加载css及javascript**/
function kgm_scripts(){
	$pjax = kgm_theme_setting::get_pjax_config();
	if($pjax['pjax_active'] && $pjax['progress_active']){
		wp_enqueue_style('kgm_nprogress','https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.css',array(),'0.2.0');
		if($pjax['progress_color']){
			$pjax_custom_css = '#nprogress .bar{background: '.esc_attr($pjax['progress_color']).';} #nprogress .peg {box-shadow: 0 0 10px '.esc_attr($pjax['progress_color']).', 0 0 5px '.esc_attr($pjax['progress_color']).';} #nprogress .spinner-icon {border-top-color: '.esc_attr($pjax['progress_color']).'; border-left-color: '.esc_attr($pjax['progress_color']).';}';
			wp_add_inline_style('kgm_nprogress', $pjax_custom_css);
		}
	}
	wp_enqueue_style('kgm_awesome','https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css',array(),'4.7.0');
	wp_enqueue_style('kgm_style',get_template_directory_uri().'/style.css',array(),kgm_version);
	wp_dequeue_style('wp-block-library');
	wp_enqueue_script('kgm_jquery','https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js',array(),'3.3.1',true);
	wp_enqueue_script('kgm_timeago','https://cdn.jsdelivr.net/npm/timeago.js@4.0.0-beta.2/dist/timeago.min.js',array(),'4.0.0',true);
	if($pjax['pjax_active']){
		if($pjax['progress_active']){
			wp_enqueue_script('kgm_nprogress','https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.min.js',array(),'0.2.0',true);
		}
		wp_enqueue_script('kgm_pjax',get_template_directory_uri().'/static/js/pjax.js',array(),kgm_version,true);
		if($pjax['progress_active']){
			wp_add_inline_script('kgm_pjax', '$(function(){$(document).pjax("a[target!=_blank]","#kgm-app",{fragment:"#kgm-app",timeout:6000});$(document).on("pjax:send",function(){NProgress.start();$("#kgm-app").fadeTo(200,0)});$(document).on("pjax:complete",function(){$("#kgm-app").fadeTo(200,1);NProgress.done()})});');
		}else{
			wp_add_inline_script('kgm_pjax', '$(function(){$(document).pjax("a[target!=_blank]","#kgm-app",{fragment:"#kgm-app",timeout:6000});$(document).on("pjax:send",function(){$("#kgm-app").fadeTo(200,0)});$(document).on("pjax:complete",function(){$("#kgm-app").fadeTo(200,1)})});');
		}
	}
	wp_enqueue_script('kgm_app',get_template_directory_uri().'/static/js/app.js',array(),kgm_version,true);
}
add_action('wp_enqueue_scripts','kgm_scripts');

/**移除window._wpemojiSettings**/
remove_action( 'admin_print_scripts', 'print_emoji_detection_script');
remove_action( 'admin_print_styles', 'print_emoji_styles');
remove_action( 'wp_head', 'print_emoji_detection_script', 7);
remove_action( 'wp_print_styles', 'print_emoji_styles');
remove_filter( 'the_content_feed', 'wp_staticize_emoji');
remove_filter( 'comment_text_rss', 'wp_staticize_emoji');
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email');

/**移除用户默认顶部工具条**/
add_filter('show_admin_bar', '__return_false');

/**移除wp_embed功能**/
require_once(get_template_directory().'/include/functions/disable-wp-embed/disable-wp-embed.php');

/**菜单注册**/
function kgm_register_menus() {
	register_nav_menus([
		'kgm-primary-menu-logout' => __('kagami - 顶部菜单(登陆前)'),
		'kgm-primary-menu-login' => __('kagami - 顶部菜单(登陆后)'),
	]);
}
add_action('after_setup_theme','kgm_register_menus');

function simple_comment($comment, $args, $depth) {?>
	<section id="kgm-comment-item-<?php echo $comment->comment_ID; ?>" class="grid-container kgm-comment-item">
		<div class="kgm-comment-item-avatar">
			<?php echo get_avatar($comment, 40, '', '', array('class'=>'kgm-comment-item-avatar-img')); ?>
		</div>
		<div class="kgm-comment-item-body">
			<header class="kgm-comment-item-header">
				<span class="kgm-comment-item-header-author"><?php echo $comment->comment_author; ?></span>
				<?php if(!$comment->comment_parent==0): $parent_comment=get_comment($comment->comment_parent); ?>
					<span class="kgm-comment-item-header-replyto"><i class="fa fa-at" aria-hidden="true"></i><?php echo $parent_comment->comment_author; ?></span>
				<?php endif; ?>
				<span class="kgm-comment-item-header-time"><i class="fa fa-clock-o" aria-hidden="true"></i><date class="kgm_timeago_render" datetime="<?php echo $comment->comment_date; ?>"></date></span>
				<?php comment_reply_link(array_merge( $args, array('reply_text' => '<i class="fa fa-comments-o" aria-hidden="true"></i>','depth' => $depth, 'max_depth' => $args['max_depth']))) ?> 
			</header>
			<div class="clearfix"></div>
			<div class="kgm-comment-item-content">
				<?php echo $comment->comment_content; ?>
			</div>
			<?php if(!$comment->comment_parent==0): ?>
				<div class="gird-container kgm-comment-item-parent">
					<div class="kgm-comment-item-parent-quote">
						<a class="kgm-comment-item-parent-author"><i class="fa fa-at" aria-hidden="true"></i><?php echo $parent_comment->comment_author; ?>:</a>
						<span class="kgm-comment-item-parent-content"><?php echo $parent_comment->comment_content; ?></span>
					</div>
					<?php comment_reply_link(array_merge( $args, array('reply_text' => '<i class="fa fa-comments-o" aria-hidden="true"></i>','depth' => $depth, 'max_depth' => $args['max_depth'])),$parent_comment); ?> 
				</div>
			<?php endif; ?>				
		</div>
	</section>
<?php
}
function move_comment_field_to_bottom($fields) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}
add_filter('comment_form_fields', 'move_comment_field_to_bottom');
?>