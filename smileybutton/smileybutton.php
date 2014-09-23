<?php
/**
 * Name: Smileybutton
 * Description: Adds a smileybutton to the Inputbox
 * Version: 0.1
 * Author: Johannes Schwab , Christian Vogeley
 * ToDo: Add this to comments, Allow to disable on webpages, nicer position of button
 */


function smileybutton_load() {

	/**
	 * 
	 * Register hooks for jot_tool and plugin_settings
	 *
	 */

	register_hook('jot_tool', 'addon/smileybutton/smileybutton.php', 'show_button');
	register_hook('feature_settings', 'addon/smileybutton/smileybutton.php', 'smileybutton_settings');
	register_hook('feature_settings_post', 'addon/smileybutton/smileybutton.php', 'smileybutton_settings_post');
 
}


function smileybutton_unload() {

	/**
	 *
	 * Delet registered hooks
	 *
	 */

	unregister_hook('jot_tool',    'addon/smileybutton/smileybutton.php', 'show_button');	
	unregister_hook('feature_settings', 'addon/smileybutton/smileybutton.php', 'smileybutton_settings');
	unregister_hook('feature_settings_post', 'addon/smileybutton/smileybutton.php', 'smileybutton_settings_post');
	 
}



function show_button($a, &$b) {

	/**
	 *
	 * Check if it is a local user and he has enabled smileybutton
	 *
	 */

	if(! local_user()) {
		$nobutton = false;
	} else {
		$nobutton = get_pconfig(local_user(), 'smileybutton', 'nobutton');
		$deactivated = get_pconfig(local_user(), 'smileybutton', 'deactivated');
	}

	/**
	 *
	 * Prepare the Smilie-Arrays
	 *
	 */

	/**
	 *
 	 * I have copied this from /include/text.php, removed dobles
	 * and some escapes.
	 *
	 */

	$texts =  array( 
		'&lt;3', 
		'&lt;/3', 
		':-)', 
		';-)', 
		':-(', 
		':-P', 
		':-X', 
		':-D', 
		':-O', 
		'\\\\o/', 
		'O_o', 
		":\'(", 
		":-!", 
		":-/", 
		":-[", 
		"8-)",
		':beer', 
		':coffee', 
		':facepalm',
		':like',
		':dislike',
                'red#'

	);

	$icons = array(
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-heart.gif" alt="<3" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-brokenheart.gif" alt="</3" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-smile.gif" alt=":-)" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-wink.gif" alt=";-)" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-frown.gif" alt=":-(" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-tongue-out.gif" alt=":-P" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-kiss.gif" alt=":-X" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-laughing.gif" alt=":-D" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-surprised.gif" alt=":-O" />',                
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-thumbsup.gif" alt="\\o/" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-Oo.gif" alt="O_o" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-cry.gif" alt=":\'(" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-foot-in-mouth.gif" alt=":-!" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-undecided.gif" alt=":-/" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-embarassed.gif" alt=":-[" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-cool.gif" alt="8-)" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/beer_mug.gif" alt=":beer" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/coffee.gif" alt=":coffee" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/smiley-facepalm.gif" alt=":facepalm" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/like.gif" alt=":like" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/dislike.gif" alt=":dislike" />',
		'<img class="smiley" src="' . $a->get_baseurl() . '/images/rhash-16.png" alt="red" />'
	);
	
	/**
	 * 
	 * Call hooks to get aditional smileies from other addons
	 *
	 */

	$params = array('texts' => $texts, 'icons' => $icons, 'string' => ""); //changed
	call_hooks('smilie', $params);

	/**
	 *
	 * Generate html for smileylist
	 *
	 */

	$s = "\t<table class=\"smiley-preview\"><tr>\n";
	for($x = 0; $x < count($params['texts']); $x ++) {
		$icon = $params['icons'][$x];
		$icon = str_replace('/>', 'onclick="smileybutton_addsmiley(\'' . $params['texts'][$x] . '\')"/>', $icon);
		$icon = str_replace('class="smiley"', 'class="smiley_preview"', $icon);
		$s .= "<td>" . $icon . "</td>";
		if (($x+1) % (sqrt(count($params['texts']))+1) == 0) {
			$s .= "</tr>\n\t<tr>";
		}
	}
	$s .= "\t</tr></table>\n";

	/**
	 *
	 * Add css to page
	 *
	 */	

	$a->page['htmlhead'] .= '<link rel="stylesheet"  type="text/css" href="' . $a->get_baseurl() . '/addon/smileybutton/smileybutton.css' . '" media="all" />' . "\r\n";

	/**
	 *
	 * Add the button to the Inputbox
	 *
	 */	
	if (! $nobutton and ! $deactivated) {
		$b = "<div id=\"profile-smiley-wrapper\"  >\n";
		//$b .= "\t<img src=\"" . $a->get_baseurl() . "/addon/smileybutton/icon.gif\" onclick=\"toggle_smileybutton()\" alt=\"smiley\">\n";
		$b .= "\t<i id=\"profile-smiley-button\" class=\"icon-smile jot-icons\" onclick=\"toggle_smileybutton()\"></i>\n";
		$b .= "\t</div>\n";
	}

 
	/**
	 *
	 * Write the smileies to an (hidden) div
	 *
	 */
	if ($deactivated){
		return;
		}
	else
	{
		if ($nobutton) {
			$b .= "\t<div id=\"smileybutton\">\n";
		} else {
			$b .= "\t<div id=\"smileybutton\" style=\"display:none;\">\n";
		}
	}
	$b .= $s . "\n"; 
	$b .= "</div>\n";

	/**
	 *
	 * Function to show and hide the smiley-list in the hidden div
	 *
	 */

	$b .= "<script>\n"; 

	if (! $nobutton) {
		$b .= "	smileybutton_show = 0;\n";
		$b .= "	function toggle_smileybutton() {\n";
		$b .= "	if (! smileybutton_show) {\n";
		$b .= "		$(\"#smileybutton\").show();\n";
		$b .= "		smileybutton_show = 1;\n";
		$b .= "	} else {\n";
		$b .= "		$(\"#smileybutton\").hide();\n";
		$b .= "		smileybutton_show = 0;\n";
		$b .= "	}}\n";
	} 

	/**
	 *
	 * Function to add the chosen smiley to the inputbox
	 *
	 */

	$b .= "	function smileybutton_addsmiley(text) {\n";
	$b .= "		if(plaintext == 'none') {\n";
	$b .= "			var v = $(\"#profile-jot-text\").val();\n";
	$b .= "			v = v + text;\n";
	$b .= "			$(\"#profile-jot-text\").val(v);\n";
	$b .= "			$(\"#profile-jot-text\").focus();\n";
	$b .= "		} else {\n";
	$b .= "			var v = tinymce.activeEditor.getContent();\n";
	$b .= "			v = v + text;\n";
	$b .= "			tinymce.activeEditor.setContent(v);\n";
	$b .= "			tinymce.activeEditor.focus();\n";
	$b .= "		}\n";
	$b .= "	}\n";
	$b .= "</script>\n";
}





