# TreeSP

Extension of [MediaWiki](https://www.mediawiki.org/), the software of [Wikipedia](https://www.wikipedia.org/).

Adds the #treesp function to the parser. Generates a list of sub-pages, directly from the database, and internally calls the #tree function of the [TreeAndMenu](https://www.mediawiki.org/wiki/Extension:TreeAndMenu) (prerequisite).

## Author

Developed by Alexandre Porto da Silva, in 2012, for internal corporate use at Banco do Brasil, on wiki.bb.com.br. In (2024) it was updated to the latest version of [MediaWiki](https://www.mediawiki.org/).

## License

[GNU General Public Licence 3.0](https://www.gnu.org/licenses/gpl-3.0.html) or later

## Installation

1. Download the [source code](https://github.com/WikiPorto/TreeSP) and place the files in a directory called **TreeSP** in the **extensions** folder of your  [MediaWiki](https://www.mediawiki.org/).

2. Add the following code to the bottom of your **LocalSettings.php** file:
```php
wfLoadExtension( 'TreeSP' );
```
3. Done – Navigate to **Special:Version** on your wiki to verify that the extension was installed successfully.

## Use:

Optionally, you can pass the parameter as an internal link and thus use the **pipe "|"**, to display a different text in the ROOT of the tree, in this case, the use of the **brackets "[[...]]"**:
```wikitext
{{#treesp:[[ROOT|different text]]}}
```
### Examples

Also available on the website: [WikiPorto](https://wikiporto.org/).

#### Example 1

Generates a list of subpages using the current page as **[[ROOT]]**:

```wikitext
{{#treesp:}}
```

#### Example 2

When using the percentage symbol: **%**, generates a list of all sub-pages of the specified or current Namespace:

```wikitext
{{#treesp:Namespace:%}}
```
or
```wikitext
{{#treesp:%}}
```

#### Example 3

Generates a list of subpages using the page **Namespace:Page** as **[[ROOT]]**:

```wikitext
{{#treesp:Namespace:Page}}
```

#### Example 4

Same command as the example above using **pipe "|"**:

```wikitext
{{#treesp:[[Namespace:Page|]]}}
```
or
```wikitext
{{#treesp:[[Namespace:Page|Page]]}}
```
