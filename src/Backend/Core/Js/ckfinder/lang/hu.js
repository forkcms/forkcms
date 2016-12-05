/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2015, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file, and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying, or distributing this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 *
 */

/**
 * @fileOverview Defines the {@link CKFinder.lang} object for the Hungarian
 *		language.
 */

/**
 * Contains the dictionary of language entries.
 * @namespace
 */
CKFinder.lang['hu'] =
{
	appTitle : 'CKFinder',

	// Common messages and labels.
	common :
	{
		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, nem elérhető</span>',
		confirmCancel	: 'Az űrlap tartalma megváltozott, ám a változásokat nem rögzítette. Biztosan be szeretné zárni az űrlapot?',
		ok				: 'Rendben',
		cancel			: 'Mégsem',
		confirmationTitle	: 'Megerősítés',
		messageTitle	: 'Információ',
		inputTitle		: 'Kérdés',
		undo			: 'Visszavonás',
		redo			: 'Ismétlés',
		skip			: 'Kihagy',
		skipAll			: 'Mindet kihagy',
		makeDecision	: 'Mi történjen a fájllal?',
		rememberDecision: 'Jegyezze meg a választásomat'
	},


	// Language direction, 'ltr' or 'rtl'.
	dir : 'ltr',
	HelpLang : 'en',
	LangCode : 'hu',

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
	DateTime : 'yyyy.mm.dd. HH:MM',
	DateAmPm : ['de.', 'du.'],

	// Folders
	FoldersTitle	: 'Mappák',
	FolderLoading	: 'Betöltés...',
	FolderNew		: 'Kérem adja meg a mappa nevét: ',
	FolderRename	: 'Kérem adja meg a mappa új nevét: ',
	FolderDelete	: 'Biztosan törölni szeretné a következő mappát: "%1"?',
	FolderRenaming	: ' (átnevezés...)',
	FolderDeleting	: ' (törlés...)',
	DestinationFolder	: 'Cél mappa',

	// Files
	FileRename		: 'Kérem adja meg a fájl új nevét: ',
	FileRenameExt	: 'Biztosan szeretné módosítani a fájl kiterjesztését? A fájl esetleg használhatatlan lesz.',
	FileRenaming	: 'Átnevezés...',
	FileDelete		: 'Biztosan törli a következő fájlt: "%1"?',
	FilesDelete	: 'Biztosan törli a kijelölt %1 fájlt?',
	FilesLoading	: 'Betöltés...',
	FilesEmpty		: 'A mappa üres.',
	DestinationFile	: 'Cél fájl',
	SkippedFiles	: 'A kihagyott fájlok listája:',

	// Basket
	BasketFolder		: 'Kosár',
	BasketClear			: 'Kosár ürítése',
	BasketRemove		: 'Törlés a kosárból',
	BasketOpenFolder	: 'A fájlt tartalmazó mappa megnyitása',
	BasketTruncateConfirm : 'Biztosan szeretne minden fájlt törölni a kosárból?',
	BasketRemoveConfirm	: 'Biztosan törölni szeretné a(z) "%1" nevű fájlt a kosárból?',
	BasketRemoveConfirmMultiple	: 'Biztosan törölni szeretné a kijelült %1 fájlt a kosárból?',
	BasketEmpty			: 'Nincsenek fájlok a kosárban.',
	BasketCopyFilesHere	: 'Fájlok másolása a kosárból',
	BasketMoveFilesHere	: 'Fájlok áthelyezése a kosárból',

	// Global messages
	OperationCompletedSuccess	: 'A művelet sikeresen befejeződött.',
	OperationCompletedErrors		: 'A művelet közben hiba történt.',
	FileError				: '%s: %e',

	// Move and Copy files
	MovedFilesNumber		: 'Az áthelyezett fájlok száma: %s.',
	CopiedFilesNumber	: 'A másolt fájlok száma: %s.',
	MoveFailedList		: 'A következő fájlok nem helyezhetőek át:<br />%s',
	CopyFailedList		: 'A következő fájlok nem másolhatóak:<br />%s',

	// Toolbar Buttons (some used elsewhere)
	Upload		: 'Feltöltés',
	UploadTip	: 'Új fájl feltöltése',
	Refresh		: 'Frissítés',
	Settings	: 'Beállítások',
	Help		: 'Súgó',
	HelpTip		: 'Súgó (angolul)',

	// Context Menus
	Select			: 'Kiválaszt',
	SelectThumbnail : 'Bélyegkép kiválasztása',
	View			: 'Megtekintés',
	Download		: 'Letöltés',

	NewSubFolder	: 'Új almappa',
	Rename			: 'Átnevezés',
	Delete			: 'Törlés',
	DeleteFiles		: 'Fájlok törlése',

	CopyDragDrop	: 'Másolás ide',
	MoveDragDrop	: 'Áthelyezés ide',

	// Dialogs
	RenameDlgTitle		: 'Átnevezés',
	NewNameDlgTitle		: 'Új név',
	FileExistsDlgTitle	: 'A fájl már létezik',
	SysErrorDlgTitle : 'Rendszerhiba',

	FileOverwrite	: 'Felülír',
	FileAutorename	: 'Automatikus átnevezés',
	ManuallyRename	: 'Átnevezés',

	// Generic
	OkBtn		: 'OK',
	CancelBtn	: 'Mégsem',
	CloseBtn	: 'Bezárás',

	// Upload Panel
	UploadTitle			: 'Új fájl feltöltése',
	UploadSelectLbl		: 'Válassza ki a feltölteni kívánt fájlt',
	UploadProgressLbl	: '(A feltöltés folyamatban, kérem várjon...)',
	UploadBtn			: 'A kiválasztott fájl feltöltése',
	UploadBtnCancel		: 'Mégsem',

	UploadNoFileMsg		: 'Kérem válassza ki a fájlt a számítógépéről.',
	UploadNoFolder		: 'A feltöltés előtt válasszon mappát.',
	UploadNoPerms		: 'A fájlok feltöltése nem engedélyezett.',
	UploadUnknError		: 'Hiba a fájl feltöltése közben.',
	UploadExtIncorrect	: 'A fájl kiterjesztése nem engedélyezett ebben a mappában.',

	// Flash Uploads
	UploadLabel			: 'Feltöltendő fájlok',
	UploadTotalFiles	: 'Összes fájl:',
	UploadTotalSize		: 'Összméret:',
	UploadSend			: 'Feltöltés',
	UploadAddFiles		: 'Fájl hozzáadása',
	UploadClearFiles	: 'Feltöltési lista törlése',
	UploadCancel		: 'Feltöltés megszakítása',
	UploadRemove		: 'Eltávolít',
	UploadRemoveTip		: 'Fájl eltávolítása a listáról: !f',
	UploadUploaded		: 'Feltöltve !n%',
	UploadProcessing	: 'Feldolgozás...',

	// Settings Panel
	SetTitle		: 'Beállítások',
	SetView			: 'Nézet:',
	SetViewThumb	: 'bélyegképes',
	SetViewList		: 'listás',
	SetDisplay		: 'Megjelenik:',
	SetDisplayName	: 'fájl neve',
	SetDisplayDate	: 'dátum',
	SetDisplaySize	: 'fájlméret',
	SetSort			: 'Rendezés:',
	SetSortName		: 'fájlnév',
	SetSortDate		: 'dátum',
	SetSortSize		: 'méret',
	SetSortExtension		: 'kiterjesztés',

	// Status Bar
	FilesCountEmpty : '<üres mappa>',
	FilesCountOne	: '1 fájl',
	FilesCountMany	: '%1 fájl',

	// Size and Speed
	Kb				: '%1 KB',
	Mb				: '%1 MB',
	Gb				: '%1 GB',
	SizePerSecond	: '%1/s',

	// Connector Error Messages.
	ErrorUnknown	: 'A parancsot nem sikerült végrehajtani. (Hiba: %1)',
	Errors :
	{
	 10 : 'Érvénytelen parancs.',
	 11 : 'A fájl típusa nem lett a kérés során beállítva.',
	 12 : 'A kívánt fájl típus érvénytelen.',
	102 : 'Érvénytelen fájl vagy könyvtárnév.',
	103 : 'Hitelesítési problémák miatt nem sikerült a kérést teljesíteni.',
	104 : 'Jogosultsági problémák miatt nem sikerült a kérést teljesíteni.',
	105 : 'Érvénytelen fájl kiterjesztés.',
	109 : 'Érvénytelen kérés.',
	110 : 'Ismeretlen hiba.',
	111 : 'A kérés nem teljesíthető a létrejövő fájl mérete miatt.',
	115 : 'A fálj vagy mappa már létezik ezen a néven.',
	116 : 'Mappa nem található. Kérem frissítsen és próbálja újra.',
	117 : 'Fájl nem található. Kérem frissítsen és próbálja újra.',
	118 : 'A forrás és a cél azonos.',
	201 : 'Ilyen nevű fájl már létezett. A feltöltött fájl a következőre lett átnevezve: "%1".',
	202 : 'Érvénytelen fájl.',
	203 : 'Érvénytelen fájl. A fájl mérete túl nagy.',
	204 : 'A feltöltött fájl hibás.',
	205 : 'A szerveren nem található a feltöltéshez ideiglenes mappa.',
	206 : 'A fájl feltötése biztonsági okból megszakadt. A fájl HTML adatokat tartalmaz.',
	207 : 'El fichero subido ha sido renombrado como "%1".',
	300 : 'A fájl(ok) áthelyezése sikertelen.',
	301 : 'A fájl(ok) másolása sikertelen.',
	500 : 'A fájl-tallózó biztonsági okok miatt nincs engedélyezve. Kérem vegye fel a kapcsolatot a rendszer üzemeltetőjével és ellenőrizze a CKFinder konfigurációs fájlt.',
	501 : 'A bélyegkép támogatás nincs engedélyezve.'
	},

	// Other Error Messages.
	ErrorMsg :
	{
		FileEmpty		: 'A fájl neve nem lehet üres.',
		FileExists		: 'A(z) %s fájl már létezik.',
		FolderEmpty		: 'A mappa neve nem lehet üres.',
		FolderExists	: 'A(z) %s mappa már létezik.',
		FolderNameExists	: 'A mappa létezik.',

		FileInvChar		: 'A fájl neve nem tartalmazhatja a következő karaktereket: \n\\ / : * ? " < > |',
		FolderInvChar	: 'A mappa neve nem tartalmazhatja a következő karaktereket: \n\\ / : * ? " < > |',

		PopupBlockView	: 'A felugró ablak megnyitása nem sikerült. Kérem ellenőrizze a böngészője beállításait és tiltsa le a felugró ablakokat blokkoló alkalmazásait erre a honlapra.',
		XmlError		: 'A webszervertől érkező XML válasz nem dolgozható fel megfelelően.',
		XmlEmpty		: 'A webszervertől érkező XML válasz nem dolgozható fel. A szerver üres választ küldött.',
		XmlRawResponse	: 'A szerver az alábbi választ adta: %s'
	},

	// Imageresize plugin
	Imageresize :
	{
		dialogTitle		: 'Kép átméretezése: %s',
		sizeTooBig		: 'Nem adható meg az eredeti fájlnál nagyobb méret (%size).',
		resizeSuccess	: 'A kép sikeresen átméretezve.',
		thumbnailNew	: 'Új bélyegkép létrehozása',
		thumbnailSmall	: 'Kicsi (%s)',
		thumbnailMedium	: 'Közepes (%s)',
		thumbnailLarge	: 'Nagy (%s)',
		newSize			: 'Adja meg az új méretet',
		width			: 'Szélesség',
		height			: 'Magasság',
		invalidHeight	: 'Érvénytelen magasság.',
		invalidWidth	: 'Érvénytelen szélesség.',
		invalidName		: 'Érvénytelen fájlnév.',
		newImage		: 'Létrehozás új fotóként',
		noExtensionChange : 'A fájl kiterjesztése nem változtatható.',
		imageSmall		: 'Az eredeti fotó mérete túl kicsi.',
		contextMenuName	: 'Átméretezés',
		lockRatio		: 'Arány megtartása',
		resetSize		: 'Eredeti méret'
	},

	// Fileeditor plugin
	Fileeditor :
	{
		save			: 'Mentés',
		fileOpenError	: 'A fájl nem nyitható meg.',
		fileSaveSuccess	: 'A fájl sikeresen mentve.',
		contextMenuName	: 'Szerkesztés',
		loadingFile		: 'Fájl betöltése, kérem várjon...'
	},

	Maximize :
	{
		maximize : 'Teljes méret',
		minimize : 'Kis méret'
	},

	Gallery :
	{
		current : 'Fotó: {current} / {total}'
	},

	Zip :
	{
		extractHereLabel	: 'Kicsomagolás ide',
		extractToLabel		: 'Kicsomagolás új mappába...',
		downloadZipLabel	: 'Letöltés zip fájlként',
		compressZipLabel	: 'Becsomagolás zip fájlba',
		removeAndExtract	: 'Létező törlése és kicsomagolás',
		extractAndOverwrite	: 'Létező felülírása és kicsomagolás',
		extractSuccess		: 'A fájl kicsomagolása megtörtént.'
	},

	Search :
	{
		searchPlaceholder : 'Keresés'
	}
};
