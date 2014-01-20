<html>
<head>
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $("#participantInfo").jPaginate({
                'max': 10,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});
</script>
<title>Event List</title>
<style>
#eventHeader{
  text-align:center;
  padding:5px;
  font-size:14px;
}

#eventDetails{
  padding:5px;
}

table#eventInfo{
  border-collapse:collapse;
  border: 1px solid black;
  width: 95%;
}

table#eventInfo td{
  border-collapse:collapse;
  border: 1px solid black;
  padding: 5px;
  font-size:14px;
}

table#participantInfo{
  border-collapse:collapse;
  border:1px solid black;
  font-size:14px;
  width: 95%;
}

table#participantInfo td,th{
  border-collapse:collapse;
  border:1px solid black;
  padding: 4px;
}

table#attachments{
  border-collapse:collapse;
  border:1px solid black;
  font-size:14px;
  width: 95%;
}

table#attachments td,th{
  border-collapse:collapse;
  border:1px solid black;
  padding: 4px;
}
</style>
</head>
<body>
<div align="center">
<table style="width:80%;">
<tr><td><b>SIGN-OFFS FOR CONTINUING PROFESSIONAL EDUCATION (CPE) REQUIREMENTS</b></td></tr>
</table>
</div>

<div align="center">
<table><tr><td><div style="border-style:solid;width:1450px"></div></td></tr></table>
</div>

<div align="center">
<table style="width:80%;">
<tr>
   <td align="left" width="55%"><font style="font-size:14px;">Required attachments to this form [to be accomplished at the end of the seminar by IIA-P Training Officer/Assistant, signature and date]</font></td>
   <td width="45%" align="center"><font style="font-size:14px;">8 hours</font></td>
</tr>
</table>
<table id="attachments">
<tr>
  <td width="55%">Total CPE hours credited</td>
  <td width="45%" align="center">/</td>
<tr>
<tr>
  <td width="55%">Copy of course outline and description (course seminar flyer)</td>
  <td width="45%" align="center">/</td>
</tr>
<tr>
  <td>Summary of seminar evaluation</td>
  <td></td>
</tr>
</table>
</div><br>

<div align="center">
<table style="width:80%;">
<tr>
   <td align="left"><font style="font-size:14px;">Required sign offs from the Centre for Professional Development and IIA-P (Name, signature and date)</font></td>
</tr>
</table>
<table id="attachments">
<tr>
  <td width="55%">Company Representative (For in-house only, please also add Position)</td>
  <td align="center" width="45%">n/a</td>
<tr>
<tr>
  <td width="55%">Speaker/Facilitator</td>
  <td width="45%"></td>
</tr>
<tr>
  <td width="55%">VP,Professional Development</td>
  <td width="45%"></td>
</tr>
</table>
</div>



</body>
</html>
