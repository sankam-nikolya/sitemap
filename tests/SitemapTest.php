<?php
namespace samdark\sitemap\tests;

use samdark\sitemap\Sitemap;

class SitemapTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingFile()
    {
        $fileName = __DIR__ . '/sitemap.xml';
        $sitemap = new Sitemap($fileName);
        $sitemap->addItem('http://example.com/mylink1');
        $sitemap->addItem('http://example.com/mylink2', time());
        $sitemap->addItem('http://example.com/mylink3', time(), Sitemap::HOURLY);
        $sitemap->addItem('http://example.com/mylink4', time(), Sitemap::DAILY, 0.3);
        $sitemap->write();

        $this->assertTrue(file_exists($fileName));
        unlink($fileName);
    }

    public function testMultipleFiles()
    {
        $sitemap = new Sitemap(__DIR__ . '/sitemap_multi.xml');
        $sitemap->setMaxUrls(2);

        for ($i = 0; $i < 20; $i++) {
            $sitemap->addItem('http://example.com/mylink' . $i, time());
        }
        $sitemap->write();

        $expectedFiles = [
            __DIR__ . '/' .'sitemap_multi.xml',
            __DIR__ . '/' .'sitemap_multi_2.xml',
            __DIR__ . '/' .'sitemap_multi_3.xml',
            __DIR__ . '/' .'sitemap_multi_4.xml',
            __DIR__ . '/' .'sitemap_multi_5.xml',
            __DIR__ . '/' .'sitemap_multi_6.xml',
            __DIR__ . '/' .'sitemap_multi_7.xml',
            __DIR__ . '/' .'sitemap_multi_8.xml',
            __DIR__ . '/' .'sitemap_multi_9.xml',
            __DIR__ . '/' .'sitemap_multi_10.xml',
        ];
        foreach ($expectedFiles as $expectedFile) {
            $this->assertTrue(file_exists($expectedFile), "$expectedFile does not exist!");
            unlink($expectedFile);
        }

        $urls = $sitemap->getSitemapUrls('http://example.com/');
        $this->assertEquals(10, count($urls), print_r($urls, true));
        $this->assertContains('http://example.com/sitemap_multi.xml', $urls);
        $this->assertContains('http://example.com/sitemap_multi_10.xml', $urls);
    }

    public function testFrequencyValidation()
    {
        $this->setExpectedException('InvalidArgumentException');

        $fileName = __DIR__ . '/sitemap.xml';
        $sitemap = new Sitemap($fileName);
        $sitemap->addItem('http://example.com/mylink1');
        $sitemap->addItem('http://example.com/mylink2', time(), 'invalid');

        unlink($fileName);
    }
}
