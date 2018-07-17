<?php

namespace SPoT\WebfontGeneratorBundle;

class FontImport extends \BackendModule
{
    protected $strTemplate;

    public function __construct()
    {
        $this->strTemplate = 'be_font_import';
        $this->import('Database');
        parent::__construct();
    }

    public function generate()
    {
        $this->Template = new \BackendTemplate($this->strTemplate);
        $this->compile();

        return $this->Template->parse();
    }

    protected function compile()
    {
    }
}
