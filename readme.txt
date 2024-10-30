=== Indizar ===
Contributors: sebaxtian
Tags: chapters
Requires at least: 2.4
Tested up to: 3.4
Stable tag: 0.8.1

A plugin to index posts and pages using chapters.

== Description ==

The _nextpage_ tag in Wordpress gives a simple indexing system, but
sometimes we need a better solution. Indizar creates chapters with
titles and gives a function to retrieve those names. Also it has a
tool to put an index whereas the user wants in the post, or a tag to
define a default configuration for the boxes.

To add a chapter use [chapter:Title] at the beginning of the chapter. 
The title would be write as a _header_, and you can define the apparance 
with a CSS file (see the FAQ). Notice that the first chapter in the list 
would have the name of the post, and wouldn't be shown at the beggining of 
the content. The first occurrence of the tag [chapter: ****] would be the 
second item in the chapters list. 

If you need another name for the first chapter you can use [firstchapter:Title]
to create a header. It doesn't have to be at the beginning of the page. 
With this you can have an introduction and the first chapter in the same page.

To allow Indizar to use the ´headers´ as chapter marks, you have to define it 
in your __wp-config__ file. Read the topic about how to modify Indizar 
configuration at the end of this README.


To add the index use [chapters:size,left|right] where size is the width 
of the context box, left | right is the float position for the box. Those 
values are optional and you can use just [chapters]. The index box is 
hidden in the index and search pages, it will be shown in each 'single page'.
Use the configuration tag instead this if you want to show a box in every 
page at the same place.

