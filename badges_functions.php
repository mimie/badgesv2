<?php
include 'dbcon.php';

/*[93] => Array
        (
            [title] => IIA-P 2013 Bowling Tournament
            [start_date] => 2013-06-29 14:30:00
        )
**/
function getAllEvents(){

  $sql = "SELECT id,title,start_date FROM civicrm_event ORDER BY start_date DESC";
  $result = mysql_query($sql) or die(mysql_error());

  $events = array();
  $details = array();

  while($row = mysql_fetch_assoc($result)){
     $eventId = $row['id'];
     $title = $row['title'];
     $startDate = $row['start_date'];

     if($title && $startDate){
     $details['title'] = $title;
     $details['start_date'] = $startDate;

     $events[$eventId] = $details;
     unset($details);
    }
  }

  return $events;

}

function getEventName($eventId){

  $sql = "SELECT title FROM civicrm_event WHERE id = '$eventId'";
  $result = mysql_query($sql) or die(mysql_error());
  $row = mysql_fetch_assoc($result);
  $eventName = $row["title"];

  return $eventName;
  
}

function getEventDate($eventId){

  $sql = "SELECT start_date FROM civicrm_event WHERE id = '$eventId'";
  $result = mysql_query($sql) or die(mysql_error());
  $row = mysql_fetch_assoc($result);
  $eventDate = $row["start_date"];

  return $eventDate;

}

/*[6017] => Array
        (
            [name] => Lady Lyn De Leon
            [org] => Isla Lipana and Co./Pricewaterhouse Coopers
        )*/
function getAllContacts(){

  $sql = "SELECT id,sort_name,organization_name,job_title FROM civicrm_contact ORDER BY organization_name";
  $result = mysql_query($sql) or die(mysql_error());

  $contacts = array();
  $details = array();

  while($row = mysql_fetch_assoc($result)){
     $contactId = $row['id'];
     $displayName = $row['sort_name'];
     $orgName = $row['organization_name'];
     $job = $row['job_title'];

     $details['name'] = $displayName;
     $details['org'] = $orgName;
     $details['job'] = $job;

     $contacts[$contactId] = $details;
     unset($details);
  }

  return $contacts;
}

/*
 *eventId is the id of a specific event
 *@return all the contact id of a specific event
 */
function getEventParticipantId($eventId){

  $eventId = mysql_real_escape_string($eventId);
  $sql = "SELECT contact_id FROM civicrm_participant,civicrm_contact WHERE event_id = '{$eventId}'"
       . " AND civicrm_contact.id=civicrm_participant.contact_id AND is_deleted='0'";
  $result = mysql_query($sql) or die(mysql_error());

  $contactIds = array();

  while($row = mysql_fetch_assoc($result)){
    $contactIds[] = $row['contact_id'];
 }
  return $contactIds;
}

/*
 *@return html table format of all events
 */
function displayAllEvents(){

  $allEvents = getAllEvents();

  $html = "<h3>List of Events</h3>";

  $html = $html."<table>"
        . "<th>Event Title</th>"
        . "<th>Event Date</th>"
        . "<th>ID Badge</th>"
        . "<th>Registration Form</th>";

  foreach($allEvents as $eventId=>$details){
        $title = $details["title"];
        $date = $details["start_date"];
        $date = formatDate($date);
       
    $html = $html."<tr>"
          . "<td>$title</td>"
          . "<td>$date</td>"
          . "<td class='center'><a href='participants.php?eventId=$eventId'>Participants</a></td>"
          . "<td class='center'><a href='eventList.php?eventId=$eventId' target='_blank'>Event Form</a></td>"
          . "</tr>";
  }

  $html = $html."</table>";

  return $html;
  
}

/*
 *date is string format of date in the form of 2013-10-23 14:00:00
 *@return date in a format 23 Oct 2013
 */
function formatDate($date){

  $getDate = explode(" ",$date);
  $date = $getDate[0];
  $date = date("j M Y",strtotime($date));
  return $date;
}

