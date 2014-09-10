<?php

namespace Incenteev\WebBundle\Tests\Markdown;

use Incenteev\WebBundle\Markdown\GithubFlavoredMarkdownParser;

class GithubFlavoredMarkdownParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GithubFlavoredMarkdownParser
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = new GithubFlavoredMarkdownParser();
    }

    public function testFencedCodeBlock()
    {
        $md = <<<EOL
Foo
```
this is code
and code
```
Bar
EOL;
        $expected = <<<HTML
<p>Foo</p>

<pre><code>this is code
and code
</code></pre>

<p>Bar</p>

HTML;

        $this->assertEquals($expected, $this->parser->transformMarkdown($md));
    }

    /**
     * @dataProvider provideAutoLink
     */
    public function testAutoLink($expected, $markdown)
    {
        $this->assertEquals($expected, $this->parser->transformMarkdown($markdown));
    }

    public function provideAutoLink()
    {
        return array(
            array("<p>Foo bar http</p>\n", 'Foo bar http '),
            array("<p>Foo <a href=\"http://google.com\">http://google.com</a> Foo</p>\n", 'Foo http://google.com Foo'),
            array("<p><a href=\"http://google.com\">http://google.com</a> Foo</p>\n", 'http://google.com Foo'),
            array("<p>Foo <a href=\"http://google.com\">http://google.com</a></p>\n", 'Foo http://google.com'),
            array("<p><a href=\"http://google.com\">Foo</a> Foo</p>\n", '[Foo](http://google.com) Foo'),
        );
    }

    /**
     * @dataProvider provideNewlines
     */
    public function testNewlines($expected, $markdown)
    {
        $this->assertEquals($expected, $this->parser->transformMarkdown($markdown));
    }

    public function provideNewlines()
    {
        return array(
            array("<p>Foo<br />\nbar</p>\n", "Foo  \nbar"),
            array("<p>Foo<br />\nbar</p>\n", "Foo\nbar"),
            array("<p>Foo</p>\n\n<p>bar</p>\n", "Foo\n\nbar"),
        );
    }
}
