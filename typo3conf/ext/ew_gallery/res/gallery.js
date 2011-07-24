var galleries = new Array();
var videoPlayer = null;
var timerVar = null;
var autoSlideInterval = 10000;
// to update the back gallery to the scroll position
var scrollTimer = null;
var detectNewPositionInterval = 3000;
var numGalleries = 0;
var numGalleriesInitialized = 0;
var currentFrontGal;
var currentBackGal;
var galleriesHidden = false;
// this will be called by the jwplayer
function playerReady(thePlayer) {
	videoPlayer = window.document[thePlayer.id];
	addListeners();
	galleriesGetReady();
}

function galleriesGetReady() {
	var allGalleries = jQuery(".ewFrontGallery");
	// if we need no video player: delete it
	if ( ( allGalleries.length == 0) && (jQuery('#ewGalleryPageVideos').children().length == 0) && (jQuery('.galleryVideoLink').length == 0) ) {
		jQuery(videoPlayer).detach();
	}
	numGalleries = allGalleries.length;
	allGalleries.each( function() {
		initGallery(this,galleryIsReady);
	});
	if ( jQuery.browser.msie ) {
		jQuery('.ewFrontGallery:visible').hide().show();
	}
}

function galleryIsReady(galId) {
	if (numGalleriesInitialized < numGalleries ) {
		return;
	}
	// ALL GALLERIES READY!!!!!
	getActiveGallery();
	jQuery(".ewBackgroundGallery").hide(); // hide all galleries
	currentBackGal = currentFrontGal.replace( 'galleryImages', 'bgGalleryImages');
	// wenn Video auf autostart und diese Gallerie im Vordergrund ist

	var videoFiles = jQuery('#ewGalleryPageVideos').children();
	if ( videoFiles.length ) { // if autostart page videos are defined
		galleriesHidden = true;
		var randomNumber = Math.floor( videoFiles.length * Math.random() );
		videoFile = videoFiles.slice(randomNumber).val();
		playVideo(videoFile,true);
	} else {
		jQuery("#"+currentBackGal).show(); // show only current gallery
	}
}

function clickPrevBgImage() { // PREV IMAGE
	if ( !jQuery(this).hasClass('disabled') ) {
		prevImage(jQuery(this).parents('.ewgallery').attr('id'));
	} else {
		jQuery(".ewBackgroundGallery .galleryArrowIcon").show();
		jQuery(".ewBackgroundGallery .hoverArrow").hide();
		minMaxOverlay(true,function(){jQuery(".ewBackgroundGallery .galleryArrowLeft").trigger('mouseenter')});
	}
}
function clickNextBgImage() { // NEXT IMAGE
	if ( !jQuery(this).hasClass('disabled') ) {
		nextImage(jQuery(this).parents('.ewgallery').attr('id'));
	} else {
		minMaxOverlay(true,function(){jQuery(".ewBackgroundGallery .galleryArrowRight").trigger('mouseenter')});
	}
}

