var skipRest;
var shift;
var numSections;
var overlayTop;
var contentTop;
var slideTo = 78;
var menuHeight;
var overlayTopInit = 0;
var screenHeight;
var contentHeight;
var scrollStep;
var currentSectionHeight = 0; // the height of the section that is being scrolled right now
var currentSection = 0;
var currentSectionFinished = true;
var numSectionsOpen = 0;
var leftToScroll;
var overlayStop;
var magnetBorder;
var doNormalScrolling = false;


function checkCookies() {
	jQuery.cookie('testCookie','testValue');
	if ( jQuery.cookie('testCookie') == 'testValue' ) {
		jQuery.cookie('testCookie',null); // delete cookie
		return true;
	} else {
		return false;
	}
}

jQuery(document).ready(function() {
	var firstVisit = checkCookies() && ( jQuery.cookie('alreadyVisited') ? false : true );
	//var firstVisit = true;
	if ( firstVisit ) {
		jQuery.cookie('alreadyVisited', 'true');
		jQuery("#overlay").css('visibility','hidden');
	}
	
	jQuery('body').mousewheel(scrollOverlay);
	jQuery('#overlay').append('<div id="hoverArea" style="display:none;"></div>');
	jQuery('#hoverArea').click(function(){minMaxOverlay(true)});
	jQuery('#overlay,#hoverArea').hover( function() { // magnetic overlay when minimized
		if ( isOverlayMinimized() ) {
			var overlay = jQuery('#overlay').filter(':not(.toggleTop:animated)');
			if ( overlay.length ) {
				overlay.animate({top:'-='+80+'px'},200,function(){jQuery('#contentWrap').hide().show()}).addClass('toggleTop');
				jQuery('#contentWrap').animate({height:'+=80px'},0);
				jQuery('#hoverArea').height(jQuery('#hoverArea').height()+jQuery('#contentWrap').height())
			}
		}
	}, function() {
		var overlay = jQuery('#overlay').filter('.toggleTop');
		if ( overlay.length) {
			overlay.stop(false,true).animate({top:'+='+80+'px'},200).removeClass('toggleTop');
			jQuery('#contentWrap').animate({height:'-=80px'},0);
			jQuery('#hoverArea').css('height','');
		}
	});
	jQuery('#minMax').click(function(event) {
		minMaxOverlay(true);
	});
	jQuery('.csc-header').click(function(event) {
		minMaxSection(this);
	});

	preMinimizeContent();
	initDimensions();
	calculateHeightForIE(jQuery('#overlay'));
	if ( jQuery('#background .videoWrap').length ) {
		calculateHeightForIE(jQuery('#background .videoWrap'));
	}
	
	// if window gets resized, recalculate height
	jQuery(window).resize( initDimensions );
	
	if ( firstVisit ) {
		// hide overlay
		minMaxOverlay(false);
		// slide overlay in at the beginning (after a short pause)
		jQuery("#overlay").css('visibility','visible');
		setTimeout("minMaxOverlay(1500);",3000);
	}
} );

function preMinimizeContent() {	
	// if contact form has been submitted, minimize everything but contact form
	if ( jQuery("#formhandler_contact_form").length && jQuery("#formhandler_contact_form #submitted").val().length ) {
		// minimize everything
		jQuery('.min-0').removeClass('min-0').addClass('min-1');
		// but show contact form
		jQuery('#formhandler_contact_form').parents('.section').removeClass('min-1').addClass('min-0');
	}
	
	// do the minimize thing
	jQuery('.min-1').children('.csc-header').trigger('click');
}

// everytime the layout changes (window resizing) we have to recalculate the sizes
function initDimensions() {
	screenHeight = jQuery(window).height();
	if ( overlayTopInit == 0) {
		overlayTopInit = jQuery('#overlay').offset().top;
	}
	overlayTop = parseInt( jQuery('#overlay').css('top') );
	menuHeight = parseInt(jQuery('#topMenu').offset().top) + jQuery('#topMenu').height() - overlayTop;
	scrollStep = Math.min( Math.max( 30, Math.round( screenHeight / 15 ) ), 75 );
	magnetBorder = slideTo + parseInt(screenHeight/5);
	
	calculateHeightForIE(jQuery('#overlay'));
	if ( jQuery('#background .videoWrap').length ) {
		calculateHeightForIE(jQuery('#background .videoWrap'));
	}

	if ( jQuery('#minMax').hasClass('minMaxClosed') ) {
		minMaxOverlay(false);
	}
}

