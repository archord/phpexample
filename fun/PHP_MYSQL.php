<?php	
	//使用UTF-8
	/**
	 * Module: MySql Class Module v1.1
	 * author: leaf
	 * email: yuyuyezi@vip.qq.com
	 * time: 2012-02-16 16:11
	 * last-modify-time: 2012-02-17 15:37
	 * copyright: All Free
	 */
	//if(!defined('DEF_INCLUDE')) exit('Access Denied');
?>
<?php
	//默认的连接参数
	define('DB_HOST','localhost');
	define('DB_NAME','test');
	define('DB_USER','root');
	define('DB_PASSWORD','123456');
	define('DEFAULT_CHASET','UTF8');
	//define('SORT_UPDATE_ASC',0);
	//define('SORT_UPDATE_DESC',1);
?>
<?php

/**
 * 获取规格化的列名称列表
 *
 * @param mix $fields
 * @return array
 */
function get_column_names($fields ){
	$fieldResult= array();	//存放结果
	if(!is_array($fields)){		//不是数组的处理
		$fields= str_replace(',',' ',$fields);
		$fields= explode(' ',$fields);
		foreach($fields as $field){
			if(trim($field) != null && !in_array($field, $fieldResult)){ //不添加重复的field value
				$fieldResult[]= $field;
			}
		}
		return $fieldResult;
	}else{
		foreach( $fields as $field){
			$arr_field= get_column_names($field);
			foreach($arr_field as $field){
				if(!in_array($field, $fieldResult)){	//不添加重复的field value
					$fieldResult[]= $field;
				}
			}
		}
		return $fieldResult;
	}
}

////class define code
class CMySql{
	private $conn= null;	//连接资源
	private $connect= false;//引用的数据库连接标记&
	private $referenceCount= 1;//引用计数器

	private $result= null;	//查询结果资源
	private $table= null;	//当前表
	private $describe= null;//连接描述
	private	$sql= null;		//最后执行的SQL


	private $db_host;		//用于重新连接数据库的参数
	private $db_name;		//
	private $db_user;		//
	private $db_password;	//
	private $db_chaset;		//

	////set Functions
	/**
	 * clone 方法已被禁用。请使用 getClone()来取代
	 *
	 */
	function __clone(){
		exit("can't be clone. using \$mysql->getClone('\$describe') instead");
	}
	
	/**
	 * __construct
	 *
	 * @param string $_describe
	 */
	function __construct($describe){
		$this->describe= $describe;
	 }

	 function __destruct(){
		$this->close();
	 }
	 
	/**
	 * 关闭当前连接
	 *
	 */
	 function close(){
	 	//free private vars
	 	$this->freeResult();
	 	$this->sql= null;
	 	$this->describe= null;
	 	$this->table= null;
		//free reference vars
	 	$this->cleanReferenceData();
	 }

	/**
	 * 检查当前引用数据连接是否被其它Mysql所引用，此方法不导出。
	 *
	 * @return boolean
	 */
	private function checkReference(){
		if($this->referenceCount >1){
			return true;
		}
		return false;
	}
	
	/**
	 * debug
	 *
	 */
	function debug(){
		$result= array();
		if($this->checkReference()){
			$result['isReference']= "true";
		}
		else{
			$result['isReference']= "false";
		}

		$result['conn']=$this->conn;
		$result['connect']=$this->connect;
		$result['referenceCount']=$this->referenceCount;
	
		$result['result']=$this->result;
		$result['table']=$this->table;
		$result['describe']=$this->describe;
		$result['sql']=$this->sql;

		$result['db_host']=$this->db_host;
		$result['db_name']=$this->db_name;
		$result['db_user']=$this->db_user;
		$result['db_password']=$this->db_password;
		$result['db_chaset']=$this->db_chaset;
		
		print_r($result);
	}
	/**
	 * 清理引用的共享数据
	 *
	 * @return true
	 */
	//function unsetReference(){
	private function cleanReferenceData(){
		if($this->checkReference()){
			$this->referenceCount --;		//计数器减一
			unset($this->conn);				//释放引用的数据
			unset($this->referenceCount);	//释放引用的数据
			unset($this->connect);			//释放引用的数据
		}else{
			if($this->isConnected() ){
				mysql_close($this->conn);
			}
		}
		$this->referenceCount= 1;		//referenceCount 重置
		$this->sql= null;
 		$this->conn= null;
 		$this->connect= false;
		return true;
	}


	////set&get functions

	/**
	 * 设置数据连接相关描述，用于错误信息显示。
	 *
	 * @param string $describe
	 */
	function setDescribe($describe){
		$this->describe= $describe;
	}
	