jQuery(document).ready( function() {
	// BUG in FIREFOX: Player muss zuerst geladen werden, da er sonst kein playerReady() aufruft. Daher warten wir bis dahin
	
	// wenn kein Videoplayer
	if (jQuery('.videoWrap').length == 0 ) {
		galleriesGetReady();
	}
	// event handlers
	jQuery(".ewgallery .galleryArrowLeft").click( clickPrevBgImage ).dblclick( clickPrevBgImage );
	jQuery(".ewgallery .galleryArrowRight").click( clickNextBgImage ).dblclick( clickNextBgImage );
	jQuery(".ewFrontGallery .galleryImage, .ewFrontGallery .galleryEnlargeWrap").click( function() { // ENLARGE IMAGE
		enlargeImage(jQuery(this).parents('.ewgallery').attr('id'));
	} );
	jQuery(".ewFrontGallery .galleryEnlargeWrap").hover(
		function() {
			if ( jQuery.browser.msie ) {
				jQuery(this).parent().find('.galleryEnlargeButton').show();
			} else {
				jQuery(this).parent().find('.galleryEnlargeButton').fadeTo(400,1);
			} }, 
		function() {
			if ( jQuery.browser.msie ) {
				jQuery(this).parent().find('.galleryEnlargeButton').hide();
			} else {
				jQuery(this).parent().find('.galleryEnlargeButton').fadeTo(200,0);
			}
		});
	jQuery(".ewBackgroundGallery .galleryArrow").hover(
		function() {
			jQuery(this).addClass('hover');
			if ( !jQuery(this).hasClass('disabled') ) {
				jQuery(this).children('.galleryArrowIcon').hide();
				jQuery(this).children('.hoverArrow').show();
			} else {
				jQuery('.ewBackgroundGallery .galleryArrowIcon').not('.hoverArrow').show();
			}
		}, 
		function() {
				jQuery(this).removeClass('hover');
				if ( !jQuery(this).hasClass('disabled') ) {
					jQuery(this).children('.galleryArrowIcon').show();
					jQuery(this).children('.hoverArrow').hide();
				} else {
					jQuery('.ewBackgroundGallery .galleryArrowIcon').not('.hoverArrow').hide();
				}
		});
	jQuery(".ewBackgroundGallery .galleryImage, .ewBackgroundGallery .mask").click( function() { minMaxOverlay(true); } );
	jQuery(".galleryVideoLink").click( playVideo );
	jQuery(window).resize( function() {
		jQuery('.ewBackgroundGallery').each( function() {
			resizeBackground(jQuery(this).attr('id'));
		});
		return true;
	});
	
	// auto play of background gallery
	autoSlideInterval = parseInt(jQuery('.slideDuration').val())*1000;
	if ( jQuery(".ewgallery").length > 0 ) {
		resetAutoSlide();
	}
});

function initGallery(gallery,callback) {
	var id = jQuery(gallery).attr('id');
	galleries[id] = new Array();
	// index all the images
	var height = parseInt(jQuery('#'+id+' .galleryImages').height());
	var i = 0;
	jQuery('#'+id+' .galleryImage').each( function() {
		var image = jQuery(this);
/*		// center images vertically OBSOLETE: DONE BY PHP
		image.css('top',(height-parseInt(image.height()))/2); */
		// change visibility to opacity setting
		//image.fadeTo(0,(i==0)?1:0).css('visibility','visible').show(i==0);
		
		galleries[id][i] = new Array();
		galleries[id][i]['id'] = image.attr('id');
		i = i+1;
	});
	if ( i <= 1 ) { // no arrows, if only one image
		jQuery(gallery).find('.galleryArrow').removeClass('galleryArrow').addClass('noArrow');
	}
	galleries[id]['current'] = 0;
	galleries[id]['length'] = i;
	galleries[id]['fadeDuration'] = parseInt( jQuery(gallery).children('.fadeDuration').val());
	galleries[id]['autostartVideo'] = parseInt( jQuery(gallery).children('.autostartVideo').val());
	var backgroundGallery = jQuery(gallery).next();
	initBackgroundGallery(backgroundGallery);
	numGalleriesInitialized = numGalleriesInitialized+1;
	if ( callback != null ) {
		callback(id);
	}
}

function initBackgroundGallery(gallery) {
	// move gallery to background element
	jQuery('#background').append(gallery);
	//return;
	var id = jQuery(gallery).attr('id');
	galleries[id] = new Array();
	gallery.show(); // we need that now already in order to calculate the width and height of images (ie)
	// index all the images
	var i = 0;
	jQuery('#'+id+' .galleryImage').each( function() {
		var image = jQuery(this);
		// change visibility to opacity setting
		//image.fadeTo(0,(i==0)?1:0).css('visibility','visible');

		galleries[id][i] = new Array();
		galleries[id][i]['id'] = image.attr('id');
		galleries[id][i]['ratio'] = parseInt(image.children('img').attr('width'))/parseInt(image.children('img').attr('height'));
		if ( image.css('visibility') == 'hidden' ) { // when we have read the image size, we switch from visibility to display
			image.hide().css('visibility','visible');
		}
		i = i+1;
	});
	if ( i <= 1 ) { // no arrows, if only one image
		jQuery(gallery).find('.galleryArrow').removeClass('galleryArrow').addClass('noArrow');
	}
	galleries[id]['current'] = 0;
	galleries[id]['length'] = i;
	galleries[id]['fadeDuration'] = parseInt( jQuery(gallery).children('.fadeDuration').val());

	resizeBackground(id);
}

