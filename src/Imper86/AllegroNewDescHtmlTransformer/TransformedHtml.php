<?php
/**
 * Copyright: IMPER.INFO Adrian Szuszkiewicz
 * Date: 04.08.17
 * Time: 09:49
 */

namespace Imper86\AllegroNewDescHtmlTransformer;


use PHPHtmlParser\Dom;

class TransformedHtml implements TransformedHtmlInterface
{

    /**
     * @var Dom
     */
    private $originalDom;
    /**
     * @var Dom
     */
    private $transformedDom;

    public function __construct(Dom $originalDom, Dom $transformedDom)
    {
        $this->originalDom = $originalDom;
        $this->transformedDom = $transformedDom;
    }

    public function getOriginalHtmlString(): string
    {
        return $this->originalDom->root->innerHtml();
    }

    public function getOriginalDom(): Dom
    {
        return $this->originalDom;
    }

    public function getTransformedHtmlString(): string
    {
        return $this->transformedDom->root->innerHtml();
    }

    public function getTransformedDom(): Dom
    {
        return $this->transformedDom;
    }


}