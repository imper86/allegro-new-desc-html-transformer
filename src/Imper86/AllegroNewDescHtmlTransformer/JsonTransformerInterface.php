<?php
/**
 * Copyright: IMPER.INFO Adrian Szuszkiewicz
 * Date: 07.08.17
 * Time: 11:38
 */

namespace Imper86\AllegroNewDescHtmlTransformer;


interface JsonTransformerInterface
{
    /**
     * This method transforms all HTML fields and returns JSON struct
     *
     * @param string $jsonString
     * @return string
     */
    public function transformJsonStruct(string $jsonString): string;
}