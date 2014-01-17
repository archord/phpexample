<?php
    require_once "fun/DataManage.php";
    $id = $_GET['id'];
    $startTime = '2012-05-04';
    $endTime = '2012-05-05';

    $table  = array('templog','templog','ccdlog','ccdlog');
    $name = array('CIN','COUT','CCDTEMP','CCDTEMP');
    $colum1 = array('time','time','time','time');
    $colum2 = array('CIN','COUT','CCDTEMP-273.15','CCDTEMP-273.15');
    $cond   = array(null,null,'settemp=0',null);
    $data1 = getData($table,$name,$colum1,$colum2,$startTime, $endTime, $cond);

    /*
    $table  = array('templog','templog','templog');
    $name = array('CPU','CAPTURE','DISK');
    $colum1 = array('time','time','time');
    $cond   = array(null,null,null);
    $data2 = getData($table,$name,$colum1,$name,$startTime, $endTime, $cond);

    $table  = array('templog','templog','templog','templog','templog','templog');
    $name = array('P1CPU','P1DISK','P1RAID','P2CPU','P2DISK','P2RAID');
    $colum1 = array('time','time','time','time','time','time');
    $cond   = array(null,null,null,null,null,null);
    $data3 = getData($table,$name,$colum1,$name,$startTime, $endTime, $cond);

    $table  = array('array1log','array1log','array1log','array1log','array1log','array1log');
    $name = array('CPUTEMP','RAIDTEMP','DISK1TEMP','DISK2TEMP','DISK3TEMP','DISK4TEMP');
    $colum1 = array('time','time','time','time','time','time');
    $cond   = array(null,null,null,null,null,null);
    $data4 = getData($table,$name,$colum1,$name,$startTime, $endTime, $cond);

    $table  = array('array2log','array2log','array2log','array2log','array2log','array2log');
    $name = array('CPUTEMP','RAIDTEMP','DISK1TEMP','DISK2TEMP','DISK3TEMP','DISK4TEMP');
    $colum1 = array('time','time','time','time','time','time');
    $cond   = array(null,null,null,null,null,null);
    $data5 = getData($table,$name,$colum1,$name,$startTime, $endTime, $cond);

    $table  = array('ccdlog','ccdlog','ccdlog','ccdlog','ccdlog','ccdlog','ccdlog','ccdlog');
    $name = array('BACKTEMP','B1TEMP','B2TEMP','B3TEMP','B4TEMP','B5TEMP','B6TEMP','IFTEMP');
    $colum1 = array('time','time','time','time','time','time','time','time');
    $cond   = array(null,null,null,null,null,null,null,null);
    $data6 = getData($table,$name,$colum1,$name,$startTime, $endTime, $cond);

    $table  = array('ccdlog','ccdlog','ccdlog','ccdlog');
    $name = array('PA5VA_A','PA5VD_A','PB15V_A','PB30V_A');
    $colum1 = array('time','time','time','time');
    $cond   = array(null,null,null,null);
    $data7 = getData($table,$name,$colum1,$name,$startTime, $endTime, $cond);

    $table  = array('ccdlog','ccdlog','ccdlog','ccdlog');
    $name = array('PA5VA_V','PA5VD_V','PB15V_V','PB30V_V');
    $colum1 = array('time','time','time','time');
    $cond   = array(null,null,null,null);
    $data8 = getData($table,$name,$colum1,$name,$startTime, $endTime, $cond);
    */

    //echo ':['.$data1.','.$data2.','.$data3.','.$data4.','.$data5.','.$data6.','.$data7.','.$data8.']:';
    echo ':['.$data1.']:';

    function getData($table, $name, $colum1, $colum2, $startTime, $endTime, $cond){

        $dbconnect = new DataManage();
        $dbconnect->init();

        $rstStr = '[';
        $arrayL = count($colum1);
        for($i=0; $i<$arrayL; $i++){
            $data = $dbconnect->getData($table[$i], $colum1[$i], $colum2[$i], $startTime, $endTime,$cond[$i]);
            $rstStr = $rstStr.'[\''.$name[$i].'\','.$data.']';
            if($i!=$arrayL-1){
                $rstStr = $rstStr.',';
            }
        }
        $rstStr = $rstStr.']';
        $dbconnect->dbClose();
        return $rstStr;
    }

    function getData1($startTime, $endTime, $timeType){
        $dbconnect = new DataManage();
        $dbconnect->init();
        $showData1 = $dbconnect->getData('templog', 'time', 'CIN', $startTime, $endTime,null);
        $showData2 = $dbconnect->getData('templog', 'time', 'COUT', $startTime, $endTime,null);
        $showData3 = $dbconnect->getData('ccdlog', 'time', 'CCDTEMP-273.15', $startTime, $endTime, 'settemp=0');
        $showData4 = $dbconnect->getData('ccdlog', 'time', 'CCDTEMP-273.15', $startTime, $endTime, null);
        $dbconnect->dbClose();
        return '[[\'CIN\','.$showData1.'],[\'COUT\','.$showData2.'],[\'CCDTEMP OFF\','.$showData3.'],[\'CCDTEMP ON\','.$showData4.']]';
    }
    
?>
