<?php
/**
 * Copyright: IMPER.INFO Adrian Szuszkiewicz
 * Date: 07.08.17
 * Time: 11:38
 */

namespace Imper86\AllegroNewDescHtmlTransformer;


use Imper86\AllegroNewDescHtmlTransformer\Exception\IncorrectStringException;

class JsonTransformer implements JsonTransformerInterface
{
    /**
     * @var HtmlTransformerInterface
     */
    private $htmlTransformer;

    public function __construct()
    {
        $this->htmlTransformer = new HtmlTransformer();
    }

    public function transformJsonStruct(string $jsonString): string
    {
        $struct = json_decode($jsonString);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new IncorrectStringException("String {$jsonString} is not correct JSON struct");
        }

        foreach ($struct->sections as $sectionKey => $section) {
            foreach ($section->items as $itemKey => $item) {
                if ('TEXT' !== $item->type) {
                    continue;
                }

                $contentConverted = $this->htmlTransformer->transformHtmlString($item->content);
                $struct->sections[$sectionKey]->items[$itemKey]->content = $contentConverted->getTransformedHtmlString();
            }
        }

        return json_encode($struct);
    }
}