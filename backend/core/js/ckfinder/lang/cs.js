/*
 * CKFinder
 * ========
 * http://ckfinder.com
 * Copyright (C) 2007-2012, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file, and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying, or distributing this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 *
 */

/**
 * @fileOverview Defines the {@link CKFinder.lang} object for the Czech
 *		language.
 */

/**
 * Contains the dictionary of language entries.
 * @namespace
 */
CKFinder.lang['cs'] =
{
	appTitle : 'CKFinder',

	// Common messages and labels.
	common :
	{
		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, nedostupné</span>',
		confirmCancel	: 'Některá z nastavení byla změněna. Skutečně chcete dialogové okno zavřít?',
		ok				: 'OK',
		cancel			: 'Zrušit',
		confirmationTitle	: 'Potvrzení',
		messageTitle	: 'Informace',
		inputTitle		: 'Otázka',
		undo			: 'Zpět',
		redo			: 'Znovu',
		skip			: 'Přeskočit',
		skipAll			: 'Přeskočit vše',
		makeDecision	: 'Co by se mělo provést?',
		rememberDecision: 'Zapamatovat si mé rozhodnutí'
	},


	// Language direction, 'ltr' or 'rtl'.
	dir : 'ltr',
	HelpLang : 'cs',
	LangCode : 'cs',

	// Date Format
	//		d    : Day
	//		dd   : Day (padding zero)
	//		m    : Month
	//		mm   : Month (padding zero)
	//		yy   : Year (two digits)
	//		yyyy : Year (four digits)
	//		h    : Hour (12 hour clock)
	//		hh   : Hour (12 hour clock, padding zero)
	//		H    : Hour (24 hour clock)
	//		HH   : Hour (24 hour clock, padding zero)
	//		M    : Minute
	//		MM   : Minute (padding zero)
	//		a    : Firt char of AM/PM
	//		aa   : AM/PM
	DateTime : 'd/m/yyyy H:MM',
	DateAmPm : ['AM', 'PM'],

	// Folders
	FoldersTitle	: 'Složky',
	FolderLoading	: 'Načítání...',
	FolderNew		: 'Zadejte název nové složky: ',
	FolderRename	: 'Zadejte nový název složky: ',
	FolderDelete	: 'Opravdu chcete složku "%1" smazat?',
	FolderRenaming	: ' (Přejmenovávání...)',
	FolderDeleting	: ' (Mazání...)',

	// Files
	FileRename		: 'Zadejte nový název souboru: ',
	FileRenameExt	: 'Opravdu chcete změnit příponu souboru? Soubor se může stát nepoužitelným.',
	FileRenaming	: 'Přejmenovávání...',
	FileDelete		: 'Opravdu chcete smazat soubor "%1"?',
	FilesLoading	: 'Načítání...',
	FilesEmpty		: 'Prázdná složka.',
	FilesMoved		: 'Soubor %1 přesunut do %2:%3.',
	FilesCopied		: 'Soubor %1 zkopírován do %2:%3.',

	// Basket
	BasketFolder		: 'Košík',
	BasketClear			: 'Vyčistit Košík',
	BasketRemove		: 'Odstranit z Košíku',
	BasketOpenFolder	: 'Otevřít nadřazenou složku',
	BasketTruncateConfirm : 'Opravdu chcete z Košíku odstranit všechny soubory?',
	BasketRemoveConfirm	: 'Opravdu chcete odstranit soubor "%1" z Košíku?',
	BasketEmpty			: 'V Košíku nejsou žádné soubory, tak sem některé přetáhněte.',
	BasketCopyFilesHere	: 'Kopírovat soubory z Košíku',
	BasketMoveFilesHere	: 'Přesunout soubory z Košíku',

	BasketPasteErrorOther	: 'Soubor %s chyba: %e',
	BasketPasteMoveSuccess	: 'Následující soubory byly přesunuty: %s',
	BasketPasteCopySuccess	: 'Následující soubory byly zkopírovány: %s',

	// Toolbar Buttons (some used elsewhere)
	Upload		: 'Nahrát',
	UploadTip	: 'Nahrát nový soubor',
	Refresh		: 'Znovu načíst',
	Settings	: 'Nastavení',
	Help		: 'Nápověda',
	HelpTip		: 'Nápověda',

	// Context Menus
	Select			: 'Vybrat',
	SelectThumbnail : 'Vybrat náhled',
	View			: 'Zobrazit',
	Download		: 'Uložit jako',

	NewSubFolder	: 'Nová podsložka',
	Rename			: 'Přejmenovat',
	Delete			: 'Smazat',

	CopyDragDrop	: 'Soubor zkopírovat sem',
	MoveDragDrop	: 'Soubor přesunout sem',

	// Dialogs
	RenameDlgTitle		: 'Přejmenovat',
	NewNameDlgTitle		: 'Nový název',
	FileExistsDlgTitle	: 'Soubor již existuje',
	SysErrorDlgTitle : 'Chyba systému',

	FileOverwrite	: 'Přepsat',
	FileAutorename	: 'Automaticky přejmenovat',

	// Generic
	OkBtn		: 'OK',
	CancelBtn	: 'Zrušit',
	CloseBtn	: 'Zavřít',

	// Upload Panel
	UploadTitle			: 'Nahrát nový soubor',
	UploadSelectLbl		: 'Zvolit soubor k nahrání',
	UploadProgressLbl	: '(Probíhá nahrávání, čekejte...)',
	UploadBtn			: 'Nahrát zvolený soubor',
	UploadBtnCancel		: 'Zrušit',

	UploadNoFileMsg		: 'Vyberte prosím soubor z Vašeho počítače.',
	UploadNoFolder		: 'Před nahráváním vyberte složku prosím.',
	UploadNoPerms		: 'Nahrávání souborů není povoleno.',
	UploadUnknError		: 'Chyba při posílání souboru.',
	UploadExtIncorrect	: 'Přípona souboru není v této složce povolena.',

	// Flash Uploads
	UploadLabel			: 'Soubory k nahrání',
	UploadTotalFiles	: 'Celkem souborů:',
	UploadTotalSize		: 'Celková velikost:',
	UploadSend			: 'Nahrát',
	UploadAddFiles		: 'Přidat soubory',
	UploadClearFiles	: 'Vyčistit soubory',
	UploadCancel		: 'Zrušit nahrávání',
	UploadRemove		: 'Odstranit',
	UploadRemoveTip		: 'Odstranit !f',
	UploadUploaded		: 'Nahráno !n%',
	UploadProcessing	: 'Zpracovávání...',

	// Settings Panel
	SetTitle		: 'Nastavení',
	SetView			: 'Zobrazení:',
	SetViewThumb	: 'Náhled',
	SetViewList		: 'Seznam',
	SetDisplay		: 'Zobrazit:',
	SetDisplayName	: 'Název',
	SetDisplayDate	: 'Datum',
	SetDisplaySize	: 'Velikost',
	SetSort			: 'Seřazení:',
	SetSortName		: 'Podle názvu',
	SetSortDate		: 'Podle data',
	SetSortSize		: 'Podle velikosti',
	SetSortExtension		: 'Podle přípony',

	// Status Bar
	FilesCountEmpty : '<Prázdná složka>',
	FilesCountOne	: '1 soubor',
	FilesCountMany	: '%1 souborů',

	// Size and Speed
	Kb				: '%1 KB',
	Mb				: '%1 MB',
	Gb				: '%1 GB',
	SizePerSecond	: '%1/s',

	// Connector Error Messages.
	ErrorUnknown	: 'Příkaz nebylo možné dokončit. (Chyba %1)',
	Errors :
	{
	 10 : 'Neplatný příkaz.',
	 11 : 'Typ zdroje nebyl v požadavku určen.',
	 12 : 'Požadovaný typ zdroje není platný.',
	102 : 'Špatné název souboru, nebo složky.',
	103 : 'Nebylo možné příkaz dokončit kvůli omezení oprávnění.',
	104 : 'Nebylo možné příkaz dokončit kvůli omezení oprávnění souborového systému.',
	105 : 'Neplatná přípona souboru.',
	109 : 'Neplatný požadavek.',
	110 : 'Neznámá chyba.',
	115 : 'Soubor nebo složka se stejným názvem již existuje.',
	116 : 'Složka nenalezena, prosím obnovte a zkuste znovu.',
	117 : 'Soubor nenalezen, prosím obnovte seznam souborů a zkuste znovu.',
	118 : 'Cesty zdroje a cíle jsou stejné.',
	201 : 'Soubor se stejným názvem je již dostupný, nahraný soubor byl přejmenován na "%1".',
	202 : 'Neplatný soubor.',
	203 : 'Neplatný soubor. Velikost souboru je příliš velká.',
	204 : 'Nahraný soubor je poškozen.',
	205 : 'Na serveru není dostupná dočasná složka pro nahrávání.',
	206 : 'Nahrávání zrušeno z bezpečnostních důvodů. Soubor obsahuje data podobná HTML.',
	207 : 'Nahraný soubor byl přejmenován na "%1".',
	300 : 'Přesunování souboru(ů) selhalo.',
	301 : 'Kopírování souboru(ů) selhalo.',
	500 : 'Průzkumník souborů je z bezpečnostních důvodů zakázán. Zdělte to prosím správci systému a zkontrolujte soubor nastavení CKFinder.',
	501 : 'Podpora náhledů je zakázána.'
	},

	// Other Error Messages.
	ErrorMsg :
	{
		FileEmpty		: 'Název souboru nemůže být prázdný.',
		FileExists		: 'Soubor %s již existuje.',
		FolderEmpty		: 'Název složky nemůže být prázdný.',

		FileInvChar		: 'Název souboru nesmí obsahovat následující znaky: \n\\ / : * ? " < > |',
		FolderInvChar	: 'Název složky nesmí obsahovat následující znaky: \n\\ / : * ? " < > |',

		PopupBlockView	: 'Soubor nebylo možné otevřít do nového okna. Prosím nastavte si Váš prohlížeč a zakažte veškeré blokování vyskakovacích oken.',
		XmlError		: 'Nebylo možné správně načíst XML odpověď z internetového serveru.',
		XmlEmpty		: 'Nebylo možné načíst XML odpověď z internetového serveru. Server vrátil prázdnou odpověď.',
		XmlRawResponse	: 'Čistá odpověď od serveru: %s'
	},

	// Imageresize plugin
	Imageresize :
	{
		dialogTitle		: 'Změnit velikost %s',
		sizeTooBig		: 'Nelze nastavit šířku či výšku obrázku na hodnotu vyšší než původní velikost (%size).',
		resizeSuccess	: 'Úspěšně změněna velikost obrázku.',
		thumbnailNew	: 'Vytvořit nový náhled',
		thumbnailSmall	: 'Malý (%s)',
		thumbnailMedium	: 'Střední (%s)',
		thumbnailLarge	: 'Velký (%s)',
		newSize			: 'Nastavit novou velikost',
		width			: 'Šířka',
		height			: 'Výška',
		invalidHeight	: 'Neplatná výška.',
		invalidWidth	: 'Neplatná šířka.',
		invalidName		: 'Neplatný název souboru.',
		newImage		: 'Vytvořit nový obrázek',
		noExtensionChange : 'Příponu souboru nelze změnit.',
		imageSmall		: 'Zdrojový obrázek je příliš malý.',
		contextMenuName	: 'Změnit velikost',
		lockRatio		: 'Uzamknout poměr',
		resetSize		: 'Původní velikost'
	},

	// Fileeditor plugin
	Fileeditor :
	{
		save			: 'Uložit',
		fileOpenError	: 'Soubor nelze otevřít.',
		fileSaveSuccess	: 'Soubor úspěšně uložen.',
		contextMenuName	: 'Upravit',
		loadingFile		: 'Načítání souboru, čekejte prosím...'
	},

	Maximize :
	{
		maximize : 'Maximalizovat',
		minimize : 'Minimalizovat'
	},

	Gallery :
	{
		current : 'Obrázek {current} z {total}'
	}
};
