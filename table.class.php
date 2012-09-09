<?php
/**
 * This class is a container for an HTML Element definition
 * @author eclipse 
 */
class Element{
    private $type = "";
    private $name = "";
    private $id   = "";
    private $style = "";
    private $class = "";
    /**
     * Function Element is the constructor for the Element Class
     * @param unknown_type $type   The type of element ie (table, td, tr, th etc..)
     * @param unknown_type $name   The name attribute of the element
     * @param unknown_type $id     The ID attribute of the element
     * @param unknown_type $style  The Style attribute of the element
     * @param unknown_type $class  The Class attribute of the element
     * @param array $attrs         An array of extra attributes not covered by the base attrs
     */
    public function Element($type, $name="", $id="", $style="", $class="", array $attrs=array()){
        $this->type = $type;
        $this->name = $name;
        $this->id = $id;
        $this->style = $style;
        $this->class = $class;
        foreach ($attrs as $attr=>$value){
            $this->$attr = $value;
        }
    }
    /**
     * Function update updates the attributes of the given element using the array provided
     * @param array $new_vars
     */
    public function update(array $new_vars=array()){
        foreach($new_vars as $var=>$value){
            $this->$var = $value;        
        }
    }
    /**
     * Function open creats the opening tag of the given element ie <table id="" name="" style="" class="">
     * @param boolean $return If true, will return the HTML instead of echo it, default is false to echo. 
     */
    public function open($return = false){
        $element = "<";
        $element .= $this->type." "; 
        foreach($this as $key=>$value){
            if ($key != "type"){
                $element .= $key."=\"".$value."\" ";
            }
        }
        $element .= ">";
        if (!$return){
            echo $element;
        }else{
            return $element;
        }
    }
    /**
     * Function close; closes the tag ie ( </table> )
     * @param bool $return If true, will return the HTML instead of echo it, default is false to echo
     */
    public function close($return = false){
        $element = "</".$this->type.">";
        if (!$return){
            echo $element;
        }else{
            return $element;
        } 
    }
}

