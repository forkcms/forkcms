{include:/Core/Layout/Templates/Mails/Header.tpl}

<h2>{$SITE_TITLE}: {$msgFormBuilderSubject|sprintf:{$name}}</h2>
<hr/>

<h3>{$lblSenderInformation|ucfirst}</h3>
<p>
  <strong>{$lblSentOn|ucfirst}:</strong><br/>
  {$sentOn|date:{$dateFormatLong}:{$LANGUAGE}}
</p>

<h3>{$lblContent|ucfirst}</h3>
{iteration:fields}
  <p><strong>{$fields.label}:</strong><br/> {$fields.value}</p>
{/iteration:fields}

{include:/Core/Layout/Templates/Mails/Footer.tpl}
