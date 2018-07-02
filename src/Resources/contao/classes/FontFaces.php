<?php

namespace SPoT\WebfontGeneratorBundle;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\Backend;
use Contao\DataContainer;

class FontFaces extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function listFontStyles($row)
    {
        return '<div class="tl_content_left">'.$row['name']."</div>\n";
    }

    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->User->canEditFieldsOf('tl_fonts_faces') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    public function getFontFaces(DataContainer $dc)
    {
        $intPid = $dc->activeRecord->pid;
		if (Input::get('act') == 'overrideAll') {
			$intPid = Input::get('id');
		}
		$objFontFaces = $this->Database->prepare("SELECT id, name FROM tl_font_faces WHERE pid=?")->execute($intPid);
		if ($objFontFaces->numRows < 1) {
			return array();
		}
		$return = array();
		while ($objFontFaces->next()) {
			$return[$objFontFaces->id] = $objFontFaces->weight;
        }

        return array ('a' => 'b');

		return $return;
    }

    public function generatePageHook(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        // generate css and append it to the combiner
        /*
        if ($layout->fontfaces) {

        }
        */
    }

    public function fontLink(DataContainer $dc)
	{
		return ' <a href="contao/main.php?do=themes&amp;table=tl_font_faces&amp;id=' . $dc->activeRecord->pid . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="edit" onclick="Backend.openModalIframe({\'title\':\'edit\',\'url\':this.href});return false">' . Image::getHtml('edit.svg') . '</a>';
	}
}