class Table{
    private $table_structure = array();
    private $table_layout = array();
    private $spcr = "    ";
    private $rows_per_page = 50;
    private $sortable = false;
    private $link_location = "bottom";
    /**
     * This is a constructor for the Table class. There are no arguments
     * to instantiate a table instance. By default it will create the basic
     * element styles using $this->layout() 
     */
    function Table(array $config=array()){
        // pass
        $this->config($config);
    }
    /**
     * This function displays the current values of the configurable table options
     */
    public function getConfig(){
        $config_options = array("rows_per_page",
                                "sortable",
                                "link_location");
        foreach($config_options as $option){
            print "Option ".$option."=".$this->$option."<br />";
        }
    }
    /**
     * This function configures the options and style if provided for the table
     * @param array $config
     */
    public function config(array $config=array()){
        $config_options = array("rows_per_page",
                                "sortable",
                                "link_location");
        foreach($config_options as $option){
            if(array_key_exists($option, $config)){
                if (!empty($config[$option]) || ($config[$option] === false) ){
                    $this->$option = $config[$option];
                }
            }
        }
        if (array_key_exists("style",$config)){
            $this->layout($config["style"]);
        }
    }
    /**
     * This function configures the HTML elements that are utilized in the table class
     * the Element class is defined at the top of this file.
     * @param array $styles
     */
    public function layout(array $styles=array()){
        $table_layout = array(
            "table"=> new Element("table"),
            "tr"   => new Element("tr"),
            "td"   => new Element("td"),
            "th"   => new Element("th",'','','',"padding-left: 5px;padding-right: 5px;"),
        	"span" => new Element("span",'','','',"font-size: x-small; margin-top: 10px;")
        );
        foreach ($styles as $element=>$layout){
            if (array_key_exists($element, $table_layout)){
                $table_layout[$element]->update($layout);    
            }
        }
        $this->table_layout = $table_layout;
    }
    /**
     * This function generates the table header for a table with the ability
     * to have sortable headings if the option $this->sortable = true, default is false;
     * @param array $columns
     * @param unknown_type $labels
     */
    private function tableHeader(array $columns, $labels=array()){
        $url = $_SERVER["PHP_SELF"]."?";
        $args = array();
        $defaults = array(
       			"order"=>"",
       			"desc"=>"",
       			"page"=>"1"
       		);
       	$newargs = array();
       	$getargs = $_GET;
       	foreach ($defaults as $key=>$val){
       		if (array_key_exists($key, $defaults)){
       			$getargs[$key] = (!array_key_exists($key, $getargs) || empty($getargs[$key]))? $defaults[$key] : $val;
       		}
       	}
       	
        if(!empty($getargs)){
            if (!empty($getargs["search"])){
                array_push($args,"search=".$getargs["search"]);    
            }
            if (!empty($getargs["field"])){
                array_push($args,"field=".$getargs["field"]);
            }
            if (!empty($getargs["page"])){
                array_push($args,"page=".$getargs["page"]);
            }
        }        
        
        foreach($columns as $column =>$meta){
        	$long_args = $args;
            $spcr = $this->spcr;
       		
            $label = (array_key_exists("label", $meta)) ? $meta["label"] : $column;
            array_push($long_args,"order=".$column);
            $desc = "DESC";
            $icon = "";
            $cols = array_keys($columns);
            if ($column == $getargs["order"] || (!isset($getargs["order"]) && $column == $cols[0])){
                if ($_GET["desc"] == "ASC"){
                    $icon = "&uarr;";
                }else{
                	$desc = "ASC";
                    $icon = "&darr;";
                }
            }
            array_push($long_args,"desc=".$desc);
            $temp_url = $url.implode("&",$long_args);

            echo $spcr.$spcr.$this->table_layout["th"]->open();
            if ($this->sortable){
                echo "<a href=\"".$temp_url."\" >".$label." ".$icon."</a>";
            }else{
                echo $label;
            }
            echo $this->table_layout["th"]->close();    
        }
    }
    
    private function labels($col_order){
        $labels = array();
        foreach($col_order as $column=>$label){
            $l = (!empty($label)) ? $label : $column;
            $labels[$column] = $l;
        }
        return $labels;
    }
    
    private function convertTokens($string, array $tokenArray){
    	$tokens = array();
    	$values = array();
    	foreach($tokenArray as $token=>$value){
    		array_push($tokens, "{".$token."}");
    		array_push($values, $value);
    	}
    	$string = str_replace($tokens, $values, $string);
    	return $string;
    }

    public function showTable(array $data, array $col_order=array()){
        $columns = $col_order;
        $labels = array();
        $spcr = $this->spcr;
        if (count($col_order) < 1){
            $col_order = array();
            foreach(array_keys($data[0]) as $key){
            	$col_order[$key] = array();
            }
        }
        // Start the Table
        echo $this->table_layout["table"]->open();
        echo "\r\n";
        // Print the Head Row
        echo $spcr.$this->table_layout["tr"]->open();
        echo "\r\n";
        $this->tableHeader($col_order);
        echo "\r\n";
        echo $spcr.$this->table_layout["tr"]->close();
        echo "\r\n";
        // Print the table body
        foreach($data as $row){
            echo $spcr.$this->table_layout["tr"]->open();
            echo "\r\n";
            foreach($columns as $column=>$meta){
            	$link = "";
                if (array_key_exists("link",$meta) && (!empty($meta["link"]))){
                	$url = $this->convertTokens($meta["link"], $row);
                	$link = "<a href=\"".$url."\" >";
                }
                echo $spcr.$spcr.$this->table_layout["td"]->open();
                echo $link;
                echo $row[$column];
                echo ($link) ? $link : "";
                echo $this->table_layout["td"]->close();  
            }
            echo $spcr.$this->table_layout["tr"]->close();
            echo "\r\n";
        }
        echo $this->table_layout["table"]->close();
        echo "\r\n";
    }
    
