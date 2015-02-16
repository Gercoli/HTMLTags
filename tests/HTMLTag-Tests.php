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

}