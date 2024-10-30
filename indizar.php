<?php
/*
Plugin Name: Indizar
Plugin URI: http://www.sebaxtian.com/acerca-de/indizar
Description: A plugin to index posts and pages using chapters.
Version: 0.8.1
Author: Juan Sebastián Echeverry
Author URI: http://www.sebaxtian.com
*/

/* Copyright 2008-2012 Juan Sebastián Echeverry (email : baxtian.echeverry@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

add_action('init', 'ind_add_buttons');
add_action('wp_head', 'ind_header');
add_action('init', 'ind_text_domain');
add_filter('query_vars', 'ind_query_var');
add_filter('the_content', 'ind_content', 5);
add_action('wp_ajax_ind_tinymce', 'ind_tinymce');
add_filter('post_rewrite_rules','ind_post_rewrite_rules');
add_filter('page_rewrite_rules','ind_page_rewrite_rules');

define('INDIZAR_HEADER_V', 1.4);

if(!defined('INDIZAR_USE_PREFACE'))
	define('INDIZAR_USE_PREFACE', false);
if(!defined('INDIZAR_PREFACE_TITLE'))
	define('INDIZAR_PREFACE_TITLE', false);

if(INDIZAR_USE_PREFACE)
	define('INDIZAR_FIRST_CHAPTER', 0);
else
	define('INDIZAR_FIRST_CHAPTER', 1);
	
if(!defined('INDIZAR_TOP'))
	define('INDIZAR_TOP', false);
if(!defined('INDIZAR_BOTTOM'))
	define('INDIZAR_BOTTOM', true);
	
if(!defined('INDIZAR_BOX'))
	define('INDIZAR_BOX', false);
if(!defined('INDIZAR_BOX_SIZE'))
	define('INDIZAR_BOX_SIZE', 200);
if(!defined('INDIZAR_BOX_FLOAT'))
	define('INDIZAR_BOX_FLOAT', 'right');
	
if(!defined('INDIZAR_USE_HEADERS'))
	define('INDIZAR_USE_HEADERS', false);

if(!defined('INDIZAR_DIV'))
	define('INDIZAR_DIV', false);
if(!defined('INDIZAR_TOPDIV'))
	define('INDIZAR_TOPDIV', false);

/**
* To declare where are the mo files (i18n).
* This function should be called by a filter.
*
* @access public
*/
function ind_text_domain() {
	load_plugin_textdomain('indizar', false, 'indizar/lang');
}

/**
* Function to add the permalink rules for posts.
* This function should be called by a filter.
*
* @access public
* @param string rules The array of rules
* @return The array of rules with the new rules
*/
function ind_post_rewrite_rules($rules) {
	global $wp_rewrite;
 
	// add rewrite tokens
	$keytag = '%chapter%';
	$wp_rewrite->add_rewrite_tag($keytag, '([0-9]+)', 'chapter=');

	// rules for 'posts'
	$post_structure = $wp_rewrite->permalink_structure . "/chapter/$keytag";
	$post_structure = str_replace('//', '/', $post_structure);
	$post_rewrite = $wp_rewrite->generate_rewrite_rules($post_structure, EP_PERMALINK);

	$rules = array_merge($post_rewrite,$rules);
	 
	return $rules;
}

/**
* Function to add the permalink rules for pages.
* This function should be called by a filter.
*
* @access public
* @param string rules The array of rules
* @return The array of rules with the new rules
*/
function ind_page_rewrite_rules($rules) {
	global $wp_rewrite;	 

	// add rewrite tokens
	$keytag = '%chapter%';
	$wp_rewrite->add_rewrite_tag($keytag, '([0-9]+)', 'chapter=');

	// rules for 'pages'
	$page_structure = $wp_rewrite->page_structure . "/chapter/$keytag";
	$page_structure = str_replace('//', '/', $page_structure);
	$wp_rewrite->page_structure = $page_structure;
	$page_rewrite = $wp_rewrite->page_rewrite_rules();

	$answer = array();
	$answer['(.?.+?)/chapter/([0-9]+)/?$'] = 'index.php?pagename=$matches[1]&chapter=$matches[2]';
	$answer = $answer + $rules + $page_rewrite; 
	
	return $answer; 
	
}

