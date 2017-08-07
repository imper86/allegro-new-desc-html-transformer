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
How to convert single HTML item with HtmlTransformer class:
```php
use Imper86\AllegroNewDescHtmlTransformer\HtmlTransformer;

$uproperHtml = '<p class="test" style="margin: 0px;">Test test <br></p>';

$transformer = new HtmlTransformer();
$properHtml = $transformer->transformHtmlString($unproperHtml);

var_dump($properHtml->getTransformedHtmlString());
var_dump($properHtml->getTransformedDom());
var_dump($properHtml->getOriginalDom());
var_dump($properHtml->getOriginalHtmlString());
```

Example of using JsonTransformer class. It can be used to transform completed structs to proper HTML format.
```php
use Imper86\AllegroNewDescHtmlTransformer\JsonTransformer;

//WARNING!! it's up to you to build well formatted JSON structure, JsonTransformer will only check your HTML sections
$unproperStruct = '{"sections":[{"items":[{"type":"TEXT","content":"<p>UWAGA AUKCJA TESTOWA, PROSIMY NIE KUPOWAĆ</p>"}]},{"items":[{"type":"IMAGE","url":"PHOTO_FID_16"},{"type":"TEXT","content":"<h1>ADIDAS ZX FLUX K S74952</h1>\n<h2>Rozmiar: 38⅔½⅓</h2>\n<ul>\n<li>Stan: nowy</li>\n<li>Producent: adidas</li>\n<li>Numer katalogowy: S74952</li>\n<li>Kolor dominujący: różowy</li>\n<li>Kolory dodatkowe: czarny, biały</li>\n<li>Materiał cholewki: materiał tekstylny + materiał syntetyczny</li>\n<li>Podeszwa: guma</li>\n</ul>"}]},{"items":[{"type":"TEXT","content":"<p>Elegancki i nowoczesny z nutką stylu retro adidas Originals ZX Flux. Cholewka z materiału tekstylnego daje odpowiednią wentylację a gumowa podeszwa zapewnia amortyzację.</p>\n<p>Wewnątrz butów znajdują się wkładki z systemem Ortholite.</p>"}]},{"items":[{"type":"IMAGE","url":"PHOTO_FID_17"}]}]}';

$jsonTransformer = new JsonTransformer();
$properStruct = $jsonTransformer->transformJsonStruct($uproperStruct);

var_dump($properStruct);
```