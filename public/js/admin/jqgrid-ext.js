"use strict";
/*
* Расширение для сетки jqGrid
*/
var jq_overlay=$('<div class="ui-widget-overlay jqoverlay"><div class="row align-items-center w-100 h-100"><div class="col-12 align-self-center text-center"></div></div></div>');
$.jgrid.ext = 
{/*прокси*/
	ajaxFormProxy: function(opts, act)
	{
        $("body").append(jq_overlay);
		opts.url = $(this).getGridParam('editurl');
		opts.iframe = false;
        opts.success=function(){$(".jqoverlay").remove();}
        opts.error=function(xhr, status, error){$(".jqoverlay").remove();$.jgrid.info_dialog("Ошибка", xhr.responseText,"Закрыть",{width:"auto",modal:true,align:"left"});}
        var $form = $('#FrmGrid_' + $(this).getGridParam('id'));
        var ele = $form.find('INPUT,TEXTAREA,SELECT').not(':file');
         ele.each(function () {$(this).data('name', $(this).attr('name'));$(this).removeAttr('name');});
        //добавим toolbar элементы, если есть
        var toolbardata=$("#toolbar_zform").serializeArray();
        toolbardata.map(function (tv){
            opts.data[tv.name]=tv.value;
        });
        $form.ajaxSubmit(opts);
		setTimeout(function()
		{
			ele.each(function()
			{
				$(this).attr('name', $(this).data('name'));
			});
             $("#"+$(this).getGridParam('id')).trigger('reloadGrid');
		}, 200);
	}
};


$.extend($.jgrid.defaults, { 
    /*вывод сообщений ошибок*/
    ajaxGridOptions:{
        error:function(xhr,status,error){
            if (xhr.status==406){
                $.jgrid.info_dialog("Ошибка", xhr.responseText,"Закрыть",{width:"auto",modal:true,align:"left",top:30})
            } else {
                $.jgrid.info_dialog("Ошибка", 'HTTP status code: ' + xhr.status + '\n' +
                                    'textStatus: ' + status + '\n' +
                                    'errorThrown: ' + error+'\n\n\n HTTP body (jqXHR.responseText): ' + '\n' + xhr.responseText,"Закрыть",{width:"auto",modal:true,align:"left"})
            }
    },

        }

});
    
