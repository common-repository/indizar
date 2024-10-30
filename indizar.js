jQuery(document).ready(function() {
	indizar_bind();
});

function indizar_bind() {
	jQuery("[class=indizar] a").click(
		function() {
			indizar_click(this.href, indizar_div, false);
			return false;
		}
	);
	jQuery(".indizar.scroll a").click(
		function() {
			indizar_click(this.href, indizar_div, indizar_top);
			return false;
		}
	);
	jQuery( 'li.share-email' ).addClass( 'share-service-visible' );
}
	
function indizar_click(url, div, jump) {
	jQuery("body").css("cursor", "wait");
	jQuery(div).animate({"opacity": .3}, 200, function() {
		if(jump) {
			jQuery.scrollTo(jump , 1000);
		}
	});
	jQuery.ajax({
		type:"POST",
		url: url,
		success: function(data) {
			jQuery(div).animate({"opacity": 0}, 200, function() {
				var output = jQuery(data).find(div);
				jQuery(div).html(output.html());
				indizar_bind();
				jQuery(div).animate({"opacity": 1}, 1200, function() {
					jQuery("body").css("cursor", "auto");
				});
			});
		},
		error: function() {
			jQuery(div).animate({"opacity": 1}, 1200, function() {
				jQuery("body").css("cursor", "auto");
			});
		}
	});
}
