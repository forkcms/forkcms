{include:/Core/Layout/Templates/Mails/Header.tpl}

<h2>Fork CMS: {$msgFaqFeedbackSubject|sprintf:'{$question}'}</h2>
<hr/>
<h3>{$lblSenderInformation|ucfirst}</h3>
<p>
  <strong>{$lblSentOn|ucfirst}:</strong><br/>
  {$sentOn|date:{$dateFormatLong}:{$LANGUAGE}}
</p>

<h3>{$lblContent|ucfirst}</h3>
<p>
  <strong>{$lblFeedback|ucfirst}:</strong><br/>
  {$text}
</p>

{include:/Core/Layout/Templates/Mails/Footer.tpl}
