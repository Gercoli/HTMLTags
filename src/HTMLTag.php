<?php namespace GErcoli\HTMLTags;

/**
 * A class designed to build an HTML tag (or tree of tags) in an OOP way.
 * @author      Garry Ercoli <Garry@GErcoli.com>
 * @package     GErcoli\HTMLTags
 * @version     v1.0-beta
 * @copyright   Garry Ercoli
 */

class HTMLTag implements HTMLTagInterface {

    /**
     * If we are encoded with XHTML, we need a self-closing tag.
     * @var bool
     */
    protected $self_closing_tag = false;

    /**
     * Does this tag have a closing tag? Exmple: <div> vs <img>
     * @var bool
     */
    protected $closing_tag = false;

    /**
     * The type of tag, div,img,span,em,etc.
     * @var string
     */
    protected $tag_type;

    /**
     * The array of content inside of the tag.
     * @var string[]|HTMLTag[]
     */
    protected $tag_content;

    /**
     * The array of attributes that belong to this tag.
     * @var string[]
     */
    protected $tag_attributes;

    /**
     * The string to prefix the tag with, usually a tab.
     * @var string
     */
    protected $tag_prefix = "\t";

    /**
     * The previous value of $tag_prefix
     * @var string
     */
    protected $tag_prefix_previous;

    /**
     * List of tags that by default don't have a closing tag.
     * @var string[]|array
     */
    protected $tags_without_closing = [
        "img",      "input",
        "br",       "hr",
        "frame",    "area",
        "base",     "basefont",
        "col",      "isindex",
        "link",     "meta",
        "param"];


    /**
     * Sets the attribute of this tag to the value given,
     * a value of NULL will list the attribute with no ="value" after
     * @param   string  $name
     * @param   string|null $value
     * @return  $this
     * @throws  HTMLTagException
     */
    public function setAttribute($name, $value)
    {
        if(strlen($name) < 1)
        {
            throw new HTMLTagException("Attribute name is empty");
        }

        $this->tag_attributes[strtolower($name)] = $value;

        return $this;
    }

    /**
     * Returns the value of the given attribute
     * @param   string  $name
     * @return  null|string
     * @throws  HTMLTagException
     */
    public function getAttribute($name)
    {
        if(strlen($name) < 1)
        {
            throw new HTMLTagException("Attribute name is empty");
        }

        if(!isset($this->tag_attributes[strtolower($name)]))
        {
            return null;
        }

        return $this->tag_attributes[strtolower($name)];
    }


    /**
     * Removes the attribute from the tag, along with its value.
     * @param   string  $name
     * @return  $this
     * @throws  HTMLTagException
     */
    public function removeAttribute($name)
    {
        if(strlen($name) < 1)
        {
            throw new HTMLTagException("Attribute name is empty");
        }

        unset($this->tag_attributes[strtolower($name)]);

        return $this;
    }

    /**
     * Returns true/false depending on if the attribute name exists.
     * @param   string  $name
     * @return  bool
     * @throws  HTMLTagException
     */
    public function hasAttribute($name)
    {
        if(strlen($name) < 1)
        {
            throw new HTMLTagException("Attribute name is empty");
        }

        return (isset($this->tag_attributes[strtolower($name)])) ? true : false;
    }

    /**
     * @return array|string[]
     */
    public function listAttributes()
    {
        if(!is_array($this->tag_attributes))
        {
            $this->tag_attributes = [];
        }
        return $this->tag_attributes;
    }

    /**
     * Add a class name to the class attribute, this won't add duplicates.
     * @param   string  $className
     * @return  $this
     * @throws  HTMLTagException
     */
    public function addClass($className)
    {
        if(strlen($className) < 1)
        {
            throw new HTMLTagException("Class name is empty");
        }

        if(!$this->hasClass($className))
        {
            $classes = $this->getClasses() . " " . $this->removeMultipleSpaces(trim($className));
            $this->setAttribute("class",$classes);
        }

        return $this;
    }

    /**
     * Removes all classes.
     * @return  $this
     * @throws  HTMLTagException
     */
    public function clearClasses()
    {
        $this->removeAttribute("class");
        return $this;
    }

