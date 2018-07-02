<?php

$GLOBALS['BE_MOD']['design'][] = array('fonts' => array('tables' => array('tl_fonts_faces', 'tl_fonts'));
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_fonts_faces';

$GLOBALS['TL_HOOKS']['generatePage'][] = array('SPoT\\WebfontGeneratorBundle\\FontFaces', 'generatePageHook');
