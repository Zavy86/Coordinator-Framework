<?php
/**
 * Table
 *
 * Coordinator Structure Class for Tables
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Table structure class
 */
class strTable{

	/** Properties */
	protected $id;
	protected $emptyrow;
	protected $class;
	protected $caption;
	protected $fixColumns;
	protected $rows_array;
	protected $current_row;

	/** @todo fare funzioni aggiuntive ( sortable, checkboxes, movable */

	/**
	 * Table structure class
	 *
	 * @param string $emptyrow Text to show if no results
	 * @param string $class CSS class
	 * @param string $caption Table caption
	 * @param string $id Table ID, if null randomly generated
	 * @return boolean
	 */
	public function __construct($emptyrow=null,$class=null,$caption=null,$id=null){
		$this->id="table_".($id?$id:api_random());
		$this->emptyrow=$emptyrow;
		$this->class=$class;
		$this->caption=$caption;
		$this->fixColumns=0;
		$this->current_row=0;
		$this->rows_array=array();
		// initialize headers row array
		$this->rows_array["headers"]=array();
		return true;
	}

	/**
	 * Add Table Header
	 *
	 * @param string $label Label
	 * @param string $class CSS class
	 * @param string $width Width
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $order Query field for order
	 * @return boolean
	 */
	public function addHeader($label,$class=null,$width=null,$style=null,$tags=null){
		if(!$label){return false;}
		// build header object
		$th=new stdClass();
		$th->label=$label;
		$th->class=$class;
		$th->width=$width;
		$th->style=$style;
		$th->tags=$tags;
		// add header to headers
		$this->rows_array["headers"][]=$th;
		return true;
	}

	/**
	 * Add Table Row Field Action
	 *
	 * @param string $url Action URL
	 * @param string $icon Button icon
	 * @param string $label Button label
	 * @param string $confirm Confirmation popuop
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @return boolean
	 */
	function addHeaderAction($url,$icon,$label,$confirm=null,$class=null,$style=null,$tags=null,$target=null){
		if(!$url){echo "ERROR - Table->addHeaderAction - URL is required";return false;}
		if(!$label){$label="&nbsp;";}
		// build field object
		$th=new stdClass();
		$th->label=api_link($url,api_icon($icon,$label,"hidden-link"),null,"btn btn-default btn-xs",false,$confirm,null,null,$target);
		$th->class=$class;
		$th->style=$style;
		$th->tags=$tags;
		// add header to headers
		$this->rows_array["headers"][]=$th;
		return true;
	}

	/**
	 * Add Table Row
	 *
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $id Row ID, if null randomly generated
	 * @return boolean
	 */
	public function addRow($class=null,$style=null,$tags=null,$id=null){
		// build row object
		$tr=new stdClass();
		$tr->id="tr_".($id?$id:api_random());
		$tr->class=$class;
		$tr->style=$style;
		$tr->tags=$tags;
		$tr->fields_array=array();
		// add row to table
		$this->current_row++;
		$this->rows_array[$this->current_row]=$tr;
		return true;
	}

	/**
	 * Add Table Row Field
	 *
	 * @param string $content Content data
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $id Field ID, if null randomly generated
	 * @return boolean
	 */
	function addRowField($content,$class=null,$style=null,$tags=null,$id=null){
		if(!$this->current_row){echo "ERROR - Table->addRowField - No row defined";return false;}
		if(!strlen((string)$content)){$content="&nbsp;";}
		// build field object
		$td=new stdClass();
		$td->id="td_".($id?$id:api_random());
		$td->content=$content;
		$td->class=$class;
		$td->style=$style;
		$td->tags=$tags;
		// checks
		if(is_int(strpos((string)$td->class,"truncate-ellipsis"))){$td->content="<span>".$td->content."</span>";}
		// add field to row
		$this->rows_array[$this->current_row]->fields_array[]=$td;
		return true;
	}