	/**
	 * 设置默认的表名
	 *
	 * @param string $table
	 * @return boolean
	 */
	function setTable($table){
		if($table != null){
			$this->table= $table;
		}
		return true;
	}

	////get Functions
	function getDbName(){
		return $this->db_name;
	}
	function &getConn(){
		if($this->isConnected()){
			return $this->conn;
		}
		return null;
	}
	function getDescribe(){
		return $this->describe;
	}

	function getLastSql(){
		return $this->sql;
	}

	function getTable($table= null){
		if($table){
			$this->table= $table;
		}
		if($this->table){
			return $this->table;
		}
		exit('no table selected error');	
	}

	/**
	 * 数据库是否连接
	 *
	 * @return boolean
	 */
	function isConnected(){
		
		if($this->connect && is_resource($this->conn)){
			return true;
		}
		return false;
	}

	/**
	 * 连接数据库
	 * 如果指定数据库名，则尝试连接指定数据库。置空则忽略连接数据库步骤，不设置则尝试连接默认数据库
	 *
	 * @param string $_dbName 数据库名
	 * @param string $_db_user mysql登录用户名
	 * @param string $_db_password mysql登录密码
	 * @param string $_db_host mysql主机
	 * @return boolean 成功返回 true，失败返回false
	 */
	function connect($db_name= DB_NAME, $db_user= DB_USER, $db_password= DB_PASSWORD, $db_host= DB_HOST){
		
		//echo "try to connect<br>";
		$this->close();//重新连接，则先断开原来的连接关系
		$this->conn= mysql_connect($db_host, $db_user, $db_password, true)or die('error at connect datebase');
		if(!$this->conn) return false;

		$this->connect= true;
		$this->db_host= $db_host;
		$this->db_user= $db_user;
		$this->db_name= $db_name;
		$this->db_password= $db_password;

		//$this->setDefaultCharset();
		//设置默认的字符集
		$this->query('SET NAMES '.DEFAULT_CHASET);
		$this->db_chaset= DEFAULT_CHASET;

		if($db_name != null){
			$this->selectDb($db_name);
		}
		return true;
	}
	
	/**
	 * 重新连接
	 *
	 * @return boolean
	 */
	private function reConnect(){

		$db_name= $this->db_name;
		$db_user= $this->db_user;
		$db_password= $this->db_password;
		$db_host= $this->db_host;
		$chaset= $this->db_chaset;

		$result= $this->connect($db_name, $db_user, $db_password, $db_host);
		if($result){
			//$this->setCharset($chaset );
			//设置字符集
			$this->query('SET NAMES '.$chaset);
			$this->db_chaset= $chaset;
		}
		return $result;
	}


	/**
	 * 选择数据库
	 *
	 * @param string $db_name 数据库名
	 * @return boolean
	 */
	function selectDb($db_name= DB_NAME){
		if(!$this->isConnected()){
			return false;
		}

		$this->freeResult();
		$this->sql= null;
		$this->table= null;
		
		if($this->checkReference()){
			$this->cleanReferenceData();
			$this->reConnect();
		}
		$result= mysql_select_db($db_name, $this->conn) or die("error at selectDb: '{$db_name}'");
		if($result) {
			$this->db_name= $db_name;
			return $result;
		}
		return false;
	}

	/**
	 * 获取 mysql连接类的克隆对象。该方法将取代 clone方法
	 *
	 * @param string $describe 新的描述
	 * @return Mysql
	 */
	function &getClone($describe= null){
		if($describe == null){
			//$describe= $this->describe.'copy of '.$this->referenceCount;
			$describe= "{$this->describe}[{$this->referenceCount}]";
		}
		$newMySql = new Mysql($describe);//新MySql类
		$this->referenceCount ++;//更新引用计数
		
		$newMySql->conn= &$this->conn;	//建立引用关联
		$newMySql->referenceCount= &$this->referenceCount;
		$newMySql->connect= &$this->connect;
		
		$newMySql->describe= $describe;
		$newMySql->db_host= $this->db_host;
		$newMySql->db_name= $this->db_name;
		$newMySql->db_user= $this->db_user;
		$newMySql->db_password= $this->db_password;
		$newMySql->db_chaset= $this->db_chaset;

		return $newMySql;
		
	}


