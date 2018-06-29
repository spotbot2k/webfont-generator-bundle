<?php

namespace SPoT\WebfontGeneratorBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use SPoT\WebfontGeneratorBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

/**
 * Plugin for the Contao Manager.
 *
 * @author Andreas Schempp <https://github.com/aschempp>
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(WebfontGeneratorBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['fonts']),
        ];
    }
}
