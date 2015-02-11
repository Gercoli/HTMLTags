# HTMLTags
Easy to use class for creating HTML tags in an OOP way.

### Instalation ###
Option 1, using the CLI type:

```
composer require gercoli/htmltags
```

Option 2, edit your composer.json file and insert the requirement like so:

```javascript
{
    "require": {
        "gercoli/htmltags": "*"
    }
}
```

### Using the class ###

First, I'm going to assume that you have a USE statement at the top of your application, if this is NOT true, just add the fully qualified namespace before each instance of HTMLTag();

```PHP
// Make a parent (or outter) HTML tag.
// NOTE: we use false as the 2nd param here, which means that this tag
// DOES NOT have a closing tag, however, as soon as we add inner content
// to the tag, the class is smart enough to know that we will now need a </div>
$parent = new HTMLTag("div",false);
$parent->addClass("container");
$parent->setAttribute("id","parentID");

// Make an child (or inner) HTML tag.
$child  = new HTMLTag("img",false);
$child->addClass("modal");
$child->setAttribute("id","picture1");

// Add the child INSIDE of the parent:
$parent->appendContent($child);
// NOTE1: At this point, the class now added the content inside of the parent tag, and knows that a closing tag is needed.
// NOTE2: you can add multiple pieces of content inside of a tag, or tags inside of tags inside of tags, etc..

// Simply echoing a HTMLTag will evaluate all of its properties as well as all properties of it's inner content
// If a tag (or tags) exist inside of the parent tag, those tags are also evaluated when "_toString()" is executed.
echo $parent;

// Resulting output:
// <div class="container" id="parentID"><img class="modal" id="picture1"></div>
```

### TO DO ###
- Better documentation
- Implement the ability to properly indent inner-tags
- Implement setting for HTML/XHTML versions, this would effect closing (or self-closing) tag formats.
- Implement the ability to parse a style attribute, we don't want duplicate styles.