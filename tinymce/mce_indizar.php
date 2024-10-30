<?php

if ( !defined('ABSPATH') )
    die('You are not allowed to call this page directly.');
    
global $wpdb;

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<head>
	<title>Indizar</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-content/plugins/indizar/tinymce/indizar.js"></script>	
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('chaptertag').focus();" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
<form name="Indizar" action="#">
	<div class="tabs">
		<ul>
			<li id="chapter_tab" class="current"><span><a href="javascript:mcTabs.displayTab('chapter_tab','chapter_panel');" onmousedown="return false;"><?php _e("Chapter", 'indizar'); ?></a></span></li>
			<li id="index_tab"><span><a href="javascript:mcTabs.displayTab('index_tab','index_panel');" onmousedown="return false;"><?php _e("Index", 'indizar'); ?></a></span></li>
			<li id="configure_tab"><span><a href="javascript:mcTabs.displayTab('configure_tab','configure_panel');" onmousedown="return false;"><?php _e("Configuration", 'indizar'); ?></a></span></li>
		</ul>
	</div>
	
	<div class="panel_wrapper">
		<!-- chapter panel -->
		<div id="chapter_panel" class="panel current">
			<br />
			<table border="0" cellpadding="4" cellspacing="0">
				<tr>
					<td nowrap="nowrap"><label for="chaptertag"><?php _e("Chapter name:", 'indizar'); ?></label></td>
					<td><input id="chaptertag" name="chaptertag" style="width: 200px"></td>
				</tr>
				<tr>
					<td nowrap="nowrap"><label for="chapterfirts"><?php _e("First Chapter:", 'indizar'); ?></label></td>
					<td><input id="chapterfirst" name="chapterfirst" type="checkbox"></td>
				</tr>
			</table>
		</div>
		<!-- chapter panel -->

		<!-- index panel -->
		<div id="index_panel" class="panel">
			<br />
			<table border="0" cellpadding="4" cellspacing="0">
				<tr>
					<td nowrap="nowrap"><label for="index_usedefault"><?php _e("Use default position", 'indizar'); ?></label></td>
					<td><input type="checkbox" id="index_usedefault" onchange="
						var aux1 = document.getElementsByName('sizetag')[0];
						var aux2 = document.getElementsByName('simplefloat')[0];
						if(this.checked) {
							aux1.disabled = true; 
							aux2.disabled = true;
						} else { 
							aux1.disabled = false;
							aux2.disabled = false;
						} " checked></td>
				</tr>
				<tr>
					<td nowrap="nowrap"><label for="indextag"><?php _e("Box size:", 'indizar'); ?></label></td>
					<td><input id="sizetag" name="sizetag" value="<?php echo INDIZAR_BOX_SIZE; ?>" style="width: 40px" disabled> px</td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top"><?php _e("Float", 'indizar'); ?></td>
					<td>
						<label>
							<select id="simplefloat" name="simplefloat" disabled>
								<option value=""><?php _e("No float", 'indizar'); ?></option>
								<option value="left"<?php if(INDIZAR_BOX_FLOAT=='left') echo " selected"; ?>><?php _e("Left", 'indizar'); ?></option>
								<option value="right"<?php if(INDIZAR_BOX_FLOAT=='right') echo " selected"; ?>><?php _e("Right", 'indizar'); ?></option>
							</select>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<!-- index panel -->
	
		<!-- configure panel -->
		<div id="configure_panel" class="panel">
			<br />
			<table border="0" cellpadding="4" cellspacing="0">
				<tr>
					<td nowrap="nowrap" colspan="2"><label for="conf_numberedlist"><?php _e("Numbered list:", 'indizar'); ?></label></td>
					<td colspan="2">
						<label>
							<select id="conf_numberedlist" name="conf_numberedlist">
								<option value="0"<?php if(!INDIZAR_TOP && !INDIZAR_BOTTOM) echo " selected"; ?>><?php _e("None", 'indizar'); ?></option>
								<option value="1"<?php if(INDIZAR_TOP && !INDIZAR_BOTTOM) echo " selected"; ?>><?php _e("Top", 'indizar'); ?></option>
								<option value="2"<?php if(!INDIZAR_TOP && INDIZAR_BOTTOM) echo " selected"; ?>><?php _e("Bottom", 'indizar'); ?></option>
								<option value="3"<?php if(INDIZAR_TOP && INDIZAR_BOTTOM) echo " selected"; ?>><?php _e("Both", 'indizar'); ?></option>
							</select>
						</label>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap"><label for="conf_box"><?php _e("Box:", 'indizar'); ?></label></td>
					<td><input id="conf_box" name="conf_box" type="checkbox" onchange="var aux1 = document.getElementsByName('conf_boxsize')[0]; aux2 = document.getElementsByName('conf_boxfloat')[0]; if(this.checked) { aux1.disabled = false; aux2.disabled = false; } else { aux1.disabled = true; aux2.disabled = true; } "<?php if(INDIZAR_BOX) echo " checked"; ?>></td>
					<td nowrap="nowrap"><label for="conf_boxsize"><?php _e("Box size:", 'indizar'); ?></label></td>
					<td><input id="conf_boxsize" name="conf_boxsize" style="width: 40px" value="<?php echo INDIZAR_BOX_SIZE; ?>"<?php if(!INDIZAR_BOX) echo " disabled"; ?>> px</td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td nowrap="nowrap" valign="top"><?php _e("Float", 'indizar'); ?></td>
					<td>
						<label>
							<select id="conf_boxfloat" name="conf_boxfloat" style="width: 120px"<?php if(!INDIZAR_BOX) echo " disabled"; ?>>
								<option value="none"><?php _e("No box", 'indizar'); ?></option>
								<option value="left"<?php if(INDIZAR_BOX_FLOAT=='left') echo " selected"; ?>><?php _e("Left", 'indizar'); ?></option>
								<option value="right"<?php if(INDIZAR_BOX_FLOAT=='right') echo " selected"; ?>><?php _e("Right", 'indizar'); ?></option>
							</select>
						</label>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap"><label for="conf_usepreface"><?php _e("Use preface:", 'indizar'); ?></label></td>
					<td><input id="conf_usepreface" name="conf_usepreface" onchange="var aux = document.getElementsByName('conf_preface')[0]; if(this.checked) aux.disabled = false; else aux.disabled = true; " type="checkbox"<?php if(INDIZAR_USE_PREFACE) echo " checked"; ?>></td>
					<td><label for="conf_preface"><?php _e("Preface title:", 'indizar'); ?></label></td>
					<td><input id="conf_preface" name="conf_preface" style="width: 120px" value="<?php if(INDIZAR_PREFACE_TITLE) echo INDIZAR_PREFACE_TITLE; else _e("Preface", 'indizar'); ?>"<?php if(!INDIZAR_USE_PREFACE) echo " disabled"; ?>></td>
				</tr>
			</table>
		</div>
		<!-- simple gallery panel -->
		
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'indizar'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'indizar'); ?>" onclick="insertIndizarLink('<?php echo INDIZAR_USE_HEADERS; ?>');" />
		</div>
	</div>
</form>
</body>
</html>
