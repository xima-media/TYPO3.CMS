<?php
namespace TYPO3\CMS\Frontend\ContentObject;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Interface for data processor classes with the FLUIDTEMPLATE content object
 */
interface FluidTemplateDataProcessorInterface {

	/**
	 * Process data passed to the FLUIDTEMPLATE content object
	 *
	 * @param array $data The data of the content element or page
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $configuration The configuration of FLUIDTEMPLATE
	 * @param \TYPO3\CMS\Fluid\View\StandaloneView $view The view
	 * @return void
	 */
	public function process(
		array &$data,
		array $processorConfiguration,
		array $configuration,
		StandaloneView $view
	);
}