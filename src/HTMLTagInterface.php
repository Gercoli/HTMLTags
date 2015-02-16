<?php namespace GErcoli\HTMLTags;


interface HTMLTagInterface {

    public function setAttribute($name,$value);
    public function getAttribute($name);
    public function removeAttribute($name);
    public function hasAttribute($name);
    public function listAttributes();

    public function addClass($className);
    public function removeClass($className);
    public function hasClass($className);
    public function getClasses();
    public function clearClasses();

    public function appendContent($content);
    public function prependContent($content);
    public function clearContent();
    public function getContent();

    public function setClosingTag($enable);
    public function getClosingTag();

    public function setXHTMLEncoding($enable);
    public function getXHTMLEncoding();

    public function getFormattedAttributes();
    public function getTagType();

    public function setTagPrefix($prefix);
    public function getTagPrefix();
    public function getPreviousTagPrefix();

    public function __toString();


}