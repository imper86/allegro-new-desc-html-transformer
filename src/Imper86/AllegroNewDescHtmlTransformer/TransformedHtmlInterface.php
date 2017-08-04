<?php
/**
 * Copyright: IMPER.INFO Adrian Szuszkiewicz
 * Date: 04.08.17
 * Time: 09:50
 */

namespace Imper86\AllegroNewDescHtmlTransformer;


use PHPHtmlParser\Dom;

interface TransformedHtmlInterface
{
    public function getOriginalHtmlString(): string;

    public function getOriginalDom(): Dom;

    public function getTransformedHtmlString(): string;

    public function getTransformedDom(): Dom;
}