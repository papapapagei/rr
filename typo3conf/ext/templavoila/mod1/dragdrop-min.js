var sortable_currentItem;function sortable_unhideRecord(it,command){jumpToUrl(command)}function sortable_hideRecord(it,command){if(!sortable_removeHidden){return jumpToUrl(command)}while(it.className!="sortableItem"){it=it.parentNode}new Ajax.Request(command);new Effect.Fade(it,{duration:0.5,afterFinish:sortable_hideRecordCallBack})}function sortable_hideRecordCallBack(obj){var el=obj.element;while(el.lastChild){el.removeChild(el.lastChild)}}function sortable_unlinkRecordCallBack(obj){var el=obj.element;var pn=el.parentNode;pn.removeChild(el);sortable_update(pn)}function sortable_unlinkRecord(pointer,id,elementPointer){new Ajax.Request("index.php?"+sortable_linkParameters+"&ajaxUnlinkRecord="+escape(pointer),{onSuccess:function(response){var node=Builder.build(response.responseText);$("tx_templavoila_mod1_sidebar-bar").setStyle({height:$("tx_templavoila_mod1_sidebar-bar").getHeight()+"px"});$("tx_templavoila_mod1_sidebar-bar").innerHTML=node.innerHTML;setTimeout(function(){sortable_unlinkRecordSidebarCallBack(elementPointer)},100)}});new Effect.Fade(id,{duration:0.5,afterFinish:sortable_unlinkRecordCallBack})}function sortable_unlinkRecordSidebarCallBack(pointer){var childNodes=childElements($("tx_templavoila_mod1_sidebar-bar"));var innerHeight=0;for(var i=0;i<childNodes.length;i++){innerHeight+=childNodes[i].getHeight()}$("tx_templavoila_mod1_sidebar-bar").morph({height:innerHeight+"px"},{duration:0.1,afterFinish:function(){$("tx_templavoila_mod1_sidebar-bar").setStyle({height:"auto"});if(pointer&&$(pointer)){$(pointer).highlight()}}})}function sortable_updateItemButtons(el,position,pID){var p=[],p1=[];var newPos=escape(pID+position);childElements(el).each(function(node){if(node.nodeName=="A"&&node.href){switch(node.className){case"tpm-new":node.href=node.href.replace(/&parentRecord=[^&]+/,"&parentRecord="+newPos);break;case"tpm-browse":if(node.rel){node.rel=node.rel.replace(/&destination=[^&]+/,"&destination="+newPos)}break;case"tpm-delete":node.href=node.href.replace(/&deleteRecord=[^&]+/,"&deleteRecord="+newPos);break;case"tpm-unlink":node.href=node.href.replace(/unlinkRecord\('[^']+'/,"unlinkRecord('"+newPos+"'");break;case"tpm-cut":case"tpm-copy":case"tpm-ref":node.href=node.href.replace(/CB\[el\]\[([^\]]+)\]=[^&]+/,"CB[el][$1]="+newPos);break;case"tpm-pasteAfter":case"tpm-pasteSubRef":node.href=node.href.replace(/&destination=[^&]+/,"&destination="+newPos);break;case"tpm-makeLocal":node.href=node.href.replace(/&makeLocalRecord=[^&]+/,"&makeLocalRecord="+newPos);break}}else{if(childElements(el)&&node.className!="tpm-subelement-table"){sortable_updateItemButtons(node,position,pID)}}})}function sortable_update(el){var node=el.firstChild;var i=1;while(node!=null){if(node.className=="sortableItem"){if(sortable_currentItem&&node.id==sortable_currentItem.id){var url="index.php?"+sortable_linkParameters+"&ajaxPasteRecord=cut&source="+sortable_items[sortable_currentItem.id]+"&destination="+sortable_items[el.id]+(i-1);new Ajax.Request(url);sortable_currentItem=false}sortable_updateItemButtons(node,i,sortable_items[el.id]);sortable_items[node.id]=sortable_items[el.id]+i;i++}node=node.nextSibling}}function sortable_change(el){sortable_currentItem=el}function childElements(node){if(typeof node.childElements!="function"){return node.immediateDescendants()}else{return node.childElements()}}function tv_createSortable(s,containment){Position.includeScrollOffsets=true;Sortable.create(s,{tag:"div",ghosting:false,format:/(.*)/,handle:"sortable_handle",scroll:window,scrollSpeed:30,dropOnEmpty:true,constraint:false,containment:containment,onChange:sortable_change,onUpdate:sortable_update})};