<html>
<head>
<title>API Authorize</title>
</head>
<body>
	<div><a href="#" onclick="requestAccessToken();">Request Access Token</a></div>
	<div id="waiting-box"></div>

	<script type="text/javascript">
		function requestAccessToken() {
	  		document.getElementById("waiting-box").innerHTML="Establishing connection...";
			if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			  }
			xmlhttp.onreadystatechange=function()
			  {
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
			    document.getElementById("waiting-box").innerHTML="Going to authorization page...";
				window.location = xmlhttp.responseText;
			    }
			  }
			xmlhttp.open("GET","save_rs_access.php",true);
			xmlhttp.send();
		}
	</script>
</body>
</html>