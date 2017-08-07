<?php
/**
 * Copyright: IMPER.INFO Adrian Szuszkiewicz
 * Date: 04.08.17
 * Time: 09:25
 */

namespace Imper86\AllegroNewDescHtmlTransformer;


use PHPHtmlParser\Dom;

class HtmlTransformer implements HtmlTransformerInterface
{
    public function transformHtmlString(string $htmlString): TransformedHtmlInterface
    {
        $originalDom = new Dom();
        $originalDom->load($htmlString, [
            'whitespaceTextNode' => false,
        ]);

        $transformedDom = new Dom();
        $transformedDom->load('', [
            'whitespaceTextNode' => false,
        ]);

        /** @var Dom\HtmlNode[] $rootChildren */
        $rootChildren = $originalDom->root->getChildren();

        foreach ($rootChildren as $rootChild) {
            if ($rootChild->getTag()->name() === 'text') {
                if (!empty($rootChild->text())) {
                    $transformedDom->root->addChild(
                        $this->convertHtmlSpecialChars(
                            $this->createSimpleNodeWithText(new Dom\Tag('p'), $rootChild->text())
                        )
                    );
                }
            } elseif (in_array($rootChild->getTag()->name(), ['ul', 'ol'])) {
                $parsedListNode = $this->prepareListNode($rootChild);
                if (null === $parsedListNode) {
                    continue;
                }

                $transformedDom->root->addChild($this->convertHtmlSpecialChars($parsedListNode));
                continue;
            } elseif (in_array($rootChild->getTag()->name(), ['h1', 'h2'])) {
                $parsedHeaders = $this->prepareHeaderNode($rootChild);

                if (empty($parsedHeaders)) {
                    continue;
                }

                foreach ($parsedHeaders as $parsedHeader) {
                    $transformedDom->root->addChild($this->convertHtmlSpecialChars($parsedHeader));
                }
            } else {
                $parsedNodes = $this->transformToParagraph($rootChild);

                if (empty($parsedNodes)) {
                    continue;
                }

                foreach ($parsedNodes as $parsedNode) {
                    $transformedDom->root->addChild($this->convertHtmlSpecialChars($parsedNode));
                }
            }
        }

        $transformedHtmlObject = new TransformedHtml($originalDom, $transformedDom);

        return $transformedHtmlObject;
    }

    private function prepareListNode(Dom\HtmlNode $htmlNode): ?Dom\HtmlNode
    {
        if (!$htmlNode->hasChildren()) {
            return null;
        }

        /** @var Dom\HtmlNode[] $children */
        $children = $htmlNode->getChildren();
        $nodeToReturn = new Dom\HtmlNode($htmlNode->getTag()->name());

        foreach ($children as $child) {
            $parsedChild = $this->prepareListElementNode($child);
            if (empty($parsedChild)) {
                continue;
            }

            $nodeToReturn->addChild($parsedChild);
        }

        return $nodeToReturn;
    }

    private function prepareListElementNode(Dom\HtmlNode $liNode): ?Dom\HtmlNode
    {
        if ($liNode->getTag()->name() !== 'li' || empty($liNode->innerHtml())) {
            return null;
        }

        $liNodeToReturn = new Dom\HtmlNode('li');

        /** @var Dom\HtmlNode[] $children */
        $children = $liNode->getChildren();

        foreach ($children as $child) {
            if ($child->getTag()->name() === 'text') {
                $textNode = new Dom\TextNode(htmlspecialchars($child->text()));
                $liNodeToReturn->addChild($textNode);
            } elseif (in_array($child->getTag()->name(), ['b', 'strong'])) {
                $liNodeToReturn->addChild(
                    $this->createSimpleNodeWithText(
                        new Dom\Tag('b'),
                        $child->innerHtml()
                    )
                );
            } else {
                $liNodeToReturn->addChild($this->extractTextFromNode($child));
            }
        }

        return $liNodeToReturn;
    }

