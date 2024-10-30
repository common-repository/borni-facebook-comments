<?php
/*
    Plugin Name: Facebook Comments for Wordpress
    Plugin URI: www.borni.co
    Description: Add facebook comments widget to your wordpress website
    Version: 1.0
    License: Facebook Comments for Wordpress
*/

// FB MODERATION CODE
function borni_fb_headjs() {
	$APP_ID  = esc_attr( get_option('fb_comments_app_id') );
	echo "<meta property=\"fb:app_id\" content=\"$APP_ID\" />";
}
add_action('wp_head', 'borni_fb_headjs');	

// Admin Styles
function borni_fb_comments_admin_styles() {
  echo '<style>
    th.fb {
    	width:300px;
    }
	td.fb {
    	width:300px; margin:0; padding:0;
    }
  </style>';
}
add_action('admin_head', 'borni_fb_comments_admin_styles');


// Add text After content
function borni_fb_comments_code($content) {


	$savedLocale 	  = esc_attr( get_option('fb_comments_langcode') );
	$savedCommentText = esc_attr( get_option('fb_comments_text') );
	$commentsNumber   = esc_attr( get_option('fb_comments_num') );

	if ( empty($savedLocale) )
		$savedLocale = 'en_US';

	if ( empty($savedCommentText) )
		$savedCommentText = 'Leave a comment';

	if ( empty($commentsNumber) )
		$commentsNumber = '10';
	
	if( is_single() ):
	
	$content .='
	<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = \'https://connect.facebook.net/'.$savedLocale.'/sdk.js#xfbml=1&version=v3.2\';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, \'script\', \'facebook-jssdk\'));</script>';
	
	$perma = get_the_permalink(get_the_ID());
	$content .= '<hr><h3>'.$savedCommentText.'</h3><div class="fb-comments" data-href="'.$perma.'" data-numposts="'.$commentsNumber.'"></div>';
	
	endif;
	
	return $content;
}
add_filter ('the_content', 'borni_fb_comments_code', 99999);


// create custom plugin settings menu
add_action('admin_menu', 'borni_fb_comments_create_menu');
function borni_fb_comments_create_menu() {

	//create new top-level menu
	add_menu_page('Facebook Comments', 'FB Comments', 'administrator', __FILE__, 'borni_fb_comments_settings_page' , '
dashicons-facebook' );

	//call register settings function
	add_action( 'admin_init', 'register_borni_fb_comments_settings' );
}


function register_borni_fb_comments_settings() {
	//register our settings
	register_setting( 'borni_fb_comments_settings', 'fb_comments_app_id',   ['default'=>''] );
	register_setting( 'borni_fb_comments_settings', 'fb_comments_text' , 	['default'=>'Leave a comment']);
	register_setting( 'borni_fb_comments_settings', 'fb_comments_langcode', ['default'=>'en_US'] );
	register_setting( 'borni_fb_comments_settings', 'fb_comments_num' ,		['default'=>'10']);
	
}

function borni_fb_comments_settings_page() {
?>
<div class="wrap">
<h1>Facebook Comments Settings</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'borni_fb_comments_settings' ); ?>
    <?php do_settings_sections( 'borni_fb_comments_settings' ); ?>
    <table class="form-table" style="width:auto;">
		
        <tr valign="top">
        	<th class="fb" scope="row">APP ID (optional)</th>
        	<td><input type="text" name="fb_comments_app_id" value="<?php echo esc_attr( get_option('fb_comments_app_id') ); ?>" /></td>
        </tr>
		<!-- explain -->
		<tr valign="top">
			<td class="fb" ><small>Create an <a href="https://developers.facebook.com/apps/">fb app ID</a> to moderate comments.</small></td>
		</tr>
			
         
        <tr valign="top">
        	<th class="fb" scope="row">Comments Title</th>
        	<td><input type="text" name="fb_comments_text" value="<?php echo esc_attr( get_option('fb_comments_text') ); ?>" /></td>
        </tr>
		<!-- explain -->
		<tr valign="top"><td class="fb"><small>Change "Leave a comment" text.</small></td></tr>
       
        <tr valign="top">
       		<th class="fb" scope="row">Comments number</th>
        	<td><input type="text" name="fb_comments_num" value="<?php echo esc_attr( get_option('fb_comments_num') ); ?>" /></td>
        </tr>
		<!-- explain -->
		<tr valign="top"><td class="fb"><small>Number of displayed comments</small></td></tr>
		
		<tr valign="top">
			<th class="fb" scope="row">Widget Language.</th>
			<td> 
				<?php
					$savedLanguage = esc_attr( get_option('fb_comments_langcode') );				
					print borni_fb_comments_langList($savedLanguage);
				?>
			</td>
		</tr>
		<!-- explain -->
		<tr valign="top"><td class="fb"><small>Ccomment widget language</small></td></tr>
		
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php }



//Available languages for fb comments plugin
function borni_fb_comments_langList($savedLang){
	$langs =  array('en_US','ca_ES','cs_CZ','cx_PH','cy_GB','da_DK','de_DE','eu_ES','en_UD','es_LA','es_ES','gn_PY','fi_FI','fr_FR','gl_ES','hu_HU','it_IT','ja_JP','ko_KR','nb_NO','nn_NO','nl_NL','fy_NL','pl_PL','pt_BR','pt_PT','ro_RO','ru_RU','sk_SK','sl_SI','sv_SE','th_TH','tr_TR','ku_TR','zh_CN','zh_HK','zh_TW','af_ZA','sq_AL','hy_AM','az_AZ','be_BY','bn_IN','bs_BA','bg_BG','hr_HR','nl_BE','en_GB','et_EE','fo_FO','fr_CA','ka_GE','el_GR','gu_IN','hi_IN','is_IS','id_ID','ga_IE','jv_ID','kn_IN','kk_KZ','lv_LV','lt_LT','mk_MK','mg_MG','ms_MY','mt_MT','mr_IN','mn_MN','ne_NP','pa_IN','sr_RS','so_SO','sw_KE','tl_PH','ta_IN','te_IN','ml_IN','uk_UA','uz_UZ','vi_VN','km_KH','tg_TJ','ar_AR','he_IL','ur_PK','fa_IR','ps_AF','my_MM','qz_MM','or_IN','si_LK','rw_RW','cb_IQ','ha_NG','ja_KS','br_FR','tz_MA','co_FR','as_IN','ff_NG','sc_IT','sz_PL ');
	
	$options = '';
	
	foreach($langs as $langCode){
		if ( $langCode == $savedLang ){
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$options .= "<option value=\"$langCode\" $selected>$langCode</option>";
	}
	
	return "<select name=\"fb_comments_langcode\">$options</select>";
	
}

?>
