function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}


function insertIndizarLink(header) {
	
	var tagtext;
	var add_text = false;
	
	var chapter = document.getElementById('chapter_panel');
	var indexbox = document.getElementById('index_panel');	
	var configure = document.getElementById('configure_panel');	
	
	// who is active ?
	if (chapter.className.indexOf('current') != -1) {
		var chapterid = document.getElementById('chaptertag').value;
		var chapterfirst = document.getElementById('chapterfirst').checked;
		if (chapterid != 0 ) {
			
			if(!chapterfirst)
				if(!header)
					tagtext = "[chapter:" + chapterid + "]";
				else
					tagtext = "<"+header+">" + chapterid + "</"+header+">";
			else
				if(!header)
					tagtext = "[firstchapter:" + chapterid + "]";
				else
					tagtext = "<"+header+" class='firstchapter'>" + chapterid + "</"+header+">";
			add_text = true;
		}
	}
   
   if (indexbox.className.indexOf('current') != -1) {
      var sizeid = document.getElementById('sizetag').value;      
      var simplefloat = document.getElementById('simplefloat').value;
      var usedefault = document.getElementById('index_usedefault').checked;
      tagtext = "[chapters]";
      if(!usedefault && sizeid != "" && simplefloat !="" ) {
         tagtext = "[chapters:" + sizeid + "," + simplefloat + "]";
      }
      add_text = true;
   }

	if (configure.className.indexOf('current') != -1) {
		var numberedlist = document.getElementById('conf_numberedlist').value;
		var box = document.getElementById('conf_box').checked;
		var boxsize = document.getElementById('conf_boxsize').value;
		var boxfloat = document.getElementById('conf_boxfloat').value;
		var usepreface = document.getElementById('conf_usepreface').checked;
		var preface = document.getElementById('conf_preface').value;
		
		var config = numberedlist;
		
		var extra = "0,none";
		if(box) {
			extra = boxsize + "," + boxfloat;
		}
		
		if(usepreface) {
			extra = extra  + "," + preface;
		}
		
		tagtext = "[indizar:" + config + "," + extra + "]";
		add_text = true;
	}
	
	if(add_text) {
		window.tinyMCEPopup.execCommand('mceInsertContent', false, tagtext);
	}
	window.tinyMCEPopup.close();
}
