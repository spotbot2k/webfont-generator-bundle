<?php

namespace SPoT\WebfontGeneratorBundle;

use Contao\Backend;

class FontStyles extends \Backend
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