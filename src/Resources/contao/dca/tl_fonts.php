<?php

/*
 * This file is part of a Contao-Webfonts extention.
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_fonts'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ptable'           => 'tl_fonts_faces',
        'sql' => array
        (
            'keys' => array
            (
                'id'  => 'primary',
                'pid' => 'index',
            )
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
            'mode'                    => 4,
            'fields'                  => array('weight', 'style'),
            'headerFields'            => array('tl_fonts_faces.name'),
            'panelLayout'             => 'sort,filter,search,limit',
            'disableGrouping'         => true,
        ),
        'label' => array
        (
            'fields'                  => array('weight'),
            'format'                  => '%s',
            'label_callback'          => array('tl_fonts', 'renderStylelabel'),
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ),
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_style_sheet']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif',
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_style_sheet']['copy'],
                'href'                => 'act=paste&amp;mode=copy',
                'icon'                => 'copy.gif',
            ),
            'cut' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_style_sheet']['cut'],
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset()"',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_style_sheet']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_style_sheet']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif',
            ),
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default'                     => '{source_legend},src_ttf,src_otf,src_woff,src_woff_two,src_svg,src_eot;{custom_legend},weight,stretch,style,use_for',
    ),
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment",
        ),
        'pid' => array
        (
            'foreignKey'              => 'tl_fonts_faces.name',
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => array('type'=>'belongsTo', 'load'=>'eager'),
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ),
        'src_ttf'                     => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['src_ttf'],
            'inputType'               => 'text',
            'eval'                    => array(
                'dcaPicker'           => array(
                    'do'              => 'files',
                    'context'         => 'file',
                    'icon'            => 'pickfile.svg',
                    'fieldType'       => 'radio',
                    'filesOnly'       => true,
                    'extensions'      => 'ttf',
                ),
                'tl_class'            => 'w50 wizard',
            ),
            'sql'                     => "VARCHAR(255) NOT NULL default ''",
        ),
        'src_otf'                     => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['src_otf'],
            'inputType'               => 'text',
            'eval'                    => array(
                'dcaPicker'           => array(
                    'do'              => 'files',
                    'context'         => 'file',
                    'icon'            => 'pickfile.svg',
                    'fieldType'       => 'radio',
                    'filesOnly'       => true,
                    'extensions'      => 'otf',
                ),
                'tl_class'            => 'w50 wizard'
            ),
            'sql'                     => "VARCHAR(255) NOT NULL default ''",
        ),
        'src_woff'                    => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['src_woff'],
            'inputType'               => 'text',
            'eval'                    => array(
                'dcaPicker'           => array(
                    'do'              => 'files',
                    'context'         => 'file',
                    'icon'            => 'pickfile.svg',
                    'fieldType'       => 'radio',
                    'filesOnly'       => true,
                    'extensions'      => 'woff',
                ),
                'tl_class'            => 'w50 wizard',
            ),
            'sql'                     => "VARCHAR(255) NOT NULL default ''",
        ),
        'src_woff_two'                => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['src_woff_two'],
            'inputType'               => 'text',
            'eval'                    => array(
                'dcaPicker'           => array(
                    'do'              => 'files',
                    'context'         => 'file',
                    'icon'            => 'pickfile.svg',
                    'fieldType'       => 'radio',
                    'filesOnly'       => true,
                    'extensions'      => 'woff2',
                ),
                'tl_class'            => 'w50 wizard',
            ),
            'sql'                     => "varchar(255) NOT NULL default ''",
        ),
        'src_svg'                     => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['src_svg'],
            'inputType'               => 'text',
            'eval'                    => array(
                'dcaPicker'           => array(
                    'do'              => 'files',
                    'context'         => 'file',
                    'icon'            => 'pickfile.svg',
                    'fieldType'       => 'radio',
                    'filesOnly'       => true,
                    'extensions'      => 'svg',
                ),
                'tl_class'            => 'w50 wizard',
            ),
            'sql'                     => "VARCHAR(255) NOT NULL default ''",
        ),
        'src_eot'                     => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['src_eot'],
            'inputType'               => 'text',
            'eval'                    => array(
                'dcaPicker'           => array(
                    'do'              => 'files',
                    'context'         => 'file',
                    'icon'            => 'pickfile.svg',
                    'fieldType'       => 'radio',
                    'filesOnly'       => true,
                    'extensions'      => 'eot',
                ),
                'tl_class'            => 'w50 wizard',
            ),
            'sql'                     => "VARCHAR(255) NOT NULL default ''",
        ),
        'stretch'                     => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['stretch'],
            'inputType'               => 'select',
            'sorting'                 => true,
            'flag'                    => 1,
            'options'                 => array('normal', 'condensed', 'ultra-condensed', 'extra-condensed', 'semi-condensed', 'expanded', 'semi-expanded', 'extra-expanded', 'ultra-expanded'),
            'eval'                    => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
            'sql'                     => "VARCHAR(16) NOT NULL default ''",
        ),
        'style'                       => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['style'],
            'inputType'               => 'select',
            'sorting'                 => true,
            'flag'                    => 1,
            'options'                 => array('normal', 'italic', 'oblique'),
            'eval'                    => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
            'sql'                     => "VARCHAR(8) NOT NULL default ''",
        ),
        'weight'                      => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['weight'],
            'inputType'               => 'select',
            'sorting'                 => true,
            'flag'                    => 1,
            'options'                 => array(
                'normal' => 'Default',
                'bold'   => 'Bold',
                '100'    => 'Ultra-Light 100',
                '200'    => 'Extra-Light 200',
                '300'    => 'Light 300',
                '400'    => 'Regular 400',
                '500'    => 'Semi-Bold 500',
                '600'    => 'Semi-Bold 600',
                '700'    => 'Bold 700',
                '800'    => 'Bold 800',
                '900'    => 'Black 900',
            ),
            'eval'                    => array('multiple' => false, 'mandatory' => true, 'tl_class' => 'w50'),
            'sql'                     => "VARCHAR(8) NOT NULL default ''",
        ),
        'use_for'                     => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_fonts']['use_for'],
            'inputType'               => 'text',
            'exclude'                 => true,
            'eval'                    => array('tl_class' => 'w50'),
            'sql'                     => "VARCHAR(255) NOT NULL default ''",
        ),
    )
);

class tl_fonts extends \Backend
{
    public function renderStyleLabel($row, $label)
    {
        if ($row['stretch'] !== 'normal') {
            $label .= ',&nbsp;'.$row['stretch'];
        }

        $format = array();
        if ($row['src_ttf']) $format[] = 'ttf';
        if ($row['src_otf']) $format[] = 'otf';
        if ($row['src_woff']) $format[] = 'woff';
        if ($row['src_woff_two']) $format[] = 'woff2';
        if ($row['src_svg']) $format[] = 'svg';
        if ($row['src_eot']) $format[] = 'eot';

        if ($format) {
            $format = implode(', ', $format);
            $label = sprintf('<span style="color:#b3b3b3;padding-left:3px">[%s]</span>%s %s', $format, $this->getFontFaceName($row['pid']), $label);
        }

        if ($row['style'] !== 'normal') {
            $label = sprintf('<i>%s, %s</i>', $label, $row['style']);
        }

        return $label;
    }

    private function getFontFaceName($fontId)
    {
        $fontFace = $this->Database->prepare('SELECT name FROM tl_fonts_faces WHERE id = ? LIMIT 1')->execute($fontId);
        if ($fontFace->name) {
            return ' '.$fontFace->name;
        }

        return '';
    }
}
