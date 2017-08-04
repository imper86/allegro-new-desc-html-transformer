# allegro-new-desc-html-transformer
Simple library to transform your html description to new Allegro.pl responsible description

## Prerequisites
* PHP ^7.1
* composer

## Installing
```$xslt
composer require imper86/allegro-new-desc-html-transformer
```

## Usage
```$xslt
use Imper86\AllegroNewDescHtmlTransformer\HtmlTransformer;

$uproperHtml = '<p class="test" style="margin: 0px;">Test test <br></p>';

$transformer = new HtmlTransformer();
$properHtml = $transformer->transformHtmlString($unproperHtml);

var_dump($properHtml->getTransformedHtmlString());
var_dump($properHtml->getTransformedDom());
var_dump($properHtml->getOriginalDom());
var_dump($properHtml->getOriginalHtmlString());
```