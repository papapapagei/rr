includeLibs.postPlayerFunc = fileadmin/templates/php/postPlayerFunc.php
background = COA

# the video player (get from content element on extra background page)
background {
	#10 = RECORDS
	#10 {
	#	if.isTrue.data = pages:tx_ewgallery_video
	#	source = 47
	#	dontCheckPid = 1
	#	tables = tt_content
	#	wrap = <div class="videoWrap videoHidden">|</div>
	#}
	20 = HTML
	20.value = <div class="videoWrap videoHidden"><video class="projekktor" id="player_a" poster="favicon.png" title="This is Projekktor" width="600" height="350" controls></video></div>
}

[globalVar = TSFE:id=3]
#background.10 >
[global]