    /**
     * Removes the specified class name.
     * @param   string  $className
     * @return  $this
     * @throws  HTMLTagException
     */
    public function removeClass($className)
    {
        if(strlen($className) < 1)
        {
            throw new HTMLTagException("Class name is empty");
        }

        $classes = explode(" ", $this->getClasses());
        $this->clearClasses();

        foreach($classes as $class) {
            if(strtolower($class) != strtolower($className))
            {
                $this->addClass($class);
            }
        }

        return $this;
    }

    /**
     * Checks to see if the given class name is inside of the class attribute
     * @param   string  $className
     * @return  bool
     * @throws  HTMLTagException
     */
    public function hasClass($className)
    {
        if(strlen($className) < 1)
        {
            throw new HTMLTagException("Class name is empty");
        }

        if($this->hasAttribute("class"))
        {
            $classes = $this->getClasses();

            foreach(explode(" ",$classes) as $class) {
                if(strtolower($class) == strtolower($className)) {
                    return true;    // We found it.
                }
            }

        }

        return false;
    }

    /**
     * Returns a space separated string of classes belonging to the tag.
     * @return  string
     * @throws  HTMLTagException
     */
    public function getClasses()
    {
        $classes = $this->getAttribute("class");
        return (strlen($classes) > 0) ? trim($this->removeMultipleSpaces($classes)) : "";
    }

    public function appendContent($content)
    {
        // TODO: Implement appendContent() method.
    }

    public function prependContent($content)
    {
        // TODO: Implement prependContent() method.
    }

    public function clearContent()
    {
        // TODO: Implement clearContent() method.
    }

    /**
     * Returns an array of inner content.
     * @return array|HTMLTag[]|\string[]
     */
    public function getContent()
    {
        if(!is_array($this->tag_content))
        {
            $this->tag_content = [];
        }
        return $this->tag_content;
    }

    /**
     * Defines if this tag needs a closing tag or not,
     * NOTE this should be FALSE for self-closing tags.
     * @param   bool    $enable
     * @return  $this
     * @throws  HTMLTagException
     */
    public function setClosingTag($enable)
    {
        if(!is_bool($enable))
        {
            throw new HTMLTagException("Only a boolean value is allowed.");
        }

        $this->closing_tag = $enable;
        return $this;
    }

    /**
     * Does this tag require a closing tag?
     * @return bool
     */
    public function getClosingTag()
    {
        return $this->closing_tag;
    }

    /**
     * This will determine if the tag (and it's children) will need
     * don't need closing tags will have self-closing tags, as XHTML does.
     * @param   bool $enable
     * @return  $this
     * @throws  HTMLTagException
     */
    public function setXHTMLEncoding($enable)
    {
        if(!is_bool($enable))
        {
            throw new HTMLTagException("Only a boolean value is allowed.");
        }

        $this->self_closing_tag = $enable;
        return $this;
    }

    /**
     * Does this tag need a self-closing tag?
     * @return bool
     */
    public function getXHTMLEncoding()
    {
        return $this->self_closing_tag;
    }

    /**
     * Format and return attributes in a name="value" format as
     * it should appear in an HTML tag.
     * @return  string
     */
    public function getFormattedAttributes()
    {
        $rtn = "";
        foreach($this->listAttributes() as $name => $value)
        {
            $rtn .= sprintf(" %s=\"%s\"",htmlentities($name),htmlentities($value));
        }
        return $rtn;
    }

    public function setTagPrefix($prefix)
    {
        // TODO: Implement setTagPrefix() method.
    }

    public function getTagPrefix()
    {
        // TODO: Implement getTagPrefix() method.
    }

    public function getPreviousTagPrefix()
    {
        // TODO: Implement getPreviousTagPrefix() method.
    }

    public function __toString()
    {
        return $this->getTagType();
    }

    function __construct($tag_type, $closing_tag = null, $XHTML_encoding = false)
    {
        // Determine the closing tag
        if($closing_tag === null || ($closing_tag !== true && $closing_tag !== false))
        {
            $closing_tag = true;
            if(in_array(strtolower($tag_type),$this->tags_without_closing))
            {
                $closing_tag = false;
            }
        }

        // Determine encoding type, HTML by default
        if($XHTML_encoding !== true && $XHTML_encoding !== false)
        {
            $XHTML_encoding = false;
        }

        $this->tag_prefix_previous = $this->tag_prefix;
        $this->tag_attributes = [];
        $this->tag_content = [];

        $this->setClosingTag($closing_tag);
        $this->setXHTMLEncoding($XHTML_encoding);
    }