    /**
     * @param Dom\HtmlNode $headerNode
     * @return Dom\HtmlNode[]
     */
    private function prepareHeaderNode(Dom\HtmlNode $headerNode): array
    {
        if (!$headerNode->hasChildren()) {
            return [];
        }

        $nodesToReturn = [];

        /** @var Dom\HtmlNode[] $children */
        $children = $headerNode->getChildren();

        $stringsToMerge = [];
        foreach ($children as $child) {
            if ($child->getTag()->name() === 'text') {
                $stringsToMerge[] = $child->text();
            } elseif ($child->getTag()->name() === 'br') {
                $nodesToReturn[] = $this->createSimpleNodeWithText($headerNode->getTag(), implode('', $stringsToMerge));
                $stringsToMerge = [];
            } else {
                $stringsToMerge[] = $this->extractTextFromNode($child);
            }
        }

        if (!empty($stringsToMerge)) {
            $nodesToReturn[] = $this->createSimpleNodeWithText($headerNode->getTag(), implode('', $stringsToMerge));
        }

        return $nodesToReturn;
    }

    private function transformToParagraph(Dom\HtmlNode $htmlNode): array
    {
        if (!$htmlNode->hasChildren()) {
            return [];
        }

        $nodesToReturn = [];

        /** @var Dom\HtmlNode[] $children */
        $children = $htmlNode->getChildren();

        $workingNode = new Dom\HtmlNode('p');
        foreach ($children as $child) {
            if ($child->getTag()->name() === 'text') {
                $workingNode->addChild(new Dom\TextNode($child->text()));
            } elseif ($child->getTag()->name() === 'br') {
                if (!empty($workingNode->innerHtml())) {
                    $nodesToReturn[] = $workingNode;
                    $workingNode = new Dom\HtmlNode('p');
                }
            } elseif (in_array($child->getTag()->name(), ['b', 'strong'])) {
                $workingNode->addChild(
                    $this->createSimpleNodeWithText(new Dom\Tag('b'), $this->extractTextFromNode($child))
                );
            } elseif (in_array($child->getTag()->name(), ['ul', 'ol'])) {
                if (!empty($workingNode->innerHtml())) {
                    $nodesToReturn[] = $workingNode;
                    $workingNode = new Dom\HtmlNode('p');
                    $nodesToReturn[] = $this->prepareListNode($child);
                }
            } else {
                $textNode = $this->extractTextFromNode($child);
                if (null !== $textNode) {
                    $workingNode->addChild($textNode);
                }
            }
        }

        if (!empty($workingNode->innerHtml())) {
            $nodesToReturn[] = $workingNode;
        }

        return $nodesToReturn;
    }

    private function extractTextFromNode(Dom\HtmlNode $node, string $brSeparator = ' '): ?Dom\TextNode
    {
        if (!$node->hasChildren()) {
            return null;
        }

        $stringsToMerge = [];

        /** @var Dom\HtmlNode[] $children */
        $children = $node->getChildren();

        foreach ($children as $child) {
            if ($child->getTag()->name() === 'text') {
                $stringsToMerge[] = $child->text();
            } elseif ($child->getTag()->name() === 'br') {
                $stringsToMerge[] = $brSeparator;
            } else {
                $stringsToMerge[] = $this->extractTextFromNode($child);
            }
        }

        $textNode = new Dom\TextNode(implode('', $stringsToMerge));

        return $textNode;
    }

    private function createSimpleNodeWithText(Dom\Tag $tag, string $text): Dom\HtmlNode
    {
        $newNode = new Dom\HtmlNode($tag->name());
        $textNode = new Dom\TextNode($text);
        $newNode->addChild($textNode);
        return $newNode;
    }

    private function convertHtmlSpecialChars(Dom\HtmlNode $htmlNode): Dom\HtmlNode
    {
        $nodeToReturn = new Dom\HtmlNode($htmlNode->getTag()->name());

        if (!$htmlNode->hasChildren()) {
            return $nodeToReturn;
        }

        /** @var Dom\HtmlNode[] $children */
        $children = $htmlNode->getChildren();

        foreach ($children as $child) {
            if ($child->getTag()->name() === 'text') {
                $text = $child->text();
                $text = str_replace('&nbsp;', ' ', $text);
                $text = htmlspecialchars($text);
                $nodeToReturn->addChild(new Dom\TextNode($text));
            } else {
                $nodeToReturn->addChild($this->convertHtmlSpecialChars($child));
            }
        }

        return $nodeToReturn;
    }
}