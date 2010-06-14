/**
 * @name         bramus_cssextras
 * @version      0.5.0
 *
 * @author       Bramus! (Bram Van Damme)
 * @authorURL    http://www.bram.us/
 * @infoURL      http://www.bram.us/projects/tinymce-plugins/
 * 
 * @license      Creative Commons Attribution-Share Alike 2.5
 * @licenseURL   http://creativecommons.org/licenses/by-sa/2.5/
 *
 * v 0.5.0 - 2007.12.11 - NEW : TinyMCE 3.x Compatibility (TinyMCE 2.x is no longer supported)
 * v 0.4.1 - 2007.11.22 - BUG : didn't work with multiple content_css files specified (@see http://www.bram.us/projects/tinymce-plugins/tinymce-classes-and-ids-plugin-bramus_cssextras/#comment-89820)
 *                      - BUG : If for example p.test is defined multiple times, show "test" only once in the dropdown.
 * v 0.4.0 - 2007.09.10 - BUG : selection noclass returned "undefined" as class, should be empty
 *                      - ADD : automatic building of the bramus_cssextras_classesstring and bramus_cssextras_idsstring
 * v 0.3.3 - 2007.07.27 - getInfo returned wrong version. Fixed + version increment.
 * v 0.3.2 - 2007.07.23 - minor change in outputted HTML of the selects
 * v 0.3.1 - 2007.06.28 - ids must be unique, so added a check and confirm thingy ;-)
 * v 0.3   - 2007.06.27 - Plugin changed from bramus_classeslist to bramus_cssextras as it now supports the settings of ids too :-)
 * v 0.2   - 2007.06.22 - added Undo Levels + a few extra comments (should be fully commented now)
 * v 0.1   - 2007.06.19 - initial build
 */
 
