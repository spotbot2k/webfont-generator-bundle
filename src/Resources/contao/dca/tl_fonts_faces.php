<?php

/*
 * This file is part of a Contao-Webfonts extention.
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_fonts_faces'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => false,
        'ctable'                      => array('tl_fonts'),
        'switchToEdit'                => true,
        'sql' => array
        (
            'keys' => array
            (
                'id'                  => 'primary',
            ),
        ),
        'onsubmit_callback' => array(
            array('SPoT\\WebfontGeneratorBundle\\FontFaces', 'updateFontFaces'),
        ),
    ),
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('name'),
            'panelLayout'             => 'sort,filter;search,limit',
            'child_record_callback'   => array('tl_fonts_faces', 'listFontVariants')
        ),
        'label' => array
        (
            'fields'                  => array('name'),
            'format'                  => '%s',
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_fonts_faces']['edit'],
                'href'                => 'table=tl_fonts',
                'icon'                => 'edit.gif',
            ),
            'editheader' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_fonts_faces']['editheader'],
                'href'                => 'table=tl_fonts_faces&amp;act=edit',
                'icon'                => 'header.gif',
                'button_callback'     => array('tl_fonts_faces', 'editHeader'),
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_fonts_faces']['copy'],
                'href'                => 'act=paste&amp;mode=copy',
                'icon'                => 'copy.gif',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_fonts_faces']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_fonts_faces']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif',
            ),
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default'                     => '{title_legend},name,fallback'
    ),
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts_faces']['name'],
            'inputType'               => 'text',
            'search'                  => true,
            'flag'                    => 1,
            'eval'                    => array(
                'mandatory'           => true,
                'unique'              => true,
                'rgxp'                => 'alnum',
                'maxlength'           => 256,
                'tl_class'            => 'w50',
                'doNotCopy'           => true,
            ),
            'sql'                     => "VARCHAR(256) NULL"
        ),
        'fallback' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts_faces']['fallback'],
            'inputType'               => 'text',
            'eval'                    => array('mandatory' => true, 'maxlength' => 256, 'tl_class' => 'w50'),
            'sql'                     => "VARCHAR(256) default ''"
        ),
    )
);

class tl_fonts_faces extends Backend
{
    /**
     * Import the back end user
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function listFontVariants($row)
    {
        return '<div class="tl_content_left">'.$row['name']."</div>\n";
    }

    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->User->canEditFieldsOf('tl_fonts_faces') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }
}
