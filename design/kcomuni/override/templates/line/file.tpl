

<div class="col-md-2 mix">
											<div class="mix-inner">
												{if $node.data_map.preview.has_content}
											<a class="fancybox-button" data-rel="fancybox-button" href="/{$node.data_map.preview.content['original'].full_path}">	<img class="img-responsive" src="/{$node.data_map.preview.content.[medium].full_path}" alt=""></a>
 {/if}
												
											</div>
											<h4><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h4>
											{if $node.data_map.protocollo.has_content}
											<h6>Prot. {$node.data_map.protocollo.content}</h6>
											{/if}

<div class="row">
{if $node.data_map.file.content.mime_type|eq("application/pdf")}
<div class="col-md-2 col-sm-3">
<a title="visualizza" href={concat('comuni/f/flip/',$node.node_id)|ezurl}}" target="_blank">
<i class="fa fa-html5 fa-lg"></i>
</a>
</div>

{/if}

{def $attribute = $node.data_map.file}
<div class="col-md-2 col-sm-3">
<a title="scarica" href={concat( 'content/download/', $attribute.contentobject_id, '/', $attribute.id,'/version/', $attribute.version , '/file/', $attribute.content.original_filename|urlencode )|ezurl} target="_blank">
<i class="fa fa-download fa-lg"></i>
</a>
</div>
{if $node.object.can_move}
<div class="col-md-2 col-sm-3">
<form method="post" action={"content/action/"|ezurl}>
<div class="form-container">
<button type="submit" class="btn ic-button" title="sposta">
<i class="fa fa-arrow-up fa-lg"></i>							</button>
						
						<input type="hidden" name="MoveNodeButton" value="MoveNodeButton" />
						<input type="hidden" name="TopLevelNode" value="{$node.node_id}">
						<input type="hidden" name="ContentNodeID" value="{$node.node_id}">
						<input type="hidden" name="ContentObjectID" value="{$node.object.id}"></div>
					</form>


</div>


{/if}


{if $node.object.can_edit}
<div class="col-md-2 col-sm-3">
<form method="post" action={"content/action/"|ezurl}>
<div class="form-container">
<button type="submit" class="btn ic-button" title="modifica">
<i class="fa fa-edit fa-lg"></i>							</button>
						
						<input type="hidden" name="EditButton" value="EditButton" />
						
						<input type="hidden" name="ContentNodeID" value="{$node.node_id}">
						<input type="hidden" name="ContentObjectID" value="{$node.object.id}"></div>
					</form>


</div>
{/if}



{if $node.object.can_remove}
<div class="col-md-2 col-sm-3">
<form method="post" action={"content/action/"|ezurl}>
<div class="form-container">
<button type="submit" class="btn ic-button" title="elimina">
<i class="fa fa-trash fa-lg"></i>							</button>
						
						<input type="hidden" name="ActionRemove" value="ActionRemove" />
						<input type="hidden" name="TopLevelNode" value="{$node.node_id}">
						<input type="hidden" name="ContentNodeID" value="{$node.node_id}">
						<input type="hidden" name="ContentObjectID" value="{$node.object.id}"></div>
					</form>


</div>
{/if}

<div class="col-md-2 col-sm-3">
<input type="checkbox" name="idtodownload[]" class="idtodownload" value="{$node.object.id}" />
</div>

<div class="clearfix"></div>
</div>
<br/>
</div>

