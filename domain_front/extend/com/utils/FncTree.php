<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace com\utils;

/**
 * 通用函数库
 * @author oliver
 *
 */
class FncTree{
	/**
	 * 数组转成树结构,树结构仍然是数组，数组的key值为数据库中的id
	 * @param unknown_type $list
	 * @param unknown_type $id
	 * @param unknown_type $pid
	 * @param unknown_type $children
	 * @param unknown_type $root
	 * @return multitype:unknown
	 */
	public static function listToTree($list, $id = 'id', $pid = 'pid', $children = '_child', $root=0) {
		$tree = array();// 创建Tree
		if(is_array($list)) {
			// 创建基于主键的数组引用
			$refer = array();
			foreach ($list as $key => $value) {
				$refer[$value[$id]] =& $list[$key];
			}
			foreach ($list as $key => $value) {
				// 判断是否存在parent
				$parentId = $value[$pid];
				if ($root == $parentId) {
					$tree[$value[$id]] =& $list[$key];
				}else{
					if (isset($refer[$parentId])) {
						$parent =& $refer[$parentId];
						$parent[$children][$list[$key][$id]] =& $list[$key];
					}
				}
			}
		}
		return $tree;
	}
	
	/**
	 * 数组转成树结构,树结构仍然是数组，数组的key为隐式
	 * @param unknown_type $list
	 * @param unknown_type $id
	 * @param unknown_type $pid
	 * @param unknown_type $children
	 * @param unknown_type $root
	 * @return multitype:unknown
	 */
	public static function listToTreeNoKey($list, $id = 'id', $pid = 'pid', $children = '_child', $root=0) {
		$tree = array();// 创建Tree
		if(is_array($list)) {
			// 创建基于主键的数组引用
			$refer = array();
			foreach ($list as $key => $value) {
				$refer[$value[$id]] =& $list[$key];
			}
			foreach ($list as $key => $value) {
				// 判断是否存在parent
				$parentId = $value[$pid];
				if ($root == $parentId) {
					$tree[] =& $list[$key];
				}else{
					if (isset($refer[$parentId])) {
						$parent =& $refer[$parentId];
						$parent[$children][] =& $list[$key];
					}
				}
			}
		}
		return $tree;
	}
	
	/**
	 * 将树结构还原成list有序结构，根据key值排序
	 * @param unknown_type $tree
	 * @param unknown_type $child
	 * @param unknown_type $order
	 * @param unknown_type $list
	 * @return Ambigous <multitype:, boolean, multitype:array >
	 */
	public static function treeToList($tree, $child = '_child', $order='id', &$list = array()){
		if(is_array($tree)) {
			foreach ($tree as $key => $value) {
				$reffer = $value;
				if(isset($reffer[$child])){
					unset($reffer[$child]);
					self::treeToList($value[$child], $child, $order, $list);
				}
				$list[] = $reffer;
			}
			$list = self::listSortBy($list, $order, $sortby='asc');
		}
		return $list;
	}
	
	/**
	 * 将数组转化成瀑布树形结构
	 * @param unknown_type $tree
	 * @param unknown_type $id
	 * @param unknown_type $pid
	 * @param unknown_type $children
	 * @param unknown_type $level
	 * @param unknown_type $cls
	 * @return Ambigous <number, multitype:string >
	 */
	public static function listToTreefalls($tree,$id = 'id', $pid = 'pid',$children = '_child',$level=1,$cls='') {
		$treefalls = array();
		foreach ($tree as $key => $value) {
			if(isset($value[$children])){
				$treesub = $value[$children];
				unset($tree[$key][$children]);
				$tree[$key]['level']= $level;
				$tree[$key]['cls']= 'pid_'.$tree[$key][$pid].' '.$cls;
				$treefalls[$key]=&$tree[$key];
					
				$treefalls = $treefalls+self::listToTreefalls($treesub,$id,$pid,$children,$level+1,$tree[$key]['cls']);
	
			}else{
				$tree[$key]['level']= $level;
				$tree[$key]['cls']= 'pid_'.$tree[$key][$pid].' '.$cls;
				$treefalls[$key]=&$tree[$key];
			}
		}
		return $treefalls;
	}
	/**
	 * 显示树形结构，和listToTreefalls搭配使用
	 * @param unknown_type $list
	 * @param unknown_type $lefthtml
	 * @param unknown_type $pid
	 * @param unknown_type $level
	 * @param unknown_type $leftpin
	 * @return Ambigous <multitype:, multitype:string >
	 */
	public static function showTree($list , $lefthtml = '─' , $pid=0 , $level=0, $leftpin=0 ){
		$arr=array();
		foreach ($list as $v){
			if($v['pid']==$pid){
				$v['lvl']=$level + 1;
				$v['leftpin']=$leftpin + 0;//左边距
				$v['lefthtml']='├'.str_repeat($lefthtml,$level);
				$arr[]=$v;
				$arr= array_merge($arr,self::showTree($list,$lefthtml,$v['id'],$level+1 , $leftpin+20));
			}
		}
		return $arr;
	}
	
	/**
	 * 获得树形结构指定节点的所有子孙节点id
	 * @param unknown_type $tree_node
	 * @param unknown_type $id
	 * @param unknown_type $pid
	 * @param unknown_type $children
	 * @return Ambigous <number, multitype:unknown >
	 */
	public static function getChildrenkeys($tree_node,$id = 'id', $pid = 'pid',$children = '_child'){
		$id_list = array();
		if(isset($tree_node[$children])){
			foreach($tree_node[$children] as $key=>$value){
				$id_list[$key]=$key;
				$id_list = $id_list+self::getChildrenkeys($value);
			}
		}
		return $id_list;
	}
	
	/**
	 * 对结果集进行排序
	 * @param unknown_type $list
	 * @param unknown_type $field
	 * @param unknown_type $sortby
	 * @return multitype:unknown |boolean
	 */
	public static function  listSortBy($list,$field, $sortby='asc') {
		if(is_array($list)){
			$refer = $resultSet = array();
			foreach ($list as $i => $data)
				$refer[$i] = &$data[$field];
			switch ($sortby) {
				case 'asc': // 正向排序
					asort($refer);
					break;
				case 'desc':// 逆向排序
					arsort($refer);
					break;
				case 'nat': // 自然排序
					natcasesort($refer);
					break;
			}
			foreach ( $refer as $key=> $val)
				$resultSet[] = &$list[$key];
			return $resultSet;
		}
		return false;
	}
}