/*
 *display all participants in a specific event
 */
function displayParticipantPerEvent($eventId){

  $allContacts = getAllContacts();
  $participants = getEventParticipantId($eventId);
  $emails = getAllEmails();

  $eventName = getEventName($eventId);
  $badgeProperties = badgePropertiesForm();

  $html = "<h3>List of Participants for ".$eventName."</h3>";
  $html = $html. "<table>"
        . "<th>Participant Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Email Address</th>"
        . "<th>Select Participant</th>"
        . "<tr>"
        . "<td colspan='4' align='center'>"
        . "<form id='participants' method='post'>"
        . "$badgeProperties"
        . "<input type='checkbox' name='checkAll' onclick='checkedAll(\"participants\");'>Check All\t\t\t"
        . "<input class='blue' type='submit' name='print' value='GENERATE BADGE'>"
        . "</td>"
        . "</tr>";

  foreach($participants as $contactId){

   $details = $allContacts[$contactId];
   $name = $details["name"];
   $org = $details["org"];
   $email = $emails[$contactId];

   if($name){

   $html = $html."<tr>"
         . "<td>$name</td>"
         . "<td>$org</td>"
         . "<td>$email</td>"
         . "<td class='center'><input type='checkbox' name='contactIds[]' value='$contactId'></td>"
         . "<tr>";
   }
 }

  $html = $html."</form></table>";

  return $html;

}

function badgePropertiesForm(){

 $html = "<select name='badgeType' id='properties' onchange='badgeProperties(this);'>"
       . "<option value='select'>Select badge properties</option>"
       . "<option value='none' disabled>--------</option>"
       . "<option value='default'>Default Badge</option>"
       . "<option id='customize' value='customize'>Customize Badge</option>"
       . "</select><br>";

 //$options = propertiesOption();
 $fontSizeOptions = fontSizeOption();


 $html = $html. "<div id='badgeForm' style='display:none;'>"
       . "<table>"
       . "<tr>"
       . "<td align='right'>Badge Width (cm):</td>"
       . "<td align='left'><input type='number' name='badgeWidth'>"
       . "</td>"
       . "</tr>"
       . "<tr>"
       . "<td align='right'>Badge Height (cm):</td>"
       . "<td align='left'><input type='number' name='badgeHeight'>"
       . "</td></tr>"
       . "<tr>"
       . "<td align='right'>Logo Size (cm):</td>"
       . "<td align='left'><select name='dimensions' onChange='changeLogoTextbox(this.value);'><option value='select' disabled>Select dimension</option>"
       . "<option value='default' selected='selected'>default</option>"
       . "<option value='width'>width</option>"
       . "<option value='height'>height</option></select>"
       . "<input type='number' id='logo' name='dimensionSize' style='display:none;'>"
       . "</td>"
       . "</tr>"
       . "<tr>"
       . "<td align='right'>Event Title Font Size:</td>"
       . "<td align='left'><select name='eventSize'>"
       . "<option value=default>default</option>"
       . "$fontSizeOptions"
       . "</select>"
       . "</td>"
       . "</tr>"
       . "<tr>"
       . "<td align='right'>Participant Name Font Size:</td>"
       . "<td align='left'><select name='participantSize'>"
       . "<option value='default'>default</option>"
       . "$fontSizeOptions"
       . "</select>"
       . "</td>"
       . "</tr>"
       . "<tr>"
       . "<td align='right'>Organization Name Font Size:</td>"
       . "<td align='left'><select name='orgSize'>"
       . "<option value='default'>default</option>"
       . "$fontSizeOptions"
       . "</select>"
       . "</td>"
       . "</tr>"
       . "<tr>"
       . "<td align='right'>Date Font Size:</td>"
       . "<td align='left'><select name='dateSize'>"
       . "<option value='default'>default</option>"
       . "$fontSizeOptions"
       . "</select>"
       . "</td>"
       . "</tr>"
       . "</table></div>";

  return $html;

}

