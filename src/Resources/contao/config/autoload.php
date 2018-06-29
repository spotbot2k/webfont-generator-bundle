<?php

/**
 * countryselect Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2009-2016, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-countryselect
 */

ClassLoader::addNamespaces(array('SPoT\\WebfontsBundle'));

/**
 * Register the classes
 */
ClassLoader::addClasses(array('SPoT\\WebfontsBundle\\FontFaces' => 'system/modules/webfonts/classes/FontFaces.php'));
