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
 * @fileOverview Defines the {@link CKFinder.lang} object, for the Turkish
 *		language.
 *
 *	Turkish translation by Abdullah M CEYLAN a.k.a. Kenan Balamir. Updated.
 * 	Günce BEKTAŞ update tr.js file and translate help folder.
 */

/**
 * Contains the dictionary of language entries.
 * @namespace
 */
CKFinder.lang['tr'] =
{
	appTitle : 'CKFinder',

	// Common messages and labels.
	common :
	{
		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility"> öğesi, mevcut değil</span>',
		confirmCancel	: 'Bazı seçenekler değiştirildi. Pencereyi kapatmak istiyor musunuz?',
		ok				: 'Tamam',
		cancel			: 'Vazgeç',
		confirmationTitle	: 'Onay',
		messageTitle	: 'Bilgi',
		inputTitle		: 'Soru',
		undo			: 'Geri Al',
		redo			: 'Yinele',
		skip			: 'Atla',
		skipAll			: 'Tümünü Atla',
		makeDecision	: 'Hangi işlem yapılsın?',
		rememberDecision: 'Kararımı hatırla'
	},


	// Language direction, 'ltr' or 'rtl'.
	dir : 'ltr',
	HelpLang : 'en',
	LangCode : 'tr',

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
	DateTime : 'd/m/yyyy h:MM aa',
	DateAmPm : ['GN', 'GC'],

	// Folders
	FoldersTitle	: 'Klasörler',
	FolderLoading	: 'Yükleniyor...',
	FolderNew		: 'Lütfen yeni klasör adını yazın: ',
	FolderRename	: 'Lütfen yeni klasör adını yazın: ',
	FolderDelete	: '"%1" klasörünü silmek istediğinizden emin misiniz?',
	FolderRenaming	: ' (Yeniden adlandırılıyor...)',
	FolderDeleting	: ' (Siliniyor...)',
	DestinationFolder	: 'Hedef Klasör',

	// Files
	FileRename		: 'Lütfen yeni dosyanın adını yazın: ',
	FileRenameExt	: 'Dosya uzantısını değiştirmek istiyor musunuz? Bu, dosyayı kullanılamaz hale getirebilir.',
	FileRenaming	: 'Yeniden adlandırılıyor...',
	FileDelete		: '"%1" dosyasını silmek istediğinizden emin misiniz?',
	FilesDelete	: '%1 adet dosyayı silmek istediğinize emin misiniz?',
	FilesLoading	: 'Yükleniyor...',
	FilesEmpty		: 'Klasör boş',
	DestinationFile	: 'Hedef Dosya',
	SkippedFiles	: 'Atlanan dosyaların listesi:',

	// Basket
	BasketFolder		: 'Sepet',
	BasketClear			: 'Sepeti temizle',
	BasketRemove		: 'Sepetten sil',
	BasketOpenFolder	: 'Üst klasörü aç',
	BasketTruncateConfirm : 'Sepetteki tüm dosyaları silmek istediğinizden emin misiniz?',
	BasketRemoveConfirm	: 'Sepetteki %1% dosyasını silmek istediğinizden emin misiniz?',
	BasketRemoveConfirmMultiple	: '%1 adet dosyayı sepetinizden çıkartmak istediğinize emin misiniz?',
	BasketEmpty			: 'Sepette hiç dosya yok, birkaç tane sürükleyip bırakabilirsiniz',
	BasketCopyFilesHere	: 'Sepetten Dosya Kopyala',
	BasketMoveFilesHere	: 'Sepetten Dosya Taşı',

	// Global messages
	OperationCompletedSuccess	: 'İşlem başarıyla tamamlandı.',
	OperationCompletedErrors		: 'İşlem hatalar olmasına karşın tamamlandı.',
	FileError				: '%s: %e',

	// Move and Copy files
	MovedFilesNumber		: 'Taşınan dosya sayısı: %s.',
	CopiedFilesNumber	: 'Kopyalanan dosya sayısı: %s.',
	MoveFailedList		: 'Taşınamayan dosyalar:<br />%s',
	CopyFailedList		: 'Koplanamayan dosyalar:<br />%s',

	// Toolbar Buttons (some used elsewhere)
	Upload		: 'Yükle',
	UploadTip	: 'Yeni Dosya Yükle',
	Refresh		: 'Yenile',
	Settings	: 'Ayarlar',
	Help		: 'Yardım',
	HelpTip		: 'Yardım',

	// Context Menus
	Select			: 'Seç',
	SelectThumbnail : 'Önizleme Olarak Seç',
	View			: 'Görüntüle',
	Download		: 'İndir',

	NewSubFolder	: 'Yeni Altklasör',
	Rename			: 'Yeniden Adlandır',
	Delete			: 'Sil',
	DeleteFiles		: 'Dosyaları sil',

	CopyDragDrop	: 'Buraya kopyala',
	MoveDragDrop	: 'Buraya taşı',

	// Dialogs
	RenameDlgTitle		: 'Yeniden Adlandır',
	NewNameDlgTitle		: 'Yeni Adı',
	FileExistsDlgTitle	: 'Dosya zaten var',
	SysErrorDlgTitle : 'Sistem hatası',

	FileOverwrite	: 'Üzerine yaz',
	FileAutorename	: 'Oto-Yeniden Adlandır',
	ManuallyRename	: 'Elle isimlendir',

	// Generic
	OkBtn		: 'Tamam',
	CancelBtn	: 'Vazgeç',
	CloseBtn	: 'Kapat',

	// Upload Panel
	UploadTitle			: 'Yeni Dosya Yükle',
	UploadSelectLbl		: 'Yüklenecek dosyayı seçin',
	UploadProgressLbl	: '(Yükleniyor, lütfen bekleyin...)',
	UploadBtn			: 'Seçili Dosyayı Yükle',
	UploadBtnCancel		: 'Vazgeç',

	UploadNoFileMsg		: 'Lütfen bilgisayarınızdan dosya seçin',
	UploadNoFolder		: 'Lütfen yüklemeden önce klasör seçin.',
	UploadNoPerms		: 'Dosya yüklemeye izin verilmiyor.',
	UploadUnknError		: 'Dosya gönderme hatası.',
	UploadExtIncorrect	: 'Bu dosya uzantısına, bu klasörde izin verilmiyor.',

	// Flash Uploads
	UploadLabel			: 'Gönderilecek Dosyalar',
	UploadTotalFiles	: 'Toplam Dosyalar:',
	UploadTotalSize		: 'Toplam Büyüklük:',
	UploadSend			: 'Yükle',
	UploadAddFiles		: 'Dosyaları Ekle',
	UploadClearFiles	: 'Dosyaları Temizle',
	UploadCancel		: 'Göndermeyi İptal Et',
	UploadRemove		: 'Sil',
	UploadRemoveTip		: '!f sil',
	UploadUploaded		: '!n% gönderildi',
	UploadProcessing	: 'Gönderiliyor...',

	// Settings Panel
	SetTitle		: 'Ayarlar',
	SetView			: 'Görünüm:',
	SetViewThumb	: 'Önizlemeler',
	SetViewList		: 'Liste',
	SetDisplay		: 'Gösterim:',
	SetDisplayName	: 'Dosya adı',
	SetDisplayDate	: 'Tarih',
	SetDisplaySize	: 'Dosya boyutu',
	SetSort			: 'Sıralama:',
	SetSortName		: 'Dosya adına göre',
	SetSortDate		: 'Tarihe göre',
	SetSortSize		: 'Boyuta göre',
	SetSortExtension		: 'Uzantısına göre',

	// Status Bar
	FilesCountEmpty : '<Klasörde Dosya Yok>',
	FilesCountOne	: '1 dosya',
	FilesCountMany	: '%1 dosya',

	// Size and Speed
	Kb				: '%1 KB',
	Mb				: '%1 MB',
	Gb				: '%1 GB',
	SizePerSecond	: '%1/sn',

	// Connector Error Messages.
	ErrorUnknown	: 'İsteğinizi yerine getirmek mümkün değil. (Hata %1)',
	Errors :
	{
	 10 : 'Geçersiz komut.',
	 11 : 'İstekte kaynak türü belirtilmemiş.',
	 12 : 'Talep edilen kaynak türü geçersiz.',
	102 : 'Geçersiz dosya ya da klasör adı.',
	103 : 'Kimlik doğrulama kısıtlamaları nedeni ile talebinizi yerine getiremiyoruz.',
	104 : 'Dosya sistemi kısıtlamaları nedeni ile talebinizi yerine getiremiyoruz.',
	105 : 'Geçersiz dosya uzantısı.',
	109 : 'Geçersiz istek.',
	110 : 'Bilinmeyen hata.',
	111 : 'Dosya boyutundan dolayı bu işlemin yapılması mümkün değil.',
	115 : 'Aynı isimde bir dosya ya da klasör zaten var.',
	116 : 'Klasör bulunamadı. Lütfen yenileyin ve tekrar deneyin.',
	117 : 'Dosya bulunamadı. Lütfen dosya listesini yenileyin ve tekrar deneyin.',
	118 : 'Kaynak ve hedef yol aynı!',
	201 : 'Aynı ada sahip bir dosya zaten var. Yüklenen dosyanın adı "%1" olarak değiştirildi.',
	202 : 'Geçersiz dosya',
	203 : 'Geçersiz dosya. Dosya boyutu çok büyük.',
	204 : 'Yüklenen dosya bozuk.',
	205 : 'Dosyaları yüklemek için gerekli geçici klasör sunucuda bulunamadı.',
	206 : 'Güvenlik nedeni ile yükleme iptal edildi. Dosya HTML benzeri veri içeriyor.',
	207 : 'Yüklenen dosyanın adı "%1" olarak değiştirildi.',
	300 : 'Dosya taşıma işlemi başarısız.',
	301 : 'Dosya kopyalama işlemi başarısız.',
	500 : 'Güvenlik nedeni ile dosya gezgini devredışı bırakıldı. Lütfen sistem yöneticiniz ile irtibata geçin ve CKFinder yapılandırma dosyasını kontrol edin.',
	501 : 'Önizleme desteği devredışı.'
	},

	// Other Error Messages.
	ErrorMsg :
	{
		FileEmpty		: 'Dosya adı boş olamaz',
		FileExists		: '%s dosyası zaten var',
		FolderEmpty		: 'Klasör adı boş olamaz',
		FolderExists	: '%s klasörü zaten mevcut.',
		FolderNameExists	: 'Klasör zaten mevcut.',

		FileInvChar		: 'Dosya adının içermesi mümkün olmayan karakterler: \n\\ / : * ? " < > |',
		FolderInvChar	: 'Klasör adının içermesi mümkün olmayan karakterler: \n\\ / : * ? " < > |',

		PopupBlockView	: 'Dosyayı yeni pencerede açmak için, tarayıcı ayarlarından bu sitenin açılır pencerelerine izin vermeniz gerekiyor.',
		XmlError		: 'Web sunucusundan XML yanıtı düzgün bir şekilde yüklenemedi.',
		XmlEmpty		: 'Web sunucusundan XML yanıtı düzgün bir şekilde yüklenemedi. Sunucudan boş cevap döndü.',
		XmlRawResponse	: 'Sunucudan gelen ham mesaj: %s'
	},

	// Imageresize plugin
	Imageresize :
	{
		dialogTitle		: 'Boyutlandır: %s',
		sizeTooBig		: 'Yükseklik ve genişlik değeri orijinal boyuttan büyük olduğundan, işlem gerçekleştirilemedi (%size).',
		resizeSuccess	: 'Resim başarıyla yeniden boyutlandırıldı.',
		thumbnailNew	: 'Yeni önizleme oluştur',
		thumbnailSmall	: 'Küçük (%s)',
		thumbnailMedium	: 'Orta (%s)',
		thumbnailLarge	: 'Büyük (%s)',
		newSize			: 'Yeni boyutu ayarla',
		width			: 'Genişlik',
		height			: 'Yükseklik',
		invalidHeight	: 'Geçersiz yükseklik.',
		invalidWidth	: 'Geçersiz genişlik.',
		invalidName		: 'Geçersiz dosya adı.',
		newImage		: 'Yeni resim oluştur',
		noExtensionChange : 'Dosya uzantısı değiştirilemedi.',
		imageSmall		: 'Kaynak resim çok küçük',
		contextMenuName	: 'Boyutlandır',
		lockRatio		: 'Oranı kilitle',
		resetSize		: 'Büyüklüğü sıfırla'
	},

	// Fileeditor plugin
	Fileeditor :
	{
		save			: 'Kaydet',
		fileOpenError	: 'Dosya açılamadı.',
		fileSaveSuccess	: 'Dosya başarıyla kaydedildi.',
		contextMenuName	: 'Düzenle',
		loadingFile		: 'Dosya yükleniyor, lütfen bekleyin...'
	},

	Maximize :
	{
		maximize : 'Büyült',
		minimize : 'Küçült'
	},

	Gallery :
	{
		current : '{current} / {total} resim'
	},

	Zip :
	{
		extractHereLabel	: 'Buraya aç',
		extractToLabel		: 'Hedefe aç...',
		downloadZipLabel	: 'Zip olarak indir',
		compressZipLabel	: 'Zip dosyası olarak sıkıştır',
		removeAndExtract	: 'Varolanı kaldır ve aç',
		extractAndOverwrite	: 'Mevcut dosyaların üzerine yazarak aç',
		extractSuccess		: 'Başarıyla açıldı.'
	},

	Search :
	{
		searchPlaceholder : 'Ara'
	}
};