/**
* Function to answer the MCE ajax call.
* This function should be called by an action.
*
* @access public
*/
function ind_tinymce() {
	// check for rights
    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
    	die(__("You are not allowed to be here"));
   	
   	require_once('tinymce/mce_indizar.php');
    
    die();
}

/**
* Function to return the url of the plugin concatenated to a string. The idea is to
* use this function to get the entire URL for some file inside the plugin.
*
* @access public
* @param string str The string to concatenate
* @return The URL of the plugin concatenated with the string 
*/
function ind_plugin_url($str = '') {

	$aux = '/wp-content/plugins/indizar/'.$str;
	$aux = str_replace('//', '/', $aux);
	$url = get_bloginfo('wpurl');
	return $url.$aux;

}

/**
* Function to add the required data to the header in the site.
* This function should be called by a filter.
*
* @access public
*/
function ind_header( ) {
	$css = get_theme_root()."/".get_template()."/indizar.css";
	if(file_exists($css)) {
		$css_register = get_bloginfo('template_directory').'/indizar.css';
	} else {
		$css_register = ind_plugin_url('/css/indizar.css');
	}
	
	if((is_single() || is_page()) && INDIZAR_DIV) {
		//Define custom JavaScript options
		//$div = str_replace(array('%id%'), array(get_the_ID()), INDIZAR_DIV);
		$div = INDIZAR_DIV;
		$top = INDIZAR_DIV;
		if(INDIZAR_TOPDIV)
			$top = INDIZAR_TOPDIV;
		//$top = str_replace(array('%id%'), array(get_the_ID()), $top);
		echo "<script type='text/javascript'>
		indizar_div = '$div';
		indizar_top = '$top';
		</script>
		";

		//Declare javascript
		wp_register_script('scrollto', $url.'/wp-content/plugins/indizar/scripts/jquery.scrollTo-min.js', array('jquery'), INDIZAR_HEADER_V);
		wp_register_script('indizar', $url.'/wp-content/plugins/indizar/indizar.js', array('scrollto'), INDIZAR_HEADER_V);
		wp_enqueue_script('indizar');
		wp_print_scripts( array( 'indizar' ));
	}
	
	//Declare style
	wp_register_style('indizar', $css_register, false, INDIZAR_HEADER_V);
	wp_enqueue_style('indizar');
	wp_print_styles( array( 'indizar' ));
}

/**
* Function to add 'chapter' to the array of variables in the permalinks.
* This function should be called by a filter.
*
* @access public
* @param array vars The array of variables to search in the permalink.
* @return The array of variables with the new variable to search.
*/
function ind_query_var($vars) {
	array_push($vars, 'chapter');
	return $vars;
}