    /**
     * The type of tag this is.
     * @return string
     */
    public function getTagType()
    {
        return $this->tag_type;
    }

    /**
     * Removes multiple consecutive spaces and replaces it with a single space.
     * @param   string  $string
     * @return  string
     */
    public static function removeMultipleSpaces($string)
    {
        return preg_replace('/[ ]{2,}/', ' ', $string);
    }
}

//class HTMLTag {
//
//    /**
//     * If this tag requires a closing tag.
//     * @var bool
//     */
//    protected $closure      = false;
//
//    /**
//     * The tag type (a, meta, div, etc)
//     * @var null|string
//     */
//    protected $type         = NULL;
//
//    /**
//     * The contents inside of the tag, if this is not empty, $closure should be true.
//     * @var array
//     */
//    protected $contents     = [];
//
//    /**
//     * The attributes/parameters of the tag
//     * @var array
//     */
//    protected $attributes   = [];
//
//    /**
//     * The string to prepend to the output of the tags
//     * @var string
//     */
//    protected $tag_prefix   = "\t";
//
//    /**
//     * Same as $tag_prefix but holds it's previous value
//     * @var string
//     */
//    protected $tag_prefix_previous;
//
//    /**
//     * Create an instance of an HTMLTag given the element/tag type
//     * @param   string  $tagType
//     * @param   bool    $requiresClosure
//     */
//    public function __construct($tagType = "div", $requiresClosure = false)
//    {
//        $this->type($tagType);
//        $this->closure  = ($requiresClosure === true) ? true :  false;
//        $this->tag_prefix_previous = $this->tag_prefix;
//    }
//
//    /**
//     * Sets or Gets an attribute from the tag.
//     * @param   null|string $attributeName
//     * @param   null|string $value
//     * @return  null|string|$this
//     * @throws  HTMLTagException
//     */
//    public function attribute($attributeName = null,$value = null)
//    {
//        if($attributeName === null)
//        {
//            throw new HTMLTagException("Attribute name was null");
//        }
//
//        if($value === null || !is_string($value))
//        {
//            if(isset($this->attributes[strtolower($attributeName)]))
//            {
//                return $this->attributes[strtolower($attributeName)];
//            }
//            return null;
//        }
//
//        $this->attributes[strtolower($attributeName)] = $value;
//        return $this;
//
//    }
//
//    /**
//     * Removes an attribute by its case-insensitive name
//     * @param   String  $attributeName
//     * @return  $this
//     */
//    public function removeAttribute($attributeName)
//    {
//        unset($this->attributes[strtolower($attributeName)]);
//        return $this;
//    }
//
//    /**
//     * Checks if the given attribute name is added to the tag.
//     * @param   String  $attributeName
//     * @return  bool
//     */
//    public function hasAttribute($attributeName)
//    {
//        if(isset($this->attributes[strtolower($attributeName)]))
//        {
//            return true;
//        }
//        return false;
//    }
//
//    /**
//     * Performs a case-insensitive search for the provided class name
//     * @param   String  $className
//     * @return  bool
//     */
//    public function hasClass($className)
//    {
//        if( $this->hasAttribute("class") )
//        {
//            foreach(explode(" ", $this->attribute("class")) as $class)
//            {
//                if(strtolower($class) == strtolower($className))
//                {
//                    return true;
//                }
//            }
//        }
//
//        return false;
//
//    }
//
//    /**
//     * Gets an array of classes assigned to this tag
//     * @return  String|null
//     */
//    public function getClasses()
//    {
//        if(!$this->hasAttribute("class"))
//        {
//            return null;
//        }
//
//        return preg_replace('!\s+!', ' ', trim($this->attribute("class")));
//    }
//
//    /**
//     * Add a class to the tag
//     * @param   string  $className
//     * @return  $this
//     */
//    public function addClass($className)
//    {
//        if(!$this->hasClass($className))
//        {
//            $this->attribute("class",$this->getClasses() . " " . preg_replace('!\s+!', ' ', trim($className)));
//        }
//        return $this;
//    }
//
//    /**
//     * Removes a single class from the list of classes.
//     * @param   String  $className
//     * @return  $this
//     */
//    public function removeClass($className)
//    {
//        $old_classes = explode(" ", $this->attribute("class"));
//
//        $this->removeAttribute("class");
//
//        foreach($old_classes as $class)
//        {
//            if(strtolower($class) == strtolower($className) || strlen(trim($class)) < 1)
//            {
//                continue;
//            }
//            $this->addClass($class);
//        }
//
//        return $this;
//
//    }
//
//    /**
//     * Adds a string or HTMLTag to the contents of this tag - this will auto-enable the closure tag.
//     * @param   String|HTMLTag  $content
//     * @return  bool|$this
//     * @see     closure()
//     */
//    public function appendContent($content)
//    {
//        if(!is_string($content) && !($content instanceof self))
//        {
//            return false;
//        }
//
//        $this->contents[] = $content;
//
//        $this->closure(true);
//
//        return $this;
//    }
//
//    /**
//     * Get the contents of the HTML tag (the portion inside of the opening and closing tags)
//     * @return  array
//     */
//    public function getContent() {
//        if(!is_array($this->contents))
//        {
//            $this->contents = [];
//        }
//        return $this->contents;
//    }
//
//    /**
//     * Define the tag type, i.e. "a", "meta", "div"
//     * @param   null|String $newType
//     * @return  string|$this
//     */
//    public function type($newType = null)
//    {
//        if($newType === null || strlen($newType) == 0)
//        {
//            return $this->type;
//        }
//
//        $this->type = strtolower($newType);
//        return $this;
//    }
//
//    /**
//     * Declare if this html tag needs a closing tag or not.
//     * @param   null|bool   $closureRequired
//     * @return  bool|$this
//     */
//    public function closure($closureRequired = null)
//    {
//        if($closureRequired !== true && $closureRequired !== false)
//        {
//            return $this->closure;
//        }
//
//        $this->closure = $closureRequired;
//        return $this;
//    }
//
//    /**
//     * Creates a formatted key1="value1" key2="value2" string for the tag
//     * @return  string
//     */
//    private function getFormattedAttributes()
//    {
//        foreach($this->attributes as $key => $value)
//        {
//            $string = ((isset($string)) ? $string : "") . sprintf(" %s=\"%s\"",htmlentities($key), htmlentities($value));
//        }
//        return ((isset($string)) ? $string : "");
//    }
//
//    public function tagPrefix($prefix = null)
//    {
//        if(!is_string($prefix))
//        {
//            return $this->tag_prefix;
//        }
//
//        $this->tag_prefix_previous = $this->tag_prefix;
//        $this->tag_prefix = $prefix;
//        return $this;
//    }
//
//    private function tagPrefixPrevious()
//    {
//        return $this->tag_prefix_previous;
//    }
//
//    /**
//     * Turns the object (and it's children) into a nice pretty lump of HTML
//     * @return string
//     */
//    public function __toString()
//    {
//        $html = sprintf("%s<%s%s>", $this->tagPrefix(),$this->type(), $this->getFormattedAttributes());
//        if($this->closure())
//        {
//            $innerHTML = "";
//            foreach($this->getContent() as $element)
//            {
//                if($element instanceof self)
//                {
//                    $old_prefix = $element->tagPrefix();
//                    $element->tagPrefix( $this->tagPrefix() . $old_prefix );
//                    $innerHTML .= "\n" . $element->__toString();
//                    $element->tagPrefix($old_prefix);
//                }
//                elseif(is_string($element))
//                {
//                    $innerHTML .= "\n" . $this->tagPrefix() . $this->tagPrefixPrevious() . str_replace("\n","\n" . $this->tagPrefix() . $this->tagPrefixPrevious(),htmlentities($element));
//                }
//            }
//            $html .= sprintf("%s\n%s</%s>",$innerHTML,$this->tagPrefix(),$this->type());
//        }
//        return $html;
//    }
//}