{include:/Core/Layout/Templates/Mails/Header.tpl}

<h2>{$msgForgotPasswordSubject}</h2>
<hr/><br/>
<p>{$msgForgotPasswordSalutation}</p>
<p>{$msgForgotPasswordBody|sprintf:{$SITE_URL}:{$resetUrl}}</p>
<p>{$msgForgotPasswordClosure}</p>

{include:/Core/Layout/Templates/Mails/Footer.tpl}
