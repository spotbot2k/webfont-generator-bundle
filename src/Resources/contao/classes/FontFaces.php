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
        
		if (file_exists($this->filePath) && !$this->Files->is_writeable($this->filePath)) {
			\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['notWriteable'], $this->filePath));
			return;
        }

        $buffer = '';
        foreach ($array as $fontFace) {
            $fontName = $this->Database->prepare('SELECT name FROM tl_fonts_faces WHERE id = ? LIMIT 1')->execute($fontFace);
            if ($fontName->name) {
                $buffer .= sprintf("/* %s */", $fontName);
            }
        }

        /*
        @font-face {
            font-family: 'Source Sans Pro';
            font-style: normal;
            font-weight: 300;
            src: url(https://fonts.gstatic.com/s/sourcesanspro/v11/6xKydSBYKcSV-LCoeQqfX1RYOo3ik4zwkxdu3cOWxy40.woff2) format('woff2');
          }
          */
        
        $objFile = new \File($this->filePath);
        $objFile->write($buffer."\n");
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