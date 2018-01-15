<?php
namespace TYPO3\CMS\Core\Tests\Unit\Resource\Rendering;

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

/**
 * Class VideoTagRendererTest
 */
class VideoTagRendererTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function getPriorityReturnsCorrectValue()
    {
        $VideoTagRenderer = new \TYPO3\CMS\Core\Resource\Rendering\VideoTagRenderer();

        $this->assertSame(1, $VideoTagRenderer->getPriority());
    }

    /**
     * @test
     */
    public function canRenderReturnsTrueOnCorrectFile()
    {
        $VideoTagRenderer = new \TYPO3\CMS\Core\Resource\Rendering\VideoTagRenderer();

        $fileResourceMock1 = $this->getMock(\TYPO3\CMS\Core\Resource\File::class, [], [], '', false);
        $fileResourceMock1->expects($this->any())->method('getMimeType')->will($this->returnValue('video/mp4'));
        $fileResourceMock2 = $this->getMock(\TYPO3\CMS\Core\Resource\File::class, [], [], '', false);
        $fileResourceMock2->expects($this->any())->method('getMimeType')->will($this->returnValue('video/webm'));
        $fileResourceMock3 = $this->getMock(\TYPO3\CMS\Core\Resource\File::class, [], [], '', false);
        $fileResourceMock3->expects($this->any())->method('getMimeType')->will($this->returnValue('video/ogg'));
        $fileResourceMock4 = $this->getMock(\TYPO3\CMS\Core\Resource\File::class, [], [], '', false);
        $fileResourceMock4->expects($this->any())->method('getMimeType')->will($this->returnValue('application/ogg'));

        $this->assertTrue($VideoTagRenderer->canRender($fileResourceMock1));
        $this->assertTrue($VideoTagRenderer->canRender($fileResourceMock2));
        $this->assertTrue($VideoTagRenderer->canRender($fileResourceMock3));
        $this->assertTrue($VideoTagRenderer->canRender($fileResourceMock4));
    }

    /**
     * @test
     */
    public function canRenderReturnsFalseOnCorrectFile()
    {
        $VideoTagRenderer = new \TYPO3\CMS\Core\Resource\Rendering\VideoTagRenderer();

        $fileResourceMock = $this->getMock(\TYPO3\CMS\Core\Resource\File::class, [], [], '', false);
        $fileResourceMock->expects($this->any())->method('getMimeType')->will($this->returnValue('audio/mpeg'));

        $this->assertFalse($VideoTagRenderer->canRender($fileResourceMock));
    }

    /**
     * Array of configurations
     */
    public function renderArgumentsDataProvider()
    {
        return [
            [
                '//:path/myVideoFile?foo=bar&baz=true',
                [],
                '<video width="300" height="200" controls><source src="//:path/myVideoFile?foo=bar&amp;baz=true" type="video/mp4"></video>',
            ],
            [
                '//:path/myVideoFile',
                ['loop' => 1],
                '<video width="300" height="200" controls loop><source src="//:path/myVideoFile" type="video/mp4"></video>',
            ],
            [
                '//:path/myVideoFile',
                ['autoplay' => 1],
                '<video width="300" height="200" controls autoplay><source src="//:path/myVideoFile" type="video/mp4"></video>',
            ],
            [
                '//:path/myVideoFile',
                ['controls' => 0, 'autoplay' => 1],
                '<video width="300" height="200" autoplay><source src="//:path/myVideoFile" type="video/mp4"></video>',
            ],
            [
                '//:path/myVideoFile',
                ['controls' => 1, 'controlsList' => 'nodownload'],
                '<video width="300" height="200" controls controlsList="nodownload"><source src="//:path/myVideoFile" type="video/mp4"></video>',
            ]
        ];
    }

    /**
     * @test
     * @dataProvider renderArgumentsDataProvider
     * @param string $url
     * @param array $arguments
     * @param string $expected
     */
    public function renderOutputIsCorrect($url, $arguments, $expected)
    {
        $VideoTagRenderer = new \TYPO3\CMS\Core\Resource\Rendering\VideoTagRenderer();

        $fileResourceMock = $this->getMock(\TYPO3\CMS\Core\Resource\File::class, [], [], '', false);
        $fileResourceMock->expects($this->any())->method('getMimeType')->will($this->returnValue('video/mp4'));
        $fileResourceMock->expects($this->any())->method('getPublicUrl')->will($this->returnValue($url));

        $this->assertSame(
            $expected,
            $VideoTagRenderer->render($fileResourceMock, '300m', '200', $arguments)
        );
    }
}
