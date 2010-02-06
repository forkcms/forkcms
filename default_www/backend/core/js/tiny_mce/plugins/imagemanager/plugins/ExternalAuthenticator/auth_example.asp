<% @LANGUAGE="VBScript" %>
<% Option Explicit %>
<!--#include file="md5.asp"-->
<%
	' Change this secret key so it matches the one in the imagemanager/filemanager config
	Private Const SECRET_KEY = "someSecretKey"
	Dim config, data, key, value

	' Check if user is logged in here
	' If Session("some_session") <> True Then
	'	Response.Write "You are not logged in."
	'	Response.End
	' End If

	Set config = Server.CreateObject("Scripting.Dictionary")

	' Override config values
	' config("filesystem.path") = "c:/Inetpub/wwwroot/somepath"
	' config("filesystem.rootpath") = "c:/Inetpub/wwwroot/somepath"

	' Generate MD5 of config values
	data = ""

	For Each value In config.Items
		data = data & value
	Next

	key = MD5(data & SECRET_KEY)
%>

<html>
<body onload="document.forms[0].submit();">
<form method="post" action="<%= Server.HTMLEncode(Request.QueryString("return_url")) %>">
<input type="hidden" name="key" value="<%= Server.HTMLEncode(key) %>" />
<%
	For Each key In config.Keys
%>
	<input type="hidden" name="<%= Server.HTMLEncode(Replace(key, ".", "__") ) %>" value="<%= Server.HTMLEncode(config(key)) %>" />
<%
	Next
%>
</form>
</body>
</html>