/*
 *@return html options for height and width dimensions
 */
function propertiesOption(){

  $html = "";

  for($i=5;$i<=30;$i++){
    $html = $html."<option value=".$i.">".$i."</option>";
  }

  return $html;
}


/*
 *@return html option for font size of the details of the badges
 */
function fontSizeOption(){

  $html = "";

  for($i=6;$i<=96;$i++){
    $html = $html."<option value=".$i."pt>".$i."</option>";
  }

  return $html;

}

/*
 *@param (text) (dimension) - dimension can be of value height/width
 *@param (number) (size) - size of the dimension given
 *@return array of dimensions of the properties of image height and width
 */
function dimensionRatio($dimension,$size){

   $dimensions = array();
   $size = (int)$size;
   $size = cmToPixel($size);

   if($dimension == 'width'){

     $height = (73*$size)/77;
     $dimensions["width"] = $size;
     $dimensions["height"] = $height;
     return $dimensions;
   }

   elseif($dimension == 'height'){

     $width = (77*$size)/73;
     $dimensions["height"] = $size;
     $dimensions["width"] = $width;
     return $dimensions;
   }

   elseif($dimension == 'default'){

     $dimensions["height"] = 77;
     $dimensions["width"] = 73;
     return $dimensions;
   }
}

/*
 *eventName is the pattern of the string searched
 *@return an array of events that matches the pattern string
 */
function searchEvent($eventName){

  $allEvents = getAllEvents();
  $eventMatches = array();

  foreach($allEvents as $eventId => $details){
    $title = $details["title"];
    $result = preg_match("/$eventName/",$title);
    if($result == 1){
      //$eventMatches[] = $event;
        $eventMatches[$eventId] = $details;
    }
  }

  return $eventMatches;
}

/*
 *@return html form for search event
 */
function searchEventForm(){

  $htmlForm = "<form name='event' action='events.php' method='post'>"
            . "<label for='eventTitle'><b>Event Title: </b></label>"
            . "<input type='text' name='eventName'>"
            . "<input class='blue' type='submit' name='searchEvent' value='SEARCH'>"
            . "</form>";

  return $htmlForm;
}

/*
 *@return an html table for the result of searched events
 */
function displaySearchEvent(array $events){
  
 if($events){
   $html = "<table>"
         . "<th>Event Title</th>"
         . "<th>Event Date</th>"
         . "<th>View Participant List</th>"
         . "<th>Download Event Form</th>";

   foreach($events as $eventId=>$details){
        $title = $details["title"];
        $date = $details["start_date"];
        $date = formatDate($date);
       
   $html = $html."<tr>"
         . "<td>$title</td>"
         . "<td>$date</td>"
         . "<td class='center'><a href='participants.php?eventId=$eventId'>Participants</a></td>"
         . "<td><a href='eventList.php?eventId=$eventId' target='_blank'>Event Form</a></td>"
         . "</tr>";
  }

  $html = $html."</table>";

  return $html;
 }

 else{

   $message = errorMessageDisplay("The event name does not exist.");
   $html = $message."<br>".displayAllEvents();
   return $html;
 }
  
}

/*
 *@return the error message
 */
function errorMessageDisplay($message){

  $html = "<table class='error'><tr><td>$message</td></tr></table>";
  return $html;
}

function getAllEmails(){

  $emails = array();
  $ids = array();
  $sql = "SELECT email,contact_id FROM civicrm_email";
  $result = mysql_query($sql) or die(mysql_error());

  while($row = mysql_fetch_array($result)){
    $email = $row["email"];
    $contactId = $row["contact_id"];
   
    $emails[] = $email;
    $ids[] = $contactId;
  }
    
  $allEmails = array_combine($ids,$emails);
  return $allEmails;
}

