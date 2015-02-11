<?php namespace GErcoli\HTMLTags;

/**
 * A class designed to build an HTML tag (or tree of tags) in an OOP way.
 * @author      Garry Ercoli <Garry@GErcoli.com>
 * @package     GErcoli\HTMLTags
 * @version     0.1
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
     * Create an instance of an HTMLTag given the element/tag type
     * @param   string  $tagType
     * @param   bool    $requiresClosure
     */
    public function __construct($tagType = "div", $requiresClosure = false)
    {
        $this->type($tagType);
        $this->closure  = ($requiresClosure === true) ? true :  false;
    }

    /**
     * Gets the value of an attributeName
     * @param   String  $attributeName
     * @return  String|null
     */
    public function getAttribute($attributeName)
    {
        if(isset($this->attributes[strtolower($attributeName)]))
        {
            return $this->attributes[strtolower($attributeName)];
        }
        return null;
    }

    /**
     * Sets the given attribute to the supplied value, overwriting it if it already exists.
     * @param   String  $attributeName
     * @param   String  $value
     * @return  void
     */
    public function setAttribute($attributeName, $value)
    {
        $this->attributes[strtolower($attributeName)] = trim($value);
    }

    /**
     * Removes an attribute by its case-insensitive name
     * @param   String  $attributeName
     */
    public function removeAttribute($attributeName)
    {
        unset($this->attributes[strtolower($attributeName)]);
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
            foreach(explode(" ", $this->getAttribute("class")) as $class)
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

        return preg_replace('!\s+!', ' ', trim($this->getAttribute("class")));
    }

    /**
     * Add a class to the tag
     * @param   string  $className
     * @return  bool
     */
    public function addClass($className)
    {
        if(!$this->hasClass($className))
        {
            $this->setAttribute("class",$this->getClasses() . " " . preg_replace('!\s+!', ' ', trim($className)));
            return true;
        }
        return false;
    }

    /**
     * Removes a single class from the list of classes.
     * @param   String  $className
     */
    public function removeClass($className)
    {
        $old_classes = explode(" ", $this->getAttribute("class"));

        $this->removeAttribute("class");

        foreach($old_classes as $class)
        {
            if(strtolower($class) == strtolower($className) || strlen(trim($class)) < 1)
            {
                continue;
            }
            $this->addClass($class);
        }

    }

    /**
     * Adds a string or HTMLTag to the contents of this tag - this will auto-enable the closure tag.
     * @param   String|HTMLTag  $content
     * @return  bool
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

        return true;
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
     * @return  null|void
     */
    public function type($newType = null)
    {
        if($newType === null || strlen($newType) == 0)
        {
            return $this->type;
        }

        $this->type = strtolower($newType);
    }

    /**
     * Declare if this html tag needs a closing tag or not.
     * @param   null|bool   $closureRequired
     * @return  bool|void
     */
    public function closure($closureRequired = null)
    {
        if($closureRequired !== true && $closureRequired !== false)
        {
            return $this->closure;
        }

        $this->closure = $closureRequired;
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
    
    /**
     * Turns the object (and it's children) into a nice pretty lump of HTML
     * @return string
     */
    public function __toString()
    {
        $html = sprintf("<%s%s>", $this->type(), $this->getFormattedAttributes());
        if($this->closure())
        {
            $innerHTML = "";
            foreach($this->getContent() as $element)
            {
                if($element instanceof self)
                {
                    $innerHTML .= $element->__toString();
                }
                elseif(is_string($element))
                {
                    $innerHTML .= htmlentities($element);
                }
            }
            $html .= sprintf("%s</%s>",$innerHTML,$this->type());
        }
        return $html;
    }
}