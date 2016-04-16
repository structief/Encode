<?php
  Class DBConnection{
    var $mysqli;
    var $selectStatement = null;
    var $updateStatement = null;
    var $deleteStatement = null;
    var $insertStatement = null;
    var $fromStatement = null;
    var $joinStatement = null;
    var $havingStatement = null;
    var $whereStatement = null;
    var $orderByStatement = null;
    var $groupByStatement = null;
    var $limitStatement = null;
    var $query = null;
    var $type = "select";
    var $debug = false;

    var $results = null;

    public function __construct($debug = false){
      $this->_Connect();
      $this->debug = $debug;
    }

    public function __destruct(){
      $this->_Disconnect();
    }

    public function select($selectStatement){
      $this->type = "select";
      if($this->selectStatement == ""){
        $this->selectStatement = "SELECT " . $selectStatement;
      }else{
        $this->selectStatement .= "," . $selectStatement;
      }

      //Return instance for chaining
      return $this;
    }

    public function query($fullQuery){
      $this->query =  $fullQuery;
      //Return instance for chaining
      return $this;
    }

    public function update($values){
      $this->type = "update";
      $this->updateStatement = " SET ";$check = 0;
      foreach($values as $column => $value){
        $check++;
        $this->updateStatement .= $column . ' = ';
        if(is_bool($value) OR is_int($value) OR is_float($value)){
          $this->updateStatement .= $value;
        }elseif(is_string($value)){
          $value = mysqli_real_escape_string($this->mysqli, $value);
          $this->updateStatement .= "'" . $value . "'";
        }
        if($check < count($values)){
          $this->updateStatement .= ',';
        }
      }

      //Return instance for chaining
      return $this;
    }

    public function delete(){
      $this->type = "delete";
      $this->deleteStatement = "DELETE";
      return $this;
    }

    public function insert($values){
      $this->type = "insert";
      $this->insertStatement = '(';$check = 0;
      foreach($values as $column => $value){
        $check++;
        $this->insertStatement .= $column;
        if($check < count($values)){
          $this->insertStatement .= ',';
        }
      }
      $this->insertStatement .= ') VALUES(';$check = 0;
      foreach($values as $column => $value){
        $check++;
        if(is_bool($value) OR is_int($value) OR is_float($value)){
          $this->insertStatement .= $value;
        }elseif(is_string($value)){
          $value = mysqli_real_escape_string($this->mysqli, $value);
          $this->insertStatement .= "'" . $value . "'";
        }
        if($check < count($values)){
          $this->insertStatement .= ',';
        }
      }
      $this->insertStatement .= ')';

      //Return instance for chaining
      return $this;
    }

    public function from($fromTable, $alias = null){
      switch($this->type){
        case 'select':
        case 'delete':
          if($this->fromStatement == ""){
            $this->fromStatement = " FROM " . DB_PREFIX . $fromTable;
            if($alias != null){
              $this->fromStatement .= " " . $alias;
            }
          }else{
            $this->fromStatement .= ", " . DB_PREFIX . $fromTable;
            if($alias != null){
              $this->fromStatement .= " " . $alias;
            }
          }
          break;
        case 'update':
          $temp = "UPDATE " . DB_PREFIX . $fromTable;
          if($alias != NULL){
            $temp .= " " . $alias;
          }
          $this->updateStatement = $temp . $this->updateStatement;
          break;
      }

      //Return instance for chaining
      return $this;
    }

    public function into($intoTable, $alias = null){
      $temp = "INSERT INTO " . DB_PREFIX . $intoTable;
      if($alias != NULL){
        $temp .= " " . $alias;
      }
      $this->insertStatement = $temp . $this->insertStatement;

      //Return instance for chaining
      return $this;
    }

    public function join($joinTable, $on, $joinType = null){
      if($joinType != null && (in_array($joinType, array("left", "Left", "LEFT", "right", "Right", "RIGHT", "inner", "Inner", "INNER", "outer", "Outer", "OUTER")))){
        $this->joinStatement .= " " . $joinType;
      }
      $this->joinStatement .= " JOIN " . DB_PREFIX . $joinTable . " ON " . $on;

      //Return instance for chaining
      return $this;
    }

    public function where($where){
      if($this->whereStatement == ""){
        $this->whereStatement = " WHERE " . $where;
      }else{
        $this->whereStatement .= " AND " . $where;
      }

      //Return instance for chaining
      return $this;
    }

    public function having($having){
      if($this->havingStatement == ""){
        $this->havingStatement = " HAVING " . $having;
      }else{
        $this->havingStatement .= " AND " . $having;
      }

      //Return instance for chaining
      return $this;
    }

    public function orderBy($orderBy, $type = "ASC"){
      if($this->orderByStatement == ""){
        $this->orderByStatement = " ORDER BY " . $orderBy . " " . $type;
      }else{
        $this->orderByStatement .= ", " . $orderBy . " " . $type;
      }

      //Return instance for chaining
      return $this;
    }

    public function groupBy($groupBy){
      if($this->groupByStatement == ""){
        $this->groupByStatement = " GROUP BY " . $groupBy;
      }else{
        $this->groupByStatement .= ", " . $groupBy;
      }

      //Return instance for chaining
      return $this;
    }

    public function limit($start, $end){
      $this->limitStatement = " LIMIT " . $start . "," . $end;

      //Return instance for chaining
      return $this;
    }

    public function createTable($tableName, $tableOptions, $keys, $ifNotExist){
      $query = "CREATE TABLE ";
      if($ifNotExist){
        $query .= "IF NOT EXISTS ";
      }
      $query .= "`" . DB_PREFIX . $tableName . "` (";

      //OPTIONS
      foreach($tableOptions as $key => $value) {
        //type(length) bv int(32)
        $query.= "`" . $key . "` " . $value[0] . "(" . $value[1] . ") ";

        //NULL?
        if($value[3]){
          if($value[2] == null){
            $query .= "DEFAULT ";
          }
          $query .= "NULL ";
        }else{
          $query .= "NOT NULL ";
        }

        //Default value
        if($value[2] !== null){
          $query .= "DEFAULT `" . $value[2] . "` ";
        }

        //Auto increment
        if($value[4]){
          $query .= "AUTO_INCREMENT";
        }

        $query .= ",";
      }

      //KEYS
      foreach($keys as $type => $key){
        switch(strtolower($type)){
          case 'primary':
            $query .= "PRIMARY KEY (";
            foreach($key as $value){
              $query .= "`" . $value . "`,";
            }

            //Remove ','
            $query = substr($query, 0, -1);

            $query .= "),";
            break;
          case 'unique':
            break;
        }
      }

      //remove ','
      $query = substr($query, 0, -1);

      $query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
      
      $this->results = $this->mysqli->query($query);

      return $this;
    }


    public function execute(){
      if(!$this->query){
        $statements = array("selectStatement", "updateStatement", "insertStatement", "deleteStatement");
        $extraClauses = array("fromStatement", "joinStatement", "whereStatement", "havingStatement", "groupByStatement", "orderByStatement", "limitStatement");
        $this->query = "";$break = false;
        foreach($statements as $statement){
          if(isset($this->$statement) AND $this->$statement != null){
            $this->query .= $this->$statement;
            if($statement == "insertStatement"){
              $break = true;
            }
            break;
          }
        }
        if($break == false){
          foreach($extraClauses as $extra){
            $this->query .= $this->$extra;
          }
        }
      }

      if($this->debug){
        echo $this->query;
      }



      $this->results = $this->mysqli->query($this->query);

      if($this->results === false){
        //Catch the error
        $error = $this->mysqli->error;
        //Catch the executing file and line number
        $bt =  debug_backtrace();
        //Trigger the error;

        $c = new \Encode\Controller();
        $c->error->trigger(515, ["error" => $error, "query" => $this->query], $bt[0]['file'], $bt[0]['line']);
      }

      //Automatic clearance of query
      $this->clear();

      //Return instance for chaining
      return $this;
    }

    public function clear(){
      $this->selectStatement = null;
      $this->updateStatement = null;
      $this->deleteStatement = null;
      $this->insertStatement = null;
      $this->fromStatement = null;
      $this->joinStatement = null;
      $this->whereStatement = null;
      $this->havingStatement = null;
      $this->orderByStatement = null;
      $this->groupByStatement = null;
      $this->limitStatement = null;
      $this->query = null;
      $this->result = null;

      //Return instance for chaining
      return $this;
    }

    public function fetch_array(){
      $temp = array();

      while($row = mysqli_fetch_array($this->results)){
        $t = array();
        foreach($row as $column => $value){
          if(!is_int($column)){
            $t[$column] = $value;
          }
        }
        array_push($temp, $t);
      }

      return $temp;
    }

    public function fetch_object(){
      $temp = new stdClass();$i=0;
    
      while($row = mysqli_fetch_array($this->results)){
        $t = new stdClass();
        foreach($row as $column => $value){
          $t->$column = $value;
        }
        $temp->$i = $t;
        $i++;
      }
      return $temp;
    }

    public function fetch_one(){
       while($row = mysqli_fetch_array($this->results)){
        $t = array();
        foreach($row as $column => $value){
          if(!is_int($column)){
            $t[$column] = $value;
          }
        }
        return $t;
      }
    }

    public function affected_rows(){
      return $this->mysqli->affected_rows;
    }

    public function getLastInsertedId(){
      return $this->mysqli->insert_id;
    }

    //Private functions, keep your hands off 'em, needed for db-connection and stuff.
    
    private function _Connect(){
        $this->mysqli = new mysqli(HOST,NAME,PSWD,DBNAME);
        if($this->mysqli->connect_error){ 
          $error = new Error();
          $error->trigger('503', $this->mysqli->connect_error);
        }
    }

    private function _Disconnect(){
      $this->mysqli->close();
    }

  }
?>