/**
* Function to prepare the content. Change the firstchapter and chapter tags with the
* chapter title and sets a tag to define if it has a new page separator.
* It also search for the indizar tag and sets the configuration if the post has it.
*
* @access private
* @param string title Title of the post.
* @param string content The content to prepare.
* @return The content modified.
*/
function ind_prepare_content($title, $content) {
	global $chapters, $page, $chapter, $indizar_first_chapter, $indizar_config;
	
	//Declare the config array
	$indizar_config=array();
	
	//search for configuration and poblate the global variable if the
	//post declared it
	$search = "@(?:<p>)*\s*\[indizar\s*:\s*(.+)\]\s*(?:</p>)*@i";
	if(preg_match_all($search, $content, $matches)) {
		if(is_array($matches)) {
			foreach ($matches[1] as $key =>$v0) {
				$search = $matches[0][$key];
				$aux = explode(',', $v0);
				$indizar_config['config'] = $aux[0];
				$indizar_config['box_size'] = $aux[1];
				$indizar_config['box_float'] = $aux[2];
				if(count($aux)>3) {
					$indizar_config['first_chapter_number'] = 0;
					$indizar_first_chapter = 0;
					$indizar_config['preface_title'] = $aux[3];
				} else {
					$indizar_config['first_chapter_number'] = 1;
					$indizar_first_chapter = 1;
				}
				$content= str_replace ($search, "", $content);
			}
		}
	}
	
	//Declare the chapters array
	$chapters=false;
	
	//Suppose the first chapter has the name of the post
	$chapters[$indizar_first_chapter]=$title;
	
	//Set the page_index starts in the number we get as first chapter
	$page_index=$indizar_first_chapter;
	
	//search for firstchapter and define it in the chapters list
	$search = "@(?:<p>)*\s*\[firstchapter\s*:\s*(.+)\]\s*(?:</p>)*@i";
	if(preg_match_all($search, $content, $matches)) {
		if(is_array($matches)) {
			foreach ($matches[1] as $key =>$v0) {
				$search = $matches[0][$key];
				//Set the title and put a box place definition to use if we need it
				$replace= "<h1 class='indizar'>$v0</h1><!--indizar_box-->";
				$content= str_replace ($search, $replace, $content);
				$chapters[$indizar_first_chapter]=$v0;
			}
		}
	}
	
	if(INDIZAR_USE_HEADERS) {
		//search for h1.firstchapter and define it in the chapters list
		$search = "@<".INDIZAR_USE_HEADERS." class=\"firstchapter\">(.+)</".INDIZAR_USE_HEADERS.">\s*@i";
		if(preg_match_all($search, $content, $matches)) {
			if(is_array($matches)) {
				foreach ($matches[1] as $key =>$v0) {
					$search = $matches[0][$key];
					//Set the title and put a box place definition to use if we need it
					$replace= "<h1 class='indizar'>$v0</h1><!--indizar_box-->";
					$content= str_replace ($search, $replace, $content);
					$chapters[$indizar_first_chapter]=$v0;
				}
			}
		}
	}
	
	//search for chapter and poblate the chapters list
	$search = "@(?:<p>)*\s*\[chapter\s*:\s*(.+)\]\s*(?:</p>)*@i";
	if(preg_match_all($search, $content, $matches)) {
		if(is_array($matches)) {
			foreach ($matches[1] as $key =>$v0) {
				$search = $matches[0][$key];
				//Set the title, put a box place definition to use if we need it
				//and create a 'chapter mark' to use as a page break
				$replace= "<!--indizar_chapter--><h1 class='indizar'>$v0</h1><!--indizar_box-->";
				$content= str_replace ($search, $replace, $content);
				$page_index++;
				$chapters[$page_index]=$v0;
			}
		}
	}
	
	if(INDIZAR_USE_HEADERS) {
		//search for h1 and poblate the chapters list
		$search = "@<".INDIZAR_USE_HEADERS.">\s*(.+)</".INDIZAR_USE_HEADERS.">\s*@i";
		if(preg_match_all($search, $content, $matches)) {
			if(is_array($matches)) {
				foreach ($matches[1] as $key =>$v0) {
					$search = $matches[0][$key];
					//Set the title, put a box place definition to use if we need it
					//and create a 'chapter mark' to use as a page break
					$replace= "<!--indizar_chapter--><h1 class='indizar'>$v0</h1><!--indizar_box-->";
					$content= str_replace ($search, $replace, $content);
					$page_index++;
					$chapters[$page_index]=$v0;
				}
			}
		}
	}
	
	//if there is just one chapter in the list it means we don't need
	//to cut the post in pages, so we don't need a chapters list and
	//this plugin isn't required to manage the content
	if(count($chapters)==1) 
		$chapters=false;
	
	return $content;

}