	/**
	 * 执行一条sql语句，需之前正确连接数据库
	 * 注意：如果执行的是select语句，则返回的记录集自动保存在成员变量$this->result中
	 *
	 * @param string $sql 执行的sql字符串
	 * @return mix
	 */
	function query($sql){
		if( $this->isConnected()){
			$this->sql= $sql;
			$result= mysql_query($sql, $this->conn)
				or die("[error at {$this->describe}][query sql:'{$this->sql}']");
			if(is_resource($result)){
				$this->result= $result;
			}
			return $result;
		}
		exit($this->describe.'[unable conn]');
	}
	/**
	 * 执行一条sql语句。此为 query()的同名方法
	 *
	 * @param string $_sql
	 * @return mix
	 */
	function execute($sql){
		return $this->query($sql);
	}

	/**
	 * 查询一条SQL语句并返回
	 * 如果为select则返回第一条结果，但此方法将不会自动保存select结果集
	 *
	 * @param string $sql
	 * @param int $result_type
	 * @return mix
	 */
	function queryOnce($sql ,$result_type= MYSQL_ASSOC){//查询一条SQL语句，如果为select则返回第一条结果
		if( $this->isConnected()){
			$this->sql= $sql;
			$result= mysql_query($sql, $this->conn)
				or die("[error at {$this->describe}][query sql:'{$this->sql}']");
			if(is_resource($result)){
				$result= mysql_fetch_array($result,$result_type);
			}
			return $result;
		}
		exit($this->describe.'[unable $conn]');
	}

	/**
	 * 获取select查询数据 返回 查询的索引数组
	 *
	 * @param int $result_type 索引数组类型：MYSQL_BOTH/MYSQL_ASSOC/MYSQL_NUM
	 * @return array 成功返回数组，失败返回false
	 */
	function fetchArray($result_type= MYSQL_BOTH ){
		if($this->isConnected() && is_resource($this->result)){
			return mysql_fetch_array( $this->result, $result_type);
		}
		return false;
	}
	/**
	 * 获取select查询数据 返回 查询的关联索引的数组
	 *
	 * @return array 成功返回数组，失败返回false
	 */
	function fetchAssoc(){
		if($this->isConnected() && is_resource($this->result)){
			return mysql_fetch_assoc( $this->result);//MYSQL_ASSOC
		}
		return false;
	}
	/**
	 * 获取select查询数据 返回 查询的数字索引的数组
	 *
	 * @return array 成功返回数组，失败返回false
	 */
	function fetchNum(){
		if($this->isConnected() && is_resource($this->result)){
			return mysql_fetch_array( $this->result, MYSQL_NUM);
		}
		return false;
	}

	 /**
	  * 释放当前的结果集
	  *
	  * @return boolean 成功返回true
	  */
	 function freeResult(){
		if($this->isConnected() && is_resource($this->result)){
			$result= mysql_free_result($this->result);
			$this->result = null;
			return $result;
		}
		return true;
	}

	/**
	 * 操作影响的数据行数
	 * 此方法应在执行select/update之后调用
	 *
	 * @return int
	 */
	function affectedRows(){
		if($this->isConnected()){
			return mysql_affected_rows($this->conn);
		}
		return null;
	}
	
	/**
	 * 获取select之后的结果集中数量
	 *
	 * @return int
	 */
	function numRows(){
		if( is_resource($this->result)){
			$num= mysql_num_rows($this->result);
			return $num;
		}
		return null;
	}
	
	/**
	 * 连接状态，状态数组信息参见 mysql_stat()
	 *
	 * @return boolean 连接正常返回状态数组，已断开返回 false
	 */
	 function stat(){
		if($this->isConnected()){
			return mysql_stat($this->conn);
		}
		return false;
	 }
	 
	 /**
	  * 检测服务器连接，如果没有连接则重新连接
	  *
	  * @return boolean 成功返回true，失败返回false
	  */
	  function ping(){
		if($this->isConnected()){
			return mysql_ping($this->conn);
		}
		return null;
	  }
	


	/**
	 * 最新插入的数据后，AUTO_INCREMENT 的 ID号
	 * 该方法需在执行insert操作之后使用
	 *
	 * @return int 失败返回 0
	 */
	function insertId(){
		if($this->isConnected()){
			return mysql_insert_id($this->conn);
		}
		return 0;
	}
	/**
	 * 返回无 Limit结果的数据数目。仅用于select sql_calc_rows查询之后
	 *
	 * @return int
	 */
	function foundRows(){//仅用于select sql_calc_rows查询之后
		$result= $this->queryOnce("SELECT FOUND_ROWS()",MYSQL_NUM);
		if($result) return $result[0];
		return false;
	}

