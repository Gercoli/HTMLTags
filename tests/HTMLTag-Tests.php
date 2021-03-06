<?php
use GErcoli\HTMLTags\HTMLTag;

class HTMLTagTest extends PHPUnit_Framework_TestCase
{
    public function testClasses()
    {
        $tag_div = new HTMLTag("div");

        // Add Classes A,B,C and remove B (only A & C should remain)
        $tag_div
            ->addClass("classA")
            ->addClass("classB")
            ->addClass("classC")
            ->removeClass("CLASSB");

        // Test getClasses()
        $this->assertContains("classA",$tag_div->getClasses());     // Has "classA"
        $this->assertContains("classC",$tag_div->getClasses());     // Has "classC"
        $this->assertNotContains("classB",$tag_div->getClasses());  // Doesn't Have "classB"

        // Test hasClass()
        $this->assertTrue($tag_div->hasClass("CLASSA"));    // Has "classA" (case-insensitive)
        $this->assertTrue($tag_div->hasClass("CLASSC"));    // Has "classC"
        $this->assertFalse($tag_div->hasClass("CLASSB"));   // Doesn't have "classB"

        $tag_div->clearClasses()->addClass("classD");
        $this->assertFalse($tag_div->hasClass("CLASSA"));   // This has been removed.
        $this->assertFalse($tag_div->hasClass("CLASSB"));   // This has been removed.
        $this->assertFalse($tag_div->hasClass("CLASSC"));   // This has been removed.
        $this->assertTrue($tag_div->hasClass("CLASSD"));    // This should be there.
    }

    public function testEncoding()
    {
        // By default, the class SHOULD know if a tag needs a closing tag,
        // based on the tag type. These settings can be overwritten.
        $tag_div    = new HTMLTag("div",null,false);    // closing tag.
        $fake_tag   = new HTMLTag("div",false,false);   // no closing tag.
        $tag_meta   = new HTMLTag("meta",null,true);    // no closing tag.

        // Closing tags
        $this->assertTrue($tag_div->getClosingTag());       // yes, closing tag.
        $this->assertFalse($tag_meta->getClosingTag());     // no, closing tag.
        $this->assertFalse($fake_tag->getClosingTag());     // no, closing tag.

        // Self-closing tags (XHTML):
        $this->assertFalse($tag_div->getXHTMLEncoding());   // HTML
        $this->assertFalse($fake_tag->getXHTMLEncoding());  // HTML
        $this->assertTrue($tag_meta->getXHTMLEncoding());   // XHTML
    }

    public function testContent()
    {
        $tag = new HTMLTag("div");
        $tag->appendContent("String 1")->appendContent("String 2")->appendContent("String 3");

        $this->assertTrue($tag->getContent()[0] == "String 1");
        $this->assertTrue($tag->getContent()[1] == "String 2");
        $this->assertTrue($tag->getContent()[2] == "String 3");

        $tag->prependContent("String 4");
        $this->assertTrue($tag->getContent()[0] == "String 4");
        $this->assertTrue($tag->getContent()[1] == "String 1");
        $this->assertTrue($tag->getContent()[2] == "String 2");
        $this->assertTrue($tag->getContent()[3] == "String 3");

        $content = $tag->clearContent()->getContent();
        $this->assertTrue(count($content) === 0);

    }

    public function testRender()
    {

        echo "\n\n================================\nTest 1 - Plain HTML div tag\n================================\n";
        $div = (new HTMLTag("div",null,true))->addClass("container")->setAttribute("id","myname");
        echo $div;

        echo "\n\n================================\nTest 2 - Meta XHTML tag\n================================\n";
        $meta = (new HTMLTag("meta",null,true))->setAttribute("name","description")->setAttribute("content","some \"description\" about the site");
        echo $meta;

        echo "\n\n================================\nTest 3 - Span inside anchor inside div with text\n================================\n";
        $inner = (new HTMLTag("a"))->appendContent("inner link")->setAttribute("href","//github.com/");
        $outer = (new HTMLTag("div"))->appendContent($inner)->setAttribute("id","container");
        echo $outer;

        echo "\n\n================================\nTest 4 - Span inside anchor inside div with text\n================================\n";
        $childA = (new HTMLTag("a"))->appendContent("this is a link")->setAttribute("href","http://www.google.com/");

        $grandchild = (new HTMLTag("span"))->appendContent("link")->addClass("icon");
        $childB = (new HTMLTag("a"))->appendContent("this is another ")->setAttribute("href","http://www.yahoo.com/")->appendContent($grandchild);

        $parent = (new HTMLTag("div"))->appendContent($childA)->appendContent($childB);
        echo $parent;

    }

}