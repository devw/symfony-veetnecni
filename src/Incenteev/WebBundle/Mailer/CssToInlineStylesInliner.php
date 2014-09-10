<?php

namespace Incenteev\WebBundle\Mailer;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class CssToInlineStylesInliner implements StyleInlinerInterface
{
    private $charset;

    public function __construct($charset = 'UTF-8')
    {
        $this->charset = $charset;
    }

    /**
     * {@inheritDoc}
     */
    public function inlineStyle($html)
    {
        if (empty($html)) {
            return $html;
        }

        $inliner = new CSSToInlineStyles($html);

        $inliner->setCleanup(false);
        $inliner->setStripOriginalStyleTags(false);
        $inliner->setUseInlineStylesBlock(true);
        $inliner->setEncoding($this->charset);

        return $inliner->convert();
    }
}
