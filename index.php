<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>AST3状态实时监控系统Beta1</title>
    <link href="css/layout.css" rel="stylesheet" type="text/css"/>
    <script language="javascript" type="text/javascript" src="js/jquery.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.flot.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.flot.axislabels.js"></script>
    <script language="javascript" type="text/javascript" src="js/excanvas.compiled.js"></script>
    <script language="javascript" type="text/javascript" src="js/ajax.js"></script>
    <script language="javascript" type="text/javascript" src="js/My97DatePicker/WdatePicker.js"></script>
    <script type="text/javascript" src="js/jquery.validVal.js"></script>
    <script type="text/javascript" src="js/jquery.validVal-customValidations.js"></script>
    <script type="text/javascript" src="js/jquery.validVal-debugger.js"></script>
    <script type="text/javascript" src="js/checkbox.valid.js"></script>
    <script type="text/javascript">
      $(function() {
        $('form[name="form1"]').validVal();
      });
    </script>
  </head>
  <body>

    <div id="page" style="border: solid 1px gray;">
      <div id="header" style="height:100px;width: 800px;margin-left: auto; margin-right: auto;">
        <p>AST3观测状态数据库在服务器aag.bao.ac.cn 上，数据库名称ast3log，包含5个表：templog（主控及数据处理计算机1、2），array1log（存储计算机1），array2log（存储计算机2），ccdlog（CCD），tellog（望远镜）。</p>
        <p>首先用表格列出最近一次的时间（templog-Time）及舱内温度（templog-CIN）、舱外温度（templog-COUT）。</p>
        <p>然后依次画出以下各状态随时间变化，时间在tellog中关键字为SENDTIME，在其余各表中为Time。</p>

      </div>

      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">南极舱基本信息</div></td></tr>
        <tr><td>
            <table id="currentTable">
              <tr><td>时间</td><td>舱内温度(℃)</td><td>舱外温度(℃)</td></tr>
              <tr><td>2012-05-05</td><td>20</td><td>-70</td></tr>
            </table></td></tr>
      </table>

      <div id="link">
        <span><a href="">按天显示</a></span>
        <span><a href="">按周显示</a></span>
        <span><a href="">按月显示</a></span>
        <span><a href="">按年显示</a></span>
        <span><a href="">显示显示所有</a></span>
      </div>

      <div id="search">
        <form name="form1" action="result1.php">
          <div><span><h4>条件查询</h4></span></div>
          <div>
            <span>开始时间：</span>
            <span><input id="starttime" type="text" class="required"/>
              <img onclick="WdatePicker({el:'starttime'})" src="js/My97DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"/>
            </span><span>&nbsp;&nbsp;</span>
            <span>结束时间</span>
            <span><input id="endtime" type="text" class="required"/>
              <img onclick="WdatePicker({el:'endtime'})" src="js/My97DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"/>
            </span><br/>

            <span>数字校验：</span>
            <span><input id="number12" name="number" type="text" value="" class="number required" size="24" /></span><br/>
            <span>时间及日期限制：</span>
            <span><input id="d424" class="Wdate" type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'%y-%M-%d 7:00:00',maxDate:'%y-%M-{%d+1} 21:00:00'})" value="2014-01-16 12:23:00"/></span>
            <span>（只能选择今天7:00:00至明天21:00:00的日期）</span><br/>
            <span>日期限制：</span>
            <span><input id="d424" class="Wdate" type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'2014-01-01',maxDate:'2014-02-26'})" value="2014-01-01"/></span>
            <span>（只能选择2014-01-01至2014-02-26的日期）</span><br/>
            <span>时间限制：</span>
            <span><input id="d425" class="Wdate" type="text" onfocus="WdatePicker({dateFmt:'H:mm:ss',minDate:'7:00:00',maxDate:'21:00:00'})" value="7:00:00"/></span>
            <span>（只能选择7:00:00至21:00:00的日期）</span><br/>
            <span>前后日期关联：</span>
            <span>开始时间<input id="d4311" class="Wdate" type="text" onFocus="WdatePicker({minDate:'2014-01-15',maxDate:'#F{$dp.$D(\'d4312\')||\'2014-01-27\'}'})"/> 
              结束时间<input id="d4312" class="Wdate" type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'d4311\')}',maxDate:'2014-01-27'})"/></span><br/>
            <span>（只能选择2014-01-15至2014-01-27的日期，且前面的时间不能大于后面的时间）</span><br/>
            <span><input type="submit" value="搜索" class="submit"/></span>

          </div>
        </form>

        <div align="top">
          Select referenc stars <span style="color:red">( &lt;= 7 )</span>
          <br>
            <!--form action="./LUT_simulator_selection_2nd_rod.php" method="post"-->	
            <form action="./LUT_simulator_selection_2nd_rod.php" method="post" onsubmit="return confirm('Are you sure you want to submit?')">
              <input type="checkbox" name="box[]" onclick="UNcheckAll()">Uncheck all

                <table width="320" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr><td width="50">N</td><td width="100">RA</td><td width="100">DEC</td><td width="70">Mag AB</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="0" checked="checked"><span style="color:red">1</span></td><td>296.526581</td><td>45.684040</td><td>8.18</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="1" checked="checked"><span style="color:red">2</span></td><td>296.467194</td><td>44.918617</td><td>9.73</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="2" checked="checked"><span style="color:red">3</span></td><td>296.797729</td><td>44.848278</td><td>10.38</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="3" checked="checked"><span style="color:red">4</span></td><td>297.224945</td><td>44.970860</td><td>10.54</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="4" checked="checked"><span style="color:red">5</span></td><td>296.183624</td><td>44.928974</td><td>10.71</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="5" checked="checked"><span style="color:red">6</span></td><td>296.579224</td><td>44.565022</td><td>10.87</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="6" checked="checked"><span style="color:red">7</span></td><td>297.234711</td><td>45.090366</td><td>10.95</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="7"><span style="color:red">8</span></td><td>297.056915</td><td>44.902557</td><td>11.03</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="8"><span style="color:red">9</span></td><td>296.572906</td><td>45.736164</td><td>11.05</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="9"><span style="color:red">10</span></td><td>295.824554</td><td>45.291744</td><td>11.36</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="10"><span style="color:red">11</span></td><td>295.903137</td><td>45.732552</td><td>11.36</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="11"><span style="color:red">12</span></td><td>295.965942</td><td>45.685795</td><td>11.46</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="12"><span style="color:red">13</span></td><td>295.657745</td><td>45.724442</td><td>11.48</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="13"><span style="color:red">14</span></td><td>297.020813</td><td>45.360607</td><td>11.66</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="14"><span style="color:red">15</span></td><td>296.934998</td><td>44.850296</td><td>11.78</td></tr>
                    <tr><td><input type="checkbox" name="box[]" value="15"><span style="color:red">16</span></td><td>297.047394</td><td>45.179070</td><td>11.85</td></tr>
                  </tbody></table>--------------------------------------------------------<br/>
                <div class="style10" align="left"></div>
                <table width="340" border="0" cellspacing="0" cellpadding="0">
                  <tbody><tr><td width="240" align="center">	Proceed to make an observation list.</td>
                      <td width="40"><img src="img/cn75icon01.jpg" width="30" height="28" style="padding:0 0px 0px 0px"></td>
                      <td width="40"><input type="submit" name="Set_ref_star" value="PROCEED"></td></tr>
                  </tbody></table>
            </form></div>

      </div>

      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天环境温度和CCD芯片温度变化图</div></td></tr>
        <tr><td><div id="placeholder1"></div></td></tr>
      </table>

      <!--table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天主控计算机温度变化图</div></td></tr>
        <tr><td><div id="placeholder2" style=""></div></td></tr>
      </table>

      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天数据处理计算机1、2温度变化图</div></td></tr>
        <tr><td><div id="placeholder3" style=""></div></td></tr>
      </table>

      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天存储计算机1温度变化图</div></td></tr>
        <tr><td><div id="placeholder4" style=""></div></td></tr>
      </table>
      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天存储计算机2温度变化图</div></td></tr>
        <tr><td><div id="placeholder5"></div></td></tr>
      </table>

      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天CCD各电路板温度变化图</div></td></tr>
        <tr><td><div id="placeholder6" style=""></div></td></tr>
      </table>

      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天CCD电流变化图</div></td></tr>
        <tr><td><div id="placeholder7" style=""></div></td></tr>
      </table>

      <table style="margin-left: auto; margin-right: auto;">
        <tr><td><div class="title">近一天CCD电压变化图</div></td></tr>
        <tr><td><div id="placeholder8" style=""></div></td></tr>
      </table-->

    </div>

    <script type="text/javascript">
      makeRequest('getData.php?id=1');
    </script>

    <div id="footer">Copyright©2011 All rights reserved, maintained by NAOC.</div>

  </body>
</html>
