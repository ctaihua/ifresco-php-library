<?php
/*
 * Copyright (C) 2005 Alfresco, Inc.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

 * As a special exception to the terms and conditions of version 2.0 of 
 * the GPL, you may redistribute this Program in connection with Free/Libre 
 * and Open Source Software ("FLOSS") applications as described in Alfresco's 
 * FLOSS exception.  You should have recieved a copy of the text describing 
 * the FLOSS exception, and it is also available here: 
 * http://www.alfresco.com/legal/licensing"
 */
 /**
 *
 * @package    ifresco PHP library
 * @author Dominik Danninger 
 * @website http://www.ifresco.at
 *
 * ifresco PHP library - extends Alfresco PHP Library
 * 
 * Copyright (c) 2011 Dominik Danninger, MAY Computer GmbH
 * 
 * This file is part of "ifresco PHP library".
 * 
 * "ifresco PHP library" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * "ifresco PHP library" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with "ifresco PHP library".  If not, see <http://www.gnu.org/licenses/>. (http://www.gnu.org/licenses/gpl.html)
 */
   set_include_path(realpath(__DIR__)."/../../");
   require_once "../../ClassLoader.php"; 
    
   require_once('../config.php');


   if (isset($_SESSION) == false)
   {
      // Start the session
      session_start();
   }
   
   // Create the session
   $repository = new Repository($repositoryUrl);
   $ticket = null;
   if (isset($_SESSION["ticket"]) == false)
   {
      $ticket = $repository->authenticate($userName, $password);
      $_SESSION["ticket"] = $ticket;	
   }   
   else
   {
     $ticket = $_SESSION["ticket"]; 	
   }
   $session = $repository->createSession($ticket);
   
   $store = new SpacesStore($session);
   $currentNode = null;

   if (isset($_REQUEST['uuid']) == false)
   {
      $currentNode = $store->companyHome;
      $path = 'Company Home';

   }
   else
   {
      $currentNode = $session->getNode($store, $_REQUEST['uuid']);
      $path = $_REQUEST['path'].'|'.$_REQUEST['uuid'].'|'.$_REQUEST['name'];
   }

   function getURL($node)
   {
      global $path;

      $result = null;
      if ($node->type == "{http://www.alfresco.org/model/content/1.0}content")
      {
      	 $contentData = $node->cm_content;
      	 if ($contentData != null)
      	 {
         	$result = $contentData->getUrl();
      	 }
      }
      else
      {
         $result = "index.php?".
                     "&uuid=".$node->id.
                     "&name=".$node->cm_name.
                     "&path=".$path;
      }

      return $result;
   }
   
   function getImageURL($current_type="{http://www.alfresco.org/model/content/1.0}folder")
   {
      $result = null;
      if ($current_type == "{http://www.alfresco.org/model/content/1.0}content")
      {
         $result = "post.gif";
      }
      else
      {
         $result = "space_small.gif";
      }

      return $result;
   }

   function outputRow($node)
   {
      print("<tr><td><img src='../Common/Images/".getImageURL($node->type)."'>&nbsp;&nbsp;<a href='");
      print(getURL($node));
      print("'>");
      print($node->cm_name);
      print("</a></td></tr>");
   }
   
   function outputTable($title, $node, $type_filter, $empty_message)
   {

     print(
     "<table cellspacing=0 cellpadding=0 border=0 width=95% align=center>".
     "   <tr>".
     "       <td width=7><img src='../Common/Images/blue_01.gif' width=7 height=7 alt=''></td><td background='../Common/Images/blue_02.gif'><img src='../Common/Images/blue_02.gif' width=7 height=7 alt=''></td>".
     "       <td width=7><img src='../Common/Images/blue_03.gif' width=7 height=7 alt=''></td></tr><tr><td background='../Common/Images/blue_04.gif'><img src='../Common/Images/blue_04.gif' width=7 height=7 alt=''></td>".
     "       <td bgcolor='#D3E6FE'>".
     "           <table border='0' cellspacing='0' cellpadding='0' width='100%'><tr><td><span class='mainSubTitle'>".$title."</span></td></tr></table>".
     "       </td>".
     "       <td background='../Common/Images/blue_06.gif'><img src='../Common/Images/blue_06.gif' width=7 height=7 alt=''></td>".
     "   </tr>".
     "   <tr>".
     "       <td width=7><img src='../Common/Images/blue_white_07.gif' width=7 height=7 alt=''></td>".
     "       <td background='../Common/Images/blue_08.gif'><img src='../Common/Images/blue_08.gif' width=7 height=7 alt=''></td>".
     "       <td width=7><img src='../Common/Images/blue_white_09.gif' width=7 height=7 alt=''></td>".
     "   </tr>".
     "   <tr>".
     "       <td background='../Common/Images/white_04.gif'><img src='../Common/Images/white_04.gif' width=7 height=7 alt=''></td>".
     "       <td bgcolor='white' style='padding-top:6px;'>".
     "           <table border='0' width='100%'>");

      foreach ($node->children as $child)
      {
      	 if ($child->child->type == $type_filter)
         {
            outputRow($child->child);
         }
      }

      print(
      "         </table>".
      "      </td>".
      "      <td background='../Common/Images/white_06.gif'><img src='../Common/Images/white_06.gif' width=7 height=7 alt=''></td>".
      "   </tr>".
      "   <tr>".
      "      <td width=7><img src='../Common/Images/white_07.gif' width=7 height=7 alt=''></td>".
      "      <td background='../Common/Images/white_08.gif'><img src='../Common/Images/white_08.gif' width=7 height=7 alt=''></td>".
      "      <td width=7><img src='../Common/Images/white_09.gif' width=7 height=7 alt=''></td>".
      "   </tr>".
      "</table>");
   }
   
   function outputBreadcrumb($path)
   {
   	 global $session, $store;
   	
      print(
          '<table border="0" width="95%" align="center">'.
          '   <tr>'.
          '      <td>');

      $values = split("\|", $path);
      $home = $values[0];
      $path = $home;
      $id_map = array();
      for ($counter = 1; $counter < count($values); $counter += 2)
      {
         $id_map[$values[$counter]] = $values[$counter+1];
      }

       print("<a href='index.php'><b>".$home."</b></a>");
       foreach($id_map as $id=>$name)
       {
          $path .= '|'.$id.'|'.$name;
          print("&nbsp;&gt;&nbsp;<a href='".getURL($session->getNode($store, $id))."'><b>".$name."</b></a>");
       }

       print(
        '      </td>'.
        '   </tr>'.
        '</table>');
   }

