
<%EnableSessionState=False
host = Request.ServerVariables("HTTP_HOST")

if host = "sensesafari.com" or host = "www.senesafari.com" then response.redirect("https://www.senesafari.com/")

else
response.redirect("https://www.senesafari.com/_error404.html")
end if
%>