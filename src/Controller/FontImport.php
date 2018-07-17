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
}