function resizeBackground(id) {
	var scrW = parseInt(jQuery(window).width());
	var scrH = parseInt(jQuery(window).height());
	var imgW = scrW;
	var imgH = scrH;
	var screenRatio = imgW/imgH;
	for ( var i = 0; i < galleries[id]['length']; i++ ) {
		var ratio = galleries[id][i]['ratio'];
		if ( screenRatio > ratio ) { // screen is more widescreen than image
			// clip height
			imgH = parseInt(imgW/ratio);
		} else {
			// clip width
			imgW = parseInt(imgH*ratio);
		}
		var image = jQuery('#'+galleries[id][i]['id']+' img');
		image.attr('width',imgW);
		image.attr('height',imgH);
		// center images vertically
		image.parent().css('top',Math.round((scrH-imgH)/2));
	}
}

function nextImage(galId) {
	var gal = jQuery('#'+galId);
	if ( gal.hasClass('ewBackgroundGallery') ) {
		resetAutoSlide(); // prevent double slide action
	}
	galId = gal.attr('id');
	var next = (galleries[galId]['current'] + 1) % galleries[galId]['length'];
	fadeToImage(galId,next,function(){resetAutoSlide();/* renew */});
}

function prevImage(galId) {
	var next = (galleries[galId]['current'] - 1 + galleries[galId]['length']) % galleries[galId]['length'];
	fadeToImage(galId,next);
}

var newImage; // this has to be global for callback
var fadeImageCallback;
function fadeToImage(galId,next,callback,bEnlarge) {
	var fadeImageCallback = callback;
	if ( typeof(bEnlarge) == 'undefined' ) {
		bEnlarge = false;
	}
	var galVisible;
	var isFrontGal = galId.indexOf('bgGalleryImages') == -1;
	if ( isFrontGal ) {
		// if frontgallery: always visible
		galVisible = true;
	} else { // if backgallery: check if visible
		galVisible = (currentBackGal == galId) && !galleriesHidden;
	}
	
	// if image already on screen & visible, do nothing
	if ( ( galleries[galId]['current'] == next ) && galVisible )
		return;
	
	var lastImage = jQuery('#'+galleries[galId][galleries[galId]['current']]['id']);
	lastImage = lastImage.add(lastImage.prev()); // add the credits
	newImage = jQuery('#'+galleries[galId][next]['id']);
	newImage = newImage.add(newImage.prev()); // add the credits
	
	if ( galVisible ) { // if gallery is already visible
		lastImage.fadeOut( galleries[galId]['fadeDuration'], function() {
			newImage.show();
			newImage.fadeIn( galleries[galId]['fadeDuration'], fadeImageCallback );
		} );
		galleries[galId]['current'] = next;
	} else { // if background gallery has been hidden (HERE THERE ARE ONLY BACKGROUND GALLERIES, BECAUSE ONLY THE CAN BE HIDDEN :) )
		// if we come from a different gallery, fade from 1 to 2
		if ( !galleriesHidden ) {
			if ( bEnlarge ) { // keep the actual image, if we do not explicitely enlarge it
				lastImage.hide();
				newImage.show();
			}
			jQuery(".ewBackgroundGallery").fadeOut(galleries[galId]['fadeDuration'], function() {
				jQuery('#'+galId).fadeIn(galleries[galId]['fadeDuration']);
			});
		} else { // we come from video. simply fade in
			jQuery('#'+galId).fadeIn(galleries[galId]['fadeDuration']);
		}
	}
	if ( !isFrontGal ) {
		currentBackGal = galId; // this is the new background gallery
		galleriesHidden = false; // background galleries is not hidden anymore
	}
}


var backId; // globals for callback use
var frontId;

function enlargeImage(galId) {
	resetAutoSlide(); // prevent autoslide directly after this
	frontId = galId;
	backId = frontId.replace( 'galleryImages', 'bgGalleryImages');
	minMaxOverlay( true, function() {
		fadeToImage( backId, galleries[frontId]['current'], function() {
			// stop video player
			if (jQuery('.videoWrap').length ) {
				videoPlayer.sendEvent('PLAY',false);
			}
			// start sliding
			resetAutoSlide();
		}, true );	
	});
}

