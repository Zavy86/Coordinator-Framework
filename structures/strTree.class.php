<?php
/**
 * Tree
 *
 * Coordinator Structure Class for Trees
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Tree structure class
 */
class strTree{

	/** Properties */
	protected $id;
	protected $content;
	protected $class;
	protected $width;
	protected $nodes_array;

	/**
	 * Tree structure class
	 *
	 * @param string $content Content data
	 * @param string $class CSS class
	 * @param integer $width Node width
	 * @param string $id Tree ID, if null randomly generated
	 * @return boolean
	 */
	public function __construct($content,$class=null,$width=null,$id=null){
		if(!strlen($content)){$content="&nbsp;";}
		$this->id="tree_".($id?$id:api_random());
		$this->content=$content;
		$this->class=$class;
		$this->width=$width;
		$this->nodes_array=array();
	}

	/**
	 * Add Node
	 *
	 * @param string $content Content data
	 * @param string $class CSS class
	 * @param integer $width Node width
	 * @param string $id Field ID, if null randomly generated
	 * @return boolean
	 */
	function addNode($content,$class=null,$width=null,$id=null){
		// build new tree
		$node_tree_obj=new strTree($content,$class,$width,$id);
		// add tree to nodes array
		$this->nodes_array[]=$node_tree_obj;
		// return new tree
		return $node_tree_obj;
	}

	/**
	 * Renderize tree object
	 *
	 * @return string HTML source code
	 */
	public function render($main=true){
		// open tree
		$return="<!-- tree_".$this->id." -->\n";
		$return.="<table class=\"tree ".$this->class."\">\n";
		// check for main rendering
		if($main){
			//$return.=" <tr><td><div class=\"node ".$this->class."\">".$this->content."</div></td></tr>\n";
			$return.=" <tr>\n  <td colspan='".count($this->nodes_array)."'>\n";
			$return.="   <div class=\"node ".$this->class."\"".($this->width?" style=\"width:".$this->width."px;\"":null).">".$this->content."</div>\n";
			$return.="   <table><tr><td class='width-50 right'>&nbsp;</td><td class='width-50'>&nbsp;</td></tr></table>\n";
			$return.="  </td>\n </tr>\n";
		}
		// open nodes
		$return.=" <tr>\n";
		// reset counter
		$count=0;
		// cycle all nodes
		foreach($this->nodes_array as $node_fobj){
			// increment counter
			$count++;
			// open node
			$return.="  <td>\n";
			// show up branch
			if($count>1){$top_left=" top";}else{$top_left=NULL;}
			if($count<count($this->nodes_array)){$top_right=" top";}else{$top_right=NULL;}
			$return.="   <table><tr><td class='width-50 right".$top_left."'>&nbsp;</td><td class='width-50".$top_right."'>&nbsp;</td></tr></table>\n";
			// show node
			$return.="   <div class=\"node ".$node_fobj->class."\"".($this->width?" style=\"width:".$this->width."px;\"":null).">".$node_fobj->content."</div>\n";
			// check for node nodes
			if(count($node_fobj->nodes_array)){
				$return.="   <table><tr><td class='width-50 right'>&nbsp;</td><td class='width-50'>&nbsp;</td></tr></table>\n";
			}
			// renderize node
			$return.=$node_fobj->render(false);
			// close node
			$return.="  </td>\n";
		}
		// close nodes
		$return.=" <tr>\n";
		// close tree
		$return.="</table><!-- tree_".$this->id." -->\n<br>\n";
		// return HTML code
		return $return;
	}

}
