<?php namespace GErcoli\HTMLTags;

/**
 * A class designed to build an HTML tag (or tree of tags) in an OOP way.
 * @author      Garry Ercoli <Garry@GErcoli.com>
 * @package     GErcoli\HTMLTags
 * @version     v1.0.1
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
     * @return  HTMLTag
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
     * @return  HTMLTag
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
     * @return  HTMLTag
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
            $this->setAttribute("class",trim($classes));
        }

        return $this;
    }

    /**
     * Removes all classes.
     * @return  HTMLTag
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
     * @return  HTMLTag
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

    /**
     * Adds content inside of a tag and places it in the last slot,
     * NOTE: performing this will automatically add a closing tag.
     * @param   self|string   $content
     * @return  HTMLTag
     * @throws  HTMLTagException
     */
    public function appendContent($content)
    {
        if(!is_string($content) && !($content instanceof self))
        {
            throw new HTMLTagException("Only HTMLTags and strings are allowed as content.");
        }

        $this->tag_content[] = $content;

        $this->setClosingTag(true);

        return $this;
    }

    /**
     * Adds content inside of a tag and places it in the first slot,
     * NOTE: performing this will automatically add a closing tag.
     * @param   self|string $content
     * @return  HTMLTag
     * @throws  HTMLTagException
     */
    public function prependContent($content)
    {
        if(!is_string($content) && !($content instanceof self))
        {
            throw new HTMLTagException("Only HTMLTags and strings are allowed as content.");
        }

        array_unshift($this->tag_content,$content);

        $this->setClosingTag(true);

        return $this;
    }

    /**
     * Empties the content, go figure.
     * @return HTMLTag
     */
    public function clearContent()
    {
        // NOTE: Do research to see if we should use unset() instead.
        $this->tag_content = [];
        return $this;
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
     * @return  HTMLTag
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
     * @return  HTMLTag
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

    /**
     * Sets the string that should precede a tag
     * @param   string|null $prefix
     * @return  HTMLTag
     * @throws  HTMLTagException
     */
    public function setTagPrefix($prefix)
    {
        if(!is_string($prefix) && $prefix !== null)
        {
            throw new HTMLTagException("The tag prefix must be a string or null.");
        }

        $this->tag_prefix_previous = $this->getTagPrefix();
        $this->tag_prefix = $prefix;

        return $this;

    }

    /**
     * The string that should prefix a tag.
     * @return string
     */
    public function getTagPrefix()
    {
        return $this->tag_prefix;
    }

    /**
     * The last known tag prefix.
     * @return  string
     */
    public function getPreviousTagPrefix()
    {
        return $this->tag_prefix_previous;
    }

    /**
     * Your standard __toString(), it allows recursive rendering so,
     * sub-tags inside of the content will also be rendered.
     * @return  string
     */
    public function __toString()
    {
        return $this->render();
    }

    public function render($indents = 0)
    {
        $self_closing = ($this->getXHTMLEncoding() && !$this->getClosingTag()) ? ' /' : '';
        $rtn = sprintf(
            "%s<%s%s%s>",
            str_repeat($this->getTagPrefix(),$indents),
            $this->getTagType(),
            $this->getFormattedAttributes(),
            $self_closing
        );

        for($i = 0; $i < ($t = count($this->getContent())); $i++)
        {
            $content = $this->getContent()[$i];

            if(is_string($content))
            {


                $prefix = ($t > 1) ? "\n" . str_repeat($this->getTagPrefix(),$indents + 1) : '' ;
                $rtn .= $prefix . htmlentities($content);
            }
            elseif ($content instanceof self)
            {
                $content->setTagPrefix($this->getTagPrefix());
                //$rtn .= (($t > 1) ? "\n" : '') . $content->render($indents + 1);
                $rtn .= "\n" . $content->render($indents + 1);
                $content->setTagPrefix($content->getPreviousTagPrefix());
                if($i == 0 && $t == 1)
                {
                    $rtn .= "\n";
                }
            }
            else
            {
                throw new HTMLTagException("Encountered a non-string and non-HTMLTag.");
            }

        }

        if($this->getClosingTag())
        {
            $rtn .= sprintf(
                "%s</%s>",
                ($t > 1) ? "\n" . str_repeat($this->getTagPrefix(),$indents) : '',
                $this->getTagType()
            );
        }

        return $rtn;


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
        $this->tag_type = $tag_type;

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