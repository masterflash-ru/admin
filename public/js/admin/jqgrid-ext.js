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
        if(!$.fmatter.isEmpty(cellval)) {
            return cellval;
            //return $.jgrid.parseDate.call(this, op.srcfullformat,cellval,op.newfullformat,op);
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
        var op = $.jgrid.getRegional(this, 'formatter.image') || {};
        if(opts.formatoptions !== undefined) {
            op = $.extend({},op,opts.formatoptions);
        }
        if(!$.fmatter.isEmpty(cellval)) {
           // return $.jgrid.parseDate.call(this, op.newfullformat,cellval,op.srcfullformat,op);
        }

        return  $('img', cell).attr('src');
    }
});

/*расширение для редактирования*/
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


/*сериализация и отправка при редактировании строки* /
$.fn.serializefiles = function() {
    var obj = $(this);

    var formData = new FormData();
    $.each($(obj).find("input[type='file']"), function(i, tag) {
        $.each($(tag)[0].files, function(i, file) {
            formData.append(tag.name, file);
        });
    });
    var params = $(obj).serializeArray();
    $.each(params, function (i, val) {
        formData.append(val.name, val.value);
    });
    return formData;
};
*/