function scrollOverlay( event, delta ) {
	if ( jQuery(':animated').length ) // when we're animating anything at the moment, ignore interaction
		return false;
	var sections = jQuery('#content .sectionContentWrap');
	sections = sections.not('.sectionContentWrap .sectionContentWrap'); // do not scroll subitems
	numSections = sections.length;
	if ( !numSections ) // if there are no sections, use normal scrolling
		return true;
	numSectionsOpen = 0;
	jQuery('#content .sectionContentWrap').each( function(i) {
		numSectionsOpen += jQuery(this).height() ? 1 : 0;
	});
	overlayTop = parseInt( jQuery('#overlay').css('top') );
	contentTop = jQuery('#content').offset().top;
	contentHeight = jQuery('#content').outerHeight();
	skipRest = false;
	doNormalScrolling = false;
	
	// if content fits screen, stop scrolling (difference between content bottom and screen height)
	leftToScroll = contentTop + contentHeight - screenHeight;
	// but at least go back to the initial top position (creating black bottom fill)
	leftToScroll = overlayTop > overlayTopInit ? overlayTop-overlayTopInit : leftToScroll;
	// the stop position for the overlay (even if content still gets sqeezed)
	overlayStop = Math.max(0,overlayTop-Math.max(0,leftToScroll));
	// this is nicer: scroll current element to it's end
	leftToScroll = (leftToScroll > 0) ? leftToScroll : currentSectionHeight;
	shift = Math.max( delta * scrollStep, Math.min( 0, -leftToScroll) );

	if ( shift && contentHeight ) {
		bBusy = true;
		// when scrolling up, begin with the last element
		if ( shift > 0 ) {
			sections = jQuery(sections.get().reverse());
		}
		sections.each( function(i) {
			j = shift < 0 ? i : numSections - i - 1; // number of the section we're working on (attention with reverse order!)
			var section = jQuery(this);
			var sectionContent = jQuery('>.sectionContent',section);
			var sectionVisible = section.offset().top - jQuery('body').scrollTop() < screenHeight;
			var notMinimized = section.parent().hasClass('min-0');
			
			// scroll only when there is still sth. to scroll in the section, else skip it
			if ( !skipRest && ( currentSectionFinished ? true : currentSection == j ) &&
				( ( (shift < 0) && (-parseInt(sectionContent.css('top')) < sectionContent.height()) && !overlayTop ) || ( (shift > 0) && (section.height() < sectionContent.height() ) ) ) && sectionVisible && notMinimized ) {
				// this one is for our friend ie7 - to prevent relatively positioned elements from getting height zero
				if ( jQuery.browser.msie) {
					section.find('*').each( function() {
						if ( jQuery(this).css('position').toLowerCase() == 'relative') {
							jQuery(this).height(jQuery(this).height());
						}
					});
				}
				// change height (min: 0, max: contentHeight)
				var newHeight = Math.max(0,Math.min(section.height()+shift,sectionContent.height()));

				section.height(newHeight); // change height of container
				sectionContent.css('top',section.height()-sectionContent.height()); // at same time scroll content up
				
				if ( jQuery.browser.msie ) {
					calculateHeightForIE(jQuery('#overlay'));
					if ( newHeight != section.height() ) { // ie does not like height=0
						section.height(1);
						sectionContent.css('top',-sectionContent.height());
						currentSectionFinished = true;
					}
					jQuery('.sectionContentWrap').each( function() { // scrolling bug in calendar - force position update in ie
						jQuery(this).css('position','static');
						jQuery(this).css('position','relative');
					});
				}
				section.parent().toggleClass('sectionClosed',newHeight==0);
				currentSection = j;
				currentSectionHeight = newHeight;
				// indicates if this section is still partly opened
				currentSectionFinished = currentSectionFinished || ( section.height() == 0 ) || (section.height() == sectionContent.height() );
				skipRest = true;
			}
			// everything expanded : scroll overlay down
			if ( (i == numSections-1) && !skipRest ) {
				var newTop = overlayTop + shift;
				if ( shift > 0 && ( newTop >= screenHeight-menuHeight - magnetBorder ) ) { // if we scrolled near the bottom
					jQuery('#overlay').animate({'top': screenHeight-slideTo + 'px'},function(){
						jQuery('#contentWrap').height(slideTo).hide().show();
						calculateHeightForIE(jQuery('#overlay'));
					});
					jQuery('#minMax').addClass('minMaxClosed');
					jQuery('#hoverArea').show();
					enableBackgroundGalleryNavigation(true);
				} else if ( shift < 0 && ( overlayTop-overlayStop <= magnetBorder ) ) { // if we scrolled near the top
					if ( ( overlayTop-overlayStop == 0 ) && (leftToScroll > 0) ) { // we are already at the top and need further scrolling
						doNormalScrolling = true; // normal scrolling
					} else {
						jQuery('#overlay').animate({'top': overlayStop+'px'},function(){calculateHeightForIE(jQuery('#overlay'));});
					}
				} else if ((jQuery('body').scrollTop() > 0) && (shift > 0)) { // scrolling up and the scrollbar is not at zero
					doNormalScrolling = true; // do normal scrolling
				} else { // overlay is closed -> open it and scroll up
					jQuery('#overlay').css({'top': newTop + 'px'});
					calculateHeightForIE(jQuery('#overlay'));
					jQuery('#contentWrap').height('').hide().show();
					jQuery('#minMax').removeClass('minMaxClosed');
					jQuery('#hoverArea').hide();
					enableBackgroundGalleryNavigation(false);
				}
			}
		});
	}
	
	// prevent event bubbling
	return doNormalScrolling;
}

