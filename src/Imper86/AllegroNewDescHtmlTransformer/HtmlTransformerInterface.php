<?php
/**
 * Copyright: IMPER.INFO Adrian Szuszkiewicz
 * Date: 04.08.17
 * Time: 09:27
 */

namespace Imper86\AllegroNewDescHtmlTransformer;


interface HtmlTransformerInterface
{
    public function transformHtmlString(string $htmlString): TransformedHtmlInterface;
}