//Requires jQuery.Widget factory and jQuery.ui.sortable


(function(){
	var $ = jQuery,
		undefined;
		
	if(typeof console === "undefined"){
		var console = {};
		console.debug = console.info = console.warn = console.error = console.log = function(){};	
	}
	
	var ccm_multiFileSelector = {
		options:{
			itemList:"ul.items",
			itemTemplate:"li.template",
			btnAdd:"a.add-file"
		},
		_init:function(options){
			var I = this;
			
			this.template = this.element.find(this.options.itemTemplate).remove();
			this.list = this.element.find(this.options.itemList);
			this.list.sortable({handle:".icon,.name", axis:"y"});
			this.list.disableSelection();
			
			this.itemActions = $.extend({}, this.itemActions);
			
			//Delegate item actions
			this.list.delegate("a", "click", function(evt){
				var $a = $(this);				
				if($a.parent(".actions").length){
					var classes = $a.attr("class").split(/\s+/g);
					for(var c = 0; c < classes.length; c++){
						var key = classes[c],
							$item = $a.closest("li");
						if($.isFunction(I.itemActions[key])){
							var args = [key, $item, $item.prevAll("li").length, I.element],
								result = I.itemActions[key].apply($item.get(0), args);
							
							if(result !== false){
								args.splice(0,0,key+"Item");
								I._trigger.apply(I, args);
							}
						}
					}
				}
				
			});
			
			
			this.element.delegate(this.options.btnAdd, "click", function(){
				I.takeover_ccm_chooseAsset();
				ccm_launchFileManager();
			});
		},
		addItem:function(fID, fName, thumb, position){
			
			var $item = this.template.clone(),
				$existing = this.list.find("input[value='"+fID+"']");
			
			if(!$existing.length){			
				$item.find("input:hidden").val(fID);
				$item.find(".name").html(fName);
			}else{
				$item = $existing.closest("li").remove();
			}
			
			if(position < 1){
				this.list.prepend($item);
			}else if(position == null || position > this.list.children("li").length){
				this.list.append($item);	
			}else{
				this.list.children("li").eq(position).insertAfter($item);
			}
			
			this._trigger('addPage', fID, fName, thumb, position);
		},
		removeItem:function(fID){
			var $in = this.list.find("input[value='"+fID+"']"),
				$item = $in.closest("li");
			if($item.length){
				$item.remove();
				this._trigger('removeItem', fID);
			}
		},
		
		itemActions:{
			remove:function(action, $item, position, $wrap){
				$wrap.ccm_multiFileSelector("removeItem", $item.find("input").val());
			},
			details:function(action, $item, position, $wrap){
				var url = CCM_TOOLS_PATH+"/files/properties?fID="+$item.find("input").val();
				jQuery.fn.dialog.open({
					width: 600,
					height: 400,
					modal: false,
					href: url,
					title: ccmi18n_filemanager.properties			
				});
			}
		},
		addItemAction:function(key, callback){
			
			var $actions = this.template.find(".act"),
				$action = $actions.children("."+key);
			
			//Add action to template, if it doesn't already exist
			if(!$action.length){
				$action = $("<a class=\""+key+"\" title=\""+(key)+"\">"+key+"</a>");
			}
			
			this.itemActions[key] = callback;
			
		},
		takeover_ccm_chooseAsset:function(){
			//IF THERE ARE ISSUES WITH THE ASSET PICKER COMMUNICATION, LOOK HERE FIRST
			var I = this,
				orig_ccm_chooseAsset = typeof(ccm_chooseAsset)==="undefined" ? false : ccm_chooseAsset;
			ccm_chooseAsset = function(fileObj){
				I.addItem(fileObj.fID, fileObj.title, fileObj.thumbnailLevel2);
				
				//Restore the original ccm_chooseAsset
				ccm_chooseAsset = orig_ccm_chooseAsset;
			};		
		}
		
	};
	//Create the widget
	$.widget("ccm.ccm_multiFileSelector", ccm_multiFileSelector);
	

})();