	///set char set functions
	/**
	 * 1.设置字符集
	 *
	 * @param string $_chaset
	 * @return 成功返回true
	 */
	function setCharset($chaset= DEFAULT_CHASET){
		if($this->isConnected()){
			if($this->checkReference()){
				$this->cleanReferenceData();
				$this->reConnect();
			}
			$this->query('SET NAMES '.$chaset);
			$this->db_chaset= $chaset;
			return true;
		}
		
		return false;
	}
	/**
	 * 2.设置数据连接字符集为 utf-8
	 * 注意，如果有多个Mysql类共用同一个数据连接，该方法将导致当前Mysql类的连接从原连接分离
	 * @return boolean true
	 */
	function setCharsetUtf8(){
		return $this->setCharset('UTF8');
	}
	/**
	 * 3.设置为默认的字符集。
	 * 注意，如果有多个Mysql类共用同一个数据连接，该方法将导致当前Mysql类的连接从原连接分离。
	 *
	 * @return boolean
	 */
	function setDefaultCharset(){
		return $this->setCharset(DEFAULT_CHASET);
	}

	
	//sort functions

	/**
	 * 1.根据主键出现的先后顺序，重新排序数据表中的数据
	 * 注意：仅支持排序ID的升序更新，如果是降序读取，更新的时候需先反转主键列表
	 *
	 * @param mix $primaryKeys 主键列表
	 * @param string $table 操作的数据表名
	 * @param string $sortField 排序列名
	 * @param string $primaryField 主键列名
	 * @return boolean 成功返回true，失败返回false
	 */
	function sortUpdate($primaryKeys,$table= null,$sortField='sortId',$primaryField= 'id'/*, $order_by= SORT_UPDATE_ASC*/){
		if(!$this->isConnected()) return false;
		$table= $this->getTable($table);

		$primaryKeys= get_column_names($primaryKeys);//反序查找的需要反序更新
		/*if($order_by == SORT_UPDATE_DESC ){
			$primaryKeys= array_reverse($primaryKeys);
		}else if($order_by != SORT_UPDATE_ASC ){
			exit();
		}*/
		$newPrimaryKeySort= $primaryKeys;	//新的排序
		$primaryKeys= implode(',',$primaryKeys);
		$sortSql= "select {$primaryField},{$sortField} from {$table} where {$primaryField} in ({$primaryKeys}) order by {$sortField} asc, {$primaryField} asc";
		$this->query($sortSql);

		$findedPrimaryKeys= array();	//查找到的数据ID
		$updateItem= array();			//查找到的数据
		while(!!$result= $this->fetchArray(MYSQL_ASSOC)){	//整理查找到的记录结果
			$findedPrimaryKeys[]= $result[$primaryField];	//记录找到的ID，用于方便检索
			$updateItem[]= $result;							//记录数据
		}

		$keyIndex= 0;	//查找到的数据ID索引初始化
		foreach($newPrimaryKeySort as $sortKey){			
			if(in_array($sortKey,$findedPrimaryKeys)){		//可以查找到的有效ID
				if($sortKey != $findedPrimaryKeys[$keyIndex]){//排序不正确的才需要更新
					$update[$sortField]= $updateItem[$keyIndex][$sortField];
					$this->update($update,"{$primaryField}= '{$sortKey}'",$table);
				}
				$keyIndex++;	//索引自加
			}
		}
		return true;
	}

	/**
	 * 2.对数据表中指定的数据的排序顺序进行上移/下移调整
	 * offset>0则向后移动该数据；offset<0则向前移动该数据。同时更新所有受影响的数据排序
	 * 仅支持排序列的升序更新，如果需要降序的更新，请把offset取负
	 * 不支持排序列存在重复的情况。为了避免此问题，请总是使用此方法排序，不要手动修改数据库。此方法已完全隐蔽排序列数据。
	 * 
	 * @param int $primaryKey 主键
	 * @param int $offset 数据位移值，0则不移动
	 * @param string $table  操作的数据表
	 * @param string $sortField 排序列名
	 * @param string $primaryField 主键列名
	 * @return boolean 成功返回true，失败返回false
	 */
	function sort($primaryKey,$offset,$table= null,$sortField='sortId',$primaryField='id'){
		if(!$this->isConnected()) return false;
		$table= $this->getTable($table);
		$sortSql= "select {$primaryField},{$sortField} from {$table} where {$primaryField}='{$primaryKey}' LIMIT 1";
		
		if(!$result= $this->queryOnce($sortSql)){
			return false;//未找到此ID数据
		}
		$offset= intval($offset);
		$sortOrder= $result[$sortField];
		$limiter = ($offset>0)? ($offset+1):(1- $offset);
		if($offset <0 ){
			$sortSql= "select {$primaryField},{$sortField} from {$table} where $sortField <= '{$sortOrder}' order by {$sortField} desc, {$primaryField} desc limit {$limiter}";
		}elseif($offset > 0){
			$sortSql= "select {$primaryField},{$sortField} from {$table} where $sortField >= '{$sortOrder}' order by {$sortField} asc, {$primaryField} asc limit {$limiter}";
		}else{
			return true;//0无须排序
		}

		$this->query($sortSql);
		$sortArray= array();
		$result= $this->fetchAssoc();	//如果数据库无错误，则第一条记录为要查找的数据ID（通常都能找到，除非数据库数据不正确）
		if($result[$primaryField] == $primaryKey){	//find the id
			$sortArray[]= $result[$primaryField];	//save
		}else{
			//重复的sortID导致查找失败，或者未找到的数据记录
			return false;
		}
		//获取剩下的数据记录
		while(!!$result= $this->fetchAssoc()){
			$sortArray[]= $result[$primaryField];
		}

		$head= array_shift($sortArray);	//移除队列头元素（查找的元素本身）
		array_push($sortArray,$head);	//将查找元素添加到数组末尾

		if($offset <0){//反序查找的数据需要反序排序
			$sortArray= array_reverse($sortArray);
		}
		if(!empty($sortArray)){
			return $this->sortUpdate($sortArray,$table,$sortField,$primaryField);
		}
		return false;
	}

	//
	/**
	 * 简化的select sql语句封装
	 *
	 * @param mix $field_names 选择的列，数组或以','分开的选项列字符串 
	 * @param string $condition 条件字符串
	 * @param string $parameters 附加参数字符串 limit、order by 等
	 * @param string $table 操作的表名
	 * @return resource
	 */
	function select($field_names= '*', $condition= null, $parameters= null, $table= null){
		if(!$this->isConnected()){
			return false;
		}
		$table= $this->getTable($table);

		$field_names= get_column_names($field_names);
		$field_names= implode(', ',$field_names);
		if(empty($field_names)) $field_names= '*';

		$condition= trim($condition);
		if(!empty($condition)){
			$condition= ' where '.$condition;
		}

		//Make sql
		$sql= "select {$field_names} from {$table} {$condition} {$parameters}";
		$result= $this->query($sql);
		if(is_resource($result)){
			$this->result= $result;
		}
		return $result;
	}

	/**
	 * 简化的insert语句操作封装
	 *
	 * @param mix $arr_fields 插入的数据数组 insert[fieldname]=fieldvalue。如果数据较少可以直接写字符串
	 * @param string $table 操作的数据表名
	 * @return boolean 返回insert操作的结果
	 */
	function insert($arr_fields, $table= null){
		if(!$this->isConnected()) return false;
		$table= $this->getTable($table);
		$field_values= null;
		if(is_array($arr_fields)){
			$arr_keys= array();
			$arr_values= array();
			foreach($arr_fields as $key => $value ){
				array_push($arr_keys, $key);
				array_push($arr_values, "'{$value}'");
			}
			$keys= implode(', ', $arr_keys);
			$values= implode(', ', $arr_values);
			$field_values= "({$keys}) values ({$values})";
		}else{
			$field_values= $arr_fields;
		}

		//Make sql
		$sql= "insert into {$table} {$field_values}";
		return $this->query($sql);
	}

	/**
	 * 简化的 update操作封装
	 *
	 * @param mix $arr_fields 更新的列数组
	 * @param string $condition 更新的条件
	 * @param string $table 操作的数据表名
	 * @return boolean 返回操作结果
	 */
	function update($arr_fields, $condition, $table= null){
		if(!$this->isConnected()){
			return false;
		}
		$table= $this->getTable($table);

		$field_values= null;
		if(is_array($arr_fields)){
			$fields_array= array();
			foreach($arr_fields as $key => $value ){
				array_push($fields_array, "{$key}= '{$value}'");
			}
			$field_values= implode(', ', $fields_array);
		}else{
			$field_values= $arr_fields;
		}
		
		$condition= trim($condition);
		if(!empty($condition)){
			$condition= ' where '.$condition;
		}

		$sql= "update {$table} set {$field_values} {$condition}";
		return $this->query($sql);
	}

	/**
	 * 简化的 delete操作封装
	 *
	 * @param string $condition 删除的条件字符串
	 * @param string $table 删除数据的表名
	 * @return boolean
	 */
	function delete($condition, $table= null){
		if(!$this->isConnected()) return false;
		$table= $this->getTable($table);

		$condition= trim($condition);
		if(!empty($condition)){
			$condition= ' where '.$condition;
		}
		$sql= "delete from {$table} {$condition}";
		return $this->query($sql);
	}
}