    private function makeURL($base="", array $args, $ret=true){
    	$url = $base."?";
    	$raw_args = array();
    	foreach($args as $arg=>$val){
    		array_push($raw_args, $arg."=".$val);
    	}
    	$url .= implode("&",$raw_args);
    	if ($ret){
    		return $url;
    	}else{
    		echo $url;
    	}
    }
    
    function pageList($max_results, $ret=false){
        $pages = 0;
        $args = $_GET;
        // Remove the page number to be altered later
        unset($args["page"]);
        $current_page = (isset($_GET["page"])) ? $_GET["page"] : 1;
        // Figure out the max number of pages
        if ($max_results > 0){
            $pages = ceil($max_results / $this->rows_per_page);
        }
        /******  build the pagination links ******/
        
        // Set temp args to work with to build urls
        $temp_args = $args;

        // Make url to first page
        $temp_args["page"] = 1;
        $first_url = $this->makeURL($_SERVER["PHP_SELF"],$temp_args);
        // Make url to previous page
        $temp_args["page"] = (($current_page-1) > 0) ? $current_page-1 : 1 ;
        $prev_url = $this->makeURL($_SERVER["PHP_SELF"],$temp_args);
        // Make url to next page
        $temp_args["page"] = (($current_page + 1) <= $pages) ? $current_page+1 : $pages;
        $next_url = $this->makeURL($_SERVER["PHP_SELF"],$temp_args);
        // Make url to Last page
        $temp_args["page"] = $pages;
        $last_url = $this->makeURL($_SERVER["PHP_SELF"],$temp_args);
        $HTML = "";
        // if not on page 1, don't show back links
        if ($current_page > 1) {
           // show << link to go back to page 1
           $HTML .= " <a href='".$first_url."'><<</a> ";
           // get previous page num
           $prevpage = $current_page - 1;
           // show < link to go back to 1 page
           echo $HTML .= " <a href='".$prev_url."'><</a> ";
        } // end if
        
        // range of links to show
        $range = 3;
        
        // loop to show links to range of pages around current page
        for ($i = ($current_page - $range); $i < (($current_page + $range)  + 1); $i++) {            
            $temp_args["page"] = $i;
            $url = $this->makeURL($_SERVER["PHP_SELF"],$temp_args);
            // if it's a valid page number...
            if (($i > 0) && ($i <= $pages)) {
               // if we're on current page...
               if ($i == $current_page) {
                  // 'highlight' it but don't make a link
                  $HTML .= " [<strong>$i</strong>] ";
               // if not current page...
               } else {
                  // make it a link
                  $HTML .= " <a href='".$url."'>$i</a> ";
               } // end else
            } // end if 
        } // end for
        
        // if not on last page, show forward and last page links        
        if ($current_page != $pages) {
            // echo forward link for next page 
           $HTML .= " <a href='".$next_url."'>></a> ";
           // echo forward link for last page
           $HTML .= " <a href='".$last_url."'>>></a> ";
        } // end if
        /****** end build pagination links ******/
        
        if (!$ret){
            echo $HTML;
        }else{
            return $HTML;
        }
    }
    
    function showPagedTable(array $data, $max_results=0, array $col_order = array()){
        $link_bar = $this->pageList($max_results, true);
        if ($this->link_location == "top" || $this->link_location == "both" ){
			echo "<div align=\"right\">";
        	echo $this->table_layout["span"]->open();
            echo $link_bar;
            echo $this->table_layout["span"]->close();
            echo "</div>";
            echo "<br />\n";
        }
        $this->showTable($data, $col_order);
        if ($this->link_location == "bottom" || $this->link_location == "both" ){
        	echo "<div align=\"right\">";
        	echo $this->table_layout["span"]->open();
            echo $link_bar;
            echo $this->table_layout["span"]->open();
            echo "</div>";
            echo "\n";
        }
    }
}