/**
 *
 * Set the configuration
 *
 */

function smileybutton_settings_post($a,$post) {
	if(! local_user())
		return;
	if($_POST['smileybutton-submit'])
		set_pconfig(local_user(),'smileybutton','nobutton',intval($_POST['smileybutton']));
		set_pconfig(local_user(),'smileybutton','deactivated',intval($_POST['deactivated']));

}


/**
 *
 * Add configuration-dialog to form
 *
 */


function smileybutton_settings(&$a,&$s) {

	if(! local_user())
		return;

	/* Add our stylesheet to the page so we can make our settings look nice */

	$a->page['htmlhead'] .= '<link rel="stylesheet"  type="text/css" href="' . $a->get_baseurl() . '/addon/smileybutton/smileybutton.css' . '" media="all" />' . "\r\n";

	/* Get the current state of our config variable */

	$nobutton = get_pconfig(local_user(),'smileybutton','nobutton');
	$checked['nobutton'] = (($nobutton) ? ' checked="checked" ' : '');
    $deactivated = get_pconfig(local_user(),'smileybutton','deactivated');
	$checked['deactivated'] = (($deactivated) ? ' checked="checked" ' : '');
	/* Add some HTML to the existing form */

	$s .= '<div class="settings-block">';
   $s .= '<button class="btn btn-default" data-target="#settings-smileybutton-wrapper" data-toggle="collapse" type="button">' . t('Smileybutton Settings') . '</button>';
   $s .= '<div id="settings-smileybutton-wrapper" class="collapse well">';    	
		
	$s .= '<div id="smileybutton-enable-wrapper">';
	$s .= '<label id="smileybutton-deactivated-label" for="smileybutton-deactivated-checkbox">'.t('Deactivate the feature').'</label>';	
	$s .= '<input id="smileybutton-deactivated-checkbox" type="checkbox" name="deactivated" value="1" ' . $checked['deactivated'] . '/>';
	$s .= '</div><div class="clear"></div>';

	$s .= '<label id="smileybutton-enable-label" for="smileybutton-nobutton-checkbox">'.t('Hide the button and show the smilies directly.').'</label>';
	$s .= '<input id="smileybutton-nobutton-checkbox" type="checkbox" name="smileybutton" value="1" ' . $checked['nobutton'] . '/>';
    $s .= '<div class="clear"></div>'; 

	/* provide a submit button */

	$s .= '<div class="settings-submit-wrapper" ><input type="submit" name="smileybutton-submit" class="settings-submit" value="' . t('Submit Smileybutton Settings') . '" /></div></div></div>';

}
