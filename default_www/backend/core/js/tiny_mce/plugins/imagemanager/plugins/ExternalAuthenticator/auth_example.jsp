<%@page import="java.util.*" %>
<%
	String key, secretKey, data = "";

	// Change this secret key so it matches the one in the imagemanager/filemanager config
	secretKey = "someSecretKey";

	// Check here if the user is logged in or not
	/*
	if (session.getAttribute("some_session") != "somevalue") {
		out.print("You are not logged in.");
		return;
	}
	*/

	// Override any config values here
	Hashtable configuration = new Hashtable();
	//configuration.put("filesystem.path", "c:/Inetpub/wwwroot/somepath");
	//configuration.put("filesystem.rootpath", "c:/Inetpub/wwwroot/somepath");

	// Generates a unique key of the config values with the secret key
	for (Enumeration e = configuration.keys(); e.hasMoreElements(); )
		data += configuration.get(e.nextElement());

	key = md5(data + secretKey);
%>
<%!
	public String md5(String str) {
		try {
			java.security.MessageDigest md5 = java.security.MessageDigest.getInstance("MD5");

			char[] charArray = str.toCharArray();
			byte[] byteArray = new byte[charArray.length];

			for (int i=0; i<charArray.length; i++)
				byteArray[i] = (byte) charArray[i];

			byte[] md5Bytes = md5.digest(byteArray);
			StringBuffer hexValue = new StringBuffer();

			for (int i=0; i<md5Bytes.length; i++) {
				int val = ((int) md5Bytes[i] ) & 0xff;

				if (val < 16)
					hexValue.append("0");

				hexValue.append(Integer.toHexString(val));
			}

			return hexValue.toString();
		} catch (java.security.NoSuchAlgorithmException e) {
			// Ignore
		}

		return "";
	}

	public String htmlEncode(String str) {
		StringBuffer buff = new StringBuffer();

		for (int i=0; i<str.length(); i++) {
			char chr = str.charAt(i);

			switch (chr) {
				case '<':
					buff.append("&lt;");
					break;

				case '>':
					buff.append("&gt;");
					break;

				case '"':
					buff.append("&quot;");
					break;

				case '&':
					buff.append("&amp;");
					break;

				default:
					buff.append(chr);
			}
		}

		return buff.toString();
	}
%>

<html>
<body onload="document.forms[0].submit();">
<form method="post" action="<%= htmlEncode(request.getParameter("return_url")) %>">
<input type="hidden" name="key" value="<%= htmlEncode(key) %>" />
<%
	for (Enumeration e = configuration.keys(); e.hasMoreElements(); ) {
		key = (String) e.nextElement();
%>
	<input type="hidden" name="<%= htmlEncode(key.replaceAll("\\.", "__")) %>" value="<%= htmlEncode((String) configuration.get(key)) %>" />
<%
	}
%>
</form>
</body>
</html>