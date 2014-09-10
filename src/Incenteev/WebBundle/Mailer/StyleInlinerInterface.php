<?php

namespace Incenteev\WebBundle\Mailer;

interface StyleInlinerInterface
{
    /**
     * Inlines the style in the provided html.
     *
     * @param string $html
     *
     * @return string
     */
    public function inlineStyle($html);
}
