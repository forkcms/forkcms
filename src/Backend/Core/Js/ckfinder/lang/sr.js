/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2014, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file, and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying, or distributing this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 *
 */

/**
 * @fileOverview Defines the {@link CKFinder.lang} object for the Serbian
 * Translation for the Serbian language: Goran Markovic, University Computer Center of Banja Luka
 *
 */

/**
 * Contains the dictionary of language entries.
 * @namespace
 */
CKFinder.lang['sr'] =
{
	appTitle : 'Датотеке',

	// Common messages and labels.
	common :
	{
		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, недоступно</span>',
		confirmCancel	: 'Неке од опција су промјењене. Да ли сте сигурни да желите затворити прозор??',
		ok				: 'У реду',
		cancel			: 'Поништи',
		confirmationTitle	: 'Потврда',
		messageTitle	: 'Информација',
		inputTitle		: 'Питање',
		undo			: 'Поништи',
		redo			: 'Преуреди',
		skip			: 'Прескочи',
		skipAll			: 'Прескочи све',
		makeDecision	: 'Шта би требали направити?',
		rememberDecision: 'Запамти мој избор'
	},


	// Language direction, 'ltr' or 'rtl'.
	dir : 'ltr',
	HelpLang : 'en',
	LangCode : 'sr',

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
	DateTime : 'd.m.yyyy HH:MM',
	DateAmPm : ['AM', 'PM'],

	// Folders
	FoldersTitle	: 'Фасцикле',
	FolderLoading	: 'Учитавам...',
	FolderNew		: 'Унесите ново име фасцикле: ',
	FolderRename	: 'Унесите ново име фасцикле: ',
	FolderDelete	: 'Да ли сте сигурни да желите обрисати фасциклу "%1"?',
	FolderRenaming	: ' (Промјена назива фасцикле...)',
	FolderDeleting	: ' (Брисање...)',
	DestinationFolder	: 'Destination Folder', // MISSING

	// Files
	FileRename		: 'Унесите нови назив датотеке: ',
	FileRenameExt	: 'Да ли сте сигурни да желите промјенити тип датотеке? Датотека може постати неискористива.',
	FileRenaming	: 'Промјена назива датотеке...',
	FileDelete		: 'Да ли сте сигурни да желите обрисати датотеку "%1"?',
	FilesDelete	: 'Are you sure you want to delete %1 files?', // MISSING
	FilesLoading	: 'Учитавам...',
	FilesEmpty		: 'Фасцикла је празна.',
	DestinationFile	: 'Destination File', // MISSING
	SkippedFiles	: 'List of skipped files:', // MISSING

	// Basket
	BasketFolder		: 'Канта',
	BasketClear			: 'Испразни канту',
	BasketRemove		: 'Уклони из канте',
	BasketOpenFolder	: 'Отвори надређену фасциклу',
	BasketTruncateConfirm : 'Да ли сте сигурни да желите обрисати све датотеке из канте?',
	BasketRemoveConfirm	: 'Да ли сте сигурни да желите обрисати датотеку "%1" из канте?',
	BasketRemoveConfirmMultiple	: 'Do you really want to remove %1 files from the basket?', // MISSING
	BasketEmpty			: 'Ниједна датотека није пронађена, додајте коју.',
	BasketCopyFilesHere	: 'Копирај датотеке из канте',
	BasketMoveFilesHere	: 'Премјести датотеке из канте',

	// Global messages
	OperationCompletedSuccess	: 'Operation completed successfully.', // MISSING
	OperationCompletedErrors		: 'Operation completed with errors.', // MISSING
	FileError				: '%s: %e', // MISSING

	// Move and Copy files
	MovedFilesNumber		: 'Number of files moved: %s.', // MISSING
	CopiedFilesNumber	: 'Number of files copied: %s.', // MISSING
	MoveFailedList		: 'The following files could not be moved:<br />%s', // MISSING
	CopyFailedList		: 'The following files could not be copied:<br />%s', // MISSING

	// Toolbar Buttons (some used elsewhere)
	Upload		: 'Отпреми',
	UploadTip	: 'Отпреми нове датотеке на сервер',
	Refresh		: 'Освјежи',
	Settings	: 'Подешавања',
	Help		: 'Помоћ',
	HelpTip		: 'Помоћ',

	// Context Menus
	Select			: 'Одабери',
	SelectThumbnail : 'Одабери мању слику',
	View			: 'Погледај',
	Download		: 'Преузми',

	NewSubFolder	: 'Нова подфасцикла',
	Rename			: 'Промјени назив',
	Delete			: 'Обриши',
	DeleteFiles		: 'Delete Files', // MISSING

	CopyDragDrop	: 'Копирај датотеку овдје',
	MoveDragDrop	: 'Премјести датотеку овдје',

	// Dialogs
	RenameDlgTitle		: 'Промјени назив',
	NewNameDlgTitle		: 'Нови назив',
	FileExistsDlgTitle	: 'Датотека већ постоји',
	SysErrorDlgTitle : 'Грешка система',

	FileOverwrite	: 'Препиши',
	FileAutorename	: 'Аутоматска промјена назива',
	ManuallyRename	: 'Manually rename', // MISSING

	// Generic
	OkBtn		: 'У реду',
	CancelBtn	: 'Поништи',
	CloseBtn	: 'Затвори',

	// Upload Panel
	UploadTitle			: 'Отпреми нову датотеку',
	UploadSelectLbl		: 'Одабери датотеку за отпремање',
	UploadProgressLbl	: '(Слање у току, молимо сачекајте...)',
	UploadBtn			: 'Отпреми одабрану датотеку',
	UploadBtnCancel		: 'Поништи',

	UploadNoFileMsg		: 'Одаберите датотеку на Вашем рачунару.',
	UploadNoFolder		: 'Одаберите фасцикле прије отпремања.',
	UploadNoPerms		: 'Отпремање датотеке није дозвољено.',
	UploadUnknError		: 'Грешка приликом отпремања датотеке.',
	UploadExtIncorrect	: 'Тип датотеке није дозвољен.',

	// Flash Uploads
	UploadLabel			: 'Датотека за отпремање:',
	UploadTotalFiles	: 'Укупно датотека:',
	UploadTotalSize		: 'Укупна величина:',
	UploadSend			: 'Отпреми',
	UploadAddFiles		: 'Додај датотеке',
	UploadClearFiles	: 'Избаци датотеке',
	UploadCancel		: 'Поништи отпремање',
	UploadRemove		: 'Уклони',
	UploadRemoveTip		: 'Уклони !f',
	UploadUploaded		: 'Послато !n%',
	UploadProcessing	: 'Обрада у току...',

	// Settings Panel
	SetTitle		: 'Подешавања',
	SetView			: 'Преглед:',
	SetViewThumb	: 'Мала слика',
	SetViewList		: 'Листа',
	SetDisplay		: 'Приказ:',
	SetDisplayName	: 'Назив датотеке',
	SetDisplayDate	: 'Датум',
	SetDisplaySize	: 'Величина датотеке',
	SetSort			: 'Сортирање:',
	SetSortName		: 'по називу',
	SetSortDate		: 'по датуму',
	SetSortSize		: 'по величини',
	SetSortExtension		: 'по врсти датотеке',

	// Status Bar
	FilesCountEmpty : '<Празна фасцикла>',
	FilesCountOne	: '1 датотека',
	FilesCountMany	: '%1 датотека(е)',

	// Size and Speed
	Kb				: '%1 KB',
	Mb				: '%1 MB',
	Gb				: '%1 GB',
	SizePerSecond	: '%1/s',

	// Connector Error Messages.
	ErrorUnknown	: 'Није могуће завршити захтјев. (Грешка %1)',
	Errors :
	{
	 10 : 'Непозната наредба.',
	 11 : 'Није наведена врста у захтјеву.',
	 12 : 'Затражена врста није важећа.',
	102 : 'Неисправан назив датотеке или фасцикле.',
	103 : 'Није могуће извршити захтјев због ограничења приступа.',
	104 : 'Није могуће извршити захтјев због ограничења поставке система.',
	105 : 'Недозвољена врста датотеке.',
	109 : 'Недозвољен захтјев.',
	110 : 'Непозната грешка.',
	111 : 'It was not possible to complete the request due to resulting file size.', // MISSING
	115 : 'Датотека или фасцикла с истим називом већ постоји.',
	116 : 'Фасцикла није пронађена. Освјежите страницу и покушајте поново.',
	117 : 'Датотека није пронађена. Освјежите листу датотека и покушајте поново.',
	118 : 'Путања извора и одредишта су исте.',
	201 : 'Датотека с истим називом већ постоји. Отпремљена датотека је промјењена у "%1".',
	202 : 'Неисправна датотека.',
	203 : 'Неисправна датотека. Величина датотеке је превелика.',
	204 : 'Отпремљена датотека је неисправна.',
	205 : 'Не постоји привремена фасцикла за отпремање на серверe.',
	206 : 'Слање је поништено због сигурносних поставки. Назив датотеке садржи HTML податке.',
	207 : 'Отпремљена датотека је промјењена у "%1".',
	300 : 'Премјештање датотеке(а) није успјело.',
	301 : 'Копирање датотеке(а) није успјело.',
	500 : 'Претраживање датотека није дозвољено из сигурносних разлога. Молимо контактирајте администратора система како би провјерили поставке CKFinder конфигурационе датотеке.',
	501 : 'Thumbnail подршка није омогућена.'
	},

	// Other Error Messages.
	ErrorMsg :
	{
		FileEmpty		: 'Назив датотеке не смије бити празан.',
		FileExists		: 'Датотека %s већ постоји.',
		FolderEmpty		: 'Назив фасцикле не смије бити празан.',
		FolderExists	: 'Folder %s already exists.', // MISSING
		FolderNameExists	: 'Folder already exists.', // MISSING

		FileInvChar		: 'Назив датотеке не смије садржавати нити један од сљедећих знакова: \n\\ / : * ? " < > |',
		FolderInvChar	: 'Назив фасцикле не смије садржавати нити један од сљедећих знакова: \n\\ / : * ? " < > |',

		PopupBlockView	: 'Није могуће одтворити датотеку у новом прозору. Промјените подешавања свог интернет претраживача и искључите све popup блокере за ове web странице.',
		XmlError		: 'Није могуће учитати XML одговор од web сервера.',
		XmlEmpty		: 'Није могуће учитати XML одговор од web сервера. Сервер је вратио празан одговор.',
		XmlRawResponse	: 'Одговор сервера: %s'
	},

	// Imageresize plugin
	Imageresize :
	{
		dialogTitle		: 'Промијени величину %s',
		sizeTooBig		: 'Није могуће поставити величину већу од оригинала (%size).',
		resizeSuccess	: 'Слика је успјешно промјењена.',
		thumbnailNew	: 'Направи малу слику',
		thumbnailSmall	: 'Мала (%s)',
		thumbnailMedium	: 'Средња (%s)',
		thumbnailLarge	: 'Велика (%s)',
		newSize			: 'Постави нову величину',
		width			: 'Ширина',
		height			: 'Висина',
		invalidHeight	: 'Неисправна висина.',
		invalidWidth	: 'Неисправна ширина.',
		invalidName		: 'Неисправан назив датотеке.',
		newImage		: 'Направи нову слику',
		noExtensionChange : 'Тип датотеке се не смије мијењати.',
		imageSmall		: 'Изворна слика је премала.',
		contextMenuName	: 'Промијени величину',
		lockRatio		: 'Закључај односе',
		resetSize		: 'Врати величину'
	},

	// Fileeditor plugin
	Fileeditor :
	{
		save			: 'Сачувај',
		fileOpenError	: 'Није могуће отворити датотеку.',
		fileSaveSuccess	: 'Датотека је успјешно сачувана.',
		contextMenuName	: 'Промјени',
		loadingFile		: 'Учитавање, молимо причекајте...'
	},

	Maximize :
	{
		maximize : 'Повећај',
		minimize : 'Смањи'
	},

	Gallery :
	{
		current : 'Слика {current} од {total}'
	},

	Zip :
	{
		extractHereLabel	: 'Extract here', // MISSING
		extractToLabel		: 'Extract to...', // MISSING
		downloadZipLabel	: 'Download as zip', // MISSING
		compressZipLabel	: 'Compress to zip', // MISSING
		removeAndExtract	: 'Remove existing and extract', // MISSING
		extractAndOverwrite	: 'Extract overwriting existing files', // MISSING
		extractSuccess		: 'File extracted successfully.' // MISSING
	},

	Search :
	{
		searchPlaceholder : 'Претрага'
	}
};
