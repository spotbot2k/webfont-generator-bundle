<?php

/*
 * This file is part of a Contao-Webfonts extention.
 *
 * @license LGPL-3.0-or-later
 */

namespace SPoT\WebfontGeneratorBundle;

use Contao\Backend;
use Contao\Image;
use Contao\Input as Input;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\StringUtil;

use Symfony\Component\VarDumper\VarDumper;

class FontFaces extends Backend
{
    /**
     * Import back end user and filesystem controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
        $this->import('Files');
    }

    /**
     * Regenerate CSS file, depending on curent object
     * @param DataContainer $dc
     */
    public function updateFontFaces($dc)
    {
        switch ($dc->table) {
            case 'tl_fonts':
                $this->saveFontFaces(array($dc->activeRecord->pid));
            break;
            case 'tl_fonts_faces':
                $this->saveFontFaces(array($dc->activeRecord->id));
            break;
        }
    }

    /**
     * Delete file while purging the font out of the database
     *
     * @param DataContainer $dc
     */
    public function deleteFontFace($dc)
    {
        $fontName = $dc->activeRecord->name;
        if (!$fontName) {
            return;
        }
        $fontPath = $this->generateFilePath($fontName);
        if (file_exists("web/".$fontPath) && $this->Files->is_writeable($fontPath)) {
            $this->Files->delete("web/".$fontPath);
        }
    }

    /**
     * Collect data from the database and generate a new CSS file
     * @param  mixed $value
     * @return mixed
     */
    public function saveFontFaces($value)
    {
        $array = StringUtil::deserialize($value);
        $fontCss = '';
        $usageCss = '';

        if (empty($array)) {
            return;
        }

        // Iterate selected fonts
        foreach ($array as $fontId) {
            $fontFace = $this->Database->prepare('SELECT `name`, `fallback`, `forceDownload` FROM `tl_fonts_faces` WHERE `id` = ? LIMIT 1')->execute($fontId);
            $fontPath = $this->generateFilePath($fontFace->name);
            if (file_exists("web/".$fontPath)) {
                if (!$this->Files->is_writeable($fontPath)) {
                    return;
                }
                $this->Files->delete("web/".$fontPath);
            }

            if ($fontFace->numRows && $fontFace->name) {
                $fontFamily = sprintf("font-family:'%s'", $fontFace->name);
                $fontStyles = $this->Database->prepare('SELECT * FROM `tl_fonts` WHERE `pid` = ?')->execute($fontId);
                while ($fontStyles->next()) {
                    $src = array();
                    $properties = '';
                    if ($fontFace->forceDownload) {
                        $src[] = "local('☺︎')";
                    }
                    if ($fontStyles->src_ttf) {
                        $src[] = sprintf("url('%s') format('truetype')", $this->pathToUrl($fontStyles->src_ttf));
                    }
                    if ($fontStyles->src_otf) {
                        $src[] = sprintf("url('%s') format('opentype')", $this->pathToUrl($fontStyles->src_otf));
                    }
                    if ($fontStyles->src_woff) {
                        $src[] = sprintf("url('%s') format('woff')", $this->pathToUrl($fontStyles->src_woff));
                    }
                    if ($fontStyle['src_woff_two']) {
                        $src[] = sprintf("url('%s') format('woff2')", $this->pathToUrl($fontStyles->src_woff_two));
                    }
                    if ($fontStyles->src_svg) {
                        $src[] = sprintf("url('%s') format('svg')", $this->pathToUrl($fontStyles->src_svg));
                    }
                    if ($fontStyles->src_eot) {
                        $src[] = sprintf("url('%s') format('embedded-opentype')", $this->pathToUrl($fontStyles->src_eot));
                    }
                    if ($fontStyles->weight && $fontStyles->weight !== 'normal') {
                        $properties .= sprintf("font-weight:%s;", $fontStyles->weight);
                    }
                    if ($fontStyles->stretch && $fontStyles->stretch !== 'normal') {
                        $properties .= sprintf("font-stretch:%s;", $fontStyles->stretch);
                    }
                    if ($fontStyles->style && $fontStyles->style !== 'normal') {
                        $properties .= sprintf("font-style:%s;", $fontStyles->style);
                    }
                    if (!empty($src)) {
                        $fontCss .= sprintf("@font-face{font-family:'%s';src:%s;%s}", $fontFace->name, implode(',', $src), $properties);
                        if ($fontStyles->use_for != '') {
                            if ($fontFace->fallback) {
                                $fontFamily .= sprintf(", '%s'", $fontFace->fallback);
                                $fontFace->fallback = false;
                            }
                            $usageCss .= sprintf("%s{%s;%s}", $fontStyles->use_for, $fontFamily, $properties);
                        }
                    }
                }
            }

            // Save generated file
            try {
                $objFile = new \File("web/".$fontPath);
                $objFile->write('');
                $objFile->append($fontCss);
                $objFile->append($usageCss);
                $objFile->close();
            } catch (\Exception $e) {
                VarDumper::dump(sprintf('%s can not be created', "web/".$fontPath));
            }
        }

        return $value;
    }

    /**
     * Append previously generated file to the CSS queue
     *
     * @param PageModel   $page
     * @param LayoutModel $layout
     * @param PageRegular $pageRegular
     */
    public function generatePageHook(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (!empty($layout->fontfaces)) {
            $array = StringUtil::deserialize($layout->fontfaces);

            foreach ($array as $fontId) {
                $fontName = $this->getFontFaceName($fontId);
                $fontPath = $this->generateFilePath($fontName, true);
                if (file_exists($fontPath)) {
                    $GLOBALS['TL_CSS'][] = $fontPath.'||static';
                }
            }
        }
    }

    /**
     * Fetch the name of the font by its id
     *
     * @param int $fontId
     * @return string
     */
    public function getFontFaceName($fontId)
    {
        $fontFace = $this->Database->prepare('SELECT `name` FROM `tl_fonts_faces` WHERE `id` = ? LIMIT 1')->execute($fontId);
        if ($fontFace->name) {
            return ' '.$fontFace->name;
        }

        return '';
    }

    /**
     * Generate unique font path
     *
     * @param string $fontName
     * @return string
     */
    public function generateFilePath($fontName)
    {
        $slug = StringUtil::generateAlias($fontName);

        return sprintf("bundles/spotwebfontgenerator/css/webfont-%s.css", $slug);
    }

    /**
     * Determine if a string is a relative path and if so
     * convert it into a full URL
     *
     * @param string $strPath
     * @return string
     */
    private function pathToUrl($strPath)
    {
        if (filter_var($strPath, FILTER_VALIDATE_URL)) {
            return $strPath;
        }

        return \Environment::get('base').$strPath;
    }
}
