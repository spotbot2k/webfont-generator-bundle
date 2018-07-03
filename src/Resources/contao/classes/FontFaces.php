<?php

namespace SPoT\WebfontGeneratorBundle;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\Backend;
use Contao\Image;
use Contao\Input as Input;
use Contao\StringUtil;

class FontFaces extends Backend
{

    private $filePath;

    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
        $this->import('Files');
        $this->filePath = '/assets/css/webfont-generator.css';
    }

    public function listFontStyles($row)
    {
        return '<div class="tl_content_left">'.$row['name']."</div>\n";
    }

    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->User->canEditFieldsOf('tl_fonts_faces') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.\StringUtil::specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    public function updateFontFaces($dc)
    {
    }

    public function saveFontFaces($value)
    {
        $array = \StringUtil::deserialize($value);
        
		if (file_exists(TL_ROOT.$this->filePath) && !$this->Files->is_writeable($this->filePath)) {
            \Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['notWriteable'], $this->filePath));

			return;
        }
        $this->Files->delete($this->filePath);

        $fontCss = '';
        $usageCss = '';

        foreach ($array as $fontId) {
            $fontFace = $this->Database->prepare('SELECT name,fallback FROM tl_fonts_faces WHERE id = ? LIMIT 1')->execute($fontId);
            if ($fontFace->numRows && $fontFace->name) {
                $fontFamily = sprintf("font-family:'%s'", $fontFace->name);
                $fontStyles = $this->Database->prepare('SELECT * FROM tl_fonts WHERE pid = ?')->execute($fontId);
                while ($fontStyles->next()) {
                    $src = array();
                    $properties = '';
                    if ($fontStyles->src_ttf) {
                        $src[] = sprintf("url('%s%s') format('truetype')", \Environment::get('base'), $fontStyles->src_ttf);
                    }
                    if ($fontStyles->src_otf) {
                        $src[] = sprintf("url('%s%s') format('opentype')", \Environment::get('base'), $fontStyles->src_otf);
                    }
                    if ($fontStyles->src_woff) {
                        $src[] = sprintf("url('%s%s') format('woff')", \Environment::get('base'), $fontStyles->src_woff);
                    }
                    if ($fontStyle['src_woff_two']) {
                        $src[] = sprintf("url('%s%s') format('woff2')", \Environment::get('base'), $fontStyles->src_woff_two);
                    }
                    if ($fontStyles->src_svg) {
                        $src[] = sprintf("url('%s%s') format('svg')", \Environment::get('base'), $fontStyles->src_svg);
                    }
                    if ($fontStyles->src_eot) {
                        $src[] = sprintf("url('%s%s') format('embedded-opentype')", \Environment::get('base'), $fontStyles->src_eot);
                    }
                    if ($fontStyles->weight) {
                        $properties .= sprintf("font-weight:%s;", $fontStyles->weight);
                    }
                    if ($fontStyles->stretch) {
                        $properties .= sprintf("font-stretch:%s;", $fontStyles->stretch);
                    }
                    if ($fontStyles->style) {
                        $properties .= sprintf("font-style:%s;", $fontStyles->style);
                    }
                    if (!empty($src)) {
                        $fontCss .= sprintf("@font-face{font-family:'%s';src:%s;%s}", $fontFace->name, implode(',', $src), $properties);
                        if ($fontStyles->use_for != '') {
                            if ($fontFace->fallback) {
                                $fontFamily .= sprintf(", '%s'", $fontFace->fallback);
                            }
                            $usageCss .= sprintf("%s{%s;%s}", $fontStyles->use_for, $fontFamily, $properties);
                        }
                    }
                }
            }
        }
        
        $objFile = new \File($this->filePath);
        $objFile->write('');
        $objFile->append($fontCss);
        $objFile->append($usageCss);
        $objFile->close();

        return $value;
    }

    public function generatePageHook(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (file_exists(TL_ROOT.$this->filePath)) {
            $GLOBALS['TL_CSS'][] = $this->filePath.'||static';
        }
    }
}
