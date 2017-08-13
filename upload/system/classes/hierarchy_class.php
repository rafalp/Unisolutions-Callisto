<?php

/**
 * @author Rafal Pawel Piton <rafio.xudb@gmail.com>
 * @copyright Copyright Rafal Piton (c) 2008
 * @version 1.0 2008-10-09 22:03
 * @link http://www.unisolutions.pl Unisolutions
 * @link http://www.rpiton.com My Homepage
 * 
 **/

/**
 * HOW IT WORKS?
 * 
 * This class is static. You dont have to create any instances of it, only to include its definition in your script.
 * The key is use of "buildTree" method. It has tree params:
 * 
 * Array to reorder - it must be specific array, for example:
 * 
 * 		$array[0][1] = array( 'label' => 'Some item with ID 1');
 * 		$array[0][2] = array( 'label' => 'Some item with ID 2');
 * 		$array[1][3] = array( 'label' => 'Some item with ID 2, being child of item with ID: 1');
 *
 * Graphical list - list can be drawed in one of tree methods:
 * 		
 * 		0 - graphical
 * 		1 - text
 * 		2 - text (wthout "|")
 * 
 * Tree image key - function returns sorted array, with each item having additional key. If this key collides with already used one, you can use this param, to use custom one.
 * 
 * Example of returned array with customly defined key "tree_image", for text 1 mode
 * 
 * 		$array[1] = array( 'label' => 'Some item with ID 1', 'tree_image' => '');
 * 		$array[3] = array( 'label' => 'Some item with ID 2, being child of item with ID: 1', 'tree_image' => '- -');
 * 		$array[2] = array( 'label' => 'Some item with ID 2', 'tree_image' => '');
 * 
 */

class hierarchy_class{
	
	/**
	 * core references
	 */
		
	private static $style;
	
	/**
	 * depth cut off
	 *
	 * @var integer
	 */
	
	private static $cut_depth = 0;
	
	/**
	 * raw, unordered array
	 *
	 * @var array
	 */
	
	private static $raw_array = array();
	
	/**
	 * finished array
	 *
	 * @var array
	 */
	
	private static $finished_array = array();
	
	/**
	 * tree key
	 *
	 * @var string
	 */
	
	private static $tree_key = '';
	
	/**
	 * tree type
	 *
	 * @var string
	 */
	
	private static $tree_type = 0;
	
	/**
	 * depth key
	 *
	 * @var string
	 */
	
	private static $depth_key = '';
	
	/**
	 * builds tree
	 *
	 * @param array $array_to_order
	 * @param integer $graphical
	 * @param string $tree_key
	 * @param string $depth_key
	 * @return array
	 */
	
	static function buildTree( $array_to_order, $graphical = 0, $tree_key = 'hierarchy_image', $depth_key = 'hierarchy_depth', $cut_depth = 0){
		
		//get singleton?
		if ( $graphical == 0){
			include( FUNCTIONS_GLOBALS);
			self::$style = $style;
		}
		
		//store tree settings
		self::$tree_key = $tree_key;
		self::$depth_key = $depth_key;
		self::$tree_type = $graphical;
		self::$cut_depth = $cut_depth;
		
		//store new array
		self::$raw_array = (array) $array_to_order;
		
		//clear finished array
		self::$finished_array = array();
		
		//run recurrention for root?
		if ( key_exists( 0, self::$raw_array))
			self::recurreTree( 0, array(), 0, true);
		
		//return it
		return self::$finished_array;
		
	}
	
	/**
	 * recurrency function for tree
	 *
	 * @param mixed $parent_id
	 */
	
