{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
  <h2>{$lblAnalytics|ucfirst}</h2>
</div>

<div class="box">
  <div class="heading">
    <h3>{$lblStatistics|ucfirst} {$lblFrom} {$startTimestamp|formatdate} {$lblTill} {$endTimestamp|formatdate}</h3>
  </div>

  <div class="footer oneLiner">
    {form:dates}
      <p>
        <label for="startDate">{$lblStartDate|ucfirst}</label>
        {$txtStartDate}
      </p>
      <p>
        <label for="endDate">{$lblEndDate|ucfirst}</label>
        {$txtEndDate}
      </p>
      <p>
        <input id="update" type="submit" name="update" value="{$lblChangePeriod|ucfirst}" />
      </p>
      {$txtStartDateError}
      {$txtEndDateError}
    {/form:dates}
  </div>
  <div class="options">
    STATISTICS YAY
  </div>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