function killTimer() {
	if ( timerVar ) {
		clearTimeout( timerVar );
		clearInterval( scrollTimer );
	}
}

function resetAutoSlide(firstInterval) {
	if (typeof(firstInterval) == "undefined") {
		firstInterval = autoSlideInterval;
	}
	killTimer();
	timerVar = setTimeout("autoSlideGallery("+autoSlideInterval+");",firstInterval);
	scrollTimer = setInterval("updateGalleryToScroll();",detectNewPositionInterval);
}

function autoSlideGallery() {
	if ( galleriesHidden && isOverlayMinimized() ) {
		// when video has been paused & overlay ist still minimized
		// repeat waiting loop
		resetAutoSlide(autoSlideInterval);
		return;
	}
	if ( isOverlayMinimized() ) {
		var backGal = currentBackGal;
	} else {
		// only update the back gallery id, when overlay is visible
		getActiveGallery();
		var backGal = currentFrontGal.replace( 'galleryImages', 'bgGalleryImages');
	}
	nextImage(backGal);
	// reset timer
	//timerVar = setTimeout("autoSlideGallery("+autoSlideInterval+");",autoSlideInterval);
}

function getActiveGallery() {
	if ( isOverlayMinimized() ) {
		if ( typeof(currentFrontGal) == 'undefined' ) {
			currentFrontGal = jQuery('.ewFrontGallery').first().attr('id');
		}
		return currentFrontGal;
	}
	// if we have only the back gallery
	if ( jQuery('.ewFrontGallery:visible').length == 0 ) {
		currentFrontGal = jQuery('.ewFrontGallery').first().attr('id');
		return currentFrontGal;
	}
	jQuery('.ewFrontGallery').each( function() {
		var gallery = jQuery(this);
		if ( gallery.parents('.sectionContent').height() == gallery.parents('.sectionContentWrap').height() ) {
			currentFrontGal = gallery.attr('id');
			return false;
		}
	});
	return currentFrontGal;
}

function updateGalleryToScroll() {
	if (typeof(currentFrontGal) == "undefined") {
		return;
	}
	if ( isOverlayMinimized() ) { // not when we are in slideshow mode
		return;
	}
	if ( currentFrontGal != getActiveGallery() ) {
		autoSlideGallery();
	}
}

/*
 * VIDEO FUNCTIONS
 */

function playVideo(videoFile,bPlayInBackground) {
	killTimer();
	if ( jQuery(this).parents('.ewgallery,.ewgalleryVideoButton').children('.videoFile').length ) { // play button clicked
		videoFile = jQuery(this).parents('.ewgallery,.ewgalleryVideoButton').children('.videoFile').val();
	}
	videoPlayer.sendEvent('LOAD','../../../../../'+videoFile);
	
	if ( bPlayInBackground == true) {
		jQuery('#'+currentBackGal).fadeOut(500,function(){videoPlayer.sendEvent('PLAY',true);galleriesHidden=true;});
	} else {
		minMaxOverlay( true, function() {
			// hide current Background Gallery
			jQuery('#'+currentBackGal).fadeOut(500,function(){videoPlayer.sendEvent('PLAY',true);galleriesHidden=true;});
		});
	}
}

function addListeners() {
	if (videoPlayer) { 
		videoPlayer.addModelListener("STATE", "stateListener");
	} else {
		setTimeout("addListeners()",100);
	}
}

function stateListener(obj) { //IDLE, BUFFERING, PLAYING, PAUSED, COMPLETED
	currentState = obj.newstate; 
	previousState = obj.oldstate; 
	if ( (previousState == "PLAYING" && currentState == "PAUSED") ||
	   (previousState == "PLAYING" && currentState == "IDLE") ) {
		if (isOverlayMinimized()) {
			minMaxOverlay(true);
		}
		// when coming back from video, delay only some seconds before switching to gallery
		resetAutoSlide(3000);
	}
	/*if (previousState == "PLAYING" && currentState == "PAUSED") { // continue auto sliding of gallery
		resetAutoSlide(autoSlideInterval);
	}*/
	if (previousState == "PAUSED" && currentState == "PLAYING") { // stop auto slide
		killTimer();
	}
}
