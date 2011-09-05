includeLibs.calendar = typo3conf/ext/ew_calendar/pi1/class.tx_ewcalendar_pi1.php

mainMenu = COA

#The Menu itself
mainMenu.10 = HMENU
mainMenu.10 {
	entryLevel = 1
	special = directory
	special.value = 2
	1 = TMENU
	1 {
		wrap = <div id="menuTableWrap"><table class="menu-level-1"><tr>|</tr></table></div>
		NO = 1
		NO {
			allWrap = <td class="arrow-left-no"></td><td class="menu-level-1">|</td><td class="arrow-right-no"></td>
			stdWrap.cObject = IMAGE
			stdWrap.cObject.altText.field = nav_title // title
			stdWrap.cObject.file = GIFBUILDER
			stdWrap.cObject.file {
				XY = [10.w]+12,27
				backColor = #363636
				10 = TEXT
				10 {
					text.field = nav_title // title
					text.case = upper
					fontSize = 14
					fontColor = #C9C9C9
					offset = 7,18
					fontFile = fileadmin/templates/fonts/Neutra2TextDemi-kerning90-custom.pfb
					#niceText = 1
				}
			}
		}
		CUR < .NO
		CUR = 1
		CUR {
			allWrap = <td class="arrow-left-cur"></td><td class="menu-level-1 menu-cur">|</td><td class="arrow-right-cur"></td>
			#10.fontFile = fileadmin/templates/fonts/NeutraTextBold.ttf
			stdWrap.cObject.file.backColor = #0d0d0d
		}
	}
}
# Home: show ticker in menu
[globalVar = TSFE:id=99999]
# CLEAR CACHE ONCE A DAY IN ORDER TO UPDATE THE TICKER
config.cache_clearAtMidnight = 1
mainMenu.10.1.NO {
	# MAKE THE BORDERS
	allWrap = 
	allWrap.cObject = CASE
	allWrap.cObject {
		key.field = uid
		default = TEXT
		default.value = <td class="arrow-left-no"></td><td class="menu-level-1">|</td><td class="arrow-right-no"></td>
		
		10 = TEXT
		10.value = <td class="arrow-left"></td><td class="menu-level-1 menu-cal"><div style="position:relative">|</div></td><td class="arrow-right"></td>
	}
	# THE TICKER GRAPHICS
	after.cObject = COA
	after.cObject.if.equals.field = uid
	after.cObject.if.value = 10
	after.cObject.10 = IMAGE
	after.cObject.10 {
		params = style="position: absolute; left: -21px; top: 20px; z-index:5;" class="tickerBackground"
		file = fileadmin/templates/images/bg_ticker.png
	}
	# THE DATE
	after.cObject.20 = IMAGE
	after.cObject.20.params = class="tickerTitle"
	after.cObject.20.file = GIFBUILDER
	after.cObject.20.file{
		XY = [10.w],[10.h]+8
		backColor = #5c5c5c
		10 = TEXT
		10 {
			text.data = lll:EXT:ew_calendar/pi1/locallang.xml:teaserTextBlink
			text.case = upper
			fontSize = 13
			fontColor = #ffffff
			offset = 0,13
			fontFile = fileadmin/templates/fonts/Neutra2Text-Demi.pfb
		}
	}
	after.cObject.20.altText < .after.cObject.20.file.10.text
	# link the whole item
	after.cObject.20.stdWrap.typolink.parameter = 10 _self tickerTitleLink
	# THE TEXT
	after.cObject.30 = TEXT
	after.cObject.30 {
		dataWrap = <table class="tickerText"><tr><td><div class="blinkWrapper">|</div></td></tr></table>
		cObject = USER
		cObject.userFunc = user_tx_ewcalendar_pi1->getNextEvent
		cObject.type = title
		preCObject = TEXT
		preCObject.value = <span class="tickerEvent">
		postCObject = TEXT
		postCObject.cObject = USER
		postCObject.cObject.userFunc = user_tx_ewcalendar_pi1->getNextEvent
		postCObject.cObject.type = date
		postCObject.wrap = </span><span class="tickerDate">|</span>
		# link the whole item
		typolink.parameter = 10
	}
}
[global]


# The Min/Max Button
mainMenu.20 = HMENU
mainMenu.20 {
	special = language
	special.value = 0,1
	1 = GMENU
	1.wrap = <div id="langMenu">|</div>
	1.NO {
		XY = [10.w],[10.h]
		10 = IMAGE
		10.file = fileadmin/templates/images/button_de.png || fileadmin/templates/images/button_en.png
	}
	1.ACT < .1.NO
	1.ACT = 1
	1.ACT.wrap = <div style="display:none;">|</div>
	# NO + Übersetzung nicht vorhanden
	1.USERDEF1 < .1.NO
	# ACT + Übersetzung nicht vorhanden
	1.USERDEF2 < .1.ACT
}
# = <div id="langMenu"></div>
mainMenu.30 = TEXT
mainMenu.30.value = <div id="minMax"></div>

