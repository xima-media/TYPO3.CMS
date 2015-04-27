<?php
namespace TYPO3\CMS\Beuser\Controller;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Backend module user group administration controller
 *
 * @author Ingo Pfennigstorf <i.pfennigstorf@gmail.com>
 */
class BackendUserGroupController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository
	 * @inject
	 */
	protected $backendUserGroupRepository;

	/**
	 * Initialize actions
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	public function initializeAction() {
		// @TODO: Extbase backend modules relies on frontend TypoScript for view, persistence
		// and settings. Thus, we need a TypoScript root template, that then loads the
		// ext_typoscript_setup.txt file of this module. This is nasty, but can not be
		// circumvented until there is a better solution in extbase.
		// For now we throw an exception if no settings are detected.
		if (empty($this->settings)) {
			throw new \RuntimeException('No settings detected. This module can not work then. This usually happens if there is no frontend TypoScript template with root flag set. ' . 'Please create a frontend page with a TypoScript root template.', 1344375003);
		}
	}

	/**
	 * Displays all BackendUserGroups
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('returnUrl', rawurlencode(BackendUtility::getModuleUrl('system_BeuserTxBeuser', array(
			'tx_beuser_system_beusertxbeuser' => array(
				'action' => 'index',
				'controller' => 'BackendUserGroup'
			)
		))));
		$this->view->assign('backendUserGroups', $this->backendUserGroupRepository->findAll());
	}
}