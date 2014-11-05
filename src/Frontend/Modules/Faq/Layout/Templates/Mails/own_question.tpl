{include:/Core/Layout/Templates/Mails/Header.tpl}

<h2>Fork CMS: {$msgFaqOwnQuestionSubject|sprintf:'{$name}'}</h2>
<h3>{$lblSenderInformation|ucfirst}</h3>
<p>
  <strong>{$lblSentOn|ucfirst}:</strong><br/>
  {$sentOn|date:{$dateFormatLong}:{$LANGUAGE}}
</p>

<h3>{$lblContent|ucfirst}</h3>
<p><strong>{$lblName|ucfirst}:</strong><br/> {$name}</p>
<p><strong>{$lblEmail|ucfirst}:</strong><br/> {$email}</p>
<p><strong>{$lblQuestion|ucfirst}:</strong><br/> {$message}</p>

{include:/Core/Layout/Templates/Mails/Footer.tpl}