	/**
	 * Add Table Row Field Action
	 *
	 * @param string $url Action URL
	 * @param string $icon Button icon
	 * @param string $label Button label
	 * @param string $confirm Confirmation popuop
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $target Link target
	 * @return boolean
	 */
	function addRowFieldAction($url,$icon,$label,$confirm=null,$class=null,$style=null,$tags=null,$target=null){
		if(!$this->current_row){echo "ERROR - Table->addRowFieldAction - No row defined";return false;}
		if(!$url){echo "ERROR - Table->addRowFieldAction - URL is required";return false;}
		if(!$label){$label="&nbsp;";}
		// build field object
		$td=new stdClass();
		//$td->content=api_link($url,$label,null,"btn btn-default btn-xs",false,$confirm);
		$td->content=api_link($url,api_icon($icon,$label,"hidden-link"),null,"btn btn-default btn-xs",false,$confirm,null,null,$target);
		$td->class=$class;
		$td->style=$style;
		$td->tags=$tags;
		// add field to row
		$this->rows_array[$this->current_row]->fields_array[]=$td;
		return true;
	}

	/**
	 * Fix columns
	 *
	 * @param integer $number Number of columns
	 * @return boolean
	 */
	function fixColumns($number=1){
		if(!$number){echo "ERROR - Table->fixColumns - Number is required";return false;}
		// set property
		$this->fixColumns=$number;
		return true;
	}

	/**
	 * Renderize table object
	 *
	 * @return string HTML source code
	 */
	public function render(){
		// make table class
		$table_class="table-responsive";
		if($this->fixColumns){$table_class="sticky-table sticky-ltr-cells table-responsive";}

		// open table
		$return="<!-- table -->\n";
		$return.="<div class=\"".$table_class."\">\n";
		$return.=" <table id=\"".$this->id."\" class=\"table table-striped table-hover table-condensed ".$this->class."\">\n";
		// table caption
		if($this->caption){$return.="  <caption>".$this->caption."</caption>\n";}
		// open head
		if(array_key_exists("headers",$this->rows_array)){
			$return.="  <thead>\n";
			$return.="   <tr>\n";
			// columns count
			$column_count=0;
			// cycle all headers
			foreach($this->rows_array["headers"] as $th){
				// increment counter
				$column_count++;
				// check for fixed columns
				if($this->fixColumns && $column_count<=$this->fixColumns){$th->class.=" sticky-cell";}
				// renderize table headers
				$return.="    <th";
				if($th->class){$return.=" class=\"".$th->class."\"";}
				if($th->width){$return.=" width=\"".$th->width."\"";}
				if($th->style){$return.=" style=\"".$th->style."\"";}
				if($th->tags){$return.=" ".$th->tags;}
				$return.=">".$th->label."</th>\n";
			}
			$return.="   </tr>\n";
			$return.="  </thead>\n";
		}
		// open body
		$return.="  <tbody>\n";
		foreach($this->rows_array as $row_id=>$tr){
			if($row_id=="headers"){continue;}
			// show rows
			$return.="   <tr id=\"".$tr->id."\"";
			if($tr->class){$return.=" class=\"".$tr->class."\"";}
			if($tr->style){$return.=" style=\"".$tr->style."\"";}
			if($tr->tags){$return.=" ".$tr->tags."";}
			$return.=">\n";
			// columns count
			$column_count=0;
			// cycle all row fields
			foreach($tr->fields_array as $td){
				// increment counter
				$column_count++;
				// check for fixed columns
				if($this->fixColumns && $column_count<=$this->fixColumns){$td->class.=" sticky-cell";}
				// show field
				$return.="    <td id=\"".$td->id."\"";
				if($td->class){$return.=" class=\"".$td->class."\"";}
				if($td->style){$return.=" style=\"".$td->style."\"";}
				if($td->tags){$return.=" ".$td->tags."";}
				$return.=">".$td->content."</td>\n";
			}
			$return.="   </tr>\n";
		}
		// show empty row text
		if(count($this->rows_array)==1 && $this->emptyrow){
			$return.="   <tr><td colspan=".count($this->rows_array["headers"]).">".$this->emptyrow."</td></tr>\n";
		}
		// closures
		$return.="  </tbody>\n";
		$return.=" </table>\n";
		$return.=" </div><!-- /table-responsive -->\n";
		// return HTML code
		return $return;
	}

}
