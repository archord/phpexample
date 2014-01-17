<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    htmlobj=$.ajax({url:"waitdata.php?startdata=2014-01-14&number=7",async:true,complete:setResult});
  });
  function setResult(XHR, TS){
    if (XHR.readyState == 4) {
      if (XHR.status == 0 || XHR.status == 200) {
        $("div#result").html(XHR.responseText);
      }else{
        $("div#result").html('<span style="color:red;">服务器端错误，请联系管理员！</span>');
      }
    }else{
      $("div#result").html('<span style="color:red;">服务器端错误，请联系管理员！</span>');
    }
  }
</script>

<table cellpadding="0" cellspacing="10" border="0">
  <tbody>
    <tr><td>
      </td></tr><tr>
      <td align="top">
        <br>-----------------------------------------------------------------------------<br>
        <div id="result">
          <span style="color:red;">该计算步骤比较复杂，需要一分钟左右的时间，请耐心等待......</span>
        </div>
        <br><table width="400" border="0" cellspacing="0" cellpadding="0">
          <tbody><tr><td> 
                Name:
              </td><td>
                <span style="color:red">delCyg</span>
              </td><td>
              </td></tr>
            <tr><td> 
                RA (J2000):
              </td><td>
                <span style="color:red">296.2436625</span>
              </td><td>
              </td></tr>
            <tr><td> 
                DEC (J2000):
              </td><td>
                <span style="color:red">45.1308111</span>
              </td><td>
              </td></tr>
            <tr><td> 
                Mag AB:
              </td><td>
                <span style="color:red">3.72</span>
              </td><td>
              </td></tr>
            <tr><td> 
                Start date:
              </td><td>
                <span style="color:red">2014-01-24</span>
              </td><td>
              </td></tr>
            <tr><td>
                Start time:
              </td><td>
                <span style="color:red">0.0</span>
              </td><td>
              </td></tr>
            <tr><td>
                End date:
              </td><td>
                <span style="color:red">2014-01-24</span>
              </td><td>
              </td></tr>
            <tr><td>
                End time:
              </td><td>
                <span style="color:red">8.6</span>
              </td><td>
              </td></tr>
            <tr><td>
                Exposure time:
              </td><td>
                <span style="color:red">30</span>
              </td><td>
                (Second)
              </td></tr>
            <tr><td>
                Gain:
              </td><td>
                <span style="color:red">1</span>
              </td><td>
              </td></tr>
            <tr><td>
                Time step:
              </td><td>
                <span style="color:red">300</span>
              </td><td>
                (Second)
              </td></tr>
            <tr><td>
                Number of referenc stars:
              </td><td>
                <span style="color:red">7</span></td><td>
              </td></tr>
            <tr><td>1</td><td>296.526581</td><td>45.684040</td><td>8.18</td></tr><tr><td>2</td><td>296.467194</td><td>44.918617</td><td>9.73</td></tr><tr><td>3</td><td>296.797729</td><td>44.848278</td><td>10.38</td></tr><tr><td>4</td><td>297.224945</td><td>44.970860</td><td>10.54</td></tr><tr><td>5</td><td>296.183624</td><td>44.928974</td><td>10.71</td></tr><tr><td>6</td><td>296.579224</td><td>44.565022</td><td>10.87</td></tr><tr><td>7</td><td>297.234711</td><td>45.090366</td><td>10.95</td></tr></tbody></table><br><table cellpadding="0" cellspacing="1" border="0"><tbody><tr><td align="top"><a href="javascript:history.back(-1);">Return</a></td></tr></tbody></table><br><table cellpadding="0" cellspacing="1" border="0"><tbody><tr><td align="top"><a href="http://localhost/ast3log/">Home</a></td></tr></tbody></table>
      </td></tr>
  </tbody>
</table>