/*расширения форматера*/
$.extend($.fn.fmatter , {
    datetime : function(cellval, opts, rwd, act) {
        var op = $.extend({},opts.date);
        if(opts.colModel !== undefined && opts.colModel.formatoptions !== undefined) {
            op = $.extend({},op,opts.colModel.formatoptions);
        }
        if(!op.reformatAfterEdit && act === 'edit'){
            return $.fn.fmatter.defaultFormat(cellval, opts);
        }
        if(op.reformatAfterEdit && act === 'edit'){
            return $cellval;
        }
        if(!$.fmatter.isEmpty(cellval)) {
            return $.jgrid.parseDate.call(this, op.srcfullformat,cellval,op.newfullformat,op);
        }
        return $.fn.fmatter.defaultFormat(cellval, opts);
    },
    image : function(cellval, opts, rwd, act) {
        var opColModel = $.extend({classes:""},opts.colModel),op={},img_class="";
        if(opts.colModel !== undefined && opts.colModel.formatoptions !== undefined) {
            op = $.extend({},op,opts.colModel.formatoptions);
        }
        if (opColModel.classes){
            img_class=' class="'+opColModel.classes+'"';
        }
        if(!op.reformatAfterEdit && act === 'edit'){
            if (!$.fmatter.isEmpty(cellval)){
                return "<img "+img_class+" src='" + cellval + "' />";
            } else {
                return "";
            }
        }
        if(!$.fmatter.isEmpty(cellval)) {
           return "<img "+img_class+" src='/" + cellval + "' />";
        } else {
            return "";
        }
    },
    permissions : function(cellval, opts, rwd, act) {
        var opColModel = $.extend({},opts.colModel),op={},users,groups;
        if(opts.colModel !== undefined && opts.colModel.editoptions !== undefined) {
            op = $.extend({},op,opts.colModel.editoptions);
            users=unserialize(op.users);
            groups=unserialize(op.groups);
        }
        if(!op.reformatAfterEdit && act === 'edit'){
            if (!$.fmatter.isEmpty(cellval)){//применить форматирование
            cellval=cellval.split(",");
           return "<div data-permissions=\""+cellval.join(",")+"\">"+users[cellval[0]]+":"+groups[cellval[1]]+" "+permissionToText(cellval[2])+" ("+parseInt(cellval[2],10).toString(8)+")"+"</div>" ;
            } 
        }
        if(!$.fmatter.isEmpty(cellval)) {
            cellval=cellval.split(",");
           return "<div data-permissions=\""+cellval.join(",")+"\">"+users[cellval[0]]+":"+groups[cellval[1]]+" "+permissionToText(cellval[2])+" ("+parseInt(cellval[2],10).toString(8)+")"+"</div>" ;
        } 
            return "";
    },
    seo : function(cellval, opts, rwd, act) {
        if(!$.fmatter.isEmpty(cellval)) {
            var seo=unserialize(cellval);
            var out=((seo.robots=="noindex")?"Запрет индексации,<br>\n":"")+
                  ((seo.canonical)?"Кан. стр.:<b>"+seo.canonical+"</b>,<br>\n":"");
            var d=$("<div>").attr("data-seo",cellval).html(out).wrap("<div></div>");
           return d.parent().html();
        } 
        return "";
    },
    interfaces : function(cellval, opts, rwd, act) {
        /*rwd - объект данных данной строки*/
        var opColModel = $.extend({},opts.colModel),op={};
        if(opts.colModel !== undefined && opts.colModel.formatoptions !== undefined) {
            op = $.extend({},op,opts.colModel.formatoptions);
        }

        if(!$.fmatter.isEmpty(cellval)) {
            var btn,iwrap=$("<div>");
            $.map(op.items,function(val){
                btn=$("<button>");
                btn.button(val);
                val.cellval=cellval;
                //дополнительные параметры в GET запрос, если есть
                val.get_parameters="";
                if (val.get_parameters_array){
                    var add_get=val.get_parameters_array;
                    $.map(add_get,function(g_item){
                        val.get_parameters+="&"+g_item+"="+rwd[g_item];
                    });
                }
                btn.attr({onclick:"interfacesClick(this)","data-val":JSON.stringify(val)});
                iwrap.append(btn);
            });
            return iwrap.html();
        } 
        return "";
    },
    jscellactions: function(cellval,opts) {
		var op={keys:false, editbutton:true, delbutton:true,onEdit:'console.log', onDel:'console.log'},
			rowid=opts.rowId, str="",ocl,
			nav = $.jgrid.getRegional(this, 'nav'),
			classes = $.jgrid.styleUI[(opts.styleUI || 'jQueryUI')].fmatter,
			common = $.jgrid.styleUI[(opts.styleUI || 'jQueryUI')].common;
		if(opts.colModel.formatoptions !== undefined) {
			op = $.extend(op,opts.colModel.formatoptions);
		}
		if(rowid === undefined || $.fmatter.isEmpty(rowid)) {return "";}
		var hover = "onmouseover=jQuery(this).addClass('" + common.hover +"'); onmouseout=jQuery(this).removeClass('" + common.hover +"');  ";
        if(op.editbutton){
			ocl = "id='jEditButton_"+rowid+"' onclick="+op.onEdit+"(this,'edit'); " + hover;
			str += "<div title='"+nav.edittitle+"' style='float:left;cursor:pointer;' class='ui-pg-div ui-inline-edit' "+ocl+"><span class='" + common.icon_base +" "+classes.icon_edit +"'></span></div>";
		}
		if(op.delbutton) {
			ocl = "id='jDeleteButton_"+rowid+"' onclick="+op.onDel+"(this,'del'); " + hover;
			str += "<div title='"+nav.deltitle+"' style='float:left;' class='ui-pg-div ui-inline-del' "+ocl+"><span class='" + common.icon_base +" "+classes.icon_del +"'></span></div>";
		}
		return "<div style='margin-left:8px;'>" + str + "</div>";
	},
    multicheckbox:function(cellval, opts) {
		cellval = String(cellval);
		var oSelect = false, ret=[], sep;
		if(opts.colModel.formatoptions !== undefined){
			oSelect= opts.colModel.formatoptions.value;/*весь список*/
			sep = opts.colModel.formatoptions.separator === undefined ? ":" : opts.colModel.formatoptions.separator;
		} else if(opts.colModel.editoptions !== undefined){
			oSelect= opts.colModel.editoptions.value;
			sep = opts.colModel.editoptions.separator === undefined ? ":" : opts.colModel.editoptions.separator;
		}
    var ctl = '<div class="checklist py-1">';
    var aValues = [];
    if (cellval && cellval.length) {
        aValues = cellval.split(",");
    }
    for(var el in oSelect) {
        ctl += '<input type="checkbox" disabled ';
        if (aValues.indexOf(el) != -1) {
            ctl += 'checked="checked" ';
        }
        ctl += 'value="' + el + '"> ' + oSelect[el] + '<br/>';
    }
    return ctl + '</div>';
}
});
$.extend($.fn.fmatter.datetime , {
    unformat : function (cellval, opts) {
        var op = $.jgrid.getRegional(this, 'formatter.datetime') || {};
        if(opts.formatoptions !== undefined) {
            op = $.extend({},op,opts.formatoptions);
        }
        if(!$.fmatter.isEmpty(cellval)) {
            return $.jgrid.parseDate.call(this, op.newfullformat,cellval,op.srcfullformat,op);
        }
        return $.fn.fmatter.defaultFormat(cellval, opts);
    }
});
$.extend($.fn.fmatter.image , {
    unformat : function (cellval, opts,cell) {
        return  $('img', cell).attr('src');
    }
});
$.extend($.fn.fmatter.permissions , {
    unformat : function (cellval, opts,cell) {
        if(!$.fmatter.isEmpty(cellval)) {
           return $('div', cell).data("permissions");
        }
        return  cellval;
    }
});
$.extend($.fn.fmatter.seo , {
    unformat : function (cellval, opts,cell) {
        if(!$.fmatter.isEmpty(cellval)) {
           return $('div', cell).data("seo");
        }
        return  cellval;
    }
});
$.extend($.fn.fmatter.multicheckbox , {
    unformat : function (cellval, opts,cell) {
    var rez = [];
    $("input[type=checkbox]:checked", cell).each(function (i, e) {
        rez[rez.length]= e.value;
    });
    return rez.join(",");
    }
});

