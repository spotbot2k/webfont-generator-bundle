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
            $arrUploaded = $objUploader->uploadTo('system/tmp');

            if (empty($arrUploaded)) {
                \Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
                $this->reload();
            }

            foreach ($arrUploaded as $strCssFile) {
                // Folders cannot be imported
                if (is_dir(TL_ROOT . '/' . $strCssFile)) {
                    \Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['importFolder'], basename($strCssFile)));
                    continue;
                }

                $objFile = new \File($strCssFile);
                // Check the file extension
                if ($objFile->extension != 'css') {
                    \Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
                    continue;
                }

                // Parse CSS to font
                $strFile = $objFile->getContent();
                $strFile = str_replace("\r", '', $strFile);

                \Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_fonts_faces']['css_imported'], $strCssFile.'.css'));
            }

            \System::setCookie('BE_PAGE_OFFSET', 0, 0);
            $this->redirect(str_replace('&key=import', '', \Environment::get('request')));
        }

        $template = new \BackendTemplate($this->strTemplate);
        $template->uploader = $objUploader;

        return \Message::generate().$template->parse();
    }

    private function parseFontFaces($strCSS)
    {
        $regEx = '/@font\-face\s*{[^}]+}/m';
        preg_match_all($regEx, $strCSS, $fontFaces, PREG_PATTERN_ORDER);
        if (is_array($fontFaces) and count($fontFaces[0])) {
            return $fontFaces[0];
        }
    }

    private function parseRule($strCSS, $rule, $default = '')
    {
        $regEx = sprintf('/%s:[\'"\s]*([^;\'"]+)[\'"]*/m', $rule);
        preg_match($regEx, $strCSS, $fontName);
        if (is_array($fontName) && isset($fontName[1])) {
            return $fontName[1];
        }

        return $default;
    }

    private function parseFontSources($strCSS)
    {
        $regEx = '/url\s*[(\'"]*([^\'"\s)]+)[\')\s]*format\s*[(\'"]*([^\'"]+)[\')\s]*[;,]/m';
        preg_match_all($regEx, $strCSS, $fontSources, PREG_SET_ORDER);
        $sources = array();
        foreach ($fontSources as $fontSource) {
            if (isset($fontSource[1], $fontSource[2])) {
                $sources[] = array($fontSource[2] => $fontSource[1]);
            }
        }

        return $sources;
    }
}