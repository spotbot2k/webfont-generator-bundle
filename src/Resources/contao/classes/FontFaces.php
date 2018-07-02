<?php

namespace SPoT\WebfontGeneratorBundle;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\Backend;
use Contao\DataContainer;
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

    public function saveFontFaces($value)
    {
        $array = \StringUtil::deserialize($value);
        
        if (empty($array) || !\is_array($array)) {
            $this->Files->delete($this->filePath);
            \Message::addInfo(sprintf('%s deleted', $this->filePath));

            return $value;
        }
        
		if (file_exists(TL_ROOT.$this->filePath) && !$this->Files->is_writeable($this->filePath)) {
			\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['notWriteable'], $this->filePath));
			return;
        }

        $buffer = '';

        foreach ($array as $fontId) {
            $fontFace = $this->Database->prepare('SELECT name FROM tl_fonts_faces WHERE id = ? LIMIT 1')->execute($fontId);
            if ($fontFace->numRows && $fontFace->name) {
                $fontStyles = $this->Database->prepare('SELECT * FROM tl_fonts WHERE pid = ?')->execute($fontId);
                while ($fontStyles->next()) {
                    $src = [];
                    if ($fontStyles->src_ttf) {
                        $src[] = sprintf("url(%s) format('truetype')", $fontStyles->src_ttf);
                    }
                    if ($fontStyles->src_ttf) {
                        $src[] = sprintf("url(%s) format('opentype')", $fontStyles->src_otf);
                    }
                    if ($fontStyles->src_woff) {
                        $src[] = sprintf("url(%s) format('woff')", $fontStyles->src_woff);
                    }
                    if ($fontStyles->src_woff_two) {
                        $src[] = sprintf("url(%s) format('woff2')", $fontStyles->src_woff_two);
                    }
                    if ($fontStyles->src_svg) {
                        $src[] = sprintf("url(%s) format('svg')", $fontStyles->src_svg);
                    }
                    if ($fontStyles->src_eot) {
                        $src[] = sprintf("url(%s) format('embedded-opentype')", $fontStyles->src_eot);
                    }
                    if (!empty($src)) {
                        $buffer .= sprintf("@font-face {
                            font-family: '%s';
                            src: %s;
                        }", $fontFace->name, implode(',', $src));
                    }
                }
            }
        }
        
        $objFile = new \File($this->filePath);
        $objFile->write($buffer);
        $objFile->close();
        \Message::addInfo(sprintf('%s generated', $this->filePath));

        return $value;
    }

    public function generatePageHook(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (file_exists(TL_ROOT.$this->filePath)) {
            $GLOBALS['TL_CSS'][] = $this->filePath.'||static';
        }
    }
}
