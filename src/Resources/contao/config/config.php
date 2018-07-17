<?php
/* Backend module */
$GLOBALS['BE_MOD']['design']['fonts'] = array(
    'tables' => array('tl_fonts_faces', 'tl_fonts'),
    'import' => array('SPoT\\WebfontGeneratorBundle\\FontImport', 'generate'),
);
/* Global Hook */
$GLOBALS['TL_HOOKS']['generatePage'][] = array('SPoT\\WebfontGeneratorBundle\\FontFaces', 'generatePageHook');
/* User permissions */
$GLOBALS['TL_PERMISSIONS'][] = 'webfont_generator';
/* Maintenance */
$GLOBALS['TL_PURGE']['custom']['webfont_generator'] = array(
    'callback' => array('SPoT\\WebfontGeneratorBundle\\FontMaintenance', 'rebuildFontCSS'),
);
