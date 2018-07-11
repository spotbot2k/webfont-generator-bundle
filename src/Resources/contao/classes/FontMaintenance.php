<?php

namespace SPoT\WebfontGeneratorBundle;

use Contao\Backend;
use Contao\System;

class FontMaintenance extends Backend
{
    public function rebuildFontCSS()
    {
        $this->import('Database');
        $this->purgeFiles();
        $this->import('SPoT\\WebfontGeneratorBundle\\FontFaces');
        $result = $this->Database->prepare("SELECT fontfaces FROM tl_layout WHERE fontfaces != ''")->execute();
        while ($result->next()) {
            $this->{'SPoT\\WebfontGeneratorBundle\\FontFaces'}->saveFontFaces($result->fontfaces);
        }
    }

    private function purgeFiles()
    {
        foreach (scandir(System::getContainer()->getParameter('kernel.project_dir').'/web/bundles/spotwebfontgenerator/css/') as $file) {
            if (substr($file, -4) === '.css') {
                $file = new \File('web/bundles/spotwebfontgenerator/css/'.$file);
                $file->delete();
            }
        }
    }
}
