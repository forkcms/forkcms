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
 * @fileOverview Defines the {@link CKFinder.lang} object for the Italian
 *		language.
 */

/**
 * Contains the dictionary of language entries.
 * @namespace
 */
CKFinder.lang['it'] =
{
	appTitle : 'CKFinder',

	// Common messages and labels.
	common :
	{
		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, non disponibile</span>',
		confirmCancel	: 'Alcune delle opzioni sono state cambiate. Sei sicuro di voler chiudere la finestra di dialogo?',
		ok				: 'OK',
		cancel			: 'Annulla',
		confirmationTitle	: 'Confermare',
		messageTitle	: 'Informazione',
		inputTitle		: 'Domanda',
		undo			: 'Annulla',
		redo			: 'Ripristina',
		skip			: 'Ignora',
		skipAll			: 'Ignora tutti',
		makeDecision	: 'Che azione prendere?',
		rememberDecision: 'Ricorda mia decisione'
	},


	// Language direction, 'ltr' or 'rtl'.
	dir : 'ltr',
	HelpLang : 'en',
	LangCode : 'it',

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
	DateTime : 'dd/mm/yyyy HH:MM',
	DateAmPm : ['AM', 'PM'],

	// Folders
	FoldersTitle	: 'Cartelle',
	FolderLoading	: 'Caricando...',
	FolderNew		: 'Nome della cartella: ',
	FolderRename	: 'Nuovo nome della cartella: ',
	FolderDelete	: 'Se sicuro di voler eliminare la cartella "%1"?',
	FolderRenaming	: ' (Rinominando...)',
	FolderDeleting	: ' (Eliminando...)',
	DestinationFolder	: 'Cartella di destinazione',

	// Files
	FileRename		: 'Nuovo nome del file: ',
	FileRenameExt	: 'Sei sicure di voler cambiare l\'estensione del file? Il file può risultare inutilizzabile.',
	FileRenaming	: 'Rinominando...',
	FileDelete		: 'Sei sicuro di voler eliminare il file "%1"?',
	FilesDelete	: 'Sei sicuro di voler eliminare %1 file?',
	FilesLoading	: 'Caricamento in corso...',
	FilesEmpty		: 'Cartella vuota',
	DestinationFile	: 'File di destinazione',
	SkippedFiles	: 'Elenco file trascurati:',

	// Basket
	BasketFolder		: 'Cestino',
	BasketClear			: 'Svuota cestino',
	BasketRemove		: 'Rimuove dal cestino',
	BasketOpenFolder	: 'Apri cartella superiore',
	BasketTruncateConfirm : 'Sei sicuro di voler svuotare il cestino?',
	BasketRemoveConfirm	: 'Sei sicuro di voler rimuovere il file "%1" dal cestino?',
	BasketRemoveConfirmMultiple	: 'Sei sicuro di voler rimuovere %1 file dal cestino?',
	BasketEmpty			: 'Cestino vuoto, trascinare prima almeno un file.',
	BasketCopyFilesHere	: 'Copia i file dal cestino',
	BasketMoveFilesHere	: 'Muovi i file dal cestino',

	// Global messages
	OperationCompletedSuccess	: 'Operazione completata con successo.',
	OperationCompletedErrors		: 'Operazione completata con errori.',
	FileError				: '%s: %e', // MISSING

	// Move and Copy files
	MovedFilesNumber		: 'Numero di file spostati: %s.',
	CopiedFilesNumber	: 'Numero di file copiati: %s.',
	MoveFailedList		: 'I seguenti file non hanno potuto essere spostati:<br />%s',
	CopyFailedList		: 'I seguenti file non hanno potuto essere copiati:<br />%s',

	// Toolbar Buttons (some used elsewhere)
	Upload		: 'Upload',
	UploadTip	: 'Carica Nuovo File',
	Refresh		: 'Aggiorna',
	Settings	: 'Configurazioni',
	Help		: 'Aiuto',
	HelpTip		: 'Aiuto (Inglese)',

	// Context Menus
	Select			: 'Seleziona',
	SelectThumbnail : 'Seleziona la miniatura',
	View			: 'Vedi',
	Download		: 'Scarica',

	NewSubFolder	: 'Nuova Sottocartella',
	Rename			: 'Rinomina',
	Delete			: 'Elimina',
	DeleteFiles		: 'Elimina i file',

	CopyDragDrop	: 'Copia qui',
	MoveDragDrop	: 'Sposta qui',

	// Dialogs
	RenameDlgTitle		: 'Rinomina',
	NewNameDlgTitle		: 'Nuovo nome',
	FileExistsDlgTitle	: 'File già esistente',
	SysErrorDlgTitle : 'Errore di Sistema',

	FileOverwrite	: 'Sovrascrivi',
	FileAutorename	: 'Rinomina automaticamente',
	ManuallyRename	: 'Rinomina manualmente',

	// Generic
	OkBtn		: 'OK',
	CancelBtn	: 'Annulla',
	CloseBtn	: 'Chiudi',

	// Upload Panel
	UploadTitle			: 'Carica Nuovo File',
	UploadSelectLbl		: 'Seleziona il file',
	UploadProgressLbl	: '(Caricamento in corso, attendere prego...)',
	UploadBtn			: 'Carica File',
	UploadBtnCancel		: 'Annulla',

	UploadNoFileMsg		: 'Seleziona il file da caricare',
	UploadNoFolder		: 'Seleziona la cartella prima di caricare.',
	UploadNoPerms		: 'Non è permesso il caricamento di file.',
	UploadUnknError		: 'Errore nel caricamento del file.',
	UploadExtIncorrect	: 'In questa cartella non sono permessi file con questa estensione.',

	// Flash Uploads
	UploadLabel			: 'File da caricare',
	UploadTotalFiles	: 'File:',
	UploadTotalSize		: 'Dimensione:',
	UploadSend			: 'Upload',
	UploadAddFiles		: 'Aggiungi file',
	UploadClearFiles	: 'Elimina file',
	UploadCancel		: 'Annulla il caricamento',
	UploadRemove		: 'Rimuovi',
	UploadRemoveTip		: 'Rimuove !f',
	UploadUploaded		: '!n% caricato',
	UploadProcessing	: 'Attendere...',

	// Settings Panel
	SetTitle		: 'Configurazioni',
	SetView			: 'Vedi:',
	SetViewThumb	: 'Anteprima',
	SetViewList		: 'Lista',
	SetDisplay		: 'Informazioni:',
	SetDisplayName	: 'Nome del file',
	SetDisplayDate	: 'Data',
	SetDisplaySize	: 'Dimensione',
	SetSort			: 'Ordina:',
	SetSortName		: 'per Nome',
	SetSortDate		: 'per Data',
	SetSortSize		: 'per Dimensione',
	SetSortExtension		: 'per Estensione',

	// Status Bar
	FilesCountEmpty : '<Nessun file>',
	FilesCountOne	: '1 file',
	FilesCountMany	: '%1 file',

	// Size and Speed
	Kb				: '%1 KB',
	Mb				: '%1 MB',
	Gb				: '%1 GB',
	SizePerSecond	: '%1/s',

	// Connector Error Messages.
	ErrorUnknown	: 'Impossibile completare la richiesta. (Errore %1)',
	Errors :
	{
	 10 : 'Comando non valido.',
	 11 : 'Il tipo di risorsa non è stato specificato nella richiesta.',
	 12 : 'Il tipo di risorsa richiesto non è valido.',
	102 : 'Nome di file o cartella non valido.',
	103 : 'Non è stato possibile completare la richiesta a causa di restrizioni di autorizzazione.',
	104 : 'Non è stato possibile completare la richiesta a causa di restrizioni nei permessi del file system.',
	105 : 'L\'estensione del file non è valida.',
	109 : 'Richiesta non valida.',
	110 : 'Errore sconosciuto.',
	111 : 'È stato impossibile completare la richiesta a causa della dimensione finale del file.',
	115 : 'Un file o cartella con lo stesso nome è già esistente.',
	116 : 'Cartella non trovata. Aggiornare e riprovare.',
	117 : 'File non trovato. Aggiornare la lista dei file e riprovare.',
	118 : 'I percorsi di origine e di destinazione sono uguali.',
	201 : 'Un file con lo stesso nome è già presente. Il file caricato è stato rinominato in "%1".',
	202 : 'File invalido.',
	203 : 'File invalido. La dimensione del file eccede i limiti del sistema.',
	204 : 'Il file caricato è corrotto.',
	205 : 'Directory temporanea non disponibile sul server.',
	206 : 'Caricamento annullato per motivi di sicurezza. Il file contiene dati in formato HTML.',
	207 : 'Il file caricato è stato rinominato in "%1".',
	300 : 'Non è stato possibile muovere i file.',
	301 : 'Non è stato possibile copiare i file.',
	500 : 'Questo programma è disabilitato per motivi di sicurezza. Contattare l\'amministratore del sistema e verificare le configurazioni di CKFinder.',
	501 : 'Il supporto alle anteprime non è attivo.'
	},

	// Other Error Messages.
	ErrorMsg :
	{
		FileEmpty		: 'Il nome del file non può essere vuoto.',
		FileExists		: 'File %s già esistente.',
		FolderEmpty		: 'Il nome della cartella non può essere vuoto.',
		FolderExists	: 'Cartella %s già esistente.',
		FolderNameExists	: 'Cartella già esistente.',

		FileInvChar		: 'I seguenti caratteri non possono essere usati per comporre il nome del file: \n\\ / : * ? " < > |',
		FolderInvChar	: 'I seguenti caratteri non possono essere usati per comporre il nome della cartella: \n\\ / : * ? " < > |',

		PopupBlockView	: 'Non è stato possile aprire il file in una nuova finestra. Configurare il browser e disabilitare il blocco delle popup.',
		XmlError		: 'Non è stato possibile caricare la risposta XML dal server.',
		XmlEmpty		: 'Non è stato possibile caricare la risposta XML dal server. La risposta è vuota.',
		XmlRawResponse	: 'Risposta originale inviata dal server: %s'
	},

	// Imageresize plugin
	Imageresize :
	{
		dialogTitle		: 'Ridimensiona %s',
		sizeTooBig		: 'Non è possibile usare valori di altezza e larghezza maggiori delle dimensioni originali (%size).',
		resizeSuccess	: 'Immagine ridimensionata.',
		thumbnailNew	: 'Crea una nuova thumbnail',
		thumbnailSmall	: 'Piccolo (%s)',
		thumbnailMedium	: 'Medio (%s)',
		thumbnailLarge	: 'Grande (%s)',
		newSize			: 'Nuove dimensioni',
		width			: 'Larghezza',
		height			: 'Altezza',
		invalidHeight	: 'Altezza non valida.',
		invalidWidth	: 'Larghezza non valida.',
		invalidName		: 'Nome del file non valido.',
		newImage		: 'Crea nuova immagine',
		noExtensionChange : 'L\'estensione del file non può essere cambiata.',
		imageSmall		: 'L\'immagine originale è molto piccola.',
		contextMenuName	: 'Ridimensiona',
		lockRatio		: 'Blocca rapporto',
		resetSize		: 'Reimposta dimensione'
	},

	// Fileeditor plugin
	Fileeditor :
	{
		save			: 'Salva',
		fileOpenError	: 'Non è stato possibile aprire il file.',
		fileSaveSuccess	: 'File salvato.',
		contextMenuName	: 'Modifica',
		loadingFile		: 'Attendere prego. Caricamento del file in corso...'
	},

	Maximize :
	{
		maximize : 'Massimizza',
		minimize : 'Minimizza'
	},

	Gallery :
	{
		current : 'Immagine {current} di {total}'
	},

	Zip :
	{
		extractHereLabel	: 'Estrai qui',
		extractToLabel		: 'Estrai come...',
		downloadZipLabel	: 'Scarica come zip',
		compressZipLabel	: 'Compremi in zip',
		removeAndExtract	: 'Rimuovi esistente ed estrai',
		extractAndOverwrite	: 'Estrai sovrascrivendo file esistenti',
		extractSuccess		: 'File estratto con successo.'
	},

	Search :
	{
		searchPlaceholder : 'Ricerca'
	}
};