(function() {
		  
	// Load plugin specific language pack
	// tinymce.PluginManager.requireLangPack('bramus_cssextras');

	tinymce.create('tinymce.plugins.BramusCssExtras', {

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
			getInfo : function() {
				return {
					longname 	: 'Plugin to support the adding of classes and ids to elements (or their parent element)',
					author 		: 'Bramus!',
					authorurl	: 'http://www.bram.us/',
					infourl		: 'http://www.bram.us/projects/tinymce-plugins/',
					version		: "0.5.0"
				};
			},
		
		
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
			init : function(ed, url) {
				this._init(ed, url);
			},


		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
			createControl : function(n, cm) {
				switch (n) {
					case 'bramus_cssextras_classes':
					case 'bramus_cssextras_ids':
						return this._createControl(n, cm);				
					break;
				}
				
				return null;
			},
		
		
		/**
		 * Internally used variables
		 * ---------------------------------------------------------------------------------------------------
		 */
		 
			// Strings that will hold all possible classes/ids /* removed, these are global now */
				// _coreClassesString			: null,
				// _coreIdsStrings				: null,
				
			// Arrays that will hold all possible classes/ids /* removed, these are global now */
				// _coreClassesArray			: null,
				// _coreIdsArray				: null,
		 
			// The dropdowns
				_coreClassesDropdown		: null,
				_coreIdsDropdown			: null,
			
			// XHR Object & Response - needed for loading the content_css file(s) if needed
				_xmlhttp					: null,
				_xmlhttpresponse			: null,

			// CPU Power Savers : The previous node (don't calculate if nothing changed) and Already loaded (only init this plugin once)
				_previousNode				: null,
				// _loaded						: null, /* removed, these are global now */
		
		
		/**
		 * Helper Functions
		 * ---------------------------------------------------------------------------------------------------
		 */
		
			// Helper function : trim a string
				_trim 						: function(str) {
					return str.replace(/^\s+|\s+$/g,"");
				},
			
			// Helper function : left trim a string
				_ltrim 						: function(str) {
					return str.replace(/^\s+/,"");
				},
			
			// Helper function : right trim a a string
				_rtrim 						: function() {
					return this.replace(/\s+$/,"");
				},
			
			// Helper function : is element in array (and return position if so)
				_inArray					: function(needle, haystack) {
					
					// loop all elements of array (haystack)
					for (var i = 0; i < haystack.length; i++){
						
						// compare with needle
						if (needle == haystack[i]) {
							
							// got hit : return position in array
							return i;
							
						}
						
					}			
					// no hit
					return false;
				},
				
		/**
		 * Init function
		 * ---------------------------------------------------------------------------------------------------
		 */
		 
		 	_init						: function (ed, url) {
				
				// if not intialized yet: get the params & build the arrays
				if (tinymce.plugins.BramusCssExtras._loaded == undefined) {
					
					// get the params
						tinymce.plugins.BramusCssExtras._coreClassesString		= tinyMCE.activeEditor.getParam("bramus_cssextras_classesstring", undefined);
						tinymce.plugins.BramusCssExtras._coreIdsString			= tinyMCE.activeEditor.getParam("bramus_cssextras_idsstring", undefined);
						
					// one of the params was not set, try to get the content_css over XHR (Ajax)
						if ((tinymce.plugins.BramusCssExtras._coreClassesString == undefined) || (tinymce.plugins.BramusCssExtras._coreIdsString == undefined)) {
							this._loadContentCSS(tinyMCE.activeEditor.getParam("content_css", false));
						}
						
					// now that we have the params, process 'm
						tinymce.plugins.BramusCssExtras._coreClassesArray		= this._processElementsAndShizzle(tinymce.plugins.BramusCssExtras._coreClassesString.split(';'));
						tinymce.plugins.BramusCssExtras._coreIdsArray			= this._processElementsAndShizzle(tinymce.plugins.BramusCssExtras._coreIdsString.split(';'));
						
					// debug
						// console.log(tinymce.plugins.BramusCssExtras._coreClassesArray);
						// console.log(tinymce.plugins.BramusCssExtras._coreIdsArray);
					
				}
				
				// hook plugin to nodeChange event
				ed.onNodeChange.add(this._nodeChange, this);
				
				// hook Commands
				ed.addCommand('bceClass', function(ui, v) {
					this._execCommand(ed, v, this._coreClassesDropdown, "class");
				}, this);
				
				ed.addCommand('bceId', function(ui, v) {
					this._execCommand(ed, v, this._coreIdsDropdown, "id");
				}, this);
				
				// set loaded
				tinymce.plugins.BramusCssExtras._loaded = true;
			},
			
		/**
		 * loadContentCSS function : loads in the content_css over XHR
		 * ---------------------------------------------------------------------------------------------------
		 */
		 
			_loadContentCSS				: function(content_css) {
	
				// only proceed if content_css is set
					if (content_css == false) {
						return;	
					}
	
				// create nex XHR object
					if (window.XMLHttpRequest) {
						this._xmlhttp 	= new XMLHttpRequest();
					} else if (window.ActiveXObject) {
						this._xmlhttp 	= new ActiveXObject('Microsoft.XMLHTTP');
					}
						
				// var which will hold all data from all files referred through content_css
					content_css_data = "";
				
				// make sure it's an object
					if (typeof(this._xmlhttp) == 'object') {
						
						// content_css exists?
							if (content_css && (content_css != null) && (content_css != "")) {
								
								// support the referring of multiple classes
									content_css_arr	= content_css.split(',');
								
								// loop all referred css files
									for (i = 0; i < content_css_arr.length; i++) {
										
										// load it in, but <<<< SYNCHRONOUS >>>>
										// this._xmlhttp.onreadystatechange	= this._doneLoadContentCSS;		// SYNCHRONOUS, no need to set onreadystatechange!
											this._xmlhttp.open('GET', this._trim(content_css_arr[i]), false);						// false == SYNCHRONOUS ;-)
											this._xmlhttp.send(null);
										
										// wait for it to load
											if (this._xmlhttp.readyState == 4) {
						
												// loaded!
												if (this._xmlhttp.status == 200) {
						
													// get the responseText
													this._xmlhttpresponse	= this._xmlhttp.responseText;
						
													// run some prelim regexes on them
													this._xmlhttpresponse 	= this._xmlhttpresponse.replace(/(\r\n)/g, "");			// get all CSS rules on 1 line per selector : 1 line on whole document
													this._xmlhttpresponse 	= this._xmlhttpresponse.replace(/(\})/g, "}\n");		// get all CSS rules on 1 line per selector : 1 line per selector
													this._xmlhttpresponse 	= this._xmlhttpresponse.replace(/\{(.*)\}/g, "");		// strip out css rules themselves
													this._xmlhttpresponse 	= this._xmlhttpresponse.replace(/\/\*(.*)\*\//g, "");	// strip out comments
													this._xmlhttpresponse 	= this._xmlhttpresponse.replace(/\t/g, "");				// strip out tabs
						
													content_css_data		+= this._xmlhttpresponse + "\n";
						
												// not loaded!
												} else {
                        							tinyMCE.activeEditor.windowManager.alert("[bramus_cssextras] Error while loading content_css file '" + content_css_arr[i] + "', make sure the path is correct!");
												}
											}
									}
			
								// clear the xhr object
									this._xmlhttp 			= null;
							
							}
					}
						
				// process the content_css_data (only if the vars are not set yet - viz. don't overwrite)
					if (tinymce.plugins.BramusCssExtras._coreClassesString == undefined) {
						tinymce.plugins.BramusCssExtras._coreClassesString	= this._processContentCSSMatches(content_css_data, ".");
					}
						
					if (tinymce.plugins.BramusCssExtras._coreIdsString == undefined) {
						tinymce.plugins.BramusCssExtras._coreIdsString		= this._processContentCSSMatches(content_css_data, "#");
					}
				
					
			},
			
		/**
		 * processContentCSSMatches function : processes the matches loaded over XHR
		 * ---------------------------------------------------------------------------------------------------
		 */
		 
			_processContentCSSMatches	: function(content_css_data, identifier) {
				
				// split da sucker!
					if (identifier == ".") {
						matches		= content_css_data.match(/([a-zA-Z0-9])+(\.)([a-zA-Z0-9_\-])+(\b)?/g);
					} else {
						matches		= content_css_data.match(/([a-zA-Z0-9])+(\#)([a-zA-Z0-9_\-])+(\b)?/g);
					}						

				// got any matches?
					if (!matches) {
						return "";	
					} else {
						arr_selectors			= new Array();
						arr_values				= new Array();
					}
				
				// run matches and build selectors and values arrays.					
					for (var i = 0; i < matches.length; i++) {
						
						// split on the identifier
						matches[i]	= matches[i].split(identifier);
						
						var position	= this._inArray(((matches[i][0] != "ul")?matches[i][0]:"li") + "::" + ((matches[i][0] != "ul")?"self":matches[i][0]), arr_selectors);
					
						// not found : add selector and classes/ids
						if (position === false) {
							arr_selectors.push(((matches[i][0] != "ul")?matches[i][0]:"li") + "::" + ((matches[i][0] != "ul")?"self":matches[i][0]));
							arr_values.push(matches[i][1]);
							
						// found, adjust ids on position
						} else {
							// extra check: check if ain't class/id isn't in values yet!
							if (this._inArray(matches[i][1], arr_values[position].split(',')) === false) {
								arr_values[position]	= arr_values[position] + "," + matches[i][1];
							}
						}
		
					}
				
				// build the elmsAndShizzleArray (Shizzle being either Ids or Classes)
					var elmsAndShizzleArray			= new Array();
					
					for (var i = 0;  i < arr_selectors.length; i++) {
						elmsAndShizzleArray.push(arr_selectors[i] + "[" + arr_values[i] + "]");
					}
					
					return elmsAndShizzleArray.join(';');
			},
			
		/**
		 * processMatches
		 * ---------------------------------------------------------------------------------------------------
		 */
		 
		 	_processElementsAndShizzle				: function(elmsAndShizzleArray) {
			
				// create new array to hold the real stuff
					coreArray			= new Array();
					
				// if no array passed in, return an empty array!
					if (!elmsAndShizzleArray) {
						return coreArray;	
					}
				
				// loop the entries of elmsAndClassesArray
					if (elmsAndShizzleArray.length > 0) {
						for (var i = 0; i < elmsAndShizzleArray.length; i++) {
							
							// check if syntax is correct and get data from the entry
							var elmAndShizzleString 	= elmsAndShizzleArray[i];
							var elmAndShizzleArray		= elmAndShizzleString.match(/(.*)::(.*)\[(.*)\]/);
							
							// got less than 4 matches : invalid entry!
							if ((!elmAndShizzleArray) || (elmAndShizzleArray.length < 4)) {
								
								// nothing
								
							// found 4 matches : valid entry!
							} else{
													
								// get elementNodeName, parentElementNodeName, elementClasses and push them on the arrayz!
								coreArray.push(new Array(elmAndShizzleArray[1], elmAndShizzleArray[2], elmAndShizzleArray[3].split(',')));
							}
						}
					}	
				
				// return it
					return coreArray;
			},
				
		/**
		 * createControl function
		 * ---------------------------------------------------------------------------------------------------
		 */
		 
		 	_createControl				: function (n, cm) {
						
				switch(n) {
					
					// bramus_cssextras_classes
						case "bramus_cssextras_classes":
							
							// create a dropdown
								ddm	= cm.createListBox('bramus_cssextras_classes_dropdown_' + tinyMCE.activeEditor.id, {
										 title 	: '.class',
										 cmd	: 'bceClass'
									});
								
							// keep a reference to it in our own instance!
								this._coreClassesDropdown = ddm;
							
							// return it
								return ddm;
							
						break;
					
					// bramus_cssextras_ids
						case "bramus_cssextras_ids":
							
							// create a dropdown
								ddm	= cm.createListBox('bramus_cssextras_ids_dropdown_' + tinyMCE.activeEditor.id, {
										 title 	: '#id',
										 cmd	: 'bceId'
									});
								
							// keep a reference to it in our own instance!
								this._coreIdsDropdown = ddm;
								
							// return it
								return ddm;
							
						break;
					
				}
					
				return null;
			},
			
			
		/**
		 * nodeChange Function : event which gets triggered when the node has changed
		 * ---------------------------------------------------------------------------------------------------
		 */

			_nodeChange : function(ed, cm, n) {
								
				// save your energy : check if ed.id equals tinyMCE.activeEditor.id
					if (tinyMCE.activeEditor.id != ed.id) {
						return;	
					}
		
				// save your energy : no node select : return!
					if (n == null)
						return;
			
				// save your energy : check if node differs from previousnode. If not, then we don't need to loop this all again ;-)
					if (n == this._previousNode) {
						return;
					} else {
						this._previousNode = n;
					}
					
				// check if current elem has a match in the _coreArrayClasses or _coreArrayIds
					var gotHitClass		= this._checkHit(tinymce.plugins.BramusCssExtras._coreClassesArray);
					var gotHitIds		= this._checkHit(tinymce.plugins.BramusCssExtras._coreIdsArray);
				
				// now do something with that hit and that dropdown!
					this._rebuildDropdown(gotHitClass, this._coreClassesDropdown, "class", ed);
					this._rebuildDropdown(gotHitIds, this._coreIdsDropdown, "id", ed);

			},
			
			
		/**
		 * checkHit : checks if the current node matches any of the arrays
		 * ---------------------------------------------------------------------------------------------------
		 */
			_checkHit					: function(coreArray) {
			
				// correarray not null?
				if (coreArray) {
					for (var i = 0; i < coreArray.length; i++) {
						if (coreArray[i][0].toLowerCase() == this._previousNode.nodeName.toLowerCase()) {
							return coreArray[i];
						}
					}
				}
				
				return null;
				
			},
			
			
		/**
		 * rebuildDropdown : rebuilds the dropdowns and makes it so that the correct value is selected
		 * ---------------------------------------------------------------------------------------------------
		 */
			_rebuildDropdown			: function(gotHit, selectDropDown, what, ed) {
				
				if (gotHit === null) {
					
					// only continue if a dropdown is present!
					if (selectDropDown) {
						
						// remove existing items
						selectDropDown.items = [];
						
						// TinyMCE fix - TIP : Moxiecode should provide a neat way to removing all items!
						selectDropDown.oldID = null;
						
						// select nothing
						selectDropDown.select();
						
						// disable the dropdown
						selectDropDown.setDisabled(true);
						
					}
		
				} else {
								
					// get params from gotHit
					var elemNodeName		= gotHit[0];
					var parentElemNodeName	= gotHit[1];
					var elementClasses		= gotHit[2];
					
					// continue if parentElemNodeName equals self, or if parent node equals parentElemNodeName
					if ((parentElemNodeName == "self") || (ed.dom.getParent(this._previousNode, parentElemNodeName).nodeName.toLowerCase() == parentElemNodeName)) {
						
						// remove existing items
						selectDropDown.items = [];
						
						// TinyMCE fix - TIP : Moxiecode should provide a neat way to removing all items!
						selectDropDown.oldID = null;
						
						// push on the new ones (and enforce first one)
						selectDropDown.add('[ no ' + what + ' ]', parentElemNodeName + "::");
						
						// select nothing
						selectDropDown.select();
						
						// fill the dropdown with the values
						for (var i = 0; i < elementClasses.length; i++) {
							
							selectDropDown.add(elementClasses[i], parentElemNodeName + "::" + elementClasses[i]);
							
							// this node or the parent node?
							if (parentElemNodeName == "self") {
								var pNode 	= this._previousNode;
							} else {
								var pNode	= ed.dom.getParent(this._previousNode, parentElemNodeName);
							}
							
							// if the instance currently has this class, set this option as selected
							switch(what) {								
								case "class":
									if (ed.dom.hasClass(pNode, elementClasses[i])) {
										selectDropDown.select(parentElemNodeName + "::" + elementClasses[i]);
									}
								break;								
								case "id":
									if (pNode.id == elementClasses[i]) {
										selectDropDown.select(parentElemNodeName + "::" + elementClasses[i]);
									}
								break;
							}
							
						}
										
					}
					
					// render the selectdropdown
					selectDropDown.renderMenu();
					
					// enable the selectbox!
					selectDropDown.setDisabled(false);
						
				}
				
			},
			
			_execCommand			: function(ed, listValue, selectDropDown, what) {
								
				// this node or the parent node?
				if (listValue.split("::")[0] == "self") {
					var node 	= ed.selection.getNode();
				} else {
					var node	= ed.dom.getParent(ed.selection.getNode(), listValue.split("::")[0]);
				}
				
				// begin Undo
				tinyMCE.execCommand('mceBeginUndoLevel');			
				
				// define what to set class or id to
				toSetTo 	= (listValue.split("::")[1] != undefined)?listValue.split("::")[1]:"";
				
				// set className
				if (what == "class") {
					node.className	= toSetTo;
					
				// set id
				} else {
					
					// toSetTo is not empty : perform a check if an element with that id already exists
					if (toSetTo != "") {
						
						// there already exists an element with that id?
						if (ed.getDoc().getElementById(toSetTo)) {
							
							// confirm the move of the id
							if (confirm("There already exists an element with that id, ids must be unique.\nPress 'OK' to move the id to the current element.\nPress 'Cancel' to leave unchanged")) {
								
								// remove id from current element with that id
								ed.getDoc().getElementById(toSetTo).id = "";
								
								// set id on node
								node.id			= toSetTo;
								
							// not confirmed, set selectedindex of node to the first item
							} else {
								
								// select the first item
								selectDropDown.select(listValue.split("::")[0] + '::');
								
								// stop the click event
								// console.log(tinymce.dom.Event.events);
								tinymce.dom.Event.cancel(tinymce.dom.Event.events[0]);	// BUG! - this ain't workin :-( ... how can I cancel the current event (viz. the click on the item in the ListBox)?
							}
							
						// no element with that id exists yet : set the id
						} else {
							
						// set id on node
						node.id				= toSetTo;
					}
						
					// toSetTo is empty : clear the id on the selected element
					} else {
							
						// set id on node
						node.id				= toSetTo;
					}
				}
				
				// endUndo
				tinyMCE.execCommand('mceEndUndoLevel');
				
				// repaint the instance
				ed.execCommand('mceRepaint');
				
				// This is needed?
				// EditorManager.activeEditor.nodeChanged();
				
			},
		
		
		/**
		 * End Of Plugin (EOP)
		 * ---------------------------------------------------------------------------------------------------
		 */
		 
		 	// The last one (so I don't forget to close)
			EOP							: true
	
	});

	// Register plugin
	tinymce.PluginManager.add('bramus_cssextras', tinymce.plugins.BramusCssExtras);
})();