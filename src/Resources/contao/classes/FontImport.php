<?php

/*
 * This file is part of a Contao-Webfonts extention.
 *
 * @license LGPL-3.0-or-later
 */

namespace SPoT\WebfontGeneratorBundle;

class FontImport extends \Backend
{
    /**
     * Import the database class and set default template
     */
    public function __construct()
    {
        parent::__construct();
        $this->strTemplate = 'be_font_import';
        $this->import('Database');
    }

    /**
     * Either show the upload form or handle the data
     */
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
                if (is_dir(\System::getContainer()->getParameter('kernel.project_dir').'/'.$strCssFile)) {
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
                $fontName = basename($strCssFile);
                $strFile = $objFile->getContent();
                $strFile = str_replace("\r", '', $strFile);
                $fontFaces = $this->parseFontFaces($strFile);
                $fontData = array();

                foreach ($fontFaces as $font) {
                    $fontData[] = array(
                        'weight'  => $this->parseRule($font, 'font\-weight'),
                        'style'   => $this->parseRule($font, 'font\-style'),
                        'stretch' => $this->parseRule($font, 'font\-strech'),
                        'src'     => $this->parseFontSources($font),
                    );
                }

                if (isset($fontFaces[0])) {
                    $fontName = $this->parseRule($fontFaces[0], 'font\-family');
                    $this->Database->prepare('UPDATE `tl_fonts_faces` SET `name` = ? WHERE `id` = ?')->execute($fontName, $parentRecord->insertId);
                }

                // Create a parent record to bind new fonts to
                $parentId = false;
                $parentRecord = $this->Database->prepare('SELECT `id` FROM `tl_fonts_faces` WHERE `name` = ? LIMIT 1')->execute($fontName);
                if (!$parentRecord->id) {
                    $parentRecord = $this->Database->prepare('INSERT INTO `tl_fonts_faces`(`tstamp`, `name`) VALUES (?,?)')->execute(time(), $fontName);
                    $parentId = $parentRecord->insertId;
                } else {
                    $parentId = $parentRecord->id;
                    // Delete meta if set so
                    if (\Input::post('ctl_overwrite_font') === 'on') {
                        $this->Database->prepare('DELETE FROM `tl_fonts` WHERE `pid` = ?')->execute($parentId);
                    }
                }

                if (!$parentId) {
                    \Message::addError($GLOBALS['TL_LANG']['tl_fonts_faces']['parent_record_error']);
                    continue;
                }

                foreach ($fontData as $font) {
                    if (!array_key_exists('src', $font)) {
                        continue;
                    }

                    $query = 'INSERT INTO tl_fonts %s';
                    $arrParams = array(
                        'pid'    => $parentId,
                        'tstamp' => time(),
                    );

                    if ($font['weight']) {
                        $arrParams['weight'] = $font['weight'];
                    } else {
                        $arrParams['weight'] = 'normal';
                    }
                    if ($font['style']) {
                        $arrParams['style'] = $font['style'];
                    } else {
                        $arrParams['style'] = 'normal';
                    }
                    if ($font['stretch']) {
                        $arrParams['stretch'] = $font['stretch'];
                    } else {
                        $arrParams['stretch'] = 'normal';
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

                    $result = $this->Database->prepare($query)->set($arrParams)->execute();
                }

                \Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_fonts_faces']['font_created'], $fontName));
            }

            \System::setCookie('BE_PAGE_OFFSET', 0, 0);
            $this->redirect(str_replace('&key=import', '', \Environment::get('request')));
        }

        $template = new \BackendTemplate($this->strTemplate);
        $template->uploader = $objUploader;

        return \Message::generate().$template->parse();
    }

    /**
     * Parse @font-family rules from a CSS file
     *
     * @param string $strCSS
     * @return array
     */
    private function parseFontFaces($strCSS)
    {
        $regEx = '/@font\-face\s*{[^}]+}/m';
        preg_match_all($regEx, $strCSS, $fontFaces, PREG_PATTERN_ORDER);
        if (is_array($fontFaces) and count($fontFaces[0])) {
            return $fontFaces[0];
        }
    }

    /**
     * Parse a CSS rule using regex
     *
     * @param string $strCSS
     * @param string $rule
     * @param string $default
     * @return string
     */
    private function parseRule($strCSS, $rule, $default = '')
    {
        $regEx = sprintf('/%s:[\'"\s]*([^;\'"]+)[\'"]*/m', $rule);
        preg_match($regEx, $strCSS, $fontName);
        if (is_array($fontName) && isset($fontName[1])) {
            return $fontName[1];
        }

        return $default;
    }

    /**
     * Parse font source rules using regex
     *
     * @param string $strCSS
     * @return array
     */
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
