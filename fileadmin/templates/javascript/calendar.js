var firsttickerDateInterval = 7 * 1000;
var tickerDateInterval = 8 * 1000;
var tickerUnblinkInterval = 3 * 1000;
var blinkTimer;
var tickerTitle;
var tickerDate;

jQuery('document').ready( function() {
	tickerBackground = jQuery('.tickerBackground');
	tickerTitle = jQuery('.tickerEvent');
	tickerDate = jQuery('.tickerDate');
	tickerText = jQuery('.tickerText');
	tickerTitleLink = jQuery('.tickerTitleLink');
	if ( tickerTitle.html() == '' ) { // if no event at all
		tickerBackground.hide();
		tickerTitle.hide();
		tickerDate.hide();
		tickerTitleLink.hide();
		tickerText.hide();
		return;
	}
	tickerDate.css('position','absolute');
	var tickerDiv = tickerDate.offsetParent();
	tickerDiv.height(tickerDiv.height());
	tickerDate.css('top',parseInt((tickerDiv.height() - tickerDate.height()) / 2 ));
	tickerDate.css('left',parseInt((tickerDiv.width() - tickerDate.width()) / 2 ));
	tickerDate.hide();
	blinkTimer = setTimeout("blinkTicker()",firsttickerDateInterval);
});

function blinkTicker() {
	tickerTitle.fadeOut('fast',function() {tickerDate.fadeIn()});
	blinkTimer = setTimeout("unblinkTicker()",tickerUnblinkInterval);
}

function unblinkTicker() {
	tickerDate.fadeOut('fast',function() {tickerTitle.fadeIn()});
	blinkTimer = setTimeout("blinkTicker()",tickerDateInterval);
}
