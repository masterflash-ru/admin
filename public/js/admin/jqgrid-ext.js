"use strict";
$.jgrid.ext = 
{/*прокси*/
	ajaxFormProxy: function(opts, act)
	{
		opts.url = $(this).getGridParam('editurl');
		opts.iframe = false;
        opts.error=function(xhr, status, error){$.jgrid.info_dialog("Ошибка", xhr.responseText,"Закрыть",{width:"auto",modal:true,align:"left"});}
        var $form = $('#FrmGrid_' + $(this).getGridParam('id'));
        var ele = $form.find('INPUT,TEXTAREA,SELECT').not(':file');
         ele.each(function () {$(this).data('name', $(this).attr('name'));$(this).removeAttr('name');});
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
            $.jgrid.info_dialog("Ошибка", 'HTTP status code: ' + xhr.status + '\n' +
              'textStatus: ' + status + '\n' +
              'errorThrown: ' + error+'\n\n\n HTTP body (jqXHR.responseText): ' + '\n' + xhr.responseText,"Закрыть",{width:"auto",modal:true,align:"left"})
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
        var opColModel = $.extend({},opts.colModel),op={},users,groups;
        if(!$.fmatter.isEmpty(cellval)) {
            if(!op.reformatAfterEdit && act === 'edit'){
               // var seo=JSON.parse(cellval);
            } 
            var seo=unserialize(cellval);
            var out=((seo.robots=="noindex")?"Запрет индексации,<br>\n":"")+
                  ((seo.canonical)?"Кан. стр.:<b>"+seo.canonical+"</b>,<br>\n":"");
            var d=$("<div>").attr("data-seo",cellval).html(out).wrap("<div></div>");
           return d.parent().html();
        } 
        return "";
    },
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
    $.map(data,function (i,j){
       ov+='<option value="'+j+'">'+i+'</option>';
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