$.extend($.fn.fmatter.jscellactions , {
    unformat : function (cellval, opts,cell) {
        return  "";
    }
});


/*редаутирование для multicheckbox*/
function multicheckboxEdit(cellval, opts)
{
    var ctl = '<div class="checklist">',oSelect=opts.value;
    var aValues = [];
    if (cellval && cellval.length) {
        aValues = cellval.split(",");
    }
    for(var el in oSelect) {
        ctl += '<label><input type="checkbox" ';
        if (aValues.indexOf(el) != -1) {
            ctl += 'checked="checked" ';
        }
        ctl += 'value="' + el + '"> ' + oSelect[el] + '</label><br/>';
    }
    return $(ctl + '</div>');

}
function multicheckboxSave(elem, operation, value)
{
 if(operation === 'get') {//запись на сервер
     var items=[];
    $("input[type=checkbox]:checked", elem).each(function (i, e) {
        items[items.length]= e.value;
    });
     return items.join(",");
    } 
}


/*расширение для редактирования image*/
function imageEdit(value, options)
{
return $("<div data-value=\""+value+"\"><img src=\""+value+"\" style='max-width:250px'/><br><input type='file' name=\"file_"+options.name+"\" id=\"file_"+options.name+"\"></div>");
}
function imageSave(elem, operation, value)
{
 if(operation === 'get') {//запись на сервер
     return $(elem).data("value");
    } else if(operation === 'set') {
       return $("img",elem).attr("src",value);
    }
}
/*расширение для редактирования file*/
function fileEdit(value, options)
{
return $("<div data-value=\""+value+"\"><input type='file' name=\"file_"+options.name+"\" id=\"file_"+options.name+"\"></div>");
}
function fileSave(elem, operation, value)
{
 if(operation === 'get') {//запись на сервер
     return $(elem).data("value");
    } else if(operation === 'set') {
       return $(elem).data("value");
    }
}

