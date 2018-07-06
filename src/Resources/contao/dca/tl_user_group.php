<?php

$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] .= ';{webfont_legend},webfont_generator';

$GLOBALS['TL_DCA']['tl_user_group']['fields']['webfont_generator'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['webfont_generator'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'options'                 => array('create', 'delete'),
    'reference'               => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                    => array('multiple'=>true),
    'sql'                     => "blob NULL"
);
