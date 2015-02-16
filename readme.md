# HTMLTags #
Easy to use class for creating HTML tags in an OOP way.

## Instalation ##

Include this into your composer.json file:
```javascript
{
    "require": {
        "gercoli/htmltags": "dev-master"
    }
}
```

## Using the class ##
The HTMLTags class uses fully qualified name spaces, so for easier use, add ```use GErcoli\HTMLTags\HTMLTag``` into your php file. Secondly, the class was designed to be easy to use by allowing the chaining of setter methods.

### Example of a simple tag ###
```PHP
    // Create an instance.
    $tag_div = new HTMLTag("div");
    
    // Add Classes A,B,C and remove B (only A & C should remain)
    $tag_div
        ->addClass("classA")
        ->addClass("classB")
        ->addClass("classC")
        ->removeClass("CLASSB")
        ->setAttribute("id","contrainer")
        ->appendContent("This is text inside of the div!");
    
    // Lets convert this tag to a formatted string!
    echo $tag_div
    
    // The string should read:
    // <div class="classA classC" id="contrainer">This is text inside of the div!</div>
```

### Nesting HTML tags ###
```PHP
    // Create a meta description tag.
    $tag_description = (new HTMLTag("meta"))
        ->setAttribute("name","description")
        ->setAttribute("content","this is a \"description\" tag.");

    // Create a title tag.
    $tag_title = (new HTMLTag("title"))
        ->appendContent("This is a page title");

    // Create a head tag and insert the two other tags INSIDE it.
    $tag_head = (new HTMLTag("head"))
        ->appendContent($tag_title)
        ->appendContent($tag_description);

    // Check the markup:
    echo $tag_head;
    
    /*
     * OUTPUT:
     *  <head>
	 *      <title>This is a page title</title>
	 *      <meta name="description" content="this is a &quot;description&quot; tag.">
     *  </head>
     *
     * Notice that using echo will convert the tag and all sub-tags (or children) to strings,
     * automatically escape invalid characters (such as quotes), and indent the tags.
     */
```

### Notes on end tags ###
By default, when you add content to a tag via ```->appendContent()``` or
```->prependContent()``` a closing tag will automatically be enabled, however, using
```->setClosingTag(false)``` will disable a closing tag, and according to the
[HTML5 spec](http://www.w3.org/html/wg/drafts/html/master/syntax.html#normal-elements), this
is perfectly valid (which is why I have not added a check for this), so be aware of this fact.

### HTML tags with XHTML children ###
When you echo out a parent tag that has children, the format of the parent tag does not
currently cascade down to it's children, and this is by design. There are tags out there,
specifically social networks that don't always properly validate non-XHTML tags, and as such
when you set a tag to being XHTML, it will remain that way until YOU change it. THIS WILL
change in the future, but to keep validators happy, I will leave self-closing tags alone.

## TO DO ##
- Better documentation (working on it)
- Implement the ability to parse a style attribute, we don't want duplicate styles.