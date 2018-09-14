<?php
class CycloneSlider_Plugin implements ArrayAccess {
    protected $contents;
    
    public function __construct() {
        $this->contents = array();
    }
    
    // ArrayAccess functions
    public function offsetSet($offset, $value) {
        $this->contents[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->contents[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->contents[$offset]);
    }

    public function offsetGet($offset) {
        if( is_callable($this->contents[$offset]) ){
            return $this->contents[$offset]( $this );
        }
        return isset($this->contents[$offset]) ? $this->contents[$offset] : null;
    }
    
    public function run(){
        $this->contents = apply_filters('cyclone_slider_services', $this->contents); // Deprecated. Maintained for BC only.
        $this->contents = apply_filters('cycloneslider_services', $this->contents);
        // Loop on contents
        foreach($this->contents as $key=>$content){
            if( is_callable($content) ){
                $content = $this[$key];
            }
            if( is_object($content) ){
                $reflection = new ReflectionClass($content);
                if($reflection->hasMethod('inject')){
                    $content->inject( $this ); // Inject our container
                }
                if($reflection->hasMethod('run')){
                    $content->run(); // Call run method on object
                }
            }
        }
    }
}