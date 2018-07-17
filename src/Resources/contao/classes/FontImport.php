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
            $fontIds = array();

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
                $fontFaces = $this->parseFontFaces($strFile);
                $fontData = array();

                foreach ($fontFaces as $font) {
                    $fontData[] = array(
                        'name'   => $this->parseRule($font, 'font\-family'),
                        'weight' => $this->parseRule($font, 'font\-weight'),
                        'style'  => $this->parseRule($font, 'font\-style'),
                        'src'    => $this->parseFontSources($font),
                    );
                }

                foreach ($fontData as $font) {
                    $query = 'INSERT INTO tl_fonts (%s) VALUES (?)';
                    $keys = array();
                    $values = array();

                    if ($font['name']) {
                        $keys[] = 'name';
                        $values[] = $font['name'];
                    }
                    if ($font['weight']) {
                        $keys[] = 'weight';
                        $values[] = $font['weight'];
                    }
                    if ($font['style']) {
                        $keys[] = 'style';
                        $values[] = $font['style'];
                    }
                    if ($font['src']['truetype']) {
                        $keys[] = 'src_ttf';
                        $values[] = $font['src']['truetype'];
                    }
                    if ($font['src']['opentype']) {
                        $keys[] = 'src_otf';
                        $values[] = $font['src']['opentype'];
                    }
                    if ($font['src']['woff']) {
                        $keys[] = 'src_woff';
                        $values[] = $font['src']['woff'];
                    }
                    if ($font['src']['woff2']) {
                        $keys[] = 'src_woff_two';
                        $values[] = $font['src']['woff2'];
                    }
                    if ($font['src']['svg']) {
                        $keys[] = 'src_svg';
                        $values[] = $font['src']['svg'];
                    }
                    if ($font['src']['embedded-opentype']) {
                        $keys[] = 'src_eot';
                        $values[] = $font['src']['embedded-opentype'];
                    }

                    $query = sprintf($query, implode(',', $keys));

                    $result = $this->Database->prepare($query)->execute(implode(",", $values));

                    if ($result->id) {
                        $fontIds[] = $result->id;
                    }
                }

                \Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_fonts_faces']['css_imported'], implode(',', $fontIds).'.css'));
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
                $sources[$fontSource[2]] = $fontSource[1];
            }
        }

        return $sources;
    }
}