/*расширение для редактирования SEO*/
function permissionsEdit(value, options)
{
var ptable=$("<table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" class=\"permission_editor\">\
  <tbody>\
    <tr>\
      <td>Код (восмеричный):</td>\
      <td id=\"mode\" style=\"font-weight: bold\"></td>\
    </tr>\
    <tr style=\"background-color:#eee\">\
      <td>Владелец:</td>\
      <td><select id=\"pu\"><select></td>\
    </tr>\
    <tr style=\"background-color:#eee\">\
      <td>Группа:</td>\
      <td><select id=\"pg\"><select></td>\
    </tr>\
    <tr style=\"background-color:#ddd\">\
      <td>SUID:</td>\
      <td><input type=\"checkbox\" id=\"suid\" class=\"perm_bits\" value=\"2048\"></td>\
    </tr>\
    <tr style=\"background-color:#ddd\">\
      <td>SGID:</td>\
      <td><input type=\"checkbox\" id=\"sgid\" class=\"perm_bits\" value=\"1024\"></td>\
    </tr>\
    <tr style=\"background-color:#ddd\">\
      <td>Sticky:</td>\
      <td><input type=\"checkbox\" id=\"sticky\" class=\"perm_bits\" value=\"512\"></td>\
    </tr>\
<tr>  <td>Доступ владельца:</td>\
      <td><select id=\"per1\"><select></td>\
    </tr>\
    <tr>\
      <td>Доступ группы:</td>\
      <td><select id=\"per2\"><select></td>\
    </tr>\
    <tr>\
      <td>Доступ остальных:</td>\
      <td><select id=\"per3\"><select></td>\
    </tr>\
  </tbody>\
</table>");
    var users=unserialize(options.users),groups=unserialize(options.groups),p=value.split(","),
        p_list='<option value="0">Нет доступа</option><option value="4">Чтение</option><option value="5">Чтение и запуск</option><option value="6">Чтение и запись</option><option value="1">Запуск</option><option value="2">Запись</option><option value="3">Запись и запуск</option><option value="7">Полный</option>';;
    for(var u in users){
        $('#pu',ptable).append($('<option>', {value: u, text : users[u]  }));
    }
    for(var g in groups){
        $('#pg',ptable).append($('<option>', {value: g, text : groups[g]  }));
    }
    $('#per1, #per2, #per3',ptable).append(p_list);
    permissionsSave(ptable, 'set', value);
   return ptable; 
}
function permissionsSave(elem, operation, value)
{
 if(operation === 'get') {
     var r=0;
     $(".perm_bits:checked",elem).each(function(index,element){
         r+=parseInt($(this).val());
     });
     r=parseInt($("#pu").val()||0)+","+parseInt($("#pg").val()||0)+","+parseInt(r+parseInt(parseInt($("#per1").val()+$("#per2").val()+$("#per3").val(),8).toString(10)))
     return r;
    }
if(operation === 'set'){
    var p=value.split(","),ptable=elem;
    if (!p[2]){p[2]=0;}
        $("#mode",ptable).text(permissionToText(p[2])+ " ("+pad(parseInt(p[2],10).toString(8),4)+")");  
        $('#pu option:selected',ptable).prop('selected', false);
        $('#pg option:selected',ptable).prop('selected', false);
        $('#pu option[value="'+[p[0]]+'"]',ptable).prop('selected', true);
        $('#pg option[value="'+[p[1]]+'"]',ptable).prop('selected', true);
        var pp=p[2],p3=pp & 7;
        $('#per3 option[value="'+p3+'"]',ptable).prop('selected', true);
        pp=pp>>>3;
        var p2=pp & 7;
        $('#per2 option[value="'+p2+'"]',ptable).prop('selected', true);
        pp=pp>>>3;
        var p1=pp & 7;
        $('#per1 option[value="'+p1+'"]',ptable).prop('selected', true);
        pp=pp>>>3;
        $('#sticky',ptable).prop('checked', pp & 1);
        $('#sgid',ptable).prop('checked', pp & 2);
        $('#suid',ptable).prop('checked', pp & 4);
    }
}