?>

<html>
   <head>
      <title>Browse Repository</title>
      <style>
         body {font-family: verdana; font-size: 8pt;}
         tr {font-family: verdana; font-size: 8pt;}
         td {font-family: verdana; font-size: 8pt;}
         input {font-family: verdana; font-size: 8pt;}
         .maintitle {font-family: verdana; font-size: 10pt; font-weight: bold; padding-bottom: 15px;}
         a:link, a:visited
         {
      	 font-size: 11px;
      	 color: #465F7D;
      	 text-decoration: none;
      	 font-family: Tahoma, Arial, Helvetica, sans-serif;
      	 font-weight: normal;
        }
        a:hover
        {
        	color: #4272B4;
        	text-decoration: underline;
        	font-weight: normal;
        }
      </style>
   </head>

   <body>

   <table cellspacing=0 cellpadding=2 width=95% align=center>
      <tr>
          <td width=100%>

            <table cellspacing=0 cellpadding=0 width=100%>
            <tr>
               <td style="padding-right:4px;"><img src="../Common/Images/AlfrescoLogo32.png" border=0 alt="Alfresco" title="Alfresco" align=absmiddle></td>
               <td><img src="../Common/Images/titlebar_begin.gif" width=10 height=30></td>
               <td width=100% style="background-image: url(../Common/Images/titlebar_bg.gif)">
                   <b><font style='color: white'>Company Home</font></b>
               </td>
               <td><img src="../Common/Images/titlebar_end.gif" width=8 height=30></td>
            </tr>
            </table>

          </td>

          <td width=8>&nbsp;</td>
          <td><nobr>
              <img src="../Common/Images/logout.gif" border=0 alt="Logout" align=absmiddle><span style='padding-left:2px'><a href='#'>Logout</a></span>
           </nobr></td>
        </tr>
   </table>
   <br>

<?php
       outputBreadcrumb($path);
?>
<br>
<?php
       outputTable("Browse Spaces", $currentNode, "{http://www.alfresco.org/model/content/1.0}folder", "There are no spaces");
?>
<br>
<?php
       outputTable("Content Items", $currentNode, "{http://www.alfresco.org/model/content/1.0}content", "There is no content");
?>

   </body>

</html>
