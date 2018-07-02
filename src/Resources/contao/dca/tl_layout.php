<?php

$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] .= ';{fonts_legend},fontfaces';

$GLOBALS['TL_DCA']['tl_layout']['fields']['fontfaces'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['fontfaces'],
    'exclude'                 => true,
    'inputType'               => 'checkboxWizard',
    'foreignKey'              => 'tl_fonts_faces.name',
    'options_callback'        => array('SPoT\\WebfontGeneratorBundle\\FontFaces', 'getFontFaces'),
    'eval'                    => array('multiple' => true),
    'sql'                     => "blob NULL",
    'relation'                => array('type' => 'hasMany', 'load' => 'lazy'),
);