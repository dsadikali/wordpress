/*
 * Transposh v0.8.1
 * http://transposh.org/
 *
 * Copyright 2011, Team Transposh
 * Licensed under the GPL Version 2 or higher.
 * http://transposh.org/license
 *
 * Date: Mon, 12 Dec 2011 12:04:59 +0200
 */
jQuery(function(){jQuery("#tr_anon").click(function(){jQuery("#tr_anon").attr("checked")&&(jQuery(".translateable").addClass("active").removeClass("translateable"),jQuery("#sortable .active").each(function(){jQuery("input",this).val(jQuery(this).attr("id")+",v,t")}));jQuery("#yellowcolor").toggleClass("hidden")});jQuery("#sortable").sortable({placeholder:"highlight",update:function(b,a){a.item.unbind("click");a.item.one("click",function(a){a.stopImmediatePropagation();jQuery(this).click(clickfunction)})}});
jQuery("#sortable").disableSelection();jQuery("#changename").click(function(){jQuery(".langname").toggleClass("hidden");return false});jQuery("#selectall").click(function(){jQuery("#sortable .languages").addClass("active").removeClass("translateable");jQuery("#sortable .active").each(function(){jQuery("input",this).val(jQuery(this).attr("id")+",v,t")});return false});clickfunction=function(){jQuery(this).attr("id")!=jQuery("#default_list li").attr("id")&&(jQuery("#tr_anon").attr("checked")?jQuery(this).toggleClass("active"):
jQuery(this).hasClass("active")?(jQuery(this).removeClass("active"),jQuery(this).addClass("translateable")):jQuery(this).hasClass("translateable")?jQuery(this).removeClass("translateable"):jQuery(this).addClass("active"),jQuery("input",this).val(jQuery(this).attr("id")+(jQuery(this).hasClass("active")?",v":",")+(jQuery(this).hasClass("translateable")?",t":",")))};jQuery(".languages").dblclick(clickfunction).click(clickfunction);jQuery("#default_lang").droppable({accept:".languages",activeClass:"highlight_default",
drop:function(b,a){jQuery("#default_list").empty();jQuery(a.draggable.clone().removeAttr("style").removeClass("active").removeClass("translateable")).appendTo("#default_list").show("slow");jQuery("#default_list .logoicon").remove();jQuery("#sortable").find("#"+a.draggable.attr("id")).addClass("active")}});jQuery("#sortiso").click(function(){jQuery("#sortable li").sort(function(b,a){return jQuery(b).attr("id")==jQuery("#default_list li").attr("id")?-1:jQuery(a).attr("id")==jQuery("#default_list li").attr("id")?
1:jQuery(b).attr("id")>jQuery(a).attr("id")?1:-1}).remove().appendTo("#sortable").dblclick(clickfunction).click(clickfunction);return false});jQuery("#sortname").click(function(){jQuery("#sortable li").sort(function(b,a){langa=jQuery(".langname",b).filter(function(){return!jQuery(this).hasClass("hidden")}).text();langb=jQuery(".langname",a).filter(function(){return!jQuery(this).hasClass("hidden")}).text();langdef=jQuery(".langname","#default_list li").filter(function(){return!jQuery(this).hasClass("hidden")}).text();
return langa==langdef?-1:langb==langdef?1:langa>langb?1:-1}).remove().appendTo("#sortable").dblclick(clickfunction).click(clickfunction);return false});jQuery.ajaxSetup({cache:false});backupclick=function(){jQuery("#transposh-backup").unbind("click").click(function(){return false}).text("Backup In Progress");jQuery.post(ajaxurl,{action:"tp_backup"},function(b){var a="red";b[0]=="2"&&(a="green");jQuery("#backup_result").html(b).css("color",a);jQuery("#transposh-backup").unbind("click").click(backupclick).text("Do Backup Now")});
return false};jQuery("#transposh-backup").click(backupclick);cleanautoclick=function(b,a){if(!confirm("Are you sure you want to do this?"))return false;if(b==0&&!confirm("Are you REALLY sure you want to do this?"))return false;var c=a.text();a.unbind("click").click(function(){return false}).text("Cleanup in progress");jQuery.post(ajaxurl,{action:"tp_cleanup",days:b},function(){a.unbind("click").click(function(){cleanautoclick(b,a);return false}).text(c)});return false};jQuery("#transposh-clean-auto").click(function(){cleanautoclick(0,
jQuery(this));return false});jQuery("#transposh-clean-auto14").click(function(){cleanautoclick(14,jQuery(this));return false});maintclick=function(b){if(!confirm("Are you sure you want to do this?"))return false;var a=b.text();b.unbind("click").click(function(){return false}).text("Maintenance in progress");jQuery.post(ajaxurl,{action:"tp_maint"},function(){b.unbind("click").click(function(){maintclick(b);return false}).text(a)});return false};jQuery("#transposh-maint").click(function(){maintclick(jQuery(this));
return false});do_translate_all=function(){jQuery("#progress_bar_all").progressbar({value:0});stop_translate_var=false;jQuery("#tr_loading").data("done",true);jQuery.ajaxSetup({cache:false});jQuery.ajax({url:ajaxurl,dataType:"json",data:{action:"tp_translate_all"},cache:false,success:function(b){dotimer=function(a){jQuery("#tr_allmsg").text("");clearTimeout(timer2);jQuery("#tr_loading").data("done")||jQuery("#tr_loading").data("attempt")>4?(jQuery("#progress_bar_all").progressbar("value",(a+1)/b.length*
100),jQuery("#tr_loading").data("attempt",0),translate_post(b[a]),typeof b[a+1]!=="undefined"&&!stop_translate_var&&(timer2=setTimeout(function(){dotimer(a+1)},5E3),jQuery("#tr_allmsg").text("Waiting 5 seconds..."))):(jQuery("#tr_loading").data("attempt",jQuery("#tr_loading").data("attempt")+1),timer2=setTimeout(function(){dotimer(a)},15E3),jQuery("#tr_allmsg").text("Translation incomplete - Waiting 15 seconds - attempt "+jQuery("#tr_loading").data("attempt")+"/5"))};timer2=setTimeout(function(){dotimer(0)},
0)}});jQuery("#transposh-translate").text("Stop translate");jQuery("#transposh-translate").unbind("click").click(stop_translate);return false};stop_translate=function(){clearTimeout(timer2);stop_translate_var=true;jQuery("#transposh-translate").text("Translate All Now");jQuery("#transposh-translate").unbind("click").click(do_translate_all);return false};jQuery("#transposh-translate").click(do_translate_all);jQuery(".warning-close").click(function(){jQuery(this).parent().hide();jQuery.post(ajaxurl,
{action:"tp_close_warning",id:jQuery(this).parent().attr("id")})})});
