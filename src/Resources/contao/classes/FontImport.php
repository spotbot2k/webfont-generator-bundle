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
        $template = $this->prepareTemplate();
        $this->compile();

        return $template->parse();
    }

    protected function compile()
    {
    }

    private function prepareTemplate()
    {
        $template = new \BackendTemplate($this->strTemplate);
        $config = $this->framework->getAdapter(Config::class);
        $uploader = $this->framework->createInstance(FileUpload::class);

        $template->uploader = $uploader->generateMarkup();
        $template->fileMaxSize = $config->get('maxFileSize');
        $template->backBTTitle = $this->translator->trans('MSC.backBTTitle', [], 'contao_default');
        $template->backBT = $this->translator->trans('MSC.backBT', [], 'contao_default');
        $template->submitLabel = $this->translator->trans('MSC.apply', [], 'contao_default');
        $template->sourceLabel = $this->translator->trans('MSC.source.0', [], 'contao_default');
        $template->sourceLabelHelp = $this->translator->trans('MSC.source.1', [], 'contao_default');

        return $template;
    }
}
