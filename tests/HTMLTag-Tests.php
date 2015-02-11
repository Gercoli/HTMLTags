<?php
use GErcoli\HTMLTags\HTMLTag;

class HTMLTagTest extends PHPUnit_Framework_TestCase
{

    function testInstantiation()
    {
        $tag_type = "meta";     // What type of tag is this?
        $tag_closure = true;    // Does this tag need a closing tag?

        // Create the object:
        $htmlTag = new HTMLTag($tag_type, $tag_closure);

        // Check to see if the object was created correctly:
        $this->assertTrue($htmlTag instanceof HTMLTag);
        $this->assertTrue($htmlTag->type() == $tag_type);
        $this->assertTrue($htmlTag->closure() == $tag_closure);
    }

    function testAddRemoveClassName()
    {
        $htmlTag = new HTMLTag("img",false);
        $className = "testName";

        // This tag should have no classes yet, so the addClass()
        // should return true. False is returned if the class already
        // exists
        $this->assertTrue($htmlTag->addClass(strtolower($className)));

        // Testing if the class (of the same case) has been added:
        $this->assertTrue($htmlTag->hasClass(strtolower($className)));

        // Testing if the class (of different case) has been added:
        $this->assertTrue($htmlTag->hasClass(strtoupper($className)));

        // Testing if true is returned for a class that shouldn't exist:
        $this->assertFalse($htmlTag->hasClass(strtolower($className . "fsdfsa")));

        // Remove the className and make sure it has been removed.
        $htmlTag->removeClass($className);
        $this->assertFalse($htmlTag->hasClass($className));

    }

    function testNestedTags()
    {
        // Setup the parent element.
        $parent = new HTMLTag("div",false);
        $parent->addClass("container");
        $parent->setAttribute("id","mommy");

        // Setup the child element.
        $child  = new HTMLTag("span",false);
        $child->addClass("boldthis");
        $child->setAttribute("id","baby");
        $child->appendContent($child_text = "This text is in the \"child\" element.");

        // Insert the child into the parent.
        $parent->appendContent($child);

        // Make the text in the parent the 2nd element.
        $parent->appendContent($parent_text = "This text is in the \"parent\" element.");

        // parent text should be the second element
        $this->assertTrue($parent->getContent()[1] === $parent_text);
        // The inserted child should be the 2nd element inside of the parent.
        $this->assertTrue($parent->getContent()[0] instanceof HTMLTag);

        // child text should be the first element inside.
        $this->assertTrue($child->getContent()[0] === $child_text, "Child text should be the first element in the child.");
    }

    function testRender()
    {
        $parent = new HTMLTag("div",false);
        $parent->addClass("container");
        $parent->setAttribute("id","parentID");

        // Make an child (or inner) HTML tag.
        $child  = new HTMLTag("img",false);
        $child->addClass("modal");
        $child->setAttribute("id","picture1");

        // Add the child INSIDE of the parent:
        $parent->appendContent($child);

        $text = $parent->__toString();
        $this->assertContains("<img ",$text,"Image tag is missing.");
        $this->assertContains("<div ",$text,"div tag is missing.");
        
    }
}
?>