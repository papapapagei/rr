var pressHeights = new Array();
var pressContainerHeights = new Array();
var autoSlidePressIntervall = 8 * 1000;
var fadePressDuration = 1 * 1000;
if ( jQuery.browser.msie ) fadePressDuration = 0; // fading klappt hier nicht :(
var pressTimer = null;

jQuery(document).ready( function() {
	jQuery('.presseteaser-container').each( function() {
		var containerId = jQuery(this).parent().attr('id');
		pressHeights[containerId] = new Array();
		jQuery(this).children().each( function(index) {
			var containerId = jQuery(this).parent().parent().attr('id');
			var itemId = jQuery(this).attr('id');
			pressHeights[containerId][index] = jQuery(this).outerHeight();
			jQuery(this).get(0).savedOuterHeight = pressHeights[containerId][index]; // we need that later on
		});
		pressHeights[containerId].sort().reverse();
		// calculate maximum height of container
		if ( pressHeights[containerId].length > 1 ) {
			pressContainerHeights[containerId] = pressHeights[containerId][0] + pressHeights[containerId][1];
		} else if ( pressHeights[containerId].length > 0 ) {
			pressContainerHeights[containerId] = pressHeights[containerId][0];
		} else {
			pressContainerHeights[containerId] = 0;
		}
		initPressContainer(containerId);
		repositionPressItems(containerId);
	});
	calculateHeightForIE(jQuery('#overlay'));
	pressTimer = setInterval("autoSlidePressItems()",autoSlidePressIntervall);
});

function initPressContainer(containerId) {
	var container = jQuery('#'+containerId+' .presseteaser-container');
	container.height(pressContainerHeights[containerId]);
	var children = container.children();
	children.css('position','absolute');
	children.css('width','100%');
	children.hide();
	container.append('<div class="press-hidden" style="display:none;"></div>');
	container.append('<div class="press-box press-box1" style="position:relative;"></div>');
	container.append('<div class="press-box press-box2" style="position:relative;"></div>');
	container.find('.press-hidden').append(children);
	//box2.append(container.find('.press-teaser:visible').last());
}

function repositionPressItems(containerId,items) {
	var container = jQuery('#'+containerId+' .presseteaser-container');
	var hidden = container.find('.press-hidden');
	var box1 = container.find('.press-box1');
	var box2 = container.find('.press-box2');
	box1.height(pressHeights[containerId][0]);
	box2.height(pressHeights[containerId][1]);
	// check if boxes fit
	if ( ( hidden.children().first().get(0).savedOuterHeight <= box1.height() ) &&
		( hidden.children().first().next().get(0).savedOuterHeight <= box2.height() ) ) {
		box1.append(hidden.children().first().show());
		box2.append(hidden.children().first().show());
	} else { // else exchange them
		box2.append(hidden.children().first().show());
		box1.append(hidden.children().first().show());
	}
}

function exchangePressItem(containerId) {
	var container = jQuery('#'+containerId+' .presseteaser-container');
	var hidden = container.find('.press-hidden');
	var box1 = container.find('.press-box1');
	var box2 = container.find('.press-box2');
	// check if boxes fit
	var element = hidden.children().first();
	if ( element.length == 0 ) {
		return;
	}
	var oldItem;
	var fitsFirstBox = element.get(0).savedOuterHeight <= box1.height();
	var fitsSecondBox = element.get(0).savedOuterHeight <= box2.height();
	var rnd = (Math.random() > 0.5);
	var takeFirstBox = ( fitsFirstBox && fitsSecondBox ) ? rnd : fitsFirstBox;
	if ( takeFirstBox ) {
		oldItem = box1.children();
		box1.append(element);
	} else { // else exchange them
		oldItem = box2.children();
		box2.append(element);
	}
	element.fadeIn(fadePressDuration);
	oldItem.fadeOut(fadePressDuration,function(){
		var hidden = container.find('.press-hidden');
		hidden.append( jQuery(this));	
	});
}

var pressPosition = new Array();
function autoSlidePressItems() {
	jQuery('.presseteaser-container').each( function() {
		var containerId = jQuery(this).parent().attr('id');
		exchangePressItem(containerId);
	});
}
