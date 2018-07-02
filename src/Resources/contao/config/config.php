<?php

array_insert($GLOBALS['BE_MOD']['design'], 4, array('fonts' => array('tables' => array('tl_fonts_faces', 'tl_fonts'))));
array_insert($GLOBALS['BE_MOD']['design']['themes']['tables'], 4, array('tl_fonts_faces', 'tl_fonts'));

$GLOBALS['TL_HOOKS']['generatePage'][] = array('SPoT\\WebfontGeneratorBundle\\FontFaces', 'generatePageHook');
