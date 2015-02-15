<?php namespace GErcoli\HTMLTags;

/**
 * A class designed to build an HTML tag (or tree of tags) in an OOP way.
 * @author      Garry Ercoli <Garry@GErcoli.com>
 * @package     GErcoli\HTMLTags
 * @version     v1.0-beta
 * @copyright   Garry Ercoli
 */


class HTMLTag {

    /**
     * If this tag requires a closing tag.
     * @var bool
     */
    protected $closure      = false;

    /**
     * The tag type (a, meta, div, etc)
     * @var null|string
     */
    protected $type         = NULL;

    /**
     * The contents inside of the tag, if this is not empty, $closure should be true.
     * @var array
     */
    protected $contents     = [];

    /**
     * The attributes/parameters of the tag
     * @var array
     */
    protected $attributes   = [];

    /**
     * The string to prepend to the output of the tags
     * @var string
     */
    protected $tag_prefix   = "\t";

    /**
     * Same as $tag_prefix but holds it's previous value
     * @var string
     */
    protected $tag_prefix_previous;

    /**
     * Create an instance of an HTMLTag given the element/tag type
     * @param   string  $tagType
     * @param   bool    $requiresClosure
     */
    public function __construct($tagType = "div", $requiresClosure = false)
    {
        $this->type($tagType);
        $this->closure  = ($requiresClosure === true) ? true :  false;
        $this->tag_prefix_previous = $this->tag_prefix;
    }

    /**
     * Sets or Gets an attribute from the tag.
     * @param   null|string $attributeName
     * @param   null|string $value
     * @return  null|string|$this
     * @throws  HTMLTagException
     */
    public function attribute($attributeName = null,$value = null)
    {
        if($attributeName === null)
        {
            throw new HTMLTagException("Attribute name was null");
        }

        if($value === null || !is_string($value))
        {
            if(isset($this->attributes[strtolower($attributeName)]))
            {
                return $this->attributes[strtolower($attributeName)];
            }
            return null;
        }

        $this->attributes[strtolower($attributeName)] = $value;
        return $this;

    }

    /**
     * Removes an attribute by its case-insensitive name
     * @param   String  $attributeName
     * @return  $this
     */
    public function removeAttribute($attributeName)
    {
        unset($this->attributes[strtolower($attributeName)]);
        return $this;
    }

    /**
     * Checks if the given attribute name is added to the tag.
     * @param   String  $attributeName
     * @return  bool
     */
    public function hasAttribute($attributeName)
    {
        if(isset($this->attributes[strtolower($attributeName)]))
        {
            return true;
        }
        return false;
    }

    /**
     * Performs a case-insensitive search for the provided class name
     * @param   String  $className
     * @return  bool
     */
    public function hasClass($className)
    {
        if( $this->hasAttribute("class") )
        {
            foreach(explode(" ", $this->attribute("class")) as $class)
            {
                if(strtolower($class) == strtolower($className))
                {
                    return true;
                }
            }
        }

        return false;

    }

    /**
     * Gets an array of classes assigned to this tag
     * @return  String|null
     */
    public function getClasses()
    {
        if(!$this->hasAttribute("class"))
        {
            return null;
        }

        return preg_replace('!\s+!', ' ', trim($this->attribute("class")));
    }

    /**
     * Add a class to the tag
     * @param   string  $className
     * @return  $this
     */
    public function addClass($className)
    {
        if(!$this->hasClass($className))
        {
            $this->attribute("class",$this->getClasses() . " " . preg_replace('!\s+!', ' ', trim($className)));
        }
        return $this;
    }

    /**
     * Removes a single class from the list of classes.
     * @param   String  $className
     * @return  $this
     */
    public function removeClass($className)
    {
        $old_classes = explode(" ", $this->attribute("class"));

        $this->removeAttribute("class");

        foreach($old_classes as $class)
        {
            if(strtolower($class) == strtolower($className) || strlen(trim($class)) < 1)
            {
                continue;
            }
            $this->addClass($class);
        }

        return $this;

    }

    /**
     * Adds a string or HTMLTag to the contents of this tag - this will auto-enable the closure tag.
     * @param   String|HTMLTag  $content
     * @return  bool|$this
     * @see     closure()
     */
    public function appendContent($content)
    {
        if(!is_string($content) && !($content instanceof self))
        {
            return false;
        }

        $this->contents[] = $content;

        $this->closure(true);

        return $this;
    }

    /**
     * Get the contents of the HTML tag (the portion inside of the opening and closing tags)
     * @return  array
     */
    public function getContent() {
        if(!is_array($this->contents))
        {
            $this->contents = [];
        }
        return $this->contents;
    }

    /**
     * Define the tag type, i.e. "a", "meta", "div"
     * @param   null|String $newType
     * @return  string|$this
     */
    public function type($newType = null)
    {
        if($newType === null || strlen($newType) == 0)
        {
            return $this->type;
        }

        $this->type = strtolower($newType);
        return $this;
    }

    /**
     * Declare if this html tag needs a closing tag or not.
     * @param   null|bool   $closureRequired
     * @return  bool|$this
     */
    public function closure($closureRequired = null)
    {
        if($closureRequired !== true && $closureRequired !== false)
        {
            return $this->closure;
        }

        $this->closure = $closureRequired;
        return $this;
    }

    /**
     * Creates a formatted key1="value1" key2="value2" string for the tag
     * @return  string
     */
    private function getFormattedAttributes()
    {
        foreach($this->attributes as $key => $value)
        {
            $string = ((isset($string)) ? $string : "") . sprintf(" %s=\"%s\"",htmlentities($key), htmlentities($value));
        }
        return ((isset($string)) ? $string : "");
    }

    public function tagPrefix($prefix = null)
    {
        if(!is_string($prefix))
        {
            return $this->tag_prefix;
        }

        $this->tag_prefix_previous = $this->tag_prefix;
        $this->tag_prefix = $prefix;
        return $this;
    }

    private function tagPrefixPrevious()
    {
        return $this->tag_prefix_previous;
    }

    /**
     * Turns the object (and it's children) into a nice pretty lump of HTML
     * @return string
     */
    public function __toString()
    {
        $html = sprintf("%s<%s%s>", $this->tagPrefix(),$this->type(), $this->getFormattedAttributes());
        if($this->closure())
        {
            $innerHTML = "";
            foreach($this->getContent() as $element)
            {
                if($element instanceof self)
                {
                    $old_prefix = $element->tagPrefix();
                    $element->tagPrefix( $this->tagPrefix() . $old_prefix );
                    $innerHTML .= "\n" . $element->__toString();
                    $element->tagPrefix($old_prefix);
                }
                elseif(is_string($element))
                {
                    $innerHTML .= "\n" . $this->tagPrefix() . $this->tagPrefixPrevious() . str_replace("\n","\n" . $this->tagPrefix() . $this->tagPrefixPrevious(),htmlentities($element));
                }
            }
            $html .= sprintf("%s\n%s</%s>",$innerHTML,$this->tagPrefix(),$this->type());
        }
        return $html;
    }
}