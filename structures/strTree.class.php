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
	protected $style;
	protected $tags;
	protected $nodes_array;

	/**
	 * Tree structure class
	 *
	 * @param string $content Content data
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $id Tree ID, if null randomly generated
	 * @return boolean
	 */
	public function __construct($content,$class=null,$style=null,$tags=null,$id=null){
		if(!strlen($content)){$content="&nbsp;";}
		$this->id="tree_".($id?$id:api_random());
		$this->content=$content;
		$this->class=$class;
		$this->style=$style;
		$this->tags=$tags;
		$this->nodes_array=array();
	}

	/**
	 * Add Node
	 *
	 * @param string $content Content data
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $id Field ID, if null randomly generated
	 * @return boolean
	 */
	function addNode($content,$class=null,$style=null,$tags=null,$id=null){
		// build new tree
		$node_tree_obj=new strTree($content,$class,$style,$tags,$id);
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
			$return.=" <tr><td colspan='".count($this->nodes_array)."'><div class=\"node ".$this->class."\">".$this->content."</div>";
			$return.="  <table><tr><td class='width-50 right'>&nbsp;</td><td class='width-50'>&nbsp;</td></tr></table>\n";
			$return.=" </td></tr>\n";
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
			$return.="<table><tr><td class='width-50 right".$top_left."'>&nbsp;</td><td class='width-50".$top_right."'>&nbsp;</td></tr></table>";
			// show node
			$return.="   <div class='node ".$node_fobj->class."'>";
			$return.=$node_fobj->content;
			$return.="   </div>\n";
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
