(()=>{"use strict";var e={};function t(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function a(e,t){for(var a=0;a<t.length;a++){var r=t[a];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function r(e,t,r){return t&&a(e.prototype,t),r&&a(e,r),e}e.d=(t,a)=>{for(var r in a)e.o(a,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:a[r]})},e.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t);var i=function(){function e(){t(this,e)}return r(e,null,[{key:"wysiwyg",value:function(e,t){window.initializedEditor=window.initializedEditor||0,e.each((function(e,a){var r=$(a);r.attr("id","editor_initialized_"+window.initializedEditor),window.initializedEditor++,setTimeout((function(){t=$.extend(!0,{forcePasteAsPlainText:!0,allowedContent:!0,htmlEncodeOutput:!1,protectedSource:[/<\?[\s\S]*?\?>/g,/<%[\s\S]*?%>/g,/(<asp:[^\>]+>[\s|\S]*?<\/asp:[^\>]+>)|(<asp:[^\>]+\/>)/gi],filebrowserImageBrowseUrl:RV_MEDIA_URL.base+"?media-action=select-files&method=ckeditor&type=image",filebrowserImageUploadUrl:RV_MEDIA_URL.media_upload_from_editor+"?method=ckeditor&type=image&_token="+$('meta[name="csrf-token"]').attr("content"),filebrowserWindowWidth:"768",filebrowserWindowHeight:"500",height:r.data("height")||"400px",toolbar:r.data("toolbar")||"full"},t),"basic"===(t=$.extend(!0,t,r.data())).toolbar&&(t.toolbar=[["mode","Source","Image","TextColor","BGColor","Styles","Format","Font","FontSize","CreateDiv","PageBreak","Bold","Italic","Underline","Strike","Subscript","Superscript","RemoveFormat"]]),CKEDITOR.replace(r.attr("id"),t)}),100)}))}},{key:"wysiwygGetContent",value:function(e){return CKEDITOR.instances[e.attr("id")].getData()}},{key:"arrayGet",value:function(e,t){var a,r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:null;try{a=e[t]}catch(e){return r}return null==a&&(a=r),a}},{key:"jsonEncode",value:function(e){return void 0===e&&(e=null),JSON.stringify(e)}},{key:"jsonDecode",value:function(e,t){if("string"==typeof e){var a;try{a=$.parseJSON(e)}catch(e){a=t}return a}return null}}]),e}(),n=function(){function e(){t(this,e),this.$body=$("body"),this.$_UPDATE_TO=$("#custom_fields_container"),this.$_EXPORT_TO=$("#custom_fields_json"),this.CURRENT_DATA=i.jsonDecode(this.base64Helper().decode(this.$_EXPORT_TO.text()),[]),this.CURRENT_DATA&&(this.handleCustomFields(),this.exportData())}return r(e,[{key:"base64Helper",value:function(){if(!this.base64){var e={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(t){var a,r,i,n,o,l,c,d="",s=0;for(t=e._utf8_encode(t);s<t.length;)n=(a=t.charCodeAt(s++))>>2,o=(3&a)<<4|(r=t.charCodeAt(s++))>>4,l=(15&r)<<2|(i=t.charCodeAt(s++))>>6,c=63&i,isNaN(r)?l=c=64:isNaN(i)&&(c=64),d=d+this._keyStr.charAt(n)+this._keyStr.charAt(o)+this._keyStr.charAt(l)+this._keyStr.charAt(c);return d},decode:function(t){var a,r,i,n,o,l,c="",d=0;for(t=t.replace(/[^A-Za-z0-9+/=]/g,"");d<t.length;)a=this._keyStr.indexOf(t.charAt(d++))<<2|(n=this._keyStr.indexOf(t.charAt(d++)))>>4,r=(15&n)<<4|(o=this._keyStr.indexOf(t.charAt(d++)))>>2,i=(3&o)<<6|(l=this._keyStr.indexOf(t.charAt(d++))),c+=String.fromCharCode(a),64!=o&&(c+=String.fromCharCode(r)),64!=l&&(c+=String.fromCharCode(i));return e._utf8_decode(c)},_utf8_encode:function(e){e=e.replace(/rn/g,"n");for(var t="",a=0;a<e.length;a++){var r=e.charCodeAt(a);r<128?t+=String.fromCharCode(r):r>127&&r<2048?(t+=String.fromCharCode(r>>6|192),t+=String.fromCharCode(63&r|128)):(t+=String.fromCharCode(r>>12|224),t+=String.fromCharCode(r>>6&63|128),t+=String.fromCharCode(63&r|128))}return t},_utf8_decode:function(e){for(var t="",a=0,r=0,i=0;a<e.length;)if((r=e.charCodeAt(a))<128)t+=String.fromCharCode(r),a++;else if(r>191&&r<224)i=e.charCodeAt(a+1),t+=String.fromCharCode((31&r)<<6|63&i),a+=2;else{i=e.charCodeAt(a+1);var n=e.charCodeAt(a+2);t+=String.fromCharCode((15&r)<<12|(63&i)<<6|63&n),a+=3}return t}};this.base64=e}return this.base64}},{key:"handleCustomFields",value:function(){var e=this,t=0,a={fieldGroup:$("#_render_custom_field_field_group_template").html(),globalSkeleton:$("#_render_custom_field_global_skeleton_template").html(),text:$("#_render_custom_field_text_template").html(),number:$("#_render_custom_field_number_template").html(),email:$("#_render_custom_field_email_template").html(),password:$("#_render_custom_field_password_template").html(),textarea:$("#_render_custom_field_textarea_template").html(),checkbox:$("#_render_custom_field_checkbox_template").html(),radio:$("#_render_custom_field_radio_template").html(),select:$("#_render_custom_field_select_template").html(),image:$("#_render_custom_field_image_template").html(),file:$("#_render_custom_field_file_template").html(),wysiwyg:$("#_render_custom_field_wysiswg_template").html(),repeater:$("#_render_custom_field_repeater_template").html(),repeaterItem:$("#_render_custom_field_repeater_item_template").html(),repeaterFieldLine:$("#_render_custom_field_repeater_line_template").html()},r=function(e,t){return i.wysiwyg(e,{toolbar:t}),e},n=function(e){var r=a[e.type],n=$('<div class="lcf-'+e.type+'-wrapper"></div>');n.data("lcf-registered-data",e);var l=null,d=null;switch(e.type){case"text":case"number":case"email":case"password":r=(r=r.replace(/__placeholderText__/gi,e.options.placeholderText||"")).replace(/__value__/gi,e.value||e.options.defaultValue||"");break;case"textarea":r=(r=(r=r.replace(/__rows__/gi,e.options.rows||3)).replace(/__placeholderText__/gi,e.options.placeholderText||"")).replace(/__value__/gi,e.value||e.options.defaultValue||"");break;case"image":if(r=r.replace(/__value__/gi,e.value||e.options.defaultValue||""),e.value)r=r.replace(/__image__/gi,e.thumb||e.options.defaultValue||"");else{var s=$(r).find("img").attr("data-default");r=r.replace(/__image__/gi,s||e.options.defaultValue||"")}break;case"file":r=(r=r.replace(/__value__/gi,e.value||e.options.defaultValue||"")).replace(/__url__/gi,e.full_url||e.options.defaultValue||"");break;case"select":return d=$(r),(l=c(e.options.selectChoices)).forEach((function(e){d.append('<option value="'+e[0]+'">'+e[1]+"</option>")})),d.val(i.arrayGet(e,"value",e.options.defaultValue)),n.append(d),n;case"checkbox":l=c(e.options.selectChoices);var u=i.jsonDecode(e.value);return l.forEach((function(e){var t=r.replace(/__value__/gi,e[0]||"");t=(t=t.replace(/__title__/gi,e[1]||"")).replace(/__checked__/gi,-1!=$.inArray(e[0],u)?"checked":""),n.append($(t))})),n;case"radio":l=c(e.options.selectChoices);var _=!1;return l.forEach((function(a){var i=r.replace(/__value__/gi,a[0]||"");i=(i=(i=i.replace(/__id__/gi,e.id+e.slug+t)).replace(/__title__/gi,a[1]||"")).replace(/__checked__/gi,e.value===a[0]?"checked":""),n.append($(i)),e.value===a[0]&&(_=!0)})),!1===_&&n.find("input[type=radio]:first").prop("checked",!0),n;case"repeater":return(d=$(r)).data("lcf-registered-data",e),d.find("> .repeater-add-new-field").html(e.options.buttonLabel||"Add new item"),d.find("> .sortable-wrapper").sortable(),o(e.items,e.value||[],d.find("> .field-group-items")),d;case"wysiwyg":r=r.replace(/__value__/gi,e.value||""),$(r).attr("data-toolbar",e.options.wysiwygToolbar||"basic")}return n.append($(r)),n},o=function(e,t,r){return r.data("lcf-registered-data",e),t.forEach((function(t){var i=r.find("> .ui-sortable-handle").length+1,n=a.repeaterItem;n=n.replace(/__position__/gi,i);var o=$(n);o.data("lcf-registered-data",e),l(e,t,o.find("> .field-line-wrapper > .field-group")),r.append(o)})),r},l=function(e,i,o){return i.forEach((function(e){t++;var i=a.repeaterFieldLine;i=(i=i.replace(/__title__/gi,e.title||"")).replace(/__instructions__/gi,e.instructions||"");var l=$(i),c=n(e);l.data("lcf-registered-data",e),l.find("> .repeater-item-input").append(c),o.append(l),"wysiwyg"===e.type&&r(l.find("> .repeater-item-input .wysiwyg-editor"),e.options.wysiwygToolbar||"basic")})),o},c=function(e){var t=[];return e.split("\n").forEach((function(e){var a=e.split(":");a[0]&&a[1]&&(a[0]=a[0].trim(),a[1]=a[1].trim()),t.push(a)})),t};this.$body.on("click",".remove-field-line",(function(e){e.preventDefault();var t=$(e.currentTarget);t.parent().animate({opacity:.1},300,(function(){t.parent().remove()}))})),this.$body.on("click",".collapse-field-line",(function(e){e.preventDefault(),$(e.currentTarget).toggleClass("collapsed-line")})),this.$body.on("click",".repeater-add-new-field",(function(e){e.preventDefault();var a=$.extend(!0,{},$(e.currentTarget).prev(".field-group-items")),r=a.data("lcf-registered-data");t++,o(r,[r],a),Impiger.initMediaIntegrate()})),this.CURRENT_DATA.forEach((function(t){var i=a.fieldGroup;i=i.replace(/__title__/gi,t.title||"");var o,l,c=$(i);o=t.items,l=c.find(".meta-boxes-body"),o.forEach((function(e){var t=a.globalSkeleton;t=(t=(t=t.replace(/__type__/gi,e.type||"")).replace(/__title__/gi,e.title||"")).replace(/__instructions__/gi,e.instructions||"");var i=$(t),o=n(e);i.find(".meta-box-wrap").append(o),i.data("lcf-registered-data",e),l.append(i),"wysiwyg"===e.type&&r(i.find(".meta-box-wrap .wysiwyg-editor"),e.options.wysiwygToolbar||"basic")})),c.data("lcf-field-group",t),e.$_UPDATE_TO.append(c)})),Impiger.initMediaIntegrate()}},{key:"exportData",value:function(){var e=this,t=function(e){var t=[];return e.each((function(e,r){t.push(a($(r)))})),t},a=function(e){var t=$.extend(!0,{},e.data("lcf-registered-data"));switch(t.type){case"text":case"number":case"email":case"password":case"image":case"file":t.value=e.find("> .meta-box-wrap input").val();break;case"wysiwyg":t.value=i.wysiwygGetContent(e.find("> .meta-box-wrap textarea"));break;case"textarea":t.value=e.find("> .meta-box-wrap textarea").val();break;case"checkbox":t.value=[],e.find("> .meta-box-wrap input:checked").each((function(e,a){t.value.push($(a).val())}));break;case"radio":t.value=e.find("> .meta-box-wrap input:checked").val();break;case"select":t.value=e.find("> .meta-box-wrap select").val();break;case"repeater":t.value=[],e.find("> .meta-box-wrap > .lcf-repeater > .field-group-items > li").each((function(e,a){var i=$(a).find("> .field-line-wrapper > .field-group");t.value.push(r(i.find("> li")))}));break;default:t=null}return t},r=function(e){var t=[];return e.each((function(e,a){var r=$(a);t.push(n(r))})),t},n=function(e){var t=$.extend(!0,{},e.data("lcf-registered-data"));switch(t.type){case"text":case"number":case"email":case"password":case"image":case"file":t.value=e.find("> .repeater-item-input input").val();break;case"wysiwyg":t.value=i.wysiwygGetContent(e.find("> .repeater-item-input > .lcf-wysiwyg-wrapper > .wysiwyg-editor"));break;case"textarea":t.value=e.find("> .repeater-item-input textarea").val();break;case"checkbox":t.value=[],e.find("> .repeater-item-input input:checked").each((function(e,a){t.value.push($(a).val())}));break;case"radio":t.value=e.find("> .repeater-item-input input:checked").val();break;case"select":t.value=e.find("> .repeater-item-input select").val();break;case"repeater":t.value=[],e.find("> .repeater-item-input > .lcf-repeater > .field-group-items > li").each((function(e,a){var i=$(a).find("> .field-line-wrapper > .field-group");t.value.push(r(i.find("> li")))}));break;default:t=null}return t};e.$_EXPORT_TO.closest("form").on("submit",(function(){var a;e.$_EXPORT_TO.val(i.jsonEncode((a=[],$("#custom_fields_container").find("> .meta-boxes").each((function(e,r){var i=$(r),n=i.data("lcf-field-group"),o=i.find("> .meta-boxes-body > .meta-box");n.items=t(o),a.push(n)})),a)))}))}}]),e}();jQuery(document).ready((function(){new n}))})();