/**
* Function to modify the content of the page or post.
* It prepares the post, gets the chapter number from the permalink, and extract
* it frim the content. 
* It also puts the chapters list and the numeric list if the user declared it in the
* post, and/or the general configuration requires it.
* This function should be called by a filter.
*
* @access public
* @param string content The content to change.
* @return The content with the changes the plugin have to do.
*/
function ind_content($content) {
	global $wp_rewrite, $wp_query, $chapter, $chapters, $post, $page, $more, $multipage, $indizar_first_chapter, $indizar_config;
	
	// Use the number defined in the general configuration;
	$indizar_first_chapter = INDIZAR_FIRST_CHAPTER;
	
	//if the post have the custom field 'indizar_first_chapter', use it
	if(is_numeric(get_post_meta($post->ID, 'indizar_first_chapter', true)))
		$indizar_first_chapter = get_post_meta($post->ID, 'indizar_first_chapter', true);

	//read the chapter data from the URL
	if($wp_rewrite->using_permalinks()) { // WordPress using Pretty Permalink structure 
		$chapter = $wp_query->query_vars['chapter'];
	} else { // WordPress using default permalink structure like www.site.com/wordpress/?p=123
		$chapter = false;
		if(isset($_GET['chapter'])) $chapter = $_GET['chapter'];
	}
	
		
	//prepare the content in search for tags
	$index=false;
	$content = ind_prepare_content($post->post_title, $content);
	
	//The system shows only the begin
	if(!$more) {
		//Delete the tags
		$content = ind_delete_extra_tags($content);
	} else { //We are showing the post!!!!
		//If there aren't chapters, we don't need Indizar with this post
		if(!$chapters) {
			//maybe someone defined just the first chapter, so we need to
			//delete the special mark for the box
			$content = str_replace ("<!--indizar_box-->", "", $content);
		} else {
			//Ok, we have chapters, do your magic Indizar
			
			//If there is a configuration tag, use it to define the first chapter
			if(count($indizar_config)>0) {
				$indizar_first_chapter = $indizar_config['first_chapter_number'];
			}
			
			//If the URL didn't declare a chapter it has to be the first chapter
			if(!$chapter) $chapter = $indizar_first_chapter;
			 
			// Use the entire post if we are using wp_print to print
			if(function_exists('wp_print') && ( intval(get_query_var('print')) == 1 || intval(get_query_var('printpage')) == 1 )) {
				//Do we have pages withe the post?
				for($page = 0; $page < $numpages; $page++) {
					$content .= $pages[$page];
				}
				
				//Declare indizar.css to 'style' the print page 
				$content = "<link rel='stylesheet' href='".get_bloginfo('wpurl')."/wp-content/plugins/indizar/indizar.css' type='text/css' media='screen' />
				".$content;
				$content = ind_delete_extra_tags($content);
			} else {
				//No wp_print, go ahead indizar
				
				//Maybe we will need a box at the begin if the configuration requires it
				//and the user didn't put a 'chapters' tag.
				$content = "<!--indizar_box_begin-->" . $content;
				
				//get the list of chapters
				$list = ind_chapters_list();
				
				//Create html code for the chapter index and the numbered list 
				//if we have a chapters list and we are showing the hole post
				$index = ind_index_list($list);
				$numbered_list = ind_numbered_list($list);
				
				//Sectionate the post using the marks created in the 'prepare_content' function
				if( strpos( $content, '<!--indizar_chapter-->' ) ) {
					//We don't need a new line tag before any new chapter
					$content = str_replace("\n<!--indizar_chapter-->", '<!--indizar_chapter-->', $content);
					$chapters_array = explode('<!--indizar_chapter-->', $content);
					
					//we just need one part, the one numbered with the chapter we are
					//loking for. Explode return an array whose first index is 0, thats why
					//we substract the 'indizar_first_chapter' variable
					$content = $chapters_array[$chapter-$indizar_first_chapter];
				}
				
				//Now we have the content we need. Add the chapters index and the numbered list
				
				//No chapters
				$search = "@(?:<p>)*\s*\[chapters:none\]\s*(?:</p>)*@i";
				if(preg_match_all($search, $content, $matches)) {
					//If we found a chapters tag that doesn't require style, use
					//the html code we have
					if(is_array($matches)) {
						$search = $matches[0][0];
						//replace the tag with the html code
						$content = str_replace ($search, "", $content);
						$content = str_replace ("<!--indizar_box-->", "", $content);
						$content = str_replace ("<!--indizar_box_begin-->", "", $content);
					}
				}
				
				//Chapters Box without style
				$search = "@(?:<p>)*\s*\[chapters\]\s*(?:</p>)*@i";
				if(preg_match_all($search, $content, $matches)) {
					//If we found a chapters tag that doesn't require style, use
					//the html code we have
					if(is_array($matches)) {
						$search = $matches[0][0];
						
						//define the html code without style
						$replace="";
						if($index) {
							$default_side = INDIZAR_BOX_FLOAT;
							$default_size = INDIZAR_BOX_SIZE;
							if($indizar_config) {
								$default_side = $indizar_config['box_float'];
								$default_size = $indizar_config['box_size'];
							}
							$replace = "<div class='indizar' id='".$default_side."' style='width: ".$default_size."px; float: $default_side;'>".$index."</div>";
						}
						
						//replace the tag with the html code
						$content = str_replace ($search, $replace, $content);
						
						//because we put a chapters box, it means we don't need
						//the marks we put for the default configuration
						$content = str_replace ("<!--indizar_box-->", "", $content);
						$content = str_replace ("<!--indizar_box_begin-->", "", $content);
					}
				}
				
				//Chapters box with style
				$search = "@(?:<p>)*\s*\[chapters\s*:\s*(\d+)(||,left|,right)\]\s*(?:</p>)*@i";
				if(preg_match_all($search, $content, $matches)) {
					//If we found a chapters tag with style, use the html 
					//code we have
					if(is_array($matches)) {
						//For each tag, configure the html code
						//to use the definitions in the tag
						foreach ($matches[1] as $key =>$v0) {
							//get the variables in the tag
							$v1=$matches[2][$key];
							if($v1) $v1=substr($v1,1);
							$search = $matches[0][$key];
							
							//configure the html code
							$replace="";
							if($index) {
								$replace = "<div class='indizar' id='".$v1."' style='width: ".$v0."px;";
								if($v1) $replace.=" float: $v1;";
								$replace.="'>".$index."</div>";
							}
							
							//replace the tag with the configured html code
							$content = str_replace ($search, $replace, $content);
						}
						
						//because we put a chapters box, it means we don't need
						//the marks we put for the default configuration
						$content = str_replace ("<!--indizar_box-->", "", $content);
						$content = str_replace ("<!--indizar_box_begin-->", "", $content);
					}
				}
				
				//Remember, if we have found previsously a 'chapters' tag,
				//we delete the '<!--indizar_box-->' tags we created to use if the 
				//configuration requires it. We don't need two boxes in the same page,
				//abey the user declared more than one 'chapters' tag.
				
				//Define default settings; no box, and numbered list at bottom
				$top = INDIZAR_TOP;
				$bottom = INDIZAR_BOTTOM;
				$box = INDIZAR_BOX;
				$box_size = INDIZAR_BOX_SIZE;
				$box_float = INDIZAR_BOX_FLOAT;
				
				//do we have configuration data?
				if(count($indizar_config)>0) {
					//Get the data from the configuration tag
					if($indizar_config['box_float'] == 'none') $box = false; else $box = true;
					if($indizar_config['config'] & 2) $bottom = true; else $bottom = false;
					if($indizar_config['config'] & 1) $top = true; else $top = false;
					
					if($box) {
						$box_float = $indizar_config['box_float'];
						$box_size = $indizar_config['box_size'];
					}
				}
				
				//Define the html code for a default box
				$div_box = "";
				if($box) {
					$div_box = "<div class='indizar' id='".$box_float."' style='width: ".$box_size."px;";
					if($box_float) $div_box.=" float: $box_float;";
					$div_box.="'>".$index."</div>";
				}
				
				//Time to use the 'indizar_box' marks.
				$search_box = "<!--indizar_box-->";
				
				//The first part of a post don't have a 'chapter' tag, ergo it doesn't have
				//an indizar_box mark, that's why we put the indizar_box_begin. It's time 
				//to use or delete it.
				//Do we have an 'indizar_box' mark? 
				if(!strpos($content,$search_box)) { //if not, use the indizar_box_begin
					$content = str_replace ("<!--indizar_box_begin-->", $div_box, $content);
				} else { //Delete it if we are not going to use it
					$content = str_replace ("<!--indizar_box_begin-->", "", $content);
				}
				
				//use the box mark to add the chapters box
				$content = str_replace ($search_box, $div_box, $content);
				
				//Do we use the numbered list at the begin?
				if($top) $content = "<p class='indizar'>$numbered_list</p> $content";
				
				//Do we use the numbered list at the end?
				if($bottom) $content .= " <p class='indizar scroll'>$numbered_list</p>";		
			}
		}
	}
	
	return $content;
}

