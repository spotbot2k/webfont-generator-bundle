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
        
        $objFile = new \File($this->filePath);
        $objFile->write("/* webfonts css */\n");
        $objFile->close();
        \Message::addInfo(sprintf('%s generated', $this->filePath));

        return $value;
    }

    public function generatePageHook(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (file_exists(TL_ROOT.$this->filePath)) {
            if (!\is_array($GLOBALS['TL_USER_CSS'])) {
                $GLOBALS['TL_USER_CSS'] = array();
            }

            $GLOBALS['TL_USER_CSS'][] = $this->filePath;
        }
    }

    public function fontLink(DataContainer $dc)
	{
		return ' <a href="contao/main.php?do=themes&amp;table=tl_font_faces&amp;id=' . $dc->activeRecord->pid . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="edit" onclick="Backend.openModalIframe({\'title\':\'edit\',\'url\':this.href});return false">' . \Image::getHtml('edit.svg') . '</a>';
	}
}