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
	public function render($main=true,$ident=0){
		// make ident spaces
		$is=str_repeat(" ",$ident);
		// open tree
		$return=$is."<!-- tree_".$this->id." -->\n";
		$return.=$is."<table class=\"tree ".$this->class."\">\n";
		// check for main rendering
		if($main){
			//$return.=" <tr><td><div class=\"node ".$this->class."\">".$this->content."</div></td></tr>\n";
			$return.=$is." <tr>\n";
			$return.=$is."  <td colspan='".count($this->nodes_array)."'>\n";
			$return.=$is."   <div class=\"node ".$this->class."\"".($this->width?" style=\"width:".$this->width."px;\"":null).">".$this->content."</div>\n";
			$return.=$is."   <table><tr><td class='width-50 right'>&nbsp;</td><td class='width-50'>&nbsp;</td></tr></table>\n";
			$return.=$is."  </td>\n";
			$return.=$is." </tr>\n";
		}
		// open nodes
		$return.=$is." <tr>\n";
		// reset counter
		$count=0;
		// cycle all nodes
		foreach($this->nodes_array as $node_fobj){
			// increment counter
			$count++;
			// open node
			$return.=$is."  <td>\n";
			// show up branch
			if($count>1){$top_left=" top";}else{$top_left=NULL;}
			if($count<count($this->nodes_array)){$top_right=" top";}else{$top_right=NULL;}
			$return.=$is."   <table><tr><td class='width-50 right".$top_left."'>&nbsp;</td><td class='width-50".$top_right."'>&nbsp;</td></tr></table>\n";
			// show node
			$return.=$is."   <div class=\"node ".$node_fobj->class."\"".($this->width?" style=\"width:".$this->width."px;\"":null).">".$node_fobj->content."</div>\n";
			// check for node nodes
			if(count($node_fobj->nodes_array)){
				$return.=$is."   <table><tr><td class='width-50 right'>&nbsp;</td><td class='width-50'>&nbsp;</td></tr></table>\n";
			}
			// renderize node
			$return.=$node_fobj->render(false,$ident+3);
			// close node
			$return.=$is."  </td>\n";
		}
		// close nodes
		$return.=$is." <tr>\n";
		// close tree
		$return.=$is."</table><!-- tree_".$this->id." -->\n";
		// check for main
		if($main){$return.=$is."<br>";}
		// return HTML code
		return $return;
	}

}
