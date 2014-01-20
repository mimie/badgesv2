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
                'max': 15,
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
  padding:0px;
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
  font-size:10px;
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
<?php
  include 'dbcon.php';
  include 'badges_functions.php';
  include '../webapp/pire/pdo_conn.php';

  $dbh = civicrmConnect();

  $eventId = $_GET["eventId"];
  $eventName = getEventName($eventId);
  $eventDate = getEventDate($eventId);
  $eventDate = DateTime::createFromFormat('Y-m-d H:i:s',$eventDate);
  $eventDate = $eventDate->format("j F Y");

  $eventLocation = getLocation($dbh,$eventId);
  $eventLocation = formatLocation($eventLocation);

  $speakerContactId = getSpeakerContactId($eventId);
  $speakerName = getParticipantName($speakerContactId);

  //$contactIds = getEventParticipantId($eventId);

  //$allContacts = getAllContacts();
  $participants = getParticipantByEvent($dbh,$eventId);
?>
<!--<div style="height:5px;border:1px solid #0489B1;background:#0489B1;"></div>-->
<div id="eventHeader">
<h4>Institute of Internal Auditors - Philippines<br>
Centre for Professional Development<br>
Attendance and CPE Form</h4>
</div>
<!--<div style="height:5px;border:1px solid #0489B1;background:#0489B1;"></div>-->

<div id="eventDetails" align="center">
 <table id="eventInfo">
  <tr>
   <td width='13%'><b>Topic</b></td>
   <td width='87%'><?=$eventName?></td>
  </tr>
  <tr>
   <td><b>Date, Time</b></td>
   <td><?=$eventDate?></td>
  </tr>
  <tr>
   <td><b>Speaker</b></td> 
   <td><?=$speakerName?></td>
  </tr>
  <tr>
   <td><b>Venue</b></td>
   <td><?=$eventLocation?></td>
  </tr>
 </table><br>
</div>

<div id="participantDetails" align="center">
 <table id = 'participantInfo'>
  <thead>
 <tr>
  <th>No.</th>
  <th>Name</th>
  <th>Company</th>
  <th>Position</th>
  <th>Nature of Business</th>
  <th>Email Address</th>
  <th>Mobile No.</th>
  <th width='10%'>Signature</th>
 </tr>
 </thead>
<tbody>

<?php
 $count = 1;
 foreach($participants as $contactInfo){

   //$contactInfo = $allContacts[$id];
   $id = $contactInfo["id"];
   $certification = getCertification($id);
   $certifications = identifyCertification($certification);

   $business = getNatureBusiness($dbh,$id);
   $email = getEmailAddress($dbh,$id);
   $phone = getPhone2($dbh,$id);
   
   echo "<tr>";
   echo "<td>$count</td>";
   echo "<td>".$contactInfo['sort_name']."</td>";
   echo "<td>".$contactInfo['organization_name']."</td>";
   echo "<td>".$contactInfo['job_title']."</td>";
   echo "<td>".$business."</td>";
   echo "<td>".$email."</td>";
   echo "<td>".$phone."</td>";
   echo "<td></td>";
   echo "</tr>";
    
   $count++;
 }
?>
 </tbody>
 </table>
</div>
<!--
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
-->



</body>
</html>
