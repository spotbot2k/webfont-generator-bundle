<?php

namespace SPoT\WebfontGeneratorBundle\Controller;

class FontImportController
{
    private $framework;
    private $connection;
    private $requestStack;
    private $translator;
    private $projectDir;

    public function __construct(ContaoFrameworkInterface $framework, Connection $connection, RequestStack $requestStack, TranslatorInterface $translator, string $projectDir)
    {
        $this->framework = $framework;
        $this->connection = $connection;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->projectDir = $projectDir;
    }

    public function importFromTemplate()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new InternalServerErrorException('No request object given.');
        }

        $this->framework->initialize();

        $uploader = $this->framework->createInstance(FileUpload::class);
        $template = $this->prepareTemplate($request, $uploader);

        if (null !== $submitLabel) {
            $template->submitLabel = $submitLabel;
        }

        return new Response($template->parse());
    }

    private function prepareTemplate(Request $request, FileUpload $uploader): BackendTemplate
    {
        $template = new BackendTemplate('be_font_import');

        $config = $this->framework->getAdapter(Config::class);

        $template->formId = $this->getFormId($request);
        $template->backUrl = $this->getBackUrl($request);
        $template->action = $request->getRequestUri();
        $template->fileMaxSize = $config->get('maxFileSize');
        $template->uploader = $uploader->generateMarkup();
        $template->submitLabel = $this->translator->trans('MSC.apply', [], 'contao_default');
        $template->backBT = $this->translator->trans('MSC.backBT', [], 'contao_default');
        $template->backBTTitle = $this->translator->trans('MSC.backBTTitle', [], 'contao_default');
        $template->separatorLabel = $this->translator->trans('MSC.separator.0', [], 'contao_default');
        $template->separatorHelp = $this->translator->trans('MSC.separator.1', [], 'contao_default');
        $template->sourceLabel = $this->translator->trans('MSC.source.0', [], 'contao_default');
        $template->sourceLabelHelp = $this->translator->trans('MSC.source.1', [], 'contao_default');

        return $template;
    }

    private function getFormId(Request $request): string
    {
        return 'tl_font_import_'.$request->query->get('key');
    }

    private function getBackUrl(Request $request): string
    {
        return str_replace('&key='.$request->query->get('key'), '', $request->getRequestUri());
    }
}