/*расширение для редактирования SEO*/
function seoEdit(value, options)
{
    var v=unserialize(stripslashes(value)),
    rez= $("<div>").attr("data-value",value),
    c=$("<input>").attr({type:"checkbox",id:"robots"}).prop('checked',(v.robots=="noindex")?true:false).val(1),
    i=$("<input>").attr({type:"text",id:"canonical",style:"padding:0;"}).val(v.canonical);
    c=$("<label>Запретить индексацию: </label>").append(c);
    i=$("<label>Канонич. адрес: </label>").append(i);
    return rez.append(c).append("<br>").append(i);
}
function seoSave(elem, operation, value)
{
 if(operation === 'get') {//запись на сервер
     var r=serialize({robots: ($("#robots",elem).prop('checked')?"noindex":""),canonical:$("#canonical",elem).val()});
     return r.replace('O:6:"Object"',"a");
    }
if(operation === 'set'){
    var seo=unserialize(value);
    $("#robots",elem).prop('checked',(seo.robots=="noindex")?true:false);
    $("#canonical",elem).val(seo.canonical);
} 
}

/*обработка кликов на кнопки открытия нового интерфейса*/
function interfacesClick(buttonItem)
{
    var opt=$(buttonItem).data("val"), interfacesDialog=$('<div id="interfacesDialog"></div>'),optdialog=opt.dialog;
    if(!opt.get_parameter_name){
        opt.get_parameter_name="id";
    }
    if(!opt.get_parameters){
        opt.get_parameters="";
    } 
    optdialog.autoOpen=false;
    optdialog.iconButtons=[
            {
                icon: "ui-icon-arrow-4-diag",
                click: function( e ) {
                    e.preventDefault;
                    var dd=$( "#interfacesDialog" );
                    if (dd.data("fill")>0){
                            dd.dialog( "option", {
                                height:parseInt(dd.data("height")),
                                width:parseInt(dd.data("width")),
                                position:dd.data("position")
                            });
                            dd.data("fill",0);
                        } else{
                            dd.data("fill",1);
                            dd.data("width",dd.width()+25);
                            dd.data("height",dd.height()+25);
                            dd.data("position",dd.dialog( "option" ,"position"));  
                            dd.dialog( "option", {
                                height:$(window).height(),
                                width:$(window).width(),
                                position:{ my: "center", at: "center", of: window }
                            });
                        }
                }
            }
    ];
    if (!$("body").has("#interfacesDialog").length){
        $("body").append(interfacesDialog);
        $("#interfacesDialog").dialog({autoOpen:false});
    }
    $("#interfacesDialog").dialog("option",optdialog);
    
    $("#interfacesDialog").dialog("open");
    $("#interfacesDialog").load(opt.interface+"?"+opt.get_parameter_name+"="+opt.cellval+opt.get_parameters);
    return false;
}

