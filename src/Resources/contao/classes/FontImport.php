<?php

namespace SPoT\WebfontGeneratorBundle;

use Symfony\Component\VarDumper\VarDumper;

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

                // Create a parent record to bind new fonts to
                $parentRecord = $this->Database->prepare('INSERT INTO tl_fonts_faces(tstamp,name) VALUES (?,?)')->execute(time(), basename($strCssFile));
                if (!$parentRecord->id) {
                    \Message::addError(sprintf('Can not create the parent record'));
                    VarDumper::dump($parentRecord);
                    continue;
                }

                // Parse CSS to font
                $strFile = $objFile->getContent();
                $strFile = str_replace("\r", '', $strFile);
                $fontFaces = $this->parseFontFaces($strFile);
                $fontData = array();

                foreach ($fontFaces as $font) {
                    $fontData[] = array(
                        'weight' => $this->parseRule($font, 'font\-weight'),
                        'style'  => $this->parseRule($font, 'font\-style'),
                        'src'    => $this->parseFontSources($font),
                    );
                }

                foreach ($fontData as $font) {
                    $query = 'INSERT INTO tl_fonts %s';
                    $arrParams = array(
                        'pid'    => $parentRecord->id,
                        'tstamp' => time(),
                    );

                    if ($font['weight']) {
                        $arrParams['weight'] = $font['weight'];
                    }
                    if ($font['style']) {
                        $arrParams['style'] = $font['style'];
                    }
                    if (!array_key_exists('src', $font)) {
                        continue;
                    }
                    if (array_key_exists('truetype', $font['src'])) {
                        $arrParams['src_ttf'] = $font['src']['truetype'];
                    }
                    if (array_key_exists('opentype', $font['src'])) {
                        $arrParams['src_otf'] = $font['src']['opentype'];
                    }
                    if (array_key_exists('woff', $font['src'])) {
                        $arrParams['src_woff'] = $font['src']['woff'];
                    }
                    if (array_key_exists('woff2', $font['src'])) {
                        $arrParams['src_woff_two'] = $font['src']['woff2'];
                    }
                    if (array_key_exists('svg', $font['src'])) {
                        $arrParams['src_svg'] = $font['src']['svg'];
                    }
                    if (array_key_exists('embedded-opentype', $font['src'])) {
                        $arrParams['src_eot'] = $font['src']['embedded-opentype'];
                    }

                    $result = $this->Database->prepare($query)->set($arrParams);
                    $result = $result->execute();

                    VarDumper::dump($arrParams);
                    VarDumper::dump($result);

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

    private function sanitizeArray($arrData)
    {
    }
}