	private static function recurreTree( $parent_id, $tree_depth = array(), $item_depth, $last_item = false){
				
		//item childs number
		$item_childs = count( self::$raw_array[$parent_id]);
		
		//actual child number
		$actual_child = 0;
				
		//we are not in the root, and depth is bigger than one step?
		if ( $parent_id > 1 && self::$tree_type != 2){
			
			//tree type?
			if ( self::$tree_type == 0){
			
				//draw space?
				if ( $item_depth - self::$cut_depth > 0){
				
					//is it an last item?
					if ( $last_item){
						
						//draw I
						$tree_depth[] = self::$style -> drawImage( 'tree-i');
					
					}else{
						
						//add space
						$tree_depth[] = self::$style -> drawImage( 'tree-s');
					
					}
				
				}else{
					
					//add empty item
					$tree_depth[] = '';
					
				}
				
			}else{
				
				//draw text tree line start
				if ( $item_depth - self::$cut_depth > 0){
				
					$tree_depth[] = '-';
				
				}else{
					
					$tree_depth[] = '';
					
				}
			}
				
		}
				
		//alternative tree
		if ( $parent_id > 0 && self::$tree_type == 2){
		
			//draw text
			$tree_depth[] = '|';
		
		}
		
		//iterate
		foreach ( self::$raw_array[$parent_id] as $array_item_id => $array_item_data){
			
			//increase child number
			$actual_child ++;
			
			//if we are not in root, draw path
			if ( $parent_id > 0){
				
				//build depth and tree
				if ( self::$tree_type == 0){
										
					//draw depth?
					if ( self::$cut_depth == 0 || $item_depth - self::$cut_depth >= 0){
						$array_item_data[self::$tree_key] = join( "", $tree_depth).( $item_childs == $actual_child ? self::$style -> drawImage( 'tree-l') : self::$style -> drawImage( 'tree-t'));
					}else{
						$array_item_data[self::$tree_key] = '';
					}
					
				}else{
					if ( self::$cut_depth == 0 || $item_depth - self::$cut_depth > 0){
						$array_item_data[self::$tree_key] = join( " ", $tree_depth)."-";
					}else{
						$array_item_data[self::$tree_key] = join( " ", $tree_depth)."";	
					}
				}
				
			}else{
				
				//build empty depth
				$array_item_data[self::$tree_key] = '';
				
			}
			
			//set depth	
			$array_item_data[self::$depth_key] = $item_depth;
				
			//add our item to list
			self::$finished_array[$array_item_id] = $array_item_data;
			
			//recure?
			if ( key_exists( $array_item_id, self::$raw_array))
				self::recurreTree( $array_item_id, $tree_depth, $item_depth + 1, ( $item_childs == $actual_child ? false : true));
		
		}
				
	}
	
	/**
	 * orders list to n-depth
	 *
	 * @param array $array_to_order
	 * @param mixed $depth_key
	 * @return array
	 */
	
	static function doOrdering( $array_to_order, $depth_key = 'hierarchy_depth'){
		
		//store ordering settings
		self::$depth_key = $depth_key;
		
		//store new array
		self::$raw_array = (array) $array_to_order;
		
		//clear finished array
		self::$finished_array = array();
		
		//run recurrention for root?
		if ( key_exists( 0, self::$raw_array))
			self::recurreOrder( 0, 0, true);
		
		//return it
		return self::$finished_array;
		
	}
	
	/**
	 * recurrency function for ordering
	 *
	 * @param mixed $parent_id
	 */
	
	private static function recurreOrder( $parent_id, $tree_depth = 0, $last_item = false){
						
		//item childs number
		$item_childs = count( self::$raw_array[$parent_id]);
		
		//actual child number
		$actual_child = 0;
		
		//increase depth
		$tree_depth ++;
		
		//iterate
		foreach ( self::$raw_array[$parent_id] as $array_item_id => $array_item_data){
			
			//increase child number
			$actual_child ++;
				
			//build depth of item
			$array_item_data[self::$depth_key] = $tree_depth;
			
			//add our item to list
			self::$finished_array[$array_item_id] = $array_item_data;
			
			//recure?
			if ( key_exists( $array_item_id, self::$raw_array))
				self::recurreOrder( $array_item_id, $tree_depth, ( $item_childs == $actual_child ? false : true));
		
		}
				
	}
	
}

?>