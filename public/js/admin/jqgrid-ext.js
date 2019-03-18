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

/*расширение для редактирования image*/
function imageEdit(value, options)
{
return $("<div data-value=\""+value+"\"><img src=\""+value+"\" style='max-width:250px'/><br><input type='file' name=\"file_"+options.name+"\"></div>");
}
function imageSave(elem, operation, value)
{
 if(operation === 'get') {//запись на сервер
     return $(elem).data("value");
    } else if(operation === 'set') {
       return "";
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
   return ptable; 
}
function permissionsSave(elem, operation, value)
{
 if(operation === 'get') {
     var r=0;
     $(".perm_bits:checked",elem).each(function(index,element){
         r+=parseInt($(this).val());
     });
     return parseInt($("#pu").val()||0)+","+parseInt($("#pg").val()||0)+","+parseInt(r+parseInt(parseInt($("#per1").val()+$("#per2").val()+$("#per3").val(),8).toString(10)))
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





