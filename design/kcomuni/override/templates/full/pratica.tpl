<h3 class="page-title"><i class="fa fa-archive fa-1x"></i> {$node.name}</h3>

{attribute_view_gui attribute=$node.data_map.description}


<h3>Informazioni</h3>
<div class="row">

<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
	


<div class="dashboard-stat red">
	<div class="visual">
		<i class="fa fa-road"></i>
	</div>
	<div class="details">
		<div class="number">Indirizzi</div>
		<div class="desc">
		{def $nodea = array()}
		{foreach $node.data_map.address.content.relation_list as $part}
			{set $nodea = fetch('content', 'node', hash('node_id', $part.node_id))}
			<a href={$nodea.url_alias|ezurl}>{$nodea.name}</a><br/>
		{/foreach}
		{if $node.data_map.interno.has_content}<strong>Interno: </strong>{$node.data_map.interno.content}<br/>{/if}
		</div>
	</div>
	
</div>
</div>

<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat red">
	<div class="visual">
		<i class="fa fa-user"></i>
	</div>
	<div class="details">
		<div class="number">Richiedenti</div>
		<div class="desc">{attribute_view_gui attribute=$node.data_map.actors}</div>
	</div>
	
</div>
</div>  



<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat red">
	<div class="visual">
		<i class="fa fa-info-circle"></i>
	</div>
	<div class="details">
		<div class="number">ID Pratica</div>
		<div class="desc">
		
			{if $node.data_map.number.has_content}<strong>Numero:</strong> {$node.data_map.number.content}<br/>{/if}
			{if $node.data_map.year.has_content}<strong>Anno:</strong> {$node.data_map.year.content}<br/>{/if}
			{if $node.data_map.tipo.has_content}<strong>Tipologia:</strong> {$node.data_map.tipo.content}<br/>{/if}
			{if $node.data_map.sector.has_content}<strong>Settore:</strong> {$node.data_map.sector.content}<br/>{/if}
			{if $node.data_map.collocazione.has_content}<strong>Collocazione:</strong> {$node.data_map.collocazione.content}<br/>{/if}
			
		
		</div>
	</div>
	
</div>
</div> 

<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat red">
	<div class="visual">
		<i class="fa fa-map-o"></i>
	</div>
	<div class="details">
		<div class="number">Dati catastali</div>
		<div class="desc">
			{if $node.data_map.foglio.has_content}<strong>Foglio:</strong> {$node.data_map.foglio.content}<br/>{/if}
			{if $node.data_map.mappale.has_content}<strong>Mappale:</strong> {$node.data_map.mappale.content}<br/>{/if}
			{if $node.data_map.sub.has_content}<strong>Sub: </strong>{$node.data_map.sub.content}<br/>{/if}
		
		</div>
	</div>
	
</div>
</div> 




 

                                                               
                                                                          
                                                                          



</div>


<div class="portlet box red">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-sitemap"></i>Documenti
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse"></a>
							</div>
						</div>
						<div class="portlet-body">

					 {def $page_limit = 36
                     $classes = ezini( 'MenuContentSettings', 'ExtraIdentifierList', 'menu.ini' )
                     $children = array()
                     $children_count = ''}
					 {set $children_count=fetch_alias( 'children_count', hash( 'parent_node_id', $node.node_id,
                                                                          'class_filter_type', 'exclude',
                                                                          'class_filter_array', $classes ) )}
                             {if  $children_count}                                            
                                                                          
                                                                          
                 <div class="row mix-grid">
					{def $num=0}

			{foreach fetch_alias( 'children', hash( 'parent_node_id', $node.node_id,
								'offset', $view_parameters.offset,
								'sort_by', array('priority', true()),
								'class_filter_type', 'include',
								'class_filter_array', array('file','folder'),
								'limit', $page_limit ) ) as $child }
								
								{set $num=$num|inc(1)}
								
								{node_view_gui view=line content_node=$child}
								
								{if $num|eq(6)}
								<div class="clearfix"></div>
								{set $num=0}
								{/if}
								
								
								
								
							{/foreach}
		
									</div>
								




                        
                    {/if}
            

                {include name=navigator
                         uri='design:navigator/google.tpl'
                         page_uri=$node.url_alias
                         item_count=$children_count
                         view_parameters=$view_parameters
                         item_limit=$page_limit}

                                                              
                                                                          
                                                                          
                                                                          
                                                                          
                                                                          



						</div>
						
</div>					