function searchParticipantPerEvent($eventId,$searchCriteria){

  $searchCriteria = mysql_real_escape_string($searchCriteria);

  $participants = array();
  $details = array();

  $sql = "SELECT display_name,organization_name,email,cc.id FROM civicrm_participant cp, civicrm_email cem, civicrm_contact cc\n"
       . "WHERE cp.event_id='$eventId'\n"
       . "AND cp.contact_id = cc.id\n"
       . "AND cem.contact_id = cc.id\n"
       . "AND (cc.display_name LIKE '%{$searchCriteria}%' OR cem.email LIKE '%{$searchCriteria}%')";
  $result = mysql_query($sql) or die(mysql_error());

  while($row = mysql_fetch_assoc($result)){
     $details["name"] = $row["display_name"];
     $details["org"] = $row["organization_name"];
     $details["email"] = $row["email"];
     $details["id"] = $row["id"];
    
     $participants[] = $details;
     unset($details);
  }

  return $participants;
}

function displaySearchParticipant(array $participantDetails,$eventId){

  $eventName = getEventName($eventId);
  $eventName = getEventName($eventId);
  $badgeProperties = badgePropertiesForm();

  $html = "<h3>List of Participants for ".$eventName."</h3>";
  $html = $html. "<table>"
        . "<th>Participant Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Email Address</th>"
        . "<th>Select Participant</th>"
        . "<tr>"
        . "<td colspan='4' align='center'>"
        . "<form id='participants' method='post'>"
        . "$badgeProperties"
        . "<input type='checkbox' name='checkAll' onclick='checkedAll(\"participants\");'>Check All\t\t\t"
        . "<input class='blue' type='submit' name='print' value='GENERATE BADGE'>"
        . "</td>"
        . "</tr>";

 foreach($participantDetails as $details){
   $name = $details["name"];
   $org = $details["org"];
   $email = $details["email"];
   $contactId = $details["id"];

   $html = $html."<tr>"
         . "<td>$name</td>"
         . "<td>$org</td>"
         . "<td>$email</td>"
         . "<td class='center'><input type='checkbox' name='contactIds[]' value='$contactId'></td>"
         . "</tr>";
 }
  
  $html = $html."</form></table>";

  return $html;

}

function searchParticipantForm(){

  $htmlForm = "<form name='searchParticipantForm' action='' method='post'>"
            . "<label for='Email or Name'>Email or Name: </label>"
            . "<input type='text' name='searchCriteria'>"
            . "<input class='blue' type='submit' name='searchParticipant' value='SEARCH'>"
            . "</form>";

  return $htmlForm;
}

function resultSearchParticipant($eventId,$searchCriteria){

   $participants = searchParticipantPerEvent($eventId,$searchCriteria);
   $html = displaySearchParticipant($participants,$eventId);

   return $html;
}

function getParticipantDetails($contactId){

  $participant = array();
  $sql = "SELECT first_name,nick_name,display_name, organization_name FROM civicrm_contact WHERE id = '$contactId'";
  $result = mysql_query($sql) or die(mysql_error());
  $row = mysql_fetch_assoc($result);
  $participant["name"] = $row["display_name"];
  $participant["org"] = $row["organization_name"];
  $participant["fname"] = $row["first_name"];
  $participant["nickname"] = $row["nick_name"];

  return $participant;
}