function permissionToText($perms)
{
    var info;
    info = (($perms & 0x0100) ? 'r' : '-');
info += (($perms & 0x0080) ? 'w' : '-');
info += (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

// Группа
info += (($perms & 0x0020) ? 'r' : '-');
info += (($perms & 0x0010) ? 'w' : '-');
info += (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

// Мир
info += (($perms & 0x0004) ? 'r' : '-');
info += (($perms & 0x0002) ? 'w' : '-');
info += (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
return info;
}

/*формирование выпадающего списка из ответа сервера*/
function buildSelect(data)
{
    var ov="<select>";
    data=JSON.parse(data);
    $.map(data,function (i,j){//цикл по optgroup
        if (typeof i === 'object'){//console.log(i)
            ov+='<optgroup label="'+j+'">';
            $.map(i,function (ii,jj){
                ov+='<option value="'+jj+'">'+ii+'</option>';
            });
            ov+='</optgroup>';
        } else {
            ov+='<option value="'+j+'">'+i+'</option>';
        }
    });
    return ov+"</select>";
}


$.jgrid.extend({
    	saveRow : function(rowid, successfunc, url, extraparam, aftersavefunc,errorfunc, afterrestorefunc) {
		// Compatible mode old versions
		var args = $.makeArray(arguments).slice(1), o = {}, $t = this[0];

		if( $.type(args[0]) === "object" ) {
			o = args[0];
		} else {
			if ($.isFunction(successfunc)) { o.successfunc = successfunc; }
			if (url !== undefined) { o.url = url; }
			if (extraparam !== undefined) { o.extraparam = extraparam; }
			if ($.isFunction(aftersavefunc)) { o.aftersavefunc = aftersavefunc; }
			if ($.isFunction(errorfunc)) { o.errorfunc = errorfunc; }
			if ($.isFunction(afterrestorefunc)) { o.afterrestorefunc = afterrestorefunc; }
		}
		o = $.extend(true, {
			successfunc: null,
			url: null,
			extraparam: {},
			aftersavefunc: null,
			errorfunc: null,
			afterrestorefunc: null,
			restoreAfterError: true,
			mtype: "POST",
			saveui : "enable",
			savetext : $.jgrid.getRegional($t,'defaults.savetext')
		}, $.jgrid.inlineEdit, o );
		// End compatible

		var success = false, nm, tmp={}, tmp2={}, tmp3= {}, editable, fr, cv, ind, nullIfEmpty=false,
		error = $.trim( $($t).jqGrid('getStyleUI', $t.p.styleUI+'.common', 'error', true) );
		if (!$t.grid ) { return success; }
		ind = $($t).jqGrid("getInd",rowid,true);
		if(ind === false) {return success;}
		var errors = $.jgrid.getRegional($t, 'errors'),
		edit =$.jgrid.getRegional($t, 'edit'),
		bfsr = $.isFunction( o.beforeSaveRow ) ? o.beforeSaveRow.call($t,o, rowid) :  undefined;
		if( bfsr === undefined ) {
			bfsr = true;
		}
		if(!bfsr) { return; }
		editable = $(ind).attr("editable");
		o.url = o.url || $t.p.editurl;
		if (editable==="1") {
			var cm, index, elem;
			$('td[role="gridcell"]',ind).each(function(i) {
				cm = $t.p.colModel[i];
				nm = cm.name;
				elem = "";
				if ( nm !== 'cb' && nm !== 'subgrid' && cm.editable===true && nm !== 'rn' && !$(this).hasClass('not-editable-cell')) {
					switch (cm.edittype) {
						case "checkbox":
							var cbv = ["Yes","No"];
							if(cm.editoptions && cm.editoptions.value) {
								cbv = cm.editoptions.value.split(":");
							}
							tmp[nm]=  $("input",this).is(":checked") ? cbv[0] : cbv[1];
							elem = $("input",this);
							break;
						case 'text':
						case 'password':
						case 'textarea':
						case "button" :
							tmp[nm]=$("input, textarea",this).val();
							elem = $("input, textarea",this);
							break;
						case 'select':
							if(!cm.editoptions.multiple) {
								tmp[nm] = $("select option:selected",this).val();
								tmp2[nm] = $("select option:selected", this).text();
							} else {
								var sel = $("select",this), selectedText = [];
								tmp[nm] = $(sel).val();
								if(tmp[nm]) { tmp[nm]= tmp[nm].join(","); } else { tmp[nm] =""; }
								$("select option:selected",this).each(
									function(i,selected){
										selectedText[i] = $(selected).text();
									}
								);
								tmp2[nm] = selectedText.join(",");
							}
							if(cm.formatter && cm.formatter === 'select') { tmp2={}; }
							elem = $("select",this);
							break;
						case 'custom' :
							try {
								if(cm.editoptions && $.isFunction(cm.editoptions.custom_value)) {
									tmp[nm] = cm.editoptions.custom_value.call($t, $(".customelement",this),'get');
									if (tmp[nm] === undefined) { throw "e2"; }
								} else { throw "e1"; }
							} catch (e) {
								if (e==="e1") { $.jgrid.info_dialog(errors.errcap,"function 'custom_value' "+edit.msg.nodefined,edit.bClose, {styleUI : $t.p.styleUI }); }
								else { $.jgrid.info_dialog(errors.errcap,e.message,edit.bClose, {styleUI : $t.p.styleUI }); }
							}
							break;
					}
					cv = $.jgrid.checkValues.call($t,tmp[nm],i);
					if(cv[0] === false) {
						index = i;
						return false;
					}
					//if($t.p.autoencode) { tmp[nm] = $.jgrid.htmlEncode(tmp[nm]); }
					if(o.url !== 'clientArray' && cm.editoptions && cm.editoptions.NullIfEmpty === true) {
						if(tmp[nm] === "") {
							tmp3[nm] = 'null';
							nullIfEmpty = true;
						}
					}
				}
			});
			if (cv[0] === false){
				try {
					if( $.isFunction($t.p.validationCell) ) {
						$t.p.validationCell.call($t, elem, cv[1], ind.rowIndex, index);
					} else {
						var tr = $($t).jqGrid('getGridRowById', rowid),
							positions = $.jgrid.findPos(tr);
						$.jgrid.info_dialog(errors.errcap,cv[1],edit.bClose,{
							left:positions[0],
							top:positions[1]+$(tr).outerHeight(),
							styleUI : $t.p.styleUI,
							onClose: function(){
								if(index >= 0 ) {
									$("#"+rowid+"_" +$t.p.colModel[index].name).focus();
								}
							}
						});
					}
				} catch (e) {
					alert(cv[1]);
				}
				return success;
			}
			var idname, opers = $t.p.prmNames, oldRowId = rowid;
			if ($t.p.keyName === false) {
				idname = opers.id;
			} else {
				idname = $t.p.keyName;
			}
			if(tmp) {
				tmp[opers.oper] = opers.editoper;
				if (tmp[idname] === undefined || tmp[idname]==="") {
					tmp[idname] = rowid;
				} else if (ind.id !== $t.p.idPrefix + tmp[idname]) {
					// rename rowid
					var oldid = $.jgrid.stripPref($t.p.idPrefix, rowid);
					if ($t.p._index[oldid] !== undefined) {
						$t.p._index[tmp[idname]] = $t.p._index[oldid];
						delete $t.p._index[oldid];
					}
					rowid = $t.p.idPrefix + tmp[idname];
					$(ind).attr("id", rowid);
					if ($t.p.selrow === oldRowId) {
						$t.p.selrow = rowid;
					}
					if ($.isArray($t.p.selarrrow)) {
						var i = $.inArray(oldRowId, $t.p.selarrrow);
						if (i>=0) {
							$t.p.selarrrow[i] = rowid;
						}
					}
					if ($t.p.multiselect) {
						var newCboxId = "jqg_" + $t.p.id + "_" + rowid;
						$("input.cbox",ind)
							.attr("id", newCboxId)
							.attr("name", newCboxId);
					}
					// TODO: to test the case of frozen columns
				}
				if($t.p.inlineData === undefined) { $t.p.inlineData ={}; }
				tmp = $.extend({},tmp,$t.p.inlineData,o.extraparam);
			}
				$($t).jqGrid("progressBar", {method:"show", loadtype : o.saveui, htmlcontent: o.savetext });
				tmp3 = $.extend({},tmp,tmp3);
				tmp3[idname] = $.jgrid.stripPref($t.p.idPrefix, tmp3[idname]);
            tmp3=$.isFunction($t.p.serializeRowData) ? $t.p.serializeRowData.call($t, tmp3) : tmp3;
           
            //смотрим файлы, если есть
            var f=$("<form>").attr({method:"POST",enctype:'multipart/form-data',action:o.url}).append($(":file",this));
				
            
            var a_proxy=$.extend({
					url:o.url,
					data: tmp3,
					type: o.mtype,
					async : false, //?!?
					complete: function(res,stat){
						$($t).jqGrid("progressBar", {method:"hide", loadtype : o.saveui, htmlcontent: o.savetext});
						if (stat === "success"){
							var ret = true, sucret, k;
							sucret = $($t).triggerHandler("jqGridInlineSuccessSaveRow", [res, rowid, o]);
							if (!$.isArray(sucret)) {sucret = [true, tmp3];}
							if (sucret[0] && $.isFunction(o.successfunc)) {sucret = o.successfunc.call($t, res);}
							if($.isArray(sucret)) {
								// expect array - status, data, rowid
								ret = sucret[0];
								tmp = sucret[1] || tmp;
							} else {
								ret = sucret;
							}
							if (ret===true) {
								if($t.p.autoencode) {
									$.each(tmp,function(n,v){
										tmp[n] = $.jgrid.htmlDecode(v);
									});
								}
								if(nullIfEmpty) {
									$.each(tmp,function( n ){
										if(tmp[n] === 'null' ) {
											tmp[n] = '';
										}
									});
								}
								tmp = $.extend({},tmp, tmp2);
								$($t).jqGrid("setRowData",rowid,tmp);
								$(ind).attr("editable","0");
								for(k=0;k<$t.p.savedRow.length;k++) {
									if( String($t.p.savedRow[k].id) === String(rowid)) {fr = k; break;}
								}
								$($t).triggerHandler("jqGridInlineAfterSaveRow", [rowid, res, tmp, o]);
								if( $.isFunction(o.aftersavefunc) ) { o.aftersavefunc.call($t, rowid, res, tmp, o); }
								if(fr >= 0) { $t.p.savedRow.splice(fr,1); }
								success = true;
								$(ind).removeClass("jqgrid-new-row").off("keydown");
							} else {
								$($t).triggerHandler("jqGridInlineErrorSaveRow", [rowid, res, stat, null, o]);
								if($.isFunction(o.errorfunc) ) {
									o.errorfunc.call($t, rowid, res, stat, null);
								}
								if(o.restoreAfterError === true) {
									$($t).jqGrid("restoreRow",rowid, o);
								}
							}
						}
					$($t).trigger('reloadGrid');
                    },
					error:function(res,stat,err){
						$("#lui_"+$.jgrid.jqID($t.p.id)).hide();
						$($t).triggerHandler("jqGridInlineErrorSaveRow", [rowid, res, stat, err, o]);
						if($.isFunction(o.errorfunc) ) {
							o.errorfunc.call($t, rowid, res, stat, err);
						} else {
							var rT = res.responseText || res.statusText;
							try {
								$.jgrid.info_dialog(errors.errcap,'<div class="'+error+'">'+ rT +'</div>', edit.bClose, {buttonalign:'right', styleUI : $t.p.styleUI });
							} catch(e) {
								alert(rT);
							}
						}
						if(o.restoreAfterError === true) {
							$($t).jqGrid("restoreRow",rowid, o);
						}
					}
				}, $.jgrid.ajaxOptions, $t.p.ajaxRowOptions || {});//);
            
            f.ajaxSubmit(a_proxy);
            
            
		}
		return success;
	}
});
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


function stripslashes_old( str ) 
{
 return (str+'').replace(/\0/g, '0').replace(/\\([\\'"])/g, '$1');
}

function unserialize(data)
{
    data=stripslashes_old( data );
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
                       .css( "right", ( parseInt( right ) + 38) + "px" )
                       .click( this.click )
                       .appendTo( $titlebar );
            });
    
        }
 });

