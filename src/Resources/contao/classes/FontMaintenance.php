<?php

namespace SPoT\WebfontGeneratorBundle;

use Contao\Backend;
use Symfony\Component\VarDumper\VarDumper;

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
        foreach (scandir(TL_ROOT.'bundles/spotwebfontgenerator/css/') as $file) {
            VarDumper::dump(sprintf('purging %s', $file));
            if (substr($file, -4) === '.php') {
                $file = new \File('bundles/spotwebfontgenerator/css/'.$file);
                $file->delete();
            }
        }
    }
}
