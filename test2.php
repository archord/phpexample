<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        require_once "fun/DataManage.php";
        $dbconnect = new DataManage();
        $dbconnect->init();
        echo $dbconnect->getData('templog', 'time', 'cin', '2012-05-04', '2012-05-05');
        $dbconnect->dbClose();
        ?>
    </body>
</html>
