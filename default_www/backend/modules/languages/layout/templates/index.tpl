{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

<h2>{$msgHeaderIndex}</h2>
{option:datagrid}{$datagrid}{/option:datagrid}
{option:!datagrid}<p>Allo kroket, der zijn precies geen items die aan uw search criteria voldoen ?</p>{/option:!datagrid}


{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}