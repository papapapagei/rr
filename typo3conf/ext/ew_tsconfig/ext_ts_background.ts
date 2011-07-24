includeLibs.postPlayerFunc = fileadmin/templates/php/postPlayerFunc.php
background = COA

# the video player (get from content element on extra background page)
background {
	10 = RECORDS
	10 {
	#	if.isTrue.data = pages:tx_ewgallery_video
		source = 47
		dontCheckPid = 1
		tables = tt_content
		wrap = <div class="videoWrap videoHidden">|</div>
	}
}

[globalVar = TSFE:id=3]
#background.10 >
[global]