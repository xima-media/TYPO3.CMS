<?php
namespace TYPO3\CMS\Fluid\ViewHelpers\Format;

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

use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Use this view helper to crop the text between its opening and closing tags.
 *
 * = Examples =
 *
 * <code title="Defaults">
 * <f:format.crop maxCharacters="10">This is some very long text</f:format.crop>
 * </code>
 * <output>
 * This is...
 * </output>
 *
 * <code title="Custom suffix">
 * <f:format.crop maxCharacters="17" append="&nbsp;[more]">This is some very long text</f:format.crop>
 * </code>
 * <output>
 * This is some&nbsp;[more]
 * </output>
 *
 * <code title="Don't respect word boundaries">
 * <f:format.crop maxCharacters="10" respectWordBoundaries="false">This is some very long text</f:format.crop>
 * </code>
 * <output>
 * This is so...
 * </output>
 *
 * <code title="Don't respect HTML tags">
 * <f:format.crop maxCharacters="28" respectWordBoundaries="false" respectHtml="false">This is some text with <strong>HTML</strong> tags</f:format.crop>
 * </code>
 * <output>
 * This is some text with <stro
 * </output>
 *
 * <code title="Inline notation">
 * {someLongText -> f:format.crop(maxCharacters: 10)}
 * </code>
 * <output>
 * someLongText cropped after 10 characters...
 * (depending on the value of {someLongText})
 * </output>
 */
class CropViewHelper extends AbstractViewHelper
{
    /**
     * The output may contain HTML and can not be escaped
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController contains a backup of the current $GLOBALS['TSFE'] if used in BE mode
     */
    protected static $tsfeBackup;

    /**
     * Render the cropped text
     *
     * @param int $maxCharacters Place where to truncate the string
     * @param string $append What to append, if truncation happened
     * @param bool $respectWordBoundaries If TRUE and division is in the middle of a word, the remains of that word is removed.
     * @param bool $respectHtml If TRUE the cropped string will respect HTML tags and entities. Technically that means, that cropHTML() is called rather than crop()
     * @return string cropped text
     */
    public function render($maxCharacters, $append = '...', $respectWordBoundaries = true, $respectHtml = true)
    {
        return static::renderStatic(
            [
                'maxCharacters' => $maxCharacters,
                'append' => $append,
                'respectWordBoundaries' => $respectWordBoundaries,
                'respectHtml' => $respectHtml,
            ],
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $maxCharacters = $arguments['maxCharacters'];
        $append = $arguments['append'];
        $respectWordBoundaries = $arguments['respectWordBoundaries'];
        $respectHtml = $arguments['respectHtml'];

        $stringToTruncate = $renderChildrenClosure();
        if (TYPO3_MODE === 'BE') {
            self::simulateFrontendEnvironment();
        }

        // Even if we are in extbase/fluid context here, we're switching to a casual class of the framework here
        // that has no dependency injection and other stuff. Therefor it is ok to use makeInstance instead of
        // the ObjectManager here directly for additional performance
        // Additionally, it would be possible to retrieve the "current" content object via ConfigurationManager->getContentObject(),
        // but both crop() and cropHTML() are "nearly" static and do not depend on current content object settings, so
        // it is safe to use a fresh instance here directly.
        /** @var ContentObjectRenderer $contentObject */
        $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        if ($respectHtml) {
            $content = $contentObject->cropHTML($stringToTruncate, $maxCharacters . '|' . $append . '|' . $respectWordBoundaries);
        } else {
            $content = $contentObject->crop($stringToTruncate, $maxCharacters . '|' . $append . '|' . $respectWordBoundaries);
        }
        if (TYPO3_MODE === 'BE') {
            self::resetFrontendEnvironment();
        }
        return $content;
    }

    /**
     * Sets the global variable $GLOBALS['TSFE']->csConvObj in Backend mode
     * This somewhat hacky work around is currently needed because the crop() and cropHTML() functions of
     * ContentObjectRenderer rely on those variables to be set
     *
     * @return void
     */
    protected static function simulateFrontendEnvironment()
    {
        self::$tsfeBackup = isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE'] : null;
        $GLOBALS['TSFE'] = new \stdClass();
        // preparing csConvObj
        if (!is_object($GLOBALS['TSFE']->csConvObj)) {
            $GLOBALS['TSFE']->csConvObj = GeneralUtility::makeInstance(CharsetConverter::class);
        }
    }

    /**
     * Resets $GLOBALS['TSFE'] if it was previously changed by simulateFrontendEnvironment()
     *
     * @return void
     * @see simulateFrontendEnvironment()
     */
    protected static function resetFrontendEnvironment()
    {
        $GLOBALS['TSFE'] = self::$tsfeBackup;
    }
}