function htmlBadge($eventId,array $participant,array $properties){

   $badgeHeight = $properties["bHeight"];
   $badgeWidth = $properties["bWidth"];
   $imgHeight = $properties["imgHeight"];
   $imgWidth = $properties["imgWidth"];
   $titleSize = $properties["titleSize"];
   $nameSize = $properties["nameSize"];
   $orgSize = $properties["orgSize"];
   $dateSize = $properties["dateSize"];

   $eventName = getEventName($eventId);
   $eventDate = getEventDate($eventId);
   $eventDate = formatDate($eventDate);

   $name = $participant["name"];
   $orgName = $participant["org"];
   $firstname = $participant["fname"];
   $nickname = $participant["nick_name"];

   $badgeName = $nickname != NULL ? $nickname : $firstname;

   $htmlBadge = "<html>"
              . "<head>"
              . "<style>" 
              . "#badge{"
              . "border:1px dashed #BDBDBD;"
              . "padding:2px;"
              . "width:".$badgeWidth.";"
              . "height:".$badgeHeight.";"
              . "}"
              . "table{"
              . "width:".$badgeWidth.";"
              . "height:".$badgeHeight.";"
              . "}"
              . "</style>"
              . "</head>";

    $htmlBadge = $htmlBadge."<body>"
               . "<div id = 'badge'>"
               . "<table>"
               . "<tr>"
               . "<td align='center' width='".$imgWidth."' height='".$imgHeight."'>"
               . "<img src='iiap_logo.png' width='".$imgWidth."' height='".$imgHeight."'></td>"
               . "<td align='center' height='".$imgHeight."' cellpadding='3px'>"
               . "<font size='".$titleSize."'><h4>".$eventName."</h4></td>"
               . "</tr>";

    $htmlBadge = $htmlBadge."<tr>"
               . "<td colspan='2' align='center'>"
               . "<b><font size='".$nameSize."'>".$badgeName."</b></br></font>"
               . "<font size='".$orgSize."'>".$name."</font></br>"
               . "<font size='".$orgSize."'>".$orgName."</font></td>"
               . "</tr>";
  
    $htmlBadge = $htmlBadge."<tr>"
               . "<td colspan=2 align='right'>"
               . "<font size='".$dateSize."'>"
               . $eventDate . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>"
               . "</tr>";

    $htmlBadge = $htmlBadge."</table>"
               . "</div>"
               . "</body>"
               . "</html>";

   return $htmlBadge;
}

function htmlCustomizeBadge($eventId, array $participant, array $properties){

   $imgHeight = $properties["imgHeight"];
   $imgWidth = $properties["imgWidth"];
   $titleSize = $properties["titleSize"];
   $nameSize = $properties["nameSize"];
   $orgSize = $properties["orgSize"];
   $dateSize = $properties["dateSize"];

   $eventName = getEventName($eventId);
   $eventDate = getEventDate($eventId);
   $eventDate = formatDate($eventDate);

   $name = $participant["name"];
   $orgName = $participant["org"];
   $firstname = $participant["fname"];
   $nickname = $participant["nickname"];

   $badgeName = $nickname != NULL ? $nickname : $firstname;

   $htmlBadge = "<div id = 'badge'>"
            . "<table>"
            . "<tr>"
            . "<td align='center' width='".$imgWidth."' height='".$imgHeight."'>"
            . "<img src='iiap_logo.png' width='".$imgWidth."' height='".$imgHeight."'></td>"
            . "<td align='center' height='".$imgHeight."' cellpadding='3px'>"
            . "<font style='font-size:".$titleSize.";'><b>".$eventName."</b></td>"
            . "</tr>";
 
   $htmlBadge = $htmlBadge."<tr>"
                . "<td colspan='2' align='center'>"
                . "<b><font style='font-size:".$nameSize.";'>".$badgeName."</b></br></font>"
                . "<font style='font-size:".$orgSize.";'>".$name."</font></br>"
                . "<font style='font-size:".$orgSize.";'>".$orgName."</font></td>"
                . "</tr>";
 
  $htmlBadge = $htmlBadge."<tr>"
                . "<td colspan=2 align='right'>"
                . "<font style='font-size:".$dateSize.";'>"
                . $eventDate . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>"
                . "</font></tr>";
 
  $htmlBadge = $htmlBadge."</table>"
                . "</div>";
  return $htmlBadge;
}

/*
 *@return conversion of centimeters to Pixels
 */
function cmToPixel($size){

  $pixels = $size*37.795275591;
  return $pixels;
}

