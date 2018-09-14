<?php
class UE_CSV extends A_UserExporter{
    protected $first_line = true;
    public static function getFileExtension(){
        return 'csv';
    }
    
    public static function getFileExtensionDescription(){
        return __('Comma-separated values','wp-users-exporter');
    }
    
    public static function getOptions($replace = true){
        $options = parent::getOptions();
        
        if($replace){
            if($options['field-delimiter'] == "\\t")
                $options['field-delimiter'] = "\t";
            if($options['field-delimiter'] == "\\n")
                $options['field-delimiter'] = "\n";
            if($options['field-delimiter'] == "\\r")
                $options['field-delimiter'] = "\r";
            
            if($options['record-delimiter'] == "\\t")
                $options['record-delimiter'] = "\t";
            if($options['record-delimiter'] == "\\n")
                $options['record-delimiter'] = "\n";
            if($options['record-delimiter'] == "\\r")
                $options['record-delimiter'] = "\r";
        }
        return $options;
    }
    
    protected function printHeader(){
        header('Pragma: public');
        header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
        header("Pragma: no-cache");
        header("Expires: 0");
        header('Content-Transfer-Encoding: none');
        header("Content-Type: application/csv") ;
        header('Content-Disposition: attachment; filename='.$this->filename.'.csv');
		
         
        $options = self::getOptions();
        if($options['include-header']){
            $descs = $this->descriptions;
            foreach ($descs as $k=>$desc)
                $descs[$k] = $this->getFormatedValue($desc);
            
            echo implode($options['field-delimiter'], $descs);
            $this->first_line = false;
        }
            
    }
    
    protected function printFooter(){
    }
    
    protected function printUser($user){
        $options = self::getOptions();
        $userdata = array();
        foreach ($this->cols as $col)
           $userdata[] = self::getFormatedValue($user->$col); 
        
        $row = implode($options['field-delimiter'], $userdata);
        if(!$this->first_line)
            echo $options['record-delimiter'];
        echo $row;
        $this->first_line = false;
    }
    
    private function getFormatedValue($value){
        $options = self::getOptions();
        if(is_int(strpos($value, $options['field-delimiter'])) || is_int(strpos($value, $options['record-delimiter'])))
            $value = '"'.str_replace('"', $options['double-quote-escape'].'"', $value).'"';
        
        return $value;
    }
    
    public static function getDefaultOptions(){
        return array(
                'include-header' => true,
                'field-delimiter' => ",",
                'record-delimiter' => "\\n",
                'double-quote-escape' => '"'
            );
    }
    
    public static function getPostedOptions(){
        
        if(isset($_POST[__CLASS__])){
            $result = array();
            $result['include-header'] = isset($_POST[__CLASS__]['include-header']);
            $result['field-delimiter'] = stripslashes($_POST[__CLASS__]['field-delimiter']);
            $result['record-delimiter'] = stripslashes($_POST[__CLASS__]['record-delimiter']);
            $result['double-quote-escape'] = stripslashes($_POST[__CLASS__]['double-quote-escape']);
        }else{
            $result = self::getOptions();
        }
        
        return $result;
    }
    
    
    public static function printOptionsForm(){
        $options = self::getOptions(false);
        
        ?>
        <label><input type='checkbox' name='<?php echo __CLASS__?>[include-header]' <?php if($options['include-header']) echo 'checked="checked"'?> value='1'/> <?php _e('include header', 'wp-users-exporter')?></label><br/>
        <strong>\n = <?php _e('new line', 'wp-users-exporter'); ?>, \t = <?php _e('tab', 'wp-users-exporter'); ?></strong><br />
        <label><?php _e('field delimiter', 'wp-users-exporter')?>: <input type='text' name='<?php echo __CLASS__?>[field-delimiter]' value="<?php echo htmlentities($options['field-delimiter'], ENT_QUOTES, 'UTF-8')?>" /></label><br />
        <label><?php _e('record delimiter', 'wp-users-exporter')?>: <input type='text' name='<?php echo __CLASS__?>[record-delimiter]' value="<?php echo htmlentities($options['record-delimiter'], ENT_QUOTES, 'UTF-8')?>" /></label> <br />
        <label><?php _e('double quote escape', 'wp-users-exporter')?>: <input type='text' name='<?php echo __CLASS__?>[double-quote-escape]' value="<?php echo htmlentities($options['double-quote-escape'], ENT_QUOTES, 'UTF-8')?>" /></label>
        <?php 
    } 
}
