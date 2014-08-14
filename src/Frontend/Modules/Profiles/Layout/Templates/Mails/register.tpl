{include:/Core/Layout/Templates/Mails/Header.tpl}

<h2>{$msgRegisterSubject}</h2>
<hr/><br/>
<p>{$msgRegisterSalutation}</p>
<p>{$msgRegisterBody|sprintf:'{$SITE_URL}':'{$activationUrl}'}</p>
<p>{$msgRegisterClosure}</p>

{include:/Core/Layout/Templates/Mails/Footer.tpl}
