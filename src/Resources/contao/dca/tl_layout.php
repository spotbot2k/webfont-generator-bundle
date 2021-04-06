<?php

$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('combineScripts;', 'combineScripts;{fonts_legend},fontfaces;', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_layout']['fields']['fontfaces'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['fontfaces'],
    'exclude'                 => true,
    'inputType'               => 'checkboxWizard',
    'foreignKey'              => 'tl_fonts_faces.name',
    'eval'                    => array('multiple' => true),
    'sql'                     => "blob NULL",
    'save_callback'           => array(
        array('SPoT\\WebfontGeneratorBundle\\FontFaces', 'saveFontFaces'),
    ),
    'relation'                => array('type' => 'hasMany', 'load' => 'lazy'),
);