function generatePDF($html,$eventId){
  
   require_once("dompdf/dompdf_config.inc.php");
   $eventName = getEventName($eventId);
   $date = time();
   $fileName = $eventName."_".$date.".pdf";
   $fileLocation = "pdf/".$fileName;
 
   $dompdf = new DOMPDF();
   $dompdf->load_html($html);
   $dompdf->set_paper('Letter','portrait');
 
   $dompdf->render();
   file_put_contents($fileLocation, $dompdf->output( array("compress" => 0) ));
}

function getSpeakerContactId($eventId){

 $sql = "SELECT contact_id FROM civicrm_participant WHERE event_id='$eventId' AND role_id='4'";
 $result = mysql_query($sql) or die(mysql_error());
 $row = mysql_fetch_assoc($result);
 $contactId = $row["contact_id"];

 return $contactId; 

}

function getAllSpeakers($dbh,$eventId){

  $sql = $dbh->prepare("SELECT cc.id,cc.display_name FROM civicrm_participant cp, civicrm_contact cc
                        WHERE cp.event_id = ? AND role_id = '4'
                        AND cp.contact_id = cc.id");
  $sql->bindValue(1,$eventId,PDO::PARAM_INT);
  $sql->execute();
  $speakers = $sql->fetchAll(PDO::FETCH_ASSOC);
  $names = array();

  foreach($speakers as $key => $field){
     $names[] = $field["display_name"];
  }

  $speakers = rtrim(implode(', ', $names), ',');

  return $speakers;
  
}

function getParticipantName($contactId){

 $sql = "SELECT display_name FROM civicrm_contact WHERE id='$contactId'";
 $result = mysql_query($sql) or die(mysql_error());
 $row = mysql_fetch_assoc($result);
 $name = $row["display_name"];

 return $name;
}

function getCertification($contactId){

 $contactId = mysql_real_escape_string($contactId);
 $sql = "SELECT are_you__10 FROM civicrm_value_business_data_1 WHERE entity_id='{$contactId}'";
 $result = mysql_query($sql) or die(mysql_error());
 $row = mysql_fetch_assoc($result);
 $certification = $row["are_you__10"];
 
 return $certification;
}

function identifyCertification($certification){

  $certifications = array();

  if($certification != NULL){

    $patternCIA = "/\b\w*CIA\w*\b/";
    $patternCPA = "/\b\w*CPA\w*\b/";
    $patternCCSA = "/\b\w*CCSA\w*\b/";
    $patternCFSA = "/\b\w*CFSA\w*\b/";
    $patternCRMA = "/\b\w*CRMA\w*\b/";
    $patternCISA = "/\b\w*CISA\w*\b/";
    $patternCFE = "/\b\w*CFE\w*\b/";
    $patternMBA = "/\b\w*MBA\w*\b/";


    $isCIA = preg_match($patternCIA,$certification);
    $isCPA = preg_match($patternCPA,$certification);
    $isCCSA = preg_match($patternCCSA,$certification);
    $isCFSA = preg_match($patternCFSA,$certification);
    $isCRMA = preg_match($patternCRMA,$certification);
    $isCISA = preg_match($patternCISA,$certification);
    $isCFE = preg_match($patternCFE,$certification);
    $isMBA = preg_match($patternMBA,$certification);

    if($isCIA == 1){
      $certifications[] = "CIA";
    }

    if($isCPA == 1){
      $certifications[] = "CPA";
    }

    if($isCSSA == 1){
      $certifications[] = "CSSA";
    }

    if($isCFSA == 1){
      $certifications[] = "CFSA";
    }

    if($isCRMA == 1){
      $certifications[] = "CRMA";
    }

    if($isCISA == 1){
      $certifications[] = "CISA";
    }

    if($isCFE == 1){
      $certifications[] = "CFE";
    }

    if($isMBA ==1){
      $certifications[] = "MBA";
    }
/**
    if($isCIA == 1 && $isCPA == 1){
       return "both";
    }

    elseif($isCIA == 1){
       return "CIA";
    }

    elseif($isCPA == 1){
       return "CPA";
    }**/

   $comma_certifications = implode(",",$certifications);

   return $comma_certifications;

  }

  else{
    return NULL;
  }
}

function getLocation($dbh,$eventId){

  $sql = $dbh->prepare("SELECT ca.street_address,ca.supplemental_address_1, ca.supplemental_address_2, ca.supplemental_address_3,
                        ca.city, ca.postal_code, csp.name as province, con.name AS country
                        FROM civicrm_event ce, civicrm_loc_block clb, civicrm_address ca, civicrm_state_province csp, civicrm_country con
                        WHERE ce.id = '$eventId'
                        AND ce.loc_block_id = clb.id
                        AND clb.address_id = ca.id
                        AND ca.state_province_id = csp.id
                        AND ca.country_id = con.id
                       ");
  $sql->execute();
  $location = $sql->fetch(PDO::FETCH_ASSOC);

  return $location;
}

function formatLocation($locationDetails){

  if($locationDetails){
     $location = "";

     foreach($locationDetails as $key => $value){

        if($value && $key!='country'){
          $location = $location.$value.",&nbsp;";
        }
    
        elseif($key == 'country'){
          $location = $location.$value;
        }
     }

    return $location;
  }
}


function getNatureBusiness($dbh,$contactId){

  $sql = $dbh->prepare("SELECT nature_of_business_37 as business
                        FROM civicrm_value_business_data_1
                        WHERE entity_id = ?
                       ");
  $sql->bindValue(1,$contactId,PDO::PARAM_INT);
  $sql->execute();

  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $business = $result["business"];

  return $business;
}

function getEmailAddress($dbh,$contactId){

 $sql = $dbh->prepare("SELECT email FROM civicrm_email
                       WHERE contact_id = ?
                       AND location_type_id = 1
                      ");
 $sql->bindValue(1,$contactId,PDO::PARAM_INT);
 $sql->execute();

 $result = $sql->fetch(PDO::FETCH_ASSOC);
 $email1 = $result["email"];
 $sql = $dbh->prepare("SELECT email FROM civicrm_email
                       WHERE contact_id = ?
                       AND location_type_id = 3
                      ");
 $sql->bindValue(1,$contactId,PDO::PARAM_INT);
 $sql->execute();

 $result = $sql->fetch(PDO::FETCH_ASSOC);
 $email2 = $result["email"];

 if(isset($email1) && isset($email2)){
   return $email1."<br>".$email2;

 }

 elseif(!$email1){
   return $email2;
 }


 else{

    return $email1;
  }

}
function getPhone(PDO $dbh,$contactId){

 $sql = $dbh->prepare("SELECT phone
                       FROM civicrm_phone
                       WHERE contact_id = :contactId
                       AND phone_type_id = 2
                      ");
 $sql->execute(array(':contactId'=>$contactId));
 $result = $sql->fetch(PDO::FETCH_ASSOC);
 $phone = $result["phone"];

 return $phone;

}

function getPhone2(PDO $dbh,$contactId){
 $sql = $dbh->prepare("SELECT mobile_number_34 as phone
                       FROM civicrm_value_address_10
                       WHERE entity_id = :contactId
                      ");
 $sql->execute(array(':contactId'=>$contactId));
 $result = $sql->fetch(PDO::FETCH_ASSOC);
 $phone = $result["phone"];

 return $phone;

}

function getParticipantByEvent($dbh,$eventId){

 $sql = $dbh->prepare("SELECT cc.id,sort_name,organization_name,job_title FROM civicrm_contact cc, civicrm_event ce, civicrm_participant cp
                       WHERE cc.id = cp.contact_id
                       AND cp.event_id = ce.id
                       AND ce.id = ?
                       AND cp.status_id IN (1,2,5)
                       ORDER BY organization_name
                      ");
 $sql->bindValue(1,$eventId,PDO::PARAM_INT);
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;

}
?>
