<?php

$GLOBALS['BE_MOD']['design']['fonts'] = array('tables' => array('tl_fonts_faces', 'tl_fonts'));
$GLOBALS['TL_HOOKS']['generatePage'][] = array('SPoT\\WebfontGeneratorBundle\\FontFaces', 'generatePageHook');
