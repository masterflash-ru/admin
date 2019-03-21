$(document).ready(function() {
    $.datepicker.regional['ru'] = {
	closeText: 'Закрыть',
	prevText: '&#x3c;Пред',
	nextText: 'След&#x3e;',
	currentText: 'Сегодня',
	monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
	monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
	dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
	dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
	dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
	dateFormat: 'dd.mm.yy',
	firstDay: 1,
	isRTL: false
}; 
$.datepicker.setDefaults($.datepicker.regional['ru']); 

$.timepicker.regional['ru'] = {
	timeOnlyTitle: 'Выберите время',
	timeText: 'Время',
	hourText: 'Часы',
	minuteText: 'Минуты',
	secondText: 'Секунды',
	millisecText: 'Миллисекунды',
	timezoneText: 'Часовой пояс',
	currentText: 'Сейчас',
	closeText: 'Закрыть',
	timeFormat: 'HH:mm:ss',
	amNames: ['AM', 'A'],
	pmNames: ['PM', 'P'],
	isRTL: false
};
$.timepicker.setDefaults($.timepicker.regional['ru']);
});

function stripslashes( str ) 
{
 return (str+'').replace(/\0/g, '0').replace(/\\([\\'"])/g, '$1');
}

function unserialize(data)
{
    data=stripslashes( data );
    if (!data) return new Array();
	//определим кодировку
	var charset_;
	if (document.all) charset_=document.charset.toLowerCase(); else charset_=document.characterSet.toLowerCase();
	
	var error = function (type, msg, filename, line){throw new window[type](msg, filename, line);};
    var read_until = function (data, offset, stopchr){//('a:2:{i:0;s:6:"ааа";i:1;s:8:"бббб";}')
        var buf = [];
        var chr = data.slice(offset, offset + 1);
        var i = 2;
        while (chr != stopchr) {
            if ((i+offset) > data.length) {
                error('Error', 'Invalid');
            }
            buf.push(chr);
            chr = data.slice(offset + (i - 1),offset + i);
            i += 1;
        }
        return [buf.length, buf.join('')];
    };
    var read_chrs = function (data, offset, length){
        var buf;
        var i = 0;
        buf = [];
        while (i < length)
			{
    	        var chr = data.slice(offset + (i - 1),offset + i);
        	    buf.push(chr);
				i++;
				
				if (chr.search(/[а-яА-Я]/)>-1 && charset_=='utf-8') length--;//коррекция, в utf-8 русские буквы кодируются 2-мя символами в РНР, здесь - одним
        	}
        return [buf.length, buf.join('')];
    };
    var _unserialize = function (data, offset){
        var readdata;
        var readData;
        var chrs = 0;
        var ccount;
        var stringlength;
        var keyandchrs;
        var keys;
 
        if(!offset) offset = 0;
        var dtype = (data.slice(offset, offset + 1)).toLowerCase();
        
        var dataoffset = offset + 2;
        var typeconvert = new Function('x', 'return x');
        
        switch(dtype){
            case "i":
                typeconvert = new Function('x', 'return parseInt(x)');
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case "b":
                typeconvert = new Function('x', 'return (parseInt(x) == 1)');
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case "d":
                typeconvert = new Function('x', 'return parseFloat(x)');
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case "n":
                readdata = null;
            break;
            case "s"://var aaa=unserialize('a:2:{i:0;s:6:"ааа";i:1;s:8:"бббб";}')
                ccount = read_until(data, dataoffset, ':');
                chrs = ccount[0];
                stringlength = ccount[1];//длина строки которая, т.е. число которое есть в s:6
                dataoffset += chrs + 2;
                
                readData = read_chrs(data, dataoffset+1, parseInt(stringlength));
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 2;
                if(chrs != parseInt(stringlength) && chrs != readdata.length){
                    error('SyntaxError', 'String length mismatch');
                }
            break;
            case "a":
                readdata = {};
                
                keyandchrs = read_until(data, dataoffset, ':');
                chrs = keyandchrs[0];
                keys = keyandchrs[1];
                dataoffset += chrs + 2;
                
                for(var i = 0;i < parseInt(keys);i++){
                    var kprops = _unserialize(data, dataoffset);
                    var kchrs = kprops[1];
                    var key = kprops[2];
                    dataoffset += kchrs;
                    
                    var vprops = _unserialize(data, dataoffset);
                    var vchrs = vprops[1];
                    var value = vprops[2];
                    dataoffset += vchrs;
                    
                    readdata[key] = value;
                }
                
                dataoffset += 1;
            break;
            default: alert(data);
                error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype+' strItem:'+dataoffset);
            break;
        }
        return [dtype, dataoffset - offset, typeconvert(readdata)];
    };
    return _unserialize(data, 0)[2];
}

function serialize( mixed_val ) {
    // 
    // +   original by: Ates Goral (http://magnetiq.com)
    // +   adapted for IE: Ilia Kantor (http://javascript.ru)
 
    switch (typeof(mixed_val)){
        case "number":
            if (isNaN(mixed_val) || !isFinite(mixed_val)){
                return false;
            } else{
                return (Math.floor(mixed_val) == mixed_val ? "i" : "d") + ":" + mixed_val + ";";
            }
        case "string":
            return "s:" + mixed_val.length + ":\"" + mixed_val + "\";";
        case "boolean":
            return "b:" + (mixed_val ? "1" : "0") + ";";
        case "object":
            if (mixed_val == null) {
                return "N;";
            } else if (mixed_val instanceof Array) {
                var idxobj = { idx: -1 };
		var map = []
		for(var i=0; i<mixed_val.length;i++) {
			idxobj.idx++;
            var ser = serialize(mixed_val[i]); 
			if (ser) {
                map.push(serialize(idxobj.idx) + ser)
            }
		}                                       
        return "a:" + mixed_val.length + ":{" + map.join("") + "}"

            } else {
                var class_name = get_class(mixed_val);
                 if (class_name == undefined){
                    return false;
                }
                 var props = new Array();
                for (var prop in mixed_val) {
                    var ser = serialize(mixed_val[prop]);
 
                    if (ser) {
                        props.push(serialize(prop) + ser);
                    }
                }
                return "O:" + class_name.length + ":\"" + class_name + "\":" + props.length + ":{" + props.join("") + "}";
            }
        case "undefined":
            return "N;";
    }
    return false;
}

function stripslashes (str) {
  return (str + '').replace(/\\(.?)/g, function (s, n1) {
    switch (n1) {
    case '\\':
      return '\\';
    case '0':
      return '\u0000';
    case '':
      return '';
    default:
      return n1;
    }
  });
}
function get_class(obj) {	// Returns the name of the class of an object
	// 
	// +   original by: Ates Goral (http://magnetiq.com)
	// +   improved by: David James

	if (obj instanceof Object && !(obj instanceof Array) &&
		!(obj instanceof Function) && obj.constructor) {
		var arr = obj.constructor.toString().match(/function\s*(\w+)/);

		if (arr && arr.length == 2) {
			return arr[1];
		}
	}

	return false;
}

function addslashes(str) {
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\'/g, '\\\'');
    str = str.replace(/\"/g, '\\"');
    str = str.replace(/\0/g, '\\0');
    return str;
}

$(document).ready(function() {
	'use strict';
$('.dtpicker' ).datetimepicker({
	timeInput: true,
	timeFormat: "HH:mm:ss",
});
/*добавление кнопок в диалоговое окно*/
$.widget( "app.dialog", $.ui.dialog, {
        options: {
            iconButtons: [],
            _flag:true
        },
         _setOptions: function( options ) {
                this._super( options );
             if (this.options._flag){
                 this._create();
                 this.options._flag=false;
             }
            },
        _create: function() {
            this._super();
            var $titlebar = this.uiDialog.find( ".ui-dialog-titlebar" );
            $.each( this.options.iconButtons, function( i, v ) {
                var $button = $( "<button/>" ).text( this.text ),
                    right = $titlebar.find( "button:last" ).css( "right" );
                $button.button( { icons: { primary: this.icon }, text: false } )
                       .addClass( "ui-dialog-titlebar-close" )
                       .css( "right", ( parseInt( right ) + 22) + "px" )
                       .click( this.click )
                       .appendTo( $titlebar );
            });
    
        }
 });

    
});
/*новый виджет для выбора из подгрузки*/
(function($) {
    $.widget("ui.tagit", {
        options: {
            allowDuplicates: false,
            caseSensitive: true,
            fieldName: "tags",
            placeholderText: null,
            readOnly: false,
            removeConfirmation: false,
            tagLimit: null,
            availableTags: [],
            autocomplete: {},
            showAutocompleteOnFocus: false,
            allowSpaces: false,
            singleField: false,
            singleFieldDelimiter: ",",
            singleFieldNode: null,
            animate: true,
            tabIndex: null,
            beforeTagAdded: null,
            afterTagAdded: null,
            beforeTagRemoved: null,
            afterTagRemoved: null,
            onTagClicked: null,
            onTagLimitExceeded: null,
            onTagAdded: null,
            onTagRemoved: null,
            tagSource: null
        },
        _create: function() {
            var that = this;
            if (this.element.is("input")) {
                this.tagList = $("<ul></ul>").insertAfter(this.element);
                this.options.singleField = true;
                this.options.singleFieldNode = this.element;
                this.element.css("display", "none")
            } else {
                this.tagList = this.element.find("ul, ol").andSelf().last()
            }
            this.tagInput = $('<input type="text" maxlength="50" />').addClass("ui-widget-content");
            if (this.options.readOnly) this.tagInput.attr("disabled", "disabled");
            if (this.options.tabIndex) {
                this.tagInput.attr("tabindex", this.options.tabIndex)
            }
            if (this.options.placeholderText) {
                this.tagInput.attr("placeholder", this.options.placeholderText)
            }
            if (!this.options.autocomplete.source) {
                this.options.autocomplete.source = function(search, showChoices) {
                    var filter = search.term.toLowerCase();
                    var choices = $.grep(this.options.availableTags, function(element) {
                        return element.toLowerCase().indexOf(filter) === 0
                    });
                    if (!this.options.allowDuplicates) {
                        choices = this._subtractArray(choices, this.assignedTags())
                    }
                    showChoices(choices)
                }
            }
            if (this.options.showAutocompleteOnFocus) {
                this.tagInput.focus(function(event, ui) {
                    that._showAutocomplete()
                });
                if (typeof this.options.autocomplete.minLength === "undefined") {
                    this.options.autocomplete.minLength = 0
                }
            }
            if ($.isFunction(this.options.autocomplete.source)) {
                this.options.autocomplete.source = $.proxy(this.options.autocomplete.source, this)
            }
            if ($.isFunction(this.options.tagSource)) {
                this.options.tagSource = $.proxy(this.options.tagSource, this)
            }
            this.tagList.addClass("tagit").addClass("ui-widget ui-widget-content ui-corner-all").append($('<li class="tagit-new"></li>').append(this.tagInput)).click(function(e) {
                var target = $(e.target);
                if (target.hasClass("tagit-label")) {
                    var tag = target.closest(".tagit-choice");
                    if (!tag.hasClass("removed")) {
                        that._trigger("onTagClicked", e, {
                            tag: tag,
                            tagLabel: that.tagLabel(tag)
                        })
                    }
                } else {
                    that.tagInput.focus()
                }
            });
            var addedExistingFromSingleFieldNode = false;
            if (this.options.singleField) {
                if (this.options.singleFieldNode) {
                    var node = $(this.options.singleFieldNode);
                    var tags = node.val().split(this.options.singleFieldDelimiter);
                    var lables = node.data("lables") ? node.data("lables").split(";") : [];
                    node.val("");
                    $.each(tags, function(index, tag) {
                        var l = typeof lables[index] != "undefined" ? lables[index] : tag;
                        that.createTag(l, null, true, tag);
                        addedExistingFromSingleFieldNode = true
                    })
                } else {
                    this.options.singleFieldNode = $('<input type="hidden" style="display:none;" value="" name="' + this.options.fieldName + '" />');
                    this.tagList.after(this.options.singleFieldNode)
                }
            }
            if (!addedExistingFromSingleFieldNode) {
                this.tagList.children("li").each(function() {
                    if (!$(this).hasClass("tagit-new")) {
                        that.createTag($(this).text(), $(this).attr("class"), true);
                        $(this).remove()
                    }
                })
            }
            that.tagInput.focus(function() {
                that.tagList.addClass("infocus")
            });
            that.tagInput.blur(function() {
                that.tagList.removeClass("infocus")
            });
            this.tagInput.keypress(function(event) {
                if (event.which === 44 || event.which === 13 || event.which === 47 || event.which == 32 && that.options.allowSpaces !== true && ($.trim(that.tagInput.val()).replace(/^s*/, "").charAt(0) != '"' || $.trim(that.tagInput.val()).charAt(0) == '"' && $.trim(that.tagInput.val()).charAt($.trim(that.tagInput.val()).length - 1) == '"' && $.trim(that.tagInput.val()).length - 1 !== 0)) {
                    if (!(event.which === 13 && that.tagInput.val() === "")) {
                        event.preventDefault()
                    }
                    that.createTag(that._cleanedInput())
                }
            }).keydown(function(event) {
                if (event.which == $.ui.keyCode.BACKSPACE && that.tagInput.val() === "") {
                    var tag = that._lastTag();
                    if (!that.options.removeConfirmation || tag.hasClass("remove")) {
                        that.removeTag(tag)
                    } else if (that.options.removeConfirmation) {
                        tag.addClass("remove ui-state-highlight")
                    }
                } else if (that.options.removeConfirmation) {
                    that._lastTag().removeClass("remove ui-state-highlight")
                }
                if (event.which == $.ui.keyCode.TAB && that.tagInput.val() !== "") {
                    if (!(event.which === $.ui.keyCode.ENTER && that.tagInput.val() === "")) {}
                    if (!that.tagInput.data("autocomplete-open")) {
                        that.createTag(that._cleanedInput())
                    }
                }
            }).blur(function(e) {
                if (!that.tagInput.data("autocomplete-open")) {
                    that.createTag(that._cleanedInput())
                }
            });
            if (this.options.availableTags || this.options.tagSource || this.options.autocomplete.source) {
                var autocompleteOptions = {
                    autoFocus: true,
                    select: function(event, ui) {
                        if (typeof ui.item.ident == "undefined") {
                            that.createTag(ui.item.value)
                        } else {
                            that.createTag(ui.item.value, null, false, ui.item.ident)
                        }
                        return false
                    }
                };
                $.extend(autocompleteOptions, this.options.autocomplete);
                autocompleteOptions.source = this.options.tagSource || autocompleteOptions.source;
                this.tagInput.autocomplete(autocompleteOptions).bind("autocompleteopen", function(event, ui) {
                    that.tagInput.data("autocomplete-open", true)
                }).bind("autocompleteclose", function(event, ui) {
                    that.tagInput.data("autocomplete-open", false)
                })
            }
        },
        _cleanedInput: function() {
            return $.trim(this.tagInput.val().replace(/^"(.*)"$/, "$1"))
        },
        _lastTag: function() {
            return this.tagList.find(".tagit-choice:last:not(.removed)")
        },
        _tags: function() {
            return this.tagList.find(".tagit-choice:not(.removed)")
        },
        assignedTags: function() {
            var that = this;
            var tags = [];
            if (this.options.singleField) {
                tags = $(this.options.singleFieldNode).val().split(this.options.singleFieldDelimiter);
                if (tags[0] === "") {
                    tags = []
                }
            } else {
                this._tags().each(function() {
                    tags.push(that.tagLabel(this))
                })
            }
            return tags
        },
        _updateSingleTagsField: function(tags) {
            $(this.options.singleFieldNode).val(tags.join(this.options.singleFieldDelimiter)).trigger("change")
        },
        _subtractArray: function(a1, a2) {
            var result = [];
            for (var i = 0; i < a1.length; i++) {
                if ($.inArray(a1[i], a2) == -1) {
                    result.push(a1[i])
                }
            }
            return result
        },
        tagLabel: function(tag) {
            if (this.options.singleField) {
                return $(tag).find(".tagit-label:first").text()
            } else {
                return $(tag).find("input:first").val()
            }
        },
        _showAutocomplete: function() {
            this.tagInput.autocomplete("search", "")
        },
        _findTagByLabel: function(name) {
            var that = this;
            var tag = null;
            this._tags().each(function(i) {
                if (that._formatStr(name) == that._formatStr(that.tagLabel(this))) {
                    tag = $(this);
                    return false
                }
            });
            return tag
        },
        _isNew: function(name) {
            return !this._findTagByLabel(name)
        },
        _formatStr: function(str) {
            if (this.options.caseSensitive) {
                return str
            }
            return $.trim(str.toLowerCase())
        },
        _effectExists: function(name) {
            return Boolean($.effects && ($.effects[name] || $.effects.effect && $.effects.effect[name]))
        },
        createTag: function(value, additionalClass, duringInitialization, ident) {
            var that = this;
            value = $.trim(value);
            ident = !ident ? value : $.trim(ident);
            if (this.options.preprocessTag) {
                value = this.options.preprocessTag(value)
            }
            if (value === "") {
                return false
            }
            if (!this.options.allowDuplicates && !this._isNew(value)) {
                var existingTag = this._findTagByLabel(value);
                if (this._trigger("onTagExists", null, {
                        existingTag: existingTag,
                        duringInitialization: duringInitialization
                    }) !== false) {
                    if (this._effectExists("highlight")) {
                        existingTag.effect("highlight")
                    }
                }
                return false
            }
            if (this.options.tagLimit && this._tags().length >= this.options.tagLimit) {
                this._trigger("onTagLimitExceeded", null, {
                    duringInitialization: duringInitialization
                });
                return false
            }
            var label = $(this.options.onTagClicked ? '<a class="tagit-label"></a>' : '<span class="tagit-label"></span>').text(value);
            var tag = $("<li></li>").addClass("tagit-choice ui-widget-content ui-state-default ui-corner-all").addClass(additionalClass).append(label).data("ident", ident);
            if (this.options.readOnly) {
                tag.addClass("tagit-choice-read-only")
            } else {
                tag.addClass("tagit-choice-editable");
                var removeTagIcon = $("<span></span>").addClass("ui-icon ui-icon-close");
                var removeTag = $('<a><span class="text-icon">×</span></a>').addClass("tagit-close").append(removeTagIcon).click(function(e) {
                    that.removeTag(tag)
                });
                tag.append(removeTag)
            }
            if (!this.options.singleField) {
                var escapedValue = label.html();
                tag.append('<input type="hidden" style="display:none;" value="' + escapedValue + '" name="' + this.options.fieldName + '" />')
            }
            if (this._trigger("beforeTagAdded", null, {
                    tag: tag,
                    tagLabel: this.tagLabel(tag),
                    duringInitialization: duringInitialization
                }) === false) {
                return
            }
            if (this.options.singleField) {
                var tags = this.assignedTags();
                tags.push(ident);
                this._updateSingleTagsField(tags)
            }
            this._trigger("onTagAdded", null, tag);
            this.tagInput.val("");
            this.tagInput.parent().before(tag);
            this._trigger("afterTagAdded", null, {
                tag: tag,
                tagLabel: this.tagLabel(tag),
                duringInitialization: duringInitialization
            });
            if (this.options.showAutocompleteOnFocus && !duringInitialization) {
                setTimeout(function() {
                    that._showAutocomplete()
                }, 0)
            }
        },
        removeTag: function(tag, animate) {
            animate = typeof animate === "undefined" ? this.options.animate : animate;
            tag = $(tag);
            this._trigger("onTagRemoved", null, tag);
            if (this._trigger("beforeTagRemoved", null, {
                    tag: tag,
                    tagLabel: this.tagLabel(tag)
                }) === false) {
                return
            }
            if (this.options.singleField) {
                var tags = this.assignedTags();
                var removedTagLabel = tag.data("ident");
                tags = $.grep(tags, function(el) {
                    return el != removedTagLabel
                });
                this._updateSingleTagsField(tags)
            }
            if (animate) {
                tag.addClass("removed");
                var hide_args = this._effectExists("blind") ? ["blind", {
                    direction: "horizontal"
                }, "fast"] : ["fast"];
                var thisTag = this;
                hide_args.push(function() {
                    tag.remove();
                    thisTag._trigger("afterTagRemoved", null, {
                        tag: tag,
                        tagLabel: thisTag.tagLabel(tag)
                    })
                });
                tag.fadeOut("fast").hide.apply(tag, hide_args).dequeue()
            } else {
                tag.remove();
                this._trigger("afterTagRemoved", null, {
                    tag: tag,
                    tagLabel: this.tagLabel(tag)
                })
            }
        },
        removeTagByLabel: function(tagLabel, animate) {
            var toRemove = this._findTagByLabel(tagLabel);
            if (!toRemove) {
                throw "No such tag exists with the name '" + tagLabel + "'"
            }
            this.removeTag(toRemove, animate)
        },
        removeAll: function() {
            var that = this;
            this._tags().each(function(index, tag) {
                that.removeTag(tag, false)
            })
        }
    })
})(jQuery);













/***********************************УСТАРЕВШЕЕ**/
$(document).ready(function() {
    f49();
gf56();
$( "#f56_dialog" ).dialog({
      resizable: true,
    autoOpen:false,

});

});



function f49()
{
    $( ".controlgroup49" ).selectmenu({
        select:function( event, ui ) {
            event.preventDefault();
            if (ui.item.value){
                window.open(ui.item.value);
                $( ".controlgroup49 option[value='']" ).prop("selected",true);
            }
        }
    });
}
function gf56()
{
    $( ".controlgroup56" ).selectmenu({
        select:function( event, ui ) {
            event.preventDefault();
            if (ui.item.value){
                var v=ui.item.value.split("@");
                f56(v[0],v[1],v[2],v[3]);
                $( ".controlgroup56 option[value='']" ).prop("selected",true);
            }
        }
    });
}

var win_name,win_names_array=[];

if (typeof(dataitem)!='object') {var dataitem=[];}
if (typeof(timeitem)!='object') {var timeitem=[];}
if (typeof(fulldataitem)!='object') {var fulldataitem=[];}
function snd(obj,item_obj)
{//подтверждение удаления
	if (window.confirm('Подтвердите операцию')) 
		{
			d=document.createElement("input");
			d.setAttribute('value',item_obj.value);
			d.setAttribute('name',obj);
			d.setAttribute('id',obj);
			d.setAttribute('type','hidden');
			item_obj.form.appendChild(d);
			item_obj.form.submit();
		 } 
		 	else return false;
}

function pole_id47(str,hidden_name)
{//для поля 47 (алфавит)
document.getElementById(hidden_name).value=str;//то что передается на сервер
document.getElementById(hidden_name).form.submit();//подписать форму, т.е. отправть на сервер
}
function nl_create_now_date(maska)
{//генерация текущей даты по маске как в пхп, например 
d=new Date();
var out='';
for (i=0;i<maska.length;i++)
	{s=maska.substr(i,1)
	if (s=='%')
		{i++;s=maska.substr(i,1)
		switch (s)
			{case 'H':{s=d.getHours();break;}
			case 'M':{s=d.getMinutes();break;}
			case 'S':{s=d.getSeconds();break;}
			case 'd':{s=d.getDate();break;}
			case 'D':{s=d.getDate();break;}
			case 'I':{s=d.getHours();if (s>12) s=s-12;break;}
			case 'm':{s=d.getMonth();s++;break;}
			case 'w':{s=d.getDay();break;}
			case 'Y':{s=d.getFullYear();break;}
			case 'y':{s=d.getFullYear();s=s.toString();s=s.substr(2,2);break;}
			case 'T':{s=d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();break;}
			}
		}
	out+=s
	}
return out
}
//*************************************************для поля 48
function htmlspecialchars_decode(text)
{//обратное преобразование специальных символов
   var chars = Array("&amp;", "&lt;", "&gt;", "&quot;", "'");
   var replacements = Array("&", "<", ">", '"', "'");
   for (var i=0; i<chars.length; i++)
   {
       var re = new RegExp(chars[i], "gi");
       if(re.test(text))
       {
           text = text.replace(re, replacements[i]);
       }
   }
  return text;
}

function db_record_item48(array_key,array_value,selected_flag)
{//массив данных для поля 48

	this.array_key = array_key
	this.array_value =htmlspecialchars_decode(array_value)
	this.selected_flag=selected_flag
	return this
}
//установка поля 48 в начальное состояние
//цикл по всем полям типа 48
if (typeof(db_item48)=="object")
for (win_name in db_item48) 
	{
		ff=db_item48[win_name]["function"];//заполнить данными
	ff();
	value_=[];
	text_=[];
	for (i=0;i<db_item48[win_name].length;i++)
		{
		if (db_item48[win_name][i].selected_flag>0) 
				{value_[value_.length]=db_item48[win_name][i].array_key;
				text_[text_.length]=db_item48[win_name][i].array_value;
				}
		
		}
	document.getElementById(db_item48[win_name]["io_item"]).value=value_.join(",");
	document.getElementById(db_item48[win_name]["io_item"]+"_text").innerHTML=text_.join(",");
	}

var win_f56;
function f56(url,w,h,reload)
{
    h=parseInt(h);
    w=parseInt(w);
    win_f56=$( "#f56_dialog" ).dialog( "option", { 
        height: h+65,
        width: w+30,
        modal: true,
        temp:{},
        open: function(ev, ui){ $('#iframe56').attr({'src':url,'width':'100%','height':h});},
        close: function(){if (reload>0){location.href=location.href.split('?')[0]+'?'+Math.random();}},
        iconButtons: [
            {
                icon: "ui-icon-arrow-4-diag",
                click: function( e ) {$( "#catalog" ).tabs( "refresh" );
                    e.preventDefault;
                    var options = $( "#f56_dialog" ).dialog( "option" );
                    if (options.temp.full){
                            $( "#f56_dialog" ).dialog( "option", {
                                height:options.temp.height,
                                width:options.temp.width,
                                temp:{
                                full:false,
                            }
                            });

                        } else{console.log(options.temp.full);
                            $( "#f56_dialog" ).dialog( "option", {
                                height:$(window).height(),
                                width:$(window).width(),
                                temp:{
                                full:true,
                                width:options.width,
                                height:options.height
                            }
                            });
                        }
                }
            },
        ],
    } );
    $( "#f56_dialog" ).dialog("open");
}
function f56_close()
{
    $( "#f56_dialog" ).dialog("close");
}

function f57(button,hiden)
{/*hiden: владелец,группа,код_доступа*/
var rez,r=0;
v=hiden.val().split(',');
$( "#f57_dialog" ).dialog({
      resizable: true,
      width: "auto",
      modal: true,
      buttons: [
            {
              text: "Применить",
            class:"permiss_ok",
              click: function() {
                  var r=0;
              $(".perm_bits:checked").each(function(index,element){
                  r+=parseInt($(this).val());
              });

                button.text($("#u option:selected").text() +":"+ $("#g option:selected").text() +" "+ $("#mode_f57").text()       );
                hiden.val(parseInt($("#u").val()||0)+","+parseInt($("#g").val()||0)+","+parseInt(r+parseInt(parseInt($("select[name=p1]").val()+$("select[name=p2]").val()+$("select[name=p3]").val(),8).toString(10))));
                $( this ).dialog( "close" );
              }
            },
            {
              text: "Отменить",
              class:"permiss_cancel",
              click: function() {
                $( this ).dialog( "close" );
              }
            }

      ],
      open: function(ev, ui){
          $("#mode_f57").text(_f57_p(v[2])+ " ("+pad(parseInt(v[2],10).toString(8),4)+")");  
          _f57_select(v);
          $("select[name^=p], .perm_bits").on("click",function(){
              
              r=0;
              $(".perm_bits:checked").each(function(index,element){
                  r+=parseInt($(this).val());
              });
              
              rez=$("select[name=p1]").val();
              rez+=$("select[name=p2]").val();
              rez+=$("select[name=p3]").val();
              rez=parseInt(parseInt(rez,8).toString(10));
              rez+=r;
              $("#mode_f57").text(_f57_p(rez)+ " ("+pad(parseInt(rez,10).toString(8),4)+")");  

          });
          }
});
    
}


function _f57_p(pp)
{
    var mode1;
    mode1= ((pp & 0x0100) ? 'r' : '-');
    mode1 += ((pp & 0x0080) ? 'w' : '-');
    mode1 += ((pp & 0x0040) ? ((pp & 0x0800) ? 's' : 'x' ) : ((pp & 0x0800) ? 'S' : '-'));
    // Группа
    mode1 += ((pp & 0x0020) ? 'r' : '-');
    mode1 += ((pp & 0x0010) ? 'w' : '-');
    mode1 += ((pp & 0x0008) ? ((pp & 0x0400) ? 's' : 'x' ) : ((pp & 0x0400) ? 'S' : '-'));
    // Мир
    mode1 += ((pp & 0x0004) ? 'r' : '-');
    mode1 += ((pp & 0x0002) ? 'w' : '-');
    mode1 += ((pp & 0x0001) ? ((pp & 0x0200) ? 't' : 'x' ) : ((pp & 0x0200) ? 'T' : '-'));
    return mode1;
    
}

function _f57_select(p)
{
    var pp=p[2];
    $('#u option:selected').prop('selected', false);
    $('#g option:selected').prop('selected', false);
    $('#u option[value="'+[p[0]]+'"]').prop('selected', true);
    $('#g option[value="'+[p[1]]+'"]').prop('selected', true);
    
    var p3=pp & 7;
    $('select[name=p3] option[value="'+p3+'"]').prop('selected', true);
    pp=pp>>>3;
    var p2=pp & 7;
    $('select[name=p2] option[value="'+p2+'"]').prop('selected', true);
    pp=pp>>>3;
    var p1=pp & 7;
    $('select[name=p1] option[value="'+p1+'"]').prop('selected', true);
    
    pp=pp>>>3;
    $('#sticky').prop('checked', pp & 1);
    $('#sgid').prop('checked', pp & 2);
    $('#suid').prop('checked', pp & 4);
}
function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}


function create_window(win_name)
{
columns=db_item48[win_name]["columns"]//кол-во колонок в окне
if (columns==0 || columns=='') columns=2;//по умоляанию 2
col=0;row=0;//текущее состояние
out='<html><body><form><table width="100%" border=0 style="font-size:12px; font-family:Verdana, Arial, Helvetica, sans-serif">';
i=0;
row_no_end=true;
while (row_no_end)
	{//цикл по строкам
	out+='<tr>';
	for (c=0;c<columns;c++)
		{//цикл по колонкам
		if (db_item48[win_name].length>i) 
				{selected='';
				if (db_item48[win_name][i].selected_flag>0) selected='checked';
				out+='<td>' + '<label><input name="checkbox['+i+']" type="checkbox" value="'+db_item48[win_name][i].array_key+'"' + selected +' /><span id="text__'+i+'">'+db_item48[win_name][i].array_value+'</span></label></td>'
				}
			else {row_no_end=false;out+='<td>&nbsp;</td>';}
		i++;
		}
	out+='</tr>'
	}

out+='</table><div align="center"><input name="save48" type="button" value="'+db_item48[win_name]["button_caption"]+'" onClick="save()" /></div></form>';
out+='<scr'+'ipt type="text/javascript">'
out+='function save()\\n'
out+='{text_=[];value=[];kk=0;'
out+='for (i=0;i<document.forms[0].elements.length-1;i++)\\n'
out+='{'
out+='if (document.forms[0].elements[i].checked) {value[kk]=document.forms[0].elements[i].value;text_[kk]=document.getElementById("text__"+i).innerHTML;kk++;}'
out+='}'

out+='opener.document.getElementById("'+db_item48[win_name]["io_item"]+'").value=value.join(",");\\n'
out+='opener.document.getElementById("'+db_item48[win_name]["io_item"]+'_text").innerHTML=text_.join(",");\\n';
out+='window.close()}\\n'
out+='window.moveTo(300,300);'
out+='</sc'+'ript>'

out+='</body></html>';
ww=db_item48[win_name]["window"][0];//ширина окна
hh=db_item48[win_name]["window"][1];//высота экрана
if (ww==0 || ww=='') ww=400;
if (hh==0 || hh=='') hh=400;
p='width='+ww+',height='+hh+'toolbar=no,menubar=no,scrollbars=yes';
win_names_array[win_name]=window.open('','',p);
//генерирум там скрипт и все остальное
win_names_array[win_name].document.write(out)
}


function create_window55(win_name)
{
columns=db_item48[win_name]["columns"]//кол-во колонок в окне
if (columns==0 || columns=='') columns=2;//по умоляанию 2
col=0;row=0;//текущее состояние
out='<table width="100%" border="0" class="win55">';
i=0;
row_no_end=true;
while (row_no_end)
	{//цикл по строкам
	out+='<tr>';
	for (c=0;c<columns;c++)
		{//цикл по колонкам
		if (db_item48[win_name].length>i) 
				{selected='';
				if (db_item48[win_name][i].selected_flag>0) selected='checked';
				out+='<td><label>';
                out+='<input name="checkbox['+i+']" type="checkbox" value="'+db_item48[win_name][i].array_key+'"' + selected +' />';
                out+='<span id="text__'+i+'">'+db_item48[win_name][i].array_value+'</span>';
                out+='</label></td>';
				}
			else {row_no_end=false;out+='<td>&nbsp;</td>';}
		i++;
		}
	out+='</tr>'
	}

out+='</table><div align="center"><input name="save55[]" type="button" value="'+db_item48[win_name]["button_caption"]+'" onClick="save55(\''+win_name+'\')" /></div>';
ww=db_item48[win_name]["window"][0];//ширина окна
hh=db_item48[win_name]["window"][1];//высота экрана
if (ww==0 || ww=='') ww=400;
if (hh==0 || hh=='') hh="auto";
$( "#f55_dialog" ).html(out);
$( "#f55_dialog" ).dialog({
      resizable: true,
      height: hh,
      width: ww,
      modal: true,

});
}

function save55(win_name)
{
var value=[],text_=[];
$( "#f55_dialog" ).dialog("close");
$( "#f55_dialog input:checked" ).each(
    function (index){value[index]=$(this).val();text_[index]=$(this).next().text();}
);
document.getElementById(db_item48[win_name]["io_item"]).value=value.join(",");
document.getElementById(db_item48[win_name]["io_item"]+"_text").innerHTML=text_.join(",");
}

/*F100*/
function f100_ini()
{
    $('.f100_del').on('click',function(){
    var id=$(this).attr('data-id');alert(id);
    $('#'+id).remove();
    $(this).remove();
    });
}
var f100=100;
$('.f100_add').on('click',function(){
    var id=$(this).attr('data-id');
    var inp='<br><input name="'+id+'" id="f100_'+f100+'" type="text"><button type="button" class="f100_del" data-id="f100_'+f100+'">-</button>';
    $('.f100-container').append(inp);
    f100_ini();
    f100++;
});
f100_ini();

setInterval(function(){
if ( $('iframe').is('#ff101')) {
document.getElementById('ff101').height = document.getElementById('ff101').contentWindow.document.body.scrollHeight+10;}
}
,2000);

function data___clock()
{
 for (i=0;i<fulldataitem.length;i++) {
     if (document.getElementById(fulldataitem[i])) {
         document.getElementById(fulldataitem[i]).value=full_data_now;
         document.getElementById(fulldataitem[i]).innerHTML=full_data_now;
     }
 }
 for (i=0;i<dataitem.length;i++) {
     if (document.getElementById(dataitem[i])) {
         document.getElementById(dataitem[i]).value=data_now;
         document.getElementById(dataitem[i]).innerHTML=data_now;
     }
 }
 for (i=0;i<timeitem.length;i++) {
     if (document.getElementById(timeitem[i])) {
         document.getElementById(timeitem[i]).value=time_now;
         document.getElementById(timeitem[i]).innerHTML=time_now;
     }
 }
setTimeout('data___clock()',2000)
}
data___clock();

/*для tabadmins*/
function select_check(obj)
{
var flag=false;
document.getElementById('delete_selected_').disabled=true;
 for (i = 0; i < obj.form.elements.length; i++)
     {
         var item = obj.form.elements[i];
	     if (typeof(item.name)!='undefined')
		 if (item.name.search(/^_select_item\[/)>-1)  
		 {
		     if (item.checked)  flag=true;
		 };
	 }
if (document.getElementById('delete_selected_')!=null) 	
	{if (flag) document.getElementById('delete_selected_').disabled=false;
	}
}
function select_all(obj)
{
 for (i = 0; i < obj.form.elements.length; i++)
     {
         var item = obj.form.elements[i];
		 if (typeof(item.name)!='undefined')
	     if (item.name.search(/^_select_item\[/)>-1)  
		 {
		     item.checked = obj.checked;
		 };
	 }
if (document.getElementById('delete_selected_')!=null) 
	{if (obj.checked) document.getElementById('delete_selected_').disabled=false; else document.getElementById('delete_selected_').disabled=true;
	}
}

/*визуализация дерева*/
var mycookie=[],db = [],target= [],indentPixels= [],collapsedWidget= [],expandedWidget=[],endpointWidget=[],
fillerimg=[],widgetWidth=[],widgetHeight=[],collapsedImg=[], endpointImg=[],expandedImg=[];


function dbRecord(mother,display,URL,indent,statusMsg,title){
	this.mother = mother   
	this.display =htmlspecialchars_decode(display)
	this.URL = URL
	this.indent = indent   
	this.statusMsg = statusMsg
	this.title=title
	return this
}
function setCurrState(setting,tree_name) {
mycookie[tree_name] = document.cookie = tree_name+"=" + escape(setting)+ '; path=/';
}
function getCurrState(tree_name) {
var label = tree_name+"="
        var labelLen = label.length
        var cLen = mycookie[tree_name].length
        var i = 0
        while (i < cLen) {
                var j = i + labelLen
                if (mycookie[tree_name].substring(i,j) == label) {
                        var cEnd = mycookie[tree_name].indexOf(";",j)
                        if (cEnd ==     -1) {
                                cEnd = mycookie[tree_name].length
                        }
                        return unescape(mycookie[tree_name].substring(j,cEnd))
                }
                i++
        }
        return ""
}

function toggle(n,tree_name) {
	var newString = ""
	var currState = getCurrState(tree_name)
	var expanded = currState.charAt(n) 
	newString += currState.substring(0,n)
	newString += expanded ^ 1 
	newString += currState.substring(n+1,currState.length)
	setCurrState(newString,tree_name) 
}

function getGIF(n, currState,tree_name) {
	var mom = db[tree_name][n].mother  
	var expanded = currState.charAt(n) 
	if (!mom) {
		return endpointWidget[tree_name]
	} else {
		if (expanded == 1) {
			return expandedWidget[tree_name]
		}
	}
	return collapsedWidget[tree_name]
}

function getGIFStatus(n, currState,tree_name)
 {
	var mom = db[tree_name][n].mother  
	var expanded = currState.charAt(n) 
	if (!mom) {	return "No further items"} 
		else {
			if (expanded == 1) 
				{return "Click to collapse nested items"	}
			}
		return "Click to expand nested item"
}
function out(tree_name)
{
var newOutline = ""
var prevIndentDisplayed = 0
var showMyDaughter = 0
var currState = getCurrState(tree_name)
for (var i = 0; i < db[tree_name].length; i++) {
	var theGIF = getGIF(i, currState,tree_name)		
	var theGIFStatus = getGIFStatus(i, currState,tree_name)  	
	var currIndent = db[tree_name][i].indent	
	var expanded = currState.charAt(i) 
	if (currIndent == 0 || currIndent <= prevIndentDisplayed || (showMyDaughter == 1 && (currIndent - prevIndentDisplayed == 1))) {
		newOutline += "<IMG vspace=\"8\" SRC=\""+fillerimg[tree_name]+"\" HEIGHT = 1 WIDTH =" + (indentPixels[tree_name] * currIndent) + ">"
		newOutline += "<A HREF=\"javascript:out('"+tree_name+"')\" " + 	"onMouseOver=\"window.status=\'" + theGIFStatus +	"\';return true;\"  onClick=\"toggle(" + i + ",'"+tree_name+"');return " + 	(theGIF != endpointWidget[tree_name]) + "\">"
		newOutline += "<IMG SRC=\"" + theGIF + "\" HEIGHT=" + widgetHeight[tree_name] + " WIDTH=" + widgetWidth[tree_name] + " BORDER=0></A>"		
		if (db[tree_name][i].URL == "" || db[tree_name][i].URL == null) 
		{
			newOutline += " " + db[tree_name][i].display + "<BR>"	// no link	
		} else {
			newOutline += "<A HREF="+db[tree_name][i].URL+" title=\""+db[tree_name][i].title +"\" onMouseOver=\"window.status=\'" +	db[tree_name][i].statusMsg + "\';return true;\" target=\""+target[tree_name]+"\">" + db[tree_name][i].display + "</A><BR>"
		}
		prevIndentDisplayed = currIndent
		showMyDaughter = expanded
		if (db[tree_name].length > 10000) {document.getElementById(tree_name+'_out').innerHTML=newOutline;newOutline = ""}
	}
}
document.getElementById(tree_name+'_out').innerHTML=newOutline;
    f49();
}


function out_intab(tree_name)
{
var newOutline = '';
var prevIndentDisplayed = 0
var showMyDaughter = 0
var currState = getCurrState(tree_name) 
for (var i = 0; i < db[tree_name].length; i++) 
{newOutline +='<table  cellpadding="0" cellspacing="0" border="0"><tr>';
	var theGIF = getGIF(i, currState,tree_name)		
	var theGIFStatus = getGIFStatus(i, currState,tree_name)  	
	var currIndent = db[tree_name][i].indent	
	var expanded = currState.charAt(i) 
	if (currIndent == 0 || currIndent <= prevIndentDisplayed || (showMyDaughter == 1 && (currIndent - prevIndentDisplayed == 1)))
	 {
		newOutline += "<td><IMG SRC=\""+fillerimg[tree_name]+"\" HEIGHT = 1 WIDTH =" + (indentPixels[tree_name] * currIndent) + "></td>"
		newOutline += "<td><A HREF=\"javascript:out_intab('"+tree_name+"')\" " + 	"onMouseOver=\"window.status=\'" + theGIFStatus +	"\';return true;\"  onClick=\"toggle(" + i + ",'"+tree_name+"');return " + 	(theGIF != endpointWidget[tree_name]) + "\">"
		newOutline += "<IMG SRC=\"" + theGIF + "\" HEIGHT=" + widgetHeight[tree_name] + " WIDTH=" + widgetWidth[tree_name] + " BORDER=0></A></td>"		
		if (db[tree_name][i].URL == "" || db[tree_name][i].URL == null) 
			{
				newOutline += "<td>" + db[tree_name][i].display + "</td></tr>"	// no link	
			} 
			else
			 {
				newOutline += "<td><A HREF="+db[tree_name][i].URL+" title=\""+db[tree_name][i].title +"\" onMouseOver=\"window.status=\'" +	db[tree_name][i].statusMsg + "\';return true;\" target=\""+target[tree_name]+"\">" + db[tree_name][i].display + "</A></td></tr>"
			}
		prevIndentDisplayed = currIndent
		showMyDaughter = expanded
		if (db[tree_name].length > 10000) {document.getElementById(tree_name+'_out').innerHTML=newOutline;newOutline = ""}
	}
newOutline +='</table>';
}

document.getElementById(tree_name+'_out').innerHTML=newOutline
}


	function makeArray ( array ) {
		var ret = Array();
		for (k in array ) ret[k]=array[k]

		return ret;
	}

/*для конструкторов интерфесов*/
function ch_item(value)
{
	if (!value) return
//value - тип (код) поля ввода
//************************* начало атрибутов

out='';
if (struct2['pole_prop']) _pole_prop=struct2['pole_prop'].split(',');//поэлементное разложение (внутри поля)
	else _pole_prop=Array();
for (i=0;i<pole_consts_styles['itemcount'][parseInt(value)];i++)
		{
		if (_pole_prop[i]) o=_pole_prop[i]; else o='';
		out=out+"Element"+i+'<textarea name="pole_prop['+i+']"  id="pole_prop['+i+']" cols="135" rows="1" wrap="VIRTUAL" >'+o+'</textarea><br>';
		}
document.getElementById("prop_out").innerHTML=out
//************************* конец атрибутов
//************************* начало вывода спец данных для выпадающих списков
 if (pole_consts_styles['itemtype'][parseInt(value)]>0)  document.getElementById("list_out").style.display=''
 	else document.getElementById("list_out").style.display='none'
//************************* конец вывода спец данных для выпадающих списков

//************************* начало вывода констант

out='';
//сами элементы для ыбора

for (i=0;i<pole_consts_styles['constcount'][parseInt(value)];i++)
	{
		out=out+pole_consts_styles['const_count_msg'][parseInt(value)][i]+"<br><b>Application::$config</b><input size='100' type='text' name='pole_global_const["+i+"]' id='pole_global_const["+i+"]' /><br>";
	}
	document.getElementById("const_out").innerHTML=out;
//установим их в текущие состояния
if (struct2['pole_global_const'])
	{
	_pole_global_const=struct2['pole_global_const'].split(',');
	for (i=0;i<pole_consts_styles['constcount'][parseInt(value)];i++)
		if (_pole_global_const[i]>'') document.getElementById("pole_global_const["+i+"]").value=_pole_global_const[i];
	}

out='';
ii=0;
if (struct2['properties']) p=unserialize(struct2['properties']);//получим маасив параметров 
  else p=Array();
if(p==null) p=Array();
//пройдемся по ассоциативному массиву, если он не пуст
//pole_consts_styles['properties_item_type'][value][t] - содержит код типа поля ввода параметров, 0-однострочный,1-список,2-многострочное
for (t in pole_consts_styles['properties_text'][value]) 
	{//в t имена свойств
	if (p[ii]) data=p[ii]; else data='';//текущее значение если есть
	
	//проверим, есть ли элменты в массиве properties_listid[value], если нет, тогда это строка ввода, иначе генерим список
	if (pole_consts_styles['properties_listid'][value] && pole_consts_styles['properties_item_type'][value][t]==1)
		{//alert(pole_consts_styles['properties_item_type'][value][t])
			if (pole_consts_styles['properties_listid'][value][t] && pole_consts_styles['properties_item_type'][value][t]==1) 
			{
			html='<select name="properties['+ii+']" >';
			i=0;
				for (tt in pole_consts_styles['properties_listid'][value][t])
						{if (data==pole_consts_styles['properties_listid'][value][t][tt]) flag= 'selected'; else flag='';
						html=html+'<option value="'+pole_consts_styles['properties_listid'][value][t][i]+'"'+flag+'>'+pole_consts_styles['properties_listtext'][value][t][i]+'</option>';
						i++;
						}
			html=html+'</select>';
			}
			else 
				{
					if (pole_consts_styles['properties_item_type'][value][t]==2) html='<br /><textarea name="properties['+ii+']"  cols="100" rows="2">'+data+'</textarea>';
						else html='<br /><input name="properties['+ii+']"  size="100" value="'+data+'">';
				}
		}
		else {
					if (pole_consts_styles['properties_item_type'][value][t]==2) html='<br /><textarea name="properties['+ii+']"  cols="100" rows="2">'+data+'</textarea>';
						else html='<br /><input name="properties['+ii+']"  size="100" value="'+data+'">';

			}
		out=out+pole_consts_styles['properties_text'][value][t]+html+'<br>';
		ii++;
	}
document.getElementById("properties_out").innerHTML=out;//alert(out)
//************************* конец вывода свойств поля


if (struct2['validator']) selected_validator=unserialize(struct2['validator']);//поэлементное разложение (внутри поля)


}