To add a global configuration use [indizar:#conf,#box\_size,[left|right|none],{preface\_title}], 
where the #conf is:

* 0 - to not show a numbered list
* 1 - to show it at the begin of the post
* 2 - to show it at the end
* 3 - to show it both sides

The size box and the float position declares the general behavior. If the float value is
´right´ or ´left´ the plugin creates a box after every chapter title in the post,
if it is ´none´ the plugin will let you define the chapters box manually with the
tag [chapters]. If there is a [chapters] tag in a page the plugin will use this 
position instead the one declared in the ´configuration´ tag. The ´preface\_title´ 
is optional and if declared the first chapter would be numbered as __0__ and the 
text you use will be the title in the numbered list.

Why use a preface? Maybe the first chapter of your post is really the second page,
and when you look at the URL the fisrt chapter is named ´post/chapter/2´. This
sets correctly the url to show your first chapter as ´post/chapter/1´.

To not display a chapter list in a page when you have a configuration set, use the tag
[chapters:none].

There is a button in the RichText Editor to add __chapters__, __chapters box__ and the 
__configuration__ tag to your post.

If you want Indizar to have a default configuration you can declare it in 
the __wp-config__ file. This is my personal configuration, modify it as you
require.
 
* define("INDIZAR\_TOP", false);
* define("INDIZAR\_BOTTOM", true);
* define("INDIZAR\_BOX", true);
* define("INDIZAR\_BOX\_SIZE", 200);
* define("INDIZAR\_BOX\_FLOAT", "right");
* define("INDIZAR\_USE\_PREFACE", false);
* define("INDIZAR\_PREFACE\_TITLE", "Prefacio");
* define("INDIZAR\_USE\_HEADERS", "h1");
* define("INDIZAR\_DIV", ".entry-content");
* define("INDIZAR\_TOPDIV", "#box");

The INDIZAR\_DIV and INDIZAR\_TOPDIV are required only if you want to enable 
the AJAX feature. The first one requires the class or the id of the div containing 
the text you want to change. The second one requires the class or the id of the 
tag where you want to scroll when using the links at the end of each chapter. In 
the example, indizar would change the content inside the class 'entry-content' and 
will scroll to the id 'box'. __These are the data for my theme, but it would be 
different in yours__.

To get the list of chapters in your theme, use the function `ind_chapters_list()`. This 
function returns the array with the chapters, or false if there are no chapters. 
It __has__ to be used after the function `the_post()`. Or, to get the html, use `$list = ind_chapters_list(); echo ind_index_list($list);`

Indizar has been translated to rusian by __[Дн](http://zauglom.info/lokalizaciya-plagina-indizar-176.html "Неполное собрание личных сочинений")__, polish by Bartosz Kowa and french by the __[InMotion Hosting Team](http://www.inmotionhosting.com/)__. Thanks for your time guys!

Screenshots are in spanish because it's my native language. As you should know yet 
I __spe'k__ english, and the plugin use it by default.

== Installation ==

1. Decompress indizar.zip and upload `/indizar/` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the __Plugins__ menu in WordPress
3. If you use permalinks, go to __Options > Permalinks__ and press the button __Save Changes__. This would recreate your permalinks cache with the __chapter__ tag.
4. You can use indizar.css to create your own style. Copy it to your theme idrectory.
5. If you want a default configuration define it in your __wp-config.php__ file.

== Frequently Asked Questions ==

= Is this plugin bug free? =

I don't think so. So far it works with my permalinks, but I didn't test it with other
 configurations. Feedbacks would be appreciated.

= Permalinks doesn't work!!!!!! =

Did you __recreate__ permalinks rules? Go to __Options > Permalinks__ and press the button __Save Changes__.

= Can I set my own CSS? =

Yes. Copy the file indizar.css to your theme folder. The plugin will check for it.

= Can I use the Ajax script in my theme? =

Yes, use the script 'indizar_click(url, div, jump)'.
* Url is the link to the page.
* Div is the class or id where the content is.
* Jump is the class or id of the tag where you want to scroll. Set this to false
if no scroll is required.

If you use a jQuery call to set the binds, remember to create te bibds again because
the new content has new DomObjects.

== Screenshots ==

1. Add one chapter
2. Add an index box
3. Configuration tag
4. Editor example using the ´chapters´ tag
5. First chapter example
6. Editor example using the ´configuration´ tag
7. Last chapter example using 'configuration' tag, see the top numbered list

== Changelog ==

= 0.8.1 =
* Solved bug with lost pages (problem with 3.4 update)

= 0.8 =
* Added ajax capabilities to change the content.

= 0.7.2 =
* If title has a link open it in a new window. 

= 0.7.1 =
* A title with a link would be treated as the URL of the chapter.

= 0.7 =
* Now you can use ´headers´ as chapter marks.
* Modified README with the new API.

= 0.6.9 =
* Checked for WP 3.1

= 0.6.8 =
* Added tag 'none' to not display a chapter list.

= 0.6.7 =
* Modified content action declaration to have a high priority.

= 0.6.6 =
* Modified TinyMCE call to solve bugs with wp-cache.

= 0.6.5 =
* Solved minor bugs.

= 0.6.4 =
* Using WP functions to add safely scripts and css.

= 0.6.3 =
* Solved a bug with category rules in permalinks.

= 0.6.2 =
* Solved a strange bug in the editor.

= 0.6.1.1 =
* Solved an error in the German translation.

= 0.6.1 =
* Polish and Russian translations updated.
* First German translation.

= 0.6 =
* Stable release.
* Solved a bug with multiple blog posts (patch by Christian Rehn).

= 0.5.4 =
* Release for developers.

= 0.5.3.1 =
* The code has been indented, documented and standardised.
* Enhaced the tinyMCE editor usability in the index tag.
* Solved a bug when tinyMCE editor was in full window.

= 0.5.3 =
* Now you can set your own css file (see FAQ).

= 0.5.2 =
* Now Indizar can be used in pages. Remember to recreate the permalinks (see the FAQ).
* Added i18n for Polish

= 0.5.1 =
* Defined special variables to use in wp-config and set a default behavior.
* Added i18n for Russian

= 0.5 =
* First version using a special tag to configure the behavior of indizar in a post.
