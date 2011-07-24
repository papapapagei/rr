<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// Adding soft reference keys in tt_content configuration
	// DAM soft reference keys are inserted in front so that their tokens are inserted first
t3lib_div::loadTCA('tt_content');
t3lib_div::loadTCA('pages');

$TCA['tt_content']['columns']['header']['config']['softref'] = 'typolink_tag' . ($TCA['tt_content']['columns']['header']['config']['softref'] ? ',' . $TCA['tt_content']['columns']['header']['config']['softref'] : '');

$tempTables = array('pages', 'tt_content');
foreach ($tempTables as $table) {
	foreach ($TCA[$table]['columns'] as $column => $config) {
		if ($config['config']['softref']) {
			if (t3lib_div::inList($config['config']['softref'], 'typolink_tag')) {
				$TCA[$table]['columns'][$column]['config']['softref'] = 'mediatag,' . $TCA[$table]['columns'][$column]['config']['softref'];
			}
			if (t3lib_div::inList($config['config']['softref'], 'images')) {
				$TCA[$table]['columns'][$column]['config']['softref'] = 'media,' . $TCA[$table]['columns'][$column]['config']['softref'];
			}
		} else {
			if ($config['config']['type'] == 'text') {
				$TCA[$table]['columns'][$column]['config']['softref'] = 'media,mediatag,typolink_tag';
			}
		}
	}
}
unset($tempTables);

if(t3lib_extMgm::isLoaded('tt_news')) {
	t3lib_div::loadTCA('tt_news');
	$TCA['tt_news']['columns']['title']['config']['softref'] = 'mediatag' . ($TCA['tt_news']['columns']['title']['config']['softref'] ? ',' . $TCA['tt_news']['columns']['title']['config']['softref'] : '');
	$TCA['tt_news']['columns']['bodytext']['config']['softref'] = 'media,mediatag' . ($TCA['tt_news']['columns']['bodytext']['config']['softref'] ? ',' . $TCA['tt_news']['columns']['bodytext']['config']['softref'] : '');
}
?>