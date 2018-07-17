<?php

namespace SPoT\WebfontGeneratorBundle;

class FontImport extends \Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->strTemplate = 'be_font_import';
        $this->import('Database');
    }

    public function importFont()
    {
        if (\Input::get('key') != 'import') {
            return '';
        }
        $objUploader = new \FileUpload();

        if (\Input::post('FORM_SUBMIT') == 'tl_font_import') {
            \System::setCookie('BE_PAGE_OFFSET', 0, 0);
            $this->redirect(str_replace('&key=import', '', \Environment::get('request')));
        }

        $template = new \BackendTemplate($this->strTemplate);
        $template->uploader = $objUploader;

        return \Message::generate().$template->parse();
    }
}