/**
* Returns the HTML code for the list of chapters, as an unordered list.
*
* @access public
* @param list List of chapters {url, chapter}.
* @return The HTML code.
*/
function ind_index_list($list) {
	global $indizar_first_chapter, $chapter;

	$index="<ul>";
	$chap_index=$indizar_first_chapter;
	foreach($list as $item) {
		if($chapter!=$chap_index)
			if($item['blank'])
				$index.="<li><a href='".$item['url']."' target='_BLANK'>".$item['chapter']."</a></li>";
			else
				$index.="<li><a href='".$item['url']."'>".$item['chapter']."</a></li>";
		else
			$index.="<li>".$item['chapter']."</li>";
		$chap_index++;
	}
	$index.="</ul>";
	return $index;
}

/**
* Returns the HTML code for the numbered list to be writen at the top or the bottom 
* of the page, if the configuration demands it. If the configuration requires a Preface, 
* this function starts the numeration with 0, using the word 'Preface' instead the 
* number. If the configuration declares another title for the preface the plugin will use it.
*
* @access public
* @param string list List of chapters {url, chapter}
* @return The HTML code.
*/
function ind_numbered_list($list) { 

	global $wp_rewrite, $wp_query, $chapter, $chapters, $post, $page, $more, $multipage, $indizar_first_chapter,$indizar_config;

	$answer="<strong>".__("Chapters", 'indizar').":</strong> ";

	//If we aren't in the first chapter we declare the previous link
	if($chapter!=$indizar_first_chapter) {
		$answer.="| "."<a href='".$list[$chapter-1]['url']."'>".__("Previous", 'indizar' )."</a> | ";
	} else {
		$answer.="| ";
	}

	//We start the numerated list in the fisrts chapter
	$count=$indizar_first_chapter;

	foreach($list as $item) {
		$text = $count;
		
		//If we are in the chapter 0, we are in the preface the preface
		if($count == 0) {
			
			//The default value
			if(INDIZAR_PREFACE_TITLE)
				$text = INDIZAR_PREFACE_TITLE;
			else
				$text = __("Preface", 'indizar');
			
			//The old school, use the user defined variable in the post if we have it
			if(strlen(get_post_meta($post->ID, 'indizar_null_chapter_title', true))>0) {
				$text = get_post_meta($post->ID, 'indizar_null_chapter_title', true);
			}
			
			//The new school, use the configuration if we have it
			if(strlen($indizar_config['preface_title'])>0) {
				$text = $indizar_config['preface_title'];
			}
		}
		
		//If the counter becomes the chapter were we are
		//just show the number
		//else create the link
		if($chapter == $count) {
			$answer.="$text | ";
		} else {
			$answer.="<a href='".$item['url']."'>$text</a> | ";
		}
		$count++;
	}

	//If we aren't at the last chapter, create the next link
	if($chapter!=$count-1) {
		$answer.="<a href='".$list[$chapter+1]['url']."'>".__("Next", 'indizar')."</a> |";
	}
	return $answer;

}

/**
* Returns the list of chapters {url, chapter}.
*
* @access public
* @return The chapters list.
*/
function ind_chapters_list() {
	global $chapters, $post, $indizar_first_chapter;

	$item=false;

	$page_index=$indizar_first_chapter;
	
	//if the prepost function poblated the chapters array 
	//create a new array and poblate it with titles and links to each chapter
	if($chapters) {
		$item=array();
		foreach($chapters as $chapter) {		
			$url = false;
			$item[$page_index]['blank'] = false;
			if($aux=ind_hrefparser($chapter)) {
				$url = $aux['url'];
				$chapter = $aux['chapter'];
				$item[$page_index]['blank'] = true;
			}
			$item[$page_index]['chapter']=$chapter;
			//The first chapter didn't use a number to define the chapter
			if( $page_index == $indizar_first_chapter ) {
				$item[$page_index]['url']=get_permalink();
			} else {
				if($url) {
					$item[$page_index]['url']=$url;
				} else {
					//Permalink without prety url
					if( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
						$item[$page_index]['url']=get_permalink() . '&amp;chapter=' . $page_index;
					else //Permalink with prety url
						$item[$page_index]['url']=trailingslashit(get_permalink()) . user_trailingslashit("chapter/".$page_index, 'ch');
				}
			}
			$page_index++;
		}
	}

	return $item;
}

/**
* Function to add the button to the TinyMCE rich text editor
* This function should be called by a filter.
*
* @access public
*/
function ind_add_buttons() {
	// Don't bother doing this stuff if the current user lacks permissions
	if( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;

	// Add only in Rich Editor mode
	if( get_user_option('rich_editing') == 'true') {
	
		// add the button for wp21 in a new way
		add_filter('mce_external_plugins', 'add_ind_script');
		add_filter('mce_buttons', 'add_ind_button');
	}
}

/**
* Function to add the button to the bar.
* This function should be called by a filter.
*
* @access public
*/
function add_ind_button($buttons) {
	array_push($buttons, 'Indizar');
	return $buttons;
}

/**
* Function to set the script who should answer when the user press the button.
* This function should be called by a filter.
*
* @access public
*/
function add_ind_script($plugins) {
	$dir_name = '/wp-content/plugins/indizar';
	$url=get_bloginfo('wpurl');
	$pluginURL = $url.$dir_name.'/tinymce/editor_plugin.js?ver='.INDIZAR_HEADER_V;
	$plugins['Indizar'] = $pluginURL;
	return $plugins;
}

/**
* Function to delete the tags leaved in the content once passed in the procesor
*
* @access public
* @param string content
* @return The content without any chapters, chapter or indizar tag.
*/
function ind_delete_extra_tags($content) {
	//Delete the chapters tags, because we don't need them
	$search = "@(?:<p>)*\s*\[chapters\]\s*(?:</p>)*@i";
	$content = preg_replace($search, '', $content);
	
	//Delete the chapters tags, because we don't need them
	$search = "@(?:<p>)*\s*\[chapters\s*:\s*(\d+)(||,left|,right)\]\s*(?:</p>)*@i";
	$content = preg_replace($search, '', $content);

	return $content;
}

/**
* Function to detect if a title has a link
*
* @access public
* @param string text
* @return An array with the url and the title, false if there isn't a link inside the string.
*/
function ind_hrefparser($text) {
	$answer = false;
	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	if(preg_match_all("/$regexp/siU", $text, $matches)) {
		$answer['url'] = $matches[2][0];
		$answer['chapter'] = $matches[3][0];
	}
	return $answer;
}

?>
