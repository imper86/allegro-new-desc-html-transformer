<?php
/**
 * Copyright: IMPER.INFO Adrian Szuszkiewicz
 * Date: 04.08.17
 * Time: 09:27
 */

namespace Imper86\AllegroNewDescHtmlTransformer;


use PHPHtmlParser\Dom;

interface HtmlTransformerInterface
{
    /**
     * Returns original Dom transformed from string given in constructor
     * @return Dom
     */
    public function getOriginalDom(): Dom;

    /**
     * Returns transformed Dom object
     * @return Dom
     */
    public function getTransformedDom(): Dom;

    /**
     * Returns original HTML string given in constructor
     * @return string
     */
    public function getOriginalString(): string;

    /**
     * Returns transformed HTML string
     * @return string
     */
    public function getTransformedString(): string;
}