function minMaxOverlay(bAnimate,callback) {
	if ( jQuery('#overlay.stopInteraction').length ) {
		return;
	}
	var animationTime = bAnimate ? 500 : 1;
	// if a number has been supplied, use it instead of the boolean value
	animationTime = (typeof(bAnimate) == 'number' ) ? bAnimate : animationTime;
	if ( isOverlayMinimized() ) { // only if on bottom, maximize
		// callback function
		var afterSlide = function(){
			if ( callback ) callback();
			jQuery('#contentWrap').hide().show();
			calculateHeightForIE(jQuery('#overlay'));
//			jQuery('#overlay').css('overflow','visible');// prevent scroll bars
			enableBackgroundGalleryNavigation(false);
		};
		if ( bAnimate ) {
			jQuery('#overlay').animate({'top': overlayTopInit+'px'},animationTime,afterSlide);
		} else {
			jQuery('#overlay').css('top', overlayTopInit+'px');
			afterSlide();
		}
//		jQuery('#overlay').css('overflow','hidden');// prevent scroll bars
		jQuery('#overlay').css('bottom','0px').removeClass('toggleTop');
		jQuery('#contentWrap').height('').hide().show();
		jQuery('#minMax').removeClass('minMaxClosed');
		jQuery('#hoverArea').hide();
	} else { // minimize if somewhere else
		var afterSlide = function(){
			if ( callback ) callback();
			jQuery('#contentWrap').height(slideTo);
			calculateHeightForIE(jQuery('#overlay'));
			enableBackgroundGalleryNavigation(true);
		};
		if ( bAnimate ) {
			jQuery('#overlay').animate({'top': screenHeight-slideTo + 'px'},afterSlide);
		} else {
			jQuery('#overlay').css('top', screenHeight-slideTo + 'px');
			afterSlide();
		}
		jQuery('#minMax').addClass('minMaxClosed')
		jQuery('#hoverArea').show();
	}
}

function isOverlayMinimized() {
	// checks if overlay is minimized
	return (parseInt( jQuery('#overlay').css('top') ) == screenHeight-slideTo) || jQuery('#overlay').hasClass('toggleTop');
}

function minMaxSection(object) {
	var clickSection = jQuery(object).next('.sectionContentWrap');
	var sectionContent = clickSection.children('.sectionContent');
	// find all sections below and maximize half open sections
	var sections = clickSection.parent().nextAll().children('.sectionContentWrap');
	sections.each( function(i) {
		var section = jQuery(this);
		var sectionContent = section.children('.sectionContent');
		// if there is an unfinished section below, maximize it
		if ( ( section.height() != 0 ) && ( section.height() != sectionContent.height() ) ) {
			section.height(sectionContent.height());
		}
	});
	numSectionsOpen = 0;
	jQuery('#content .sectionContentWrap').each( function(i) {
		numSectionsOpen += (jQuery(this).height() > 1) ? 1 : 0;
	});
	if ( ( clickSection.height() > 1) && (numSectionsOpen > 1) ) { // expanded => minimize
		clickSection.height(0);
		if ( clickSection.height() != 0 ) { // ie does not like height=0
			clickSection.height(1);
		}
		sectionContent.css('top',-sectionContent.height());
		currentSectionFinished = true;
		clickSection.parent().addClass('sectionClosed');
	} else { // minimized => expand
		// this one is for our friend ie7 - to prevent relatively positioned elements from getting height zero
		clickSection.find('*').each( function() {
			if ( jQuery(this).css('position').toLowerCase() == 'relative') {
				jQuery(this).height(jQuery(this).height());
			}
		});
		clickSection.height(sectionContent.height());
		sectionContent.css('top',0);
		currentSectionFinished = true;
		clickSection.parent().removeClass('sectionClosed');
	}
}

function calculateHeightForIE(object) {
	if ( !jQuery.browser.msie )
		return 0;
	// calculates the height from top/bottom
	var minHeight = object.offsetParent().height()-parseInt(object.css('bottom'))-parseInt(object.position().top)-20;
	if ( typeof(minHeight) == 'undefined' ) {
		return 0;
	}
	if ( minHeight > object.height() ) {
		object.height(minHeight);
		// das scheint's zu brauchen
		object.hide();
		object.show();
		return minHeight;
	}
}

function enableBackgroundGalleryNavigation( bEnable ) {
	var jGal = jQuery("#"+currentBackGal);
	if ( bEnable && jGal.length ) {
		jGal.find('.galleryArrow').removeClass('disabled').fadeIn(200); // enable background gallery's navigation
		jGal.filter('.hover').trigger('mouseenter');
		galleries[currentBackGal]['startedWithImage'] = galleries[currentBackGal]['current'];
	} else if (jGal.length) {
		jGal.find('.galleryArrow').addClass('disabled'); // disable background gallery's navigation
		jGal.filter(':not(.hover)').trigger('mouseleave').find('.galleryArrow').fadeOut(200);
	}
}

