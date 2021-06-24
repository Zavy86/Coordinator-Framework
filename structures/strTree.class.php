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
	//protected $class;
	protected $leafs_array;

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
		//$this->class=$class;
		$this->leafs_array=array();
	}

	/**
	 * Add Leaf
	 *
	 * @param string $content Content data
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $id Field ID, if null randomly generated
	 * @return boolean
	 */
	function addLeaf($content,$class=null,$style=null,$tags=null,$id=null){


		$leaf_tree_obj=new strTree($content,$class=null,$style=null,$tags=null,$id=null);

		$this->leafs_array[]=$leaf_tree_obj;

		return $leaf_tree_obj;
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

		if($main){
			$return.=" <tr>\n";
			$return.="  <td>";
			$return.="   <div class='leaf'>";
			$return.=$this->content;
			$return.="</div>\n";
			$return.="  </td>\n";
			$return.=" </tr>\n";
		}


		$return.=" <tr>\n";

		$count=0;
		foreach($this->leafs_array as $leaf_fobj){
			//$return.=$leaf_fobj->render(count($this->leafs_array));


			$count++;

			// open leaf
			$return.="  <td>\n";

			// show up branch
			if($count>1){$top_left=" top";}else{$top_left=NULL;}
			if($count<count($this->leafs_array)){$top_right=" top";}else{$top_right=NULL;}
			$return.="<table><tr><td class='width-50 right".$top_left."'>&nbsp;</td><td class='width-50".$top_right."'>&nbsp;</td></tr></table>";

			// show leaf
			if($main){$div_class="active";}else{$div_class=NULL;}
			$return.="   <div class='leaf ".$div_class."'>";
			$return.=$leaf_fobj->content;
			$return.="</div>\n";

			if(count($leaf_fobj->leafs_array)){
				$return.="   <table><tr><td class='width-50 right'>&nbsp;</td><td class='width-50'>&nbsp;</td></tr></table>\n";
			}

			//$leafs_return.=$leaf_fobj->render(count($this->leafs_array));
			$return.=$leaf_fobj->render(false);

			// close leaf
			$return.="  </td>\n";

		}


		$return.=" <tr>\n";

		//$return.=$leafs_return;




		$return.="</table><!-- tree_".$this->id." -->\n<br>\n";

		// return HTML code
		return $return;
	}


	/**
	 * Renderize leaf objects
	 *
	 * @return string HTML source code
	 */
	public function render_leaf($childrens){
		$return="";

		$leafs_return="";

		if(!count($this->leafs_array)){return null;}

		$return.=" <tr>\n  <td colspan='".$childrens."'>a$childrens\n";
		$return.="   <table><tr><td class='width-50 right'>&nbsp;L</td><td class='width-50'>&nbsp;R</td></tr></table>\n";
		$return.="  </td>\n </tr>\n";


		$return.=" <tr>\n";

		$count=0;
		foreach($this->leafs_array as $leaf_fobj){
			//$return.=$leaf_fobj->render(count($this->leafs_array));


			$count++;

			// open leaf
			$return.="  <td>$count\n";

			// show up branch
			if($count>1){$top_left=" top";}else{$top_left=NULL;}
			if($count<count($this->leafs_array)){$top_right=" top";}else{$top_right=NULL;}
			//if($level>0){
			$return.="<table><tr><td class='width-50 right".$top_left."'>&nbsp;L</td><td class='width-50".$top_right."'>&nbsp;R</td></tr></table>";
			//}

			// show leaf
			//if($level==0){$div_class="active";}else{$div_class=NULL;}
			$return.="   <div class='leaf ".$div_class."'>";
			$return.=$childrens.$leaf_fobj->content;
			$return.="</div>\n";

			// sub branchs
			//if(count($group->groups)){
			//	groups_tree_table($group->groups,($level+1),0,$max);
			//}

			$leafs_return.=$leaf_fobj->render_leaf(count($this->leafs_array));

			// close leaf
			$return.="  </td>\n";

		}


		$return.=" <tr>\n";

		$return.=$leafs_return;


		// return HTML code
		return $return;
	}

}
