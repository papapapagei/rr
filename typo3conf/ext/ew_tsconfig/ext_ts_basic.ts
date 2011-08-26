config {
	linkVars = L
	# Standardsprache Deutsch
	sys_language_uid = 0
	language = de
	locale_all = de_DE
	sys_language_mode = content_fallback
	sys_language_overlay = 1
	simulateStaticDocuments = 0
	tx_realurl_enable = 1
	doctype = html_5
}

config.baseURL = http://www.r-revue.de/
[globalString = ENV:HTTP_HOST = localhost]
	config.baseURL = http://localhost/r-revue/
[global]
[globalString = ENV:HTTP_HOST = elio-laptop]
	config.baseURL = http://elio-laptop/r-revue/
[global]
[globalString = ENV:HTTP_HOST = 85.183.86.106]
	config.baseURL = http://85.183.86.106/r-revue/
[global]
[globalString = ENV:HTTP_HOST = realitaten-revue.de]
	config.baseURL = http://www.realitaten-revue.de/
[global]
[globalString = ENV:HTTP_HOST = s142297232.online.de]
	config.baseURL = http://s142297232.online.de/rr2011/
[global]

[globalVar = GP:L = 1]
config {
	sys_language_uid = 1
	language = en
	locale_all = en_US
}
[global]

page = PAGE
page {
   typeNum = 0  
   10 = USER
   10.userFunc = tx_templavoila_pi1->main_page
   headerData.10 = TEXT
   headerData.10.value = <link rel="shortcut icon" href="favicon.png" type="image/png">
}

page.includeCSS {
	base = fileadmin/templates/css/base.css
	content = fileadmin/templates/css/content.css
}
[browser = msie]
	page.includeCSS.ie7 = fileadmin/templates/css/ie7.css
[global]

page.includeJS {
	jquery = fileadmin/templates/javascript/jquery-1.4.2.min.js
	mousewheel = fileadmin/templates/javascript/jquery.mousewheel.min.js
	cookie = fileadmin/templates/javascript/jquery.cookie.js
	scroll = fileadmin/templates/javascript/scroll.js
	press = fileadmin/templates/javascript/press.js
	calendar = fileadmin/templates/javascript/calendar.js
}


# this is the condition (nested elements dont't get wrapped)
onlyIfNotNested{
	equals.data = register:tx_templavoila_pi1.parentRec.CType
	value = templavoila_pi1
	negate = 1
}

renderedHeader = IMAGE
renderedHeader {
	altText.current = 1
	altText.insertData = 1
	file = GIFBUILDER
	file {
		backColor = #0d0d0d
		XY = [10.w]+7,23
		10 = TEXT
		10 {
			text.current = 1
			text.insertData = 1
			text.case = upper
			fontSize = 17
			fontColor = #FFFFFF
			offset = 0,17
			fontFile = fileadmin/templates/fonts/Neutra2TextDemi-kerning90-custom.pfb
		}
	}
}

subHeader < renderedHeader
subHeader.file.XY = [10.w]+7,18
subHeader.file.10.fontSize = 13
subHeader.file.10.offset = 0,13
subHeader.file.10.fontColor = #C9C9C9


header1 = COA
header1 {
	10 < lib.stdheader.10.1
	10.if < onlyIfNotNested
	20 < subHeader
	20.if < onlyIfNotNested
	20.if.negate = 0
	20.wrap = <div class="subHeader">|</div>
}

# EXTEND THE STANDARD HEADER WRAPPER
lib.stdheader.stdWrap.dataWrap = <div class="csc-header csc-header-n{cObj:parentRecordNumber} headerType{field:header_layout}">|</div>

# REPLACE NESTED H1 HEADERS WITH RENDERED ONES
lib.stdheader.10.1 < header1
lib.stdheader.10.3 < renderedHeader

# IF NESTED IN A TV CONTAINER -> no "section" wrapper class
tt_content.stdWrap.innerWrap.cObject.default.15.if < onlyIfNotNested
tt_content.stdWrap.innerWrap.cObject.default.16 < tt_content.stdWrap.innerWrap.cObject.default.15
tt_content.stdWrap.innerWrap.cObject.default.15.if.negate = 0
tt_content.stdWrap.innerWrap.cObject.default.16.value = csc-default section Header-CType-{field:CType} TV-TO-{field:tx_templavoila_to} min-{field:tx_ewcontent_minimized}
tt_content.stdWrap.innerWrap.cObject.default.16.insertData = 1

# "section Content" Wrapper ( only if not nested in TV)
sectionTags = TEXT
sectionTags.value = <div class="sectionContentWrap"><div class="sectionContent">
sectionEnd = TEXT
sectionEnd.value = </div></div>
sectionTags.if < onlyIfNotNested
sectionEnd.if < onlyIfNotNested

# WRAP ALL THOSE ELEMENTS
tt_content.list.15 < sectionTags
tt_content.list.25 < sectionEnd
tt_content.text.15 < sectionTags
tt_content.text.25 < sectionEnd
tt_content.image.15 < sectionTags
tt_content.image.25 < sectionEnd
tt_content.textpic.15 < sectionTags
tt_content.textpic.25 < sectionEnd
tt_content.bullets.15 < sectionTags
tt_content.bullets.25 < sectionEnd
tt_content.table.15 < sectionTags
tt_content.table.25 < sectionEnd
tt_content.templavoila_pi1.15 < sectionTags
tt_content.templavoila_pi1.25 < sectionEnd
#tt_content.ew_calendar_pi1.15 < sectionTags
#tt_content.ew_calendar_pi1.25 < sectionEnd


# flash content shall be hidden behind other elements
plugin.tx_mediacenter_pi1.wmode = transparent
plugin.tx_mediacenter_pi1.flashvars.usefullscreen >
plugin.tx_mediacenter_pi1.flashvars.fullscreen = false
plugin.tx_mediacenter_pi1.flashvars.controlbar_DOT_idlehide = true
plugin.tx_mediacenter_pi1.flashvars.controlbar_DOT_margin=40
plugin.tx_mediacenter_pi1.flashvars.screencolor = #000000
