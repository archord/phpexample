

<?php

require_once "fun/PHP_MYSQL.php";
require_once "fun/ReadDataFromIni.php";

class DataManage {

    private $index = 0;
    private $db_host;
    private $db_database;
    private $db_user;
    private $db_password;

    private $dbconnect;

    function init(){

        $config = new ReadDataFromIni();
        $config->load('fun/config.ini');
        $this->db_host = $config->get('db_host');
        $this->db_database = $config->get('db_database');
        $this->db_user = $config->get('db_user');
        $this->db_password = $config->get('db_password');


        $this->dbconnect = new CMySql($this->db_database);
        $this->dbconnect->connect($this->db_database, $this->db_user, $this->db_password, $this->db_host);
    }

    //colum1:time colum2:object's temperature
    function getData($table, $colum1, $colum2, $startTime, $endTime, $cond){
        
        $colums = 'UNIX_TIMESTAMP('.$colum1.')*1000,'.$colum2;
        $condition = $colum1.'>=\''.$startTime.'\' and '.$colum1.'<\''.$endTime.'\' ';
        if($cond!=null)
            $condition = $condition.' and '.$cond;

//        echo $colums.'<br>';
//        echo $condition.'<br>';
        $resultSet = $this->dbconnect->select($colums, $condition, '', $table);
        $number = mysql_num_rows($resultSet);
        //echo $number.'<br>';
        $DOMData = '[';
        $index = $this->index;
        while ($row = mysql_fetch_array($resultSet)) {
            $DOMData = $DOMData . '['. $row[0] . ',' . $row[1] . '],';
        }
        $DOMData = substr($DOMData, 0, strlen($DOMData) - 1) . ']';
        return $DOMData;
    }

    function getDataFromDB1() {

        $resultSet = $this->dbconnect->select('UNIX_TIMESTAMP(time)*1000,cin', 'time>=\'2012-05-04 \' and time<\'2012-05-05\'', '', 'templog');

        $DOMData = '[';
        $index = $this->index;
        while ($row = mysql_fetch_array($resultSet)) {
            $DOMData = $DOMData . '['. $row[0] . ',' . $row[1] . '],';
        }
        $DOMData = substr($DOMData, 0, strlen($DOMData) - 1) . ']';
        return $DOMData;
    }

    function getDataFromDB2() {

        $resultSet = $this->dbconnect->select('UNIX_TIMESTAMP(time)*1000,cout', 'time>\'2012-05-04 \' and time<=\'2012-05-05\'', '', 'templog');

        $DOMData = '[';
        $index = $this->index;
        while ($row = mysql_fetch_array($resultSet)) {
            $DOMData = $DOMData . '['. $row[0] . ',' . $row[1] . '],';
        }
        $DOMData = substr($DOMData, 0, strlen($DOMData) - 1) . ']';
        return $DOMData;
    }

    function dbClose(){
        $this->dbconnect->close();
    }


    function initData() {

        $resultSet = $this->dbconnect->select('indx', '', '', 'record_indx');
        while ($row = mysql_fetch_array($resultSet)) {
            $index = $row['indx'] + 10;
            if ($index > 1900)
                $index = 0;
            $this->index = $index;
        }
    }

    function writeBack() {
        $array['indx'] = $this->index;
        $this->dbconnect->update($array, '', 'record_indx');
    }

    //
    function randomData() {

        $datas = Array();
        $prev = 50;
        for ($i = 0; $i < 0; $i++) {
            $datas['id'] = $i + 1;
            if ($i == 0) {
                $datas['num'] = $prev;
            } else {
                $prev = $prev + rand(0, 10) - 5;
                if ($prev > 100)
                    $prev = 100;
                else if ($prev < 0)
                    $prev = 0;
                else
                    $datas['num'] = $prev;
            }
            $this->dbconnect->insert($datas, 'table1');
        }
    }
    
}
?>

