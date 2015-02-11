# HTMLTags #
Easy to use class for creating HTML tags in an OOP way.

### Instalation ###

Include this into your composer.json file:
```javascript
{
    "require": {
        "gercoli/htmltags": "*"
    }
}
```

### Using the class ###

First, I'm going to assume that you have a USE statement at the top of your application,
if this is NOT true, just add the fully qualified namespace **GErcoli\HTMLTags\HTMLTag**
before each instance of HTMLTag();

```PHP
// Make a parent (or outer) HTML tag.
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

// NOTE1:   At this point, the child is now inserted inside of the parent tag, and since the parent
//          tag now has content inside of it, it also knows that it will need a closing tag.
//          Using appendContent() automatically sets "closure" (closing tag) to true.

// NOTE2:   As of right now, the appendContent() method accepts strings and other HTMLTag objects.
//          You can nest as many tags inside as you want, tags inside of tags inside of more tags
//          etc.

// Just using echo on the HTMLTag will force it to evaluate all of it's properties and present it
// as normal HTML. This is done on the spot and is not pre-compiled, meaning if you change a child
// object at any time, the changes will immediately be reflected when you use echo next.
echo $parent;

// Resulting output:
// <div class="container" id="parentID"><img class="modal" id="picture1"></div>
```

### TO DO ###
- Better documentation
- Implement the ability to properly indent inner-tags
- Implement setting for HTML/XHTML versions, this would effect closing (or self-closing) tag formats.
- Implement the ability to parse a style attribute, we don't want duplicate styles.