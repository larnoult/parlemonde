<?php
class UE_XLS extends A_UserExporter{
    public static function getFileExtension(){
        return 'xls';
    }
    
    public static function getFileExtensionDescription(){
        return __('MS Excel Spreadsheet','wp-users-exporter');
    }
    
    protected function printHeader(){
        header('Pragma: public');
        header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
        header("Pragma: no-cache");
        header("Expires: 0");
        header('Content-Transfer-Encoding: none');
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8'); // This should work for IE & Opera
        header("Content-type: application/x-msexcel; charset=UTF-8"); // This should work for the rest
        header('Content-Disposition: attachment; filename='.$this->filename.'.xls');
		
         
        
        echo '<table><tr>';
        foreach ($this->descriptions as $description)
            echo "<th>".htmlentities($description, ENT_QUOTES, 'UTF-8')."</th>";
            
        echo '</tr><tbody>';
    }
    
    protected function printFooter(){
        echo '</tbody></table>';
    }
    
    protected function printUser($user){
        echo '<tr>';
        foreach ($this->cols as $col){
           $data = htmlentities($user->$col, ENT_QUOTES, 'UTF-8');
           echo "<td>$data</td>"; 
        }
        echo '</tr>';
    }
}
