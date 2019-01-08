$(document).ready(function() {
	'use strict';
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
	timeFormat: 'HH:mm',
	amNames: ['AM', 'A'],
	pmNames: ['PM', 'P'],
	isRTL: false
};
$.timepicker.setDefaults($.timepicker.regional['ru']);

    
    
});



function f56(url,w,h)
{
$( "#f56_dialog" ).dialog({
      resizable: true,
      height: h+65,
      width: w+30,
      modal: true,
      open: function(ev, ui){
             $('#iframe56').attr({'src':url,'width':w,'height':h});
          }

});
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
                hiden.val($("#u").val()+","+$("#g").val()+","+parseInt(r+parseInt(parseInt($("select[name=p1]").val()+$("select[name=p2]").val()+$("select[name=p3]").val(),8).toString(10))));
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
          ;
          $("#mode_f57").text(_f57_p(v[2])+ " ("+pad(parseInt(v[2],10).toString(8),4)+")");  
          _f57_select(v[2]);
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

function _f57_select(pp)
{
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

