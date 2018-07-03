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

    public function saveFontFaces($value, \DataContainer $dc)
    {
        $array = \StringUtil::deserialize($value);
        
        $this->Files->delete($this->filePath);
        
		if (file_exists(TL_ROOT.$this->filePath) && !$this->Files->is_writeable($this->filePath)) {
			\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['notWriteable'], $this->filePath));
			return;
        }

        $buffer = '';

        foreach ($array as $fontId) {
            $fontFace = $this->Database->prepare('SELECT name FROM tl_fonts_faces WHERE id = ? LIMIT 1')->execute($fontId)->fetchRow();
            if ($fontFace->numRows && $fontFace->name) {
                $fontStyles = $this->Database->prepare('SELECT * FROM tl_fonts WHERE pid = ?')->execute($fontId)->fetchAllAssoc();
                while ($fontStyle = $fontStyles->fetchRow()) {
                    $src = array();
                    if ($fontStyle['src_ttf']) {
                        $src[] = sprintf("url(%s) format('truetype')", $fontStyle['src_ttf']);
                    }
                    if ($fontStyle['src_otf']) {
                        $src[] = sprintf("url(%s) format('opentype')", $fontStyle['src_otf']);
                    }
                    if ($fontStyle['src_woff']) {
                        $src[] = sprintf("url(%s) format('woff')", $fontStyle['src_woff']);
                    }
                    if ($fontStyle['src_woff_two']) {
                        $src[] = sprintf("url(%s) format('woff2')", $fontStyle['src_woff_two']);
                    }
                    if ($fontStyle['src_svg']) {
                        $src[] = sprintf("url(%s) format('svg')", $fontStyle['src_svg']);
                    }
                    if ($fontStyle['src_eot']) {
                        $src[] = sprintf("url(%s) format('embedded-opentype')", $fontStyle['src_eot']);
                    }
                    if (!empty($src)) {
                        $buffer .= sprintf("@font-face {
                            font-family: '%s';
                            src: %s;
                        }", $fontFace['name'], implode(',', $src));
                    }
                }
            }
        }
        
        $objFile = new \File($this->filePath);
        $objFile->write('');
        $objFile->append($buffer);
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
