RCPDL = RCPDL || {};

var getHrefHash = function(href) {
	var hash;

	if ( href.indexOf('#') != -1 ) {
		hash = href.substr(href.indexOf('#'));
		hash = hash.replace(/#rcpdl=/g, '');
	}

	return hash;
}

RCPDL.init = function($){
	var links = $('a[href*="#rcpdl="]')
	  , hash;

	if ( $('.rcpdl-modal').length == 0 ) {
		$('<span class="rcpdl-modal-overlay" style="display:none"></span><div class="rcpdl-modal" style="display:none"><span class="close" title="close">&times;</span></div>').appendTo($('body'));
	}
	if ( $('link[href="'+RCPDL.stylesheet+'"]').length == 0 ) {
		$('head').append('<link rel="stylesheet" type="text/css" href="'+RCPDL.stylesheet+'">');
	}

	$('body').append('<script src="'+RCPDL.recaptcha+'" type="text/javascript"></script>');

	links.each(function(i,link){
		link = $(link);
		hash = getHrefHash(link.prop('href'));
		link.attr('data-rcpdl-hash', hash);
		link.click(function(evt){
			evt.preventDefault();

			var modal = $('.rcpdl-modal').first()
			  , wwidth = jQuery(window).width()
			  , wheight = jQuery(window).height()

			modal.css({
			    maxHeight: parseInt(wheight * 0.85), // 85% of window height to leave extra space (15% wh) on top and bottom
			}).attr('data-rcpdl-hash', hash);

			var modal = $('.rcpdl-modal')
			  , mwidth = modal.width()
			  , mheight = modal.height()

			modal.css({
			    right: (wwidth/2)-(mwidth/2),
			    bottom: ((wheight/2)-(mheight/2)) * 0.5
			});

			modal.fadeIn({queue: false, duration: 200});
			modal.animate({ bottom: (wheight/2)-(mheight/2) }, 200);

			modal.html(
				$('#rcpdl-recaptcha').html()
					.replace(/{{recaptcha}}/g, RCPDL.recaptchaHTML)
			);

			$.getScript( RCPDL.recaptcha, function( data, textStatus, jqxhr ) {
			  $(window).trigger('resize');
			});

			$('.rcpdl-modal-overlay').fadeIn(200);

			RCPDL.listener.listen(hash, function(){
		  		var m = $('.rcpdl-modal').first()
		  		  , rc = $('#g-recaptcha-response', m)
		  		  , link = $('a[data-rcpdl-hash="'+hash+'"]');
	            if ( rc.length == 0 )
	                return;

	            if ( $.trim(rc.val()) ) {
	                RCPDL.listener.stop(hash);

	                closeModal();

	                link.attr('data-text', function(){
	                	return $(this).text();
	                }).text(function(){
	                	return $(this).text() + ' ' + RCPDL.i18n.loading;
	                }).attr('disabled', 'disabled');

	                $.ajax({
	                	type: 'POST',
	                	url: RCPDL.ajaxurl,
	                	data: {
	                		action: 'rcpdl_verify',
	                		recaptcha: rc.val(),
	                		hash: hash
	                	},
	                	success: function(res){
	                		if ( res.download_link ) {
	                			window.location.assign(res.download_link);
	                		} else {
	                			alert(RCPDL.i18n.err_general);
	                			link.click();
	                		}

	                		link.text(function(){
	                			return $(this).attr('data-text');
	                		}).removeAttr('disabled');
	                	},
	                	error: function(){
	                		alert(RCPDL.i18n.err_general);

	                		link.text(function(){
	                			return $(this).attr('data-text');
	                		}).removeAttr('disabled');

	                		link.click();
	                	}
	                })
	            }
		  	});
		});
	});

	$(window).resize(function() {
		var modal = $('.rcpdl-modal');

		if ( !(modal.is(":visible")) ) return;

		var wwidth = jQuery(window).width()
		  , wheight = jQuery(window).height()

		modal.css(
		  {
		    maxHeight: parseInt(wheight * 0.85), // 85% of window height to leave extra space (15% wh) on top and bottom
		   // width: parseInt(wwidth*0.44)
		  }
		);

		var modal = $('.rcpdl-modal')
		  , mwidth = modal.width()
		  , mheight = modal.height()

		modal.css({
		    right: (wwidth/2)-(mwidth/2),
		    bottom: (wheight/2)-(mheight/2),
		});
	});

	var closeModal = function() {
	  var modal = $('.rcpdl-modal')
	    , wheight = jQuery(window).height()
	    , mheight = modal.height();
	  if ( !(modal.is(":visible")) ) return;

	  if ( modal.attr('data-rcpdl-hash') )
	  	RCPDL.listener.stop(modal.attr('data-rcpdl-hash'));

	  modal.fadeOut({queue: false, duration: 200});
	  modal.animate({ bottom: (wheight/2)-(mheight/2) - 30 }, 200);
	  $('.rcpdl-modal-overlay').fadeOut(200);
	}

	$(document).keyup(function(e) {
		if (27 == e.keyCode) { // ESC
		  return closeModal();
		}
	});

	$('.rcpdl-modal span.close').click(function(){
		return closeModal();
	});

	$('.rcpdl-modal-overlay').click(function(){
		return closeModal();
	});
}

if ( !window.jQuery ) {
	var loadScript = function(url, callback) { // ref https://www.sitepoint.com/?p=9364
	    var script = document.createElement("script")
	    script.type = "text/javascript";
	    if (script.readyState) { //IE
	        script.onreadystatechange = function () {
	            if (script.readyState == "loaded" || script.readyState == "complete") {
	                script.onreadystatechange = null;
	                callback();
	            }
	        };
	    } else { //Others
	        script.onload = function () {
	            callback();
	        };
	    }
	    script.src = url;
	    document.getElementsByTagName("head")[0].appendChild(script);
	}

	loadScript(RCPDL.jquery, function () {
		RCPDL.init($);
	});
} else {
	jQuery(document).ready(function($){
		RCPDL.init($);
	});
}