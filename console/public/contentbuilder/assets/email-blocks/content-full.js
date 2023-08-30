
function _tabs(n) {
    var html = '';
    for (var i = 1; i <= n; i++) {
        html += '\t';
    }
    return '\n' + html;
}

// source: https: //stackoverflow.com/questions/2255689/how-to-get-the-file-path-of-the-currently-executing-javascript-code
function _path() {
    var scripts = document.querySelectorAll('script[src]');
    var currentScript = scripts[scripts.length - 1].src;
    var currentScriptChunks = currentScript.split('/');
    var currentScriptFile = currentScriptChunks[currentScriptChunks.length - 1];
    return currentScript.replace(currentScriptFile, '');
}
var _snippets_path = _path();

var data_basic = {
    'snippets': [

		{
		    'thumbnail': 'preview/001.png',
		    'category': '1',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td class="wrapper-inner">' +
									'<table align="center" class="container">' +
										'<tbody>' +
										'<tr>' +
											'<td>' +
												'<table class="row collapse">' +
													'<tbody>' +
													'<tr>' +
														'<th class="small-6 large-6 columns first">' +
															'<table>' +
																'<tbody>' +
																'<tr>' +
																	'<th><img src="/contentbuilder/assets//email-blocks/images/image.png" style="width: 200px; height: 60px;"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
														'<th class="small-6 large-6 columns last">' +
															'<table>' +
																'<tbody>' +
																'<tr>' +
																	'<th><p class="text-right"></p></th>' +                                   				
																'</tr>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</td>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/002.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th><h1>LOREM IPSUM IS SIMPLY DUMMY TEXT</h1></th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/003.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +           
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th><h1>Lorem Ipsum is dummy text</h1></th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/004.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th><p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p></th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/006.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +  
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4>Lorem Ipsum is dummy text</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/008.png',
		    'category': '3',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +      
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1>BEAUTIFUL CONTENT</h1>' +
															'<p class="lead"><i>Lorem Ipsum is simply dummy text of the printing industry.</i></p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/009.png',
		    'category': '3',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                             
															'<h1>Hi, John Roberts</h1>' +
															'<p class="lead">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +                                   
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                                   
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/010.png',
		    'category': '3',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 class="text-center">Say Hello to Our New Look</h1>' +
															'<p class="lead text-center">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/011.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h2>Lorem Ipsum is dummy text</h2>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/012.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +         
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h2>Lorem Ipsum <small>This is a note.</small></h2>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/013.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4>Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +                              
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4>Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +                                
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/014.png',
		    'category': '10',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +           
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/014.jpg">' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. <a href="#">Click it!</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/015.png',
		    'category': '10',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/015.jpg">' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner primary">' +
																		'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. <a href="#">Click it!</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/016.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th><img src="/contentbuilder/assets//email-blocks/images/016.jpg"></th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/017.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/017.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/018.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/018.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/019.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/019.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/020.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/020.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/021.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/021.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/022.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Item</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Item</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/023.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Item</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-4 columns">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Item</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Item</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/024.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/024-1.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature One</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/024-2.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Two</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/025.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/025-1.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature One</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-4 columns">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/025-2.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Two</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/025-3.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Three</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/026.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png">' +
															'<h5>Feature Item</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png">' +
															'<h5>Feature Item</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/027.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png">' +
															'<h5>Feature Item</h5>' +
															'<p>Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-4 columns">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png">' +
															'<h5>Feature Item</h5>' +
															'<p>Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png">' +
															'<h5>Feature Item</h5>' +
															'<p>Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +	                          
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/028.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h3 class="text-center">Create Something Awesome</h3>' +
															'<p class="text-center">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button small float-center">' +
																	'<tbody>' +
																		'<tr>' +
																			'<td>' +
																				'<table>' +
																					'<tbody>' +
																					'<tr>' +
																						'<td>' +
																							'<a href="#" align="center" title="">Get Started</a>' +
																						'</td>' +
																					'</tr>' +
																					'</tbody>' +
																				'</table>' +
																			'</td>' +
																			'<td class="expander"></td>' +
																		'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/029.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/029-1.jpg">' +                                  				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">View More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/029-2.jpg">' +                                  				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">View More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/030.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/030-1.jpg">' +                                  				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h5 style="margin-top:0">LOREM IPSUM</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">View More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/030-2.jpg">' +                                  				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h5 style="margin-top:0">LOREM IPSUM</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">View More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +	
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/031.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
											'<tr>' +
												'<th class="small-12 large-4 columns first">' +
													'<table>' +
														'<tbody>' +
														'<tr>' +
															'<th>' +
																'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/031.jpg" align="center" class="float-center"></center>' +
															'</th>' +
														'</tr>' +
														'</tbody>' +
													'</table>' +
												'</th>' +
												'<th class="small-12 large-8 columns last">' +
													'<table>' +
														'<tbody>' +
														'<tr>' +
															'<th>' +
																'<h1>Insert Text Here.</h1>' +
																'<table class="button large">' +
																	'<tbody>' +
																		'<tr>' +
																			'<td>' +
																				'<table>' +
																					'<tbody>' +
																					'<tr>' +
																						'<td><a href="#">Sign Up</a></td>' +
																					'</tr>' +
																					'</tbody>' +
																				'</table>' +
																			'</td>' +
																		'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</th>' +
														'</tr>' +
														'</tbody>' +
													'</table>' +
												'</th>' +
											'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/032.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-8 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/032.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum dolor sit amet.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/033.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum dolor sit amet.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-8 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/033.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/034.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-8 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/034.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															 '<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/035.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-8 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/035.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/036.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/036.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/037.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/037.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +	
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/038.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/038.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/039.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/039.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/040.png',
		    'category': '10',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/040-1.jpg">' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<p>Lorem Ipsum is simply text of the printing industry.&nbsp;<a href="#">Click it!</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/040-2.jpg">' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<p>Lorem Ipsum is simply text of the printing industry.&nbsp;<a href="#">Click it!</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +	
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/041.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/041-1.jpg">' +                                 				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/041-2.jpg">' +                                  				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +	
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/042.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/042-1.jpg">' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-4 columns">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/042-2.jpg">' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/042-3.jpg">' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/043.png',
		    'category': '16',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" align="center" class="float-center"></center>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead text-center">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
															'<p class="text-center">By Your Name</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/044.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 style="font-size:32px;">LOREM IPSUM IS SIMPLY DUMMY TEXT OF THE PRINTING INDUSTRY</h1>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/045.png',
		    'category': '4',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p class="lead"><i>This is a special report</i></p>' +
															'<h1 style="font-size:48px;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/046.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/046.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 class="text-center">Lorem Ipsum is Simply Text</h4>' +
															'<p class="text-center">15 sections | 567 Min</p>' +
														'</th>' +
														'<th class="expander"></th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/047.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 class="text-center" style="font-size:48px;">BEAUTIFUL CONTENT</h1>' +
															'<p class="lead text-center"><i>Lorem Ipsum is simply dummy text of the printing industry.</i></p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button small float-center">' +
																	'<tbody>' +
																		'<tr>' +
																			'<td>' +
																				'<table>' +
																					'<tbody>' +
																					'<tr>' +
																						'<td>' +
																							'<a href="#" align="center" title="">Read More</a>' +
																						'</td>' +
																					'</tr>' +
																					'</tbody>' +
																				'</table>' +
																			'</td>' +
																			'<td class="expander"></td>' +
																		'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/048.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/048.png" align="center" class="float-center"></center>' +
															'<h1 class="text-center" style="font-size:48px;">Beautiful Content</h1>' +
															'<p class="text-center">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					 '<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button small float-center">' +
																	'<tbody>' +
																		'<tr>' +
																			'<td>' +
																				'<table>' +
																					'<tbody>' +
																					'<tr>' +
																						'<td>' +
																							'<a href="#" align="center" title="">Read More</a>' +
																						'</td>' +
																					'</tr>' +
																					'</tbody>' +
																				'</table>' +
																			'</td>' +
																			'<td class="expander"></td>' +
																		'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/049.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/049.png" align="center" class="float-center"></center>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead text-center">BEAUTIFUL CONTENT</p>' +
															'<h1 class="text-center" style="font-size:48px;">LOREM IPSUM IS SIMPLY TEXT</h1>' +
															'<p class="text-center">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button small float-center">' +
																	'<tbody>' +
																		'<tr>' +
																			'<td>' +
																				'<table>' +
																					'<tbody>' +
																					'<tr>' +
																						'<td>' +
																							'<a href="#" align="center" title="">Read More</a>' +
																						'</td>' +
																					'</tr>' +
																					'</tbody>' +
																				'</table>' +
																			'</td>' +
																			'<td class="expander"></td>' +
																		'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/050.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/050.png">' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead">BEAUTIFUL CONTENT</p>' +
															'<h1 style="font-size:48px;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					 '<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" align="center" title="">Download</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																		'<td class="expander"></td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +												
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/051.png',
		    'category': '18',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="container secondary" align="center">' +
										'<tbody>' +
											'<tr>' +
												'<td class="wrapper-inner">' +
													'<table class="row">' +
														'<tbody>' +
														'<tr>' +
															'<th class="small-12 large-6 columns first">' +
																'<table>' +
																	'<tbody>' +
																	'<tr>' +
																		'<th>' +
																			'<h5>Connect With Us:</h5>' +
																			'<table align="left" class="menu vertical">' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<table>' +
																							'<tbody>' +
																							'<tr>' +
																								'<th style="text-align: left;" class="menu-item float-center"><a href="#">Twitter</a></th>' +
																								'<th style="text-align: left;" class="menu-item float-center"><a href="#">Facebook</a></th>' +
																								'<th style="text-align: left;" class="menu-item float-center"><a href="#">Google +</a></th>' +
																							'</tr>' +
																							'</tbody>' +
																						'</table>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</th>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</th>' +
															'<th class="small-12 large-6 columns last">' +
																'<table>' +
																	'<tbody>' +
																	'<tr>' +
																		'<th>' +
																			'<h5>Contact Info:</h5>' +
																			'<p>Phone: 123-456-7890</p>' +
																			'<p>Email: <a href="mailto:#" title="">example@example.com</a></p>' +
																		'</th>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</th>' +
														'</tr>' +
														'</tbody>' +
													'</table>' +                 
												'</td>' +
											'</tr>' +
										'</tbody>' +
									'</table>' +  
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/052.png',
		    'category': '18',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row footer text-center">' +
										'<tbody>' +
											'<tr>' +
												'<th class="small-12 large-4 columns first">' +
													'<table>' +
														'<tbody>' +
														'<tr>' +
															'<th>' +
																'<img src="/contentbuilder/assets//email-blocks/images/image.png" style="width: 170px; height: 50px;">' +
															'</th>' +
														'</tr>' +
														'</tbody>' +
													'</table>' +
												'</th>' +
												'<th class="small-12 large-4 columns">' +
													'<table>' +
														'<tbody>' +
														'<tr>' +
															'<th>' +
																'<p>Call us at 123.456.7890<br> Email us at example@example.com</p>' +
															'</th>' +
														'</tr>' +
														'</tbody>' +
													'</table>' +
												'</th>' +
												'<th class="small-12 large-4 columns last">' +
													'<table>' +
														'<tbody>' +
														'<tr>' +
															'<th>' +
																'<p>123 Street Name<br> State 12345</p>' +
															'</th>' +
														'</tr>' +
														'</tbody>' +
													'</table>' +
												'</th>' +
											'</tr>' +
										'</tbody>' +
									'</table>' +                                  
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/053.png',
		    'category': '18',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4>More Reading:</h4>' +
															'<ul>' +
																'<li><a href="#" title="">Lorem Ipsum Dolor Sit Amet</a></li>' +
															  	'<li><a href="#">Lorem Ipsum Dolor Sit Amet</a></li>' +
															 	'<li><a href="#">Lorem Ipsum Dolor Sit Amet</a></li>' +
															'</ul>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4>Get Involved:</h4>' +
															'<ul>' +
															  	'<li><a href="#">Facebook</a></li>' +
															  	'<li><a href="#">Twitter</a></li>' +
															  	'<li><a href="#">Instagram</a></li>' +
															'</ul>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +		
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/054.png',
		    'category': '18',
		    'html':
				'<div>' +
					'<table align="center" class="container body-border float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table align="center" class="menu float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<th class="menu-item float-center"><a href="#" title="">example@example.com</a></th>' +
																					'<th class="menu-item float-center"><a href="#" title="">Facebook</a></th>' +
																					'<th class="menu-item float-center"><a href="#" title="">Twitter</a></th>' +
																					'<th class="menu-item float-center"><a href="#" title="">(123)-456-7890</a></th>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/055.png',
		    'category': '18',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table align="center" class="menu text-center float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<th class="menu-item float-center"><a href="#" title="">Home</a></th>' +
																					'<th class="menu-item float-center"><a href="#" title="">About</a></th>' +
																					'<th class="menu-item float-center"><a href="#" title="">Portfolio</a></th>' +
																					'<th class="menu-item float-center"><a href="#">Contact</a></th>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/056.png',
		    'category': '19',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table align="center" class="menu float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<th class="menu-item float-center"><a href="#" title="">Terms</a></th>' +
																					'<th class="menu-item float-center"><a href="#" title="">Privacy</a></th>' +
																					'<th class="menu-item float-center"><a href="#">Unsubscribe</a></th>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/057.png',
		    'category': '19',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row collapsed footer">' +
										'<tbody>' +
											'<tr>' +
												'<th class="small-12 large-12 columns first last">' +
													'<table>' +
														'<tbody>' +
														'<tr>' +
															'<th>' +
																'<p class="text-center">@copywrite nobody<br> <a href="#" title="">example@example.com</a> | <a href="#" title="">Manage Email Notifications</a> | <a href="#" title="">Unsubscribe</a></p>' +
															'</th>' +
														'</tr>' +
														'</tbody>' +
													'</table>' +
												'</th>' +
											'</tr>' +
										'</tbody>' +
									'</table>' +                                 
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/058.png',
		    'category': '19',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>You received this email because you\'re signed up to receive updates from us. <a href="#" title="">Click here to unsubscribe.</a></p>' +
														'</th>' +
														'<th class="expander"></th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/059.png',
		    'category': '19',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p><small>You\'re getting this email because you\'ve signed up for email updates. If you want to opt-out of future emails, <a href="#">unsubscribe here</a>.</small></p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/060.png',
		    'category': '19',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p><small>You received this email because you\'re signed up to get updates from us. <a href="#">Click here to unsubscribe.</a></small></p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/061.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="button large expand">' +
																'<tbody>' +
																'<tr>' +
																	'<td>' +
																		'<table>' +
																			'<tbody>' +
																			'<tr>' +
																				'<td>' +
																					'<center data-parsed=""><a href="#" align="center" class="float-center">Button</a></center>' +
																				'</td>' +
																			'</tr>' +
																			'</tbody>' +
																		'</table>' +
																	'</td>' +
																	'<td class="expander"></td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/062.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="button large">' +
																'<tbody>' +
																'<tr>' +
																	'<td>' +
																		'<table>' +
																			'<tbody>' +
																			'<tr>' +
																				'<td>' +
																					'<a href="#" align="center">Button</a>' +
																				'</td>' +
																			'</tr>' +
																			'</tbody>' +
																		'</table>' +
																	'</td>' +
																	'<td class="expander"></td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/063.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="button">' +
																'<tbody>' +
																'<tr>' +
																	'<td>' +
																		'<table>' +
																			'<tbody>' +
																			'<tr>' +
																				'<td>' +
																					'<a href="#" align="center">Button</a>' +
																				'</td>' +
																			'</tr>' +
																			'</tbody>' +
																		'</table>' +
																	'</td>' +
																	'<td class="expander"></td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/064.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="button small">' +
																'<tbody>' +
																'<tr>' +
																	'<td>' +
																		'<table>' +
																			'<tbody>' +
																			'<tr>' +
																				'<td>' +
																					'<a href="#" align="center">Button</a>' +
																				'</td>' +
																			'</tr>' +
																			'</tbody>' +
																		'</table>' +
																	'</td>' +
																	'<td class="expander"></td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/065.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="button tiny">' +
																'<tbody>' +
																'<tr>' +
																	'<td>' +
																		'<table>' +
																			'<tbody>' +
																			'<tr>' +
																				'<td>' +
																					'<a href="#" align="center">Button</a>' +
																				'</td>' +
																			'</tr>' +
																			'</tbody>' +
																		'</table>' +
																	'</td>' +
																	'<td class="expander"></td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/066.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button large float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" align="center">Button</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																		'<td class="expander"></td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/067.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" align="center">Button</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																		'<td class="expander"></td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/068.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button small float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" align="center">Button</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																		'<td class="expander"></td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/069.png',
		    'category': '8',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button tiny float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" align="center">Button</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																		'<td class="expander"></td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/070.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/071.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner primary">' +
																		'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		
		{
		    'thumbnail': 'preview/072.png',
		    'category': '20',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/073.png',
		    'category': '20',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<hr>' +			
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/074.png',
		    'category': '16',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" align="center" class="float-center"></center>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead text-center">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<p class="text-center">By Your Name</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" align="center" class="float-center"></center>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead text-center">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<p class="text-center">By Your Name</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/075.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row collapse">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th><img src="/contentbuilder/assets//email-blocks/images/075.jpg"></th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +                                			
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/076.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<table class="button small rounded">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +
																							
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/076.jpg">' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/077.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h5 style="margin-top:0">LOREM IPSUM</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +
															'<table class="button small rounded">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +
																							
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/077.jpg">' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/078.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/078.jpg">' +                                 				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-8 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/079.png',
		    'category': '6',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-8 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h4 style="margin-top:0">Lorem Ipsum</h4>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +   
								
											'<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/079.jpg">' +                                 				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +                         		     
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/081.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/081.jpg">' +   	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-8 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>' +											
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/082.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-8 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                               				
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +										
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +   
								
											'<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/082.jpg">' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +                         		     
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/080.png',
		    'category': '15',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                  				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h1 class="text-center">$19</h1>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
																		'<p class="text-center"><a href="#" title="">Buy Now</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h1 class="text-center">$39</h1>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
																		'<p class="text-center"><a href="#">Buy Now</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/083.png',
		    'category': '15',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                  				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h5 class="text-center">PRODUCT NAME</h5>' +
																		'<h1 class="text-center">$19</h1>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
																		'<center data-parsed="">' +
																			'<table class="button small float-center">' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<table>' +
																							'<tbody>' +
																							'<tr>' +
																								'<td>' +
																									'<a href="#" align="center" title="">BUY NOW</a>' +
																								'</td>' +
																							'</tr>' +
																							'</tbody>' +
																						'</table>' +
																					'</td>' +
																					'<td class="expander"></td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</center>' +
																	'</th>' +               					
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                  				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h5 class="text-center">PRODUCT NAME</h5>' +
																		'<h1 class="text-center">$29</h1>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
																		'<center data-parsed="">' +
																			'<table class="button small float-center">' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<table>' +
																							'<tbody>' +
																							'<tr>' +
																								'<td>' +
																									'<a href="#" align="center" title="">BUY NOW</a>' +
																								'</td>' +
																							'</tr>' +
																							'</tbody>' +
																						'</table>' +
																					'</td>' +
																					'<td class="expander"></td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</center>' +
																	'</th>' +                                     					
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/084.png',
		    'category': '15',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +	
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +															
																		'<h1 class="text-center">$19</h1>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text.</p>' +
																		'<p class="text-center"><a href="#" title="">Buy Now</a></p>' +
																	'</th>' +                                      					
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-4 columns">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +														
																		'<h1 class="text-center">$29</h1>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text.</p>' +
																		'<p class="text-center"><a href="#" title="">Buy Now</a></p>' +
																	'</th>' +                           					
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +														
																		'<h1 class="text-center">$39</h1>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text.</p>' +
																		'<p class="text-center"><a href="#" title="">Buy Now</a></p>' +
																	'</th>' +                                   					
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/085.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h3 class="text-center">LOREM IPSUM IS SIMPLY TEXT</h3>' +
																		'<p class="text-center">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
																		'<p class="text-center"><a href="#">Read More</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/086.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner primary">' +
																		'<h3 class="text-center">LOREM IPSUM IS SIMPLY TEXT</h3>' +
																		'<p class="text-center">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
																		'<p class="text-center"><a href="#">Read More</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/087.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                  				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h5>LOREM IPSUM</h5>' +
																		'<p>Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<p><a href="#" title="">Read More</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                               				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h5>LOREM IPSUM</h5>' +
																		'<p>Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<p><a href="#" title="">Read More</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/088.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                  				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner primary">' +
																		'<h5>LOREM IPSUM</h5>' +
																		'<p>Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<p><a href="#" title="">Read More</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                  				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner primary">' +
																		'<h5>LOREM IPSUM</h5>' +
																		'<p>Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<p><a href="#" title="">Read More</a></p>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/089.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                 				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h5 class="text-center">LOREM IPSUM</h5>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<center data-parsed="">' +
																			'<table class="button small rounded float-center">' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<table>' +
																							'<tbody>' +
																							'<tr>' +
																								'<td>' +
																									'<a href="#" align="center" title="">Discover More</a>' +
																								'</td>' +
																							'</tr>' +
																							'</tbody>' +
																						'</table>' +
																					'</td>' +                                 						
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</center>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                 				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner">' +
																		'<h5 class="text-center">LOREM IPSUM</h5>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<center data-parsed="">' +
																			'<table class="button small rounded float-center">' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<table>' +
																							'<tbody>' +
																							'<tr>' +
																								'<td>' +
																									'<a href="#" align="center" title="">Discover More</a>' +
																								'</td>' +
																							'</tr>' +
																							'</tbody>' +
																						'</table>' +
																					'</td>' +                                      						
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</center>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/090.png',
		    'category': '9',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                 				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner primary">' +
																		'<h5 class="text-center">LOREM IPSUM</h5>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<center data-parsed="">' +
																			'<table class="button small rounded float-center">' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<table>' +
																							'<tbody>' +
																							'<tr>' +
																								'<td>' +
																									'<a href="#" align="center" title="">Discover More</a>' +
																								'</td>' +
																							'</tr>' +
																							'</tbody>' +
																						'</table>' +
																					'</td>' +                                 						
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</center>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +                                 				
															'<table class="callout">' +
																'<tbody>' +
																'<tr>' +
																	'<th class="callout-inner primary">' +
																		'<h5 class="text-center">LOREM IPSUM</h5>' +
																		'<p class="text-center">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
																		'<center data-parsed="">' +
																			'<table class="button small rounded float-center">' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<table>' +
																							'<tbody>' +
																							'<tr>' +
																								'<td>' +
																									'<a href="#" align="center" title="">Discover More</a>' +
																								'</td>' +
																							'</tr>' +
																							'</tbody>' +
																						'</table>' +
																					'</td>' +                                      						
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</center>' +
																	'</th>' +
																	'<th class="expander"></th>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/091.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/091.png">' +                                																
															'<h1>LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/092.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/092.jpg">' +                                 				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-8 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 style="font-size:40px;;margin-top:0">LOREM IPSUM IS SIMPLY TEXT</h1>' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/093.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-8 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +          				
															'<h1 style="font-size:40px;;margin-top:0">LOREM IPSUM IS SIMPLY TEXT</h1>' +												
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +   
								
											'<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/093.jpg">' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +                         		     
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/094.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/094.jpg">' +                                  				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 style="font-size:36px;;margin-top:0">LOREM IPSUM IS SIMPLY TEXT</h1>' +
															'<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +												
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/095.png',
		    'category': '2',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 style="font-size:36px;;margin-top:0">LOREM IPSUM IS SIMPLY TEXT</h1>' +
															'<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +                                 				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/095.jpg">' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/096.png',
		    'category': '3',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/096.jpg" align="center" class="float-center"></center>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<h1 class="text-center">BEAUTIFUL CONTENT</h1>' +
															'<p class="text-center lead"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +									
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/097.png',
		    'category': '3',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/097.png" align="center" class="float-center"></center>' +											
															'<h1 class="text-center">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
															'<p class="text-center lead"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/098.png',
		    'category': '3',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/098.png">' +                        												
															'<h1>LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
															'<p class="lead"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/099.png',
		    'category': '3',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/099.png">' +                                 																
															'<h2>Lorem Ipsum is Simply Dummy Text of the Printing Industry.</h2>' +
															'<p class="lead"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/100.png',
		    'category': '4',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/100.png">' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead"><i>Lorem Ipsum Dolor sit Amet</i></p>' +
															'<h1>LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/101.png',
		    'category': '4',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/101.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p class="lead"><i>Beautiful Content</i></p>' +
															'<h1 style="font-size:30px;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/102.png',
		    'category': '4',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p class="lead"><i>Beautiful Content</i></p>' +
															'<h1 style="font-size:30px;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/102.jpg" align="center" class="float-center"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/103.png',
		    'category': '12',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row collapse">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/103-1.jpg">' +                                 				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/103-2.jpg">' +                                   				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/104.png',
		    'category': '10',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/104-1.jpg">' +
															'<h5>Lorem Ipsum</h5>' +			
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +                           				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/104-2.jpg">' + 
															'<h5>Lorem Ipsum</h5>' +	
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +                                 				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/105.png',
		    'category': '10',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/105-1.jpg">' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="18px" style="font-size:18px;line-height:18px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +		
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +                                 				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/105-2.jpg">' + 
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="18px" style="font-size:18px;line-height:18px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +		
															'<p>Lorem Ipsum is simply dummy text of the printing industry.</p>' +                                 				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/106.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/106.jpg">' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-4 columns">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/107.png',
		    'category': '7',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-4 columns">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/107.jpg">' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/108.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 class="text-center">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
															'<p class="text-center">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +              
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button small rounded float-center">' +
																	'<tbody>' +
																		'<tr>' +
																			'<td>' +
																				'<table>' +
																					'<tbody>' +
																					'<tr>' +
																						'<td>' +
																							'<a href="#" align="center" title="">Read More</a>' +
																						'</td>' +
																					'</tr>' +
																					'</tbody>' +
																				'</table>' +
																			'</td>' +
																			'<td class="expander"></td>' +
																		'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +    
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/109.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/109.png" align="center" class="float-center"></center>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="text-center">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
		
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +		
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed="">' +
																'<table class="button small rounded float-center">' +
																	'<tbody>' +
																		'<tr>' +
																			'<td>' +
																				'<table>' +
																					'<tbody>' +
																					'<tr>' +
																						'<td>' +
																							'<a href="#" align="center" title="">Read More</a>' +
																						'</td>' +
																					'</tr>' +
																					'</tbody>' +
																				'</table>' +
																			'</td>' +
																			'<td class="expander"></td>' +
																		'</tr>' +
																	'</tbody>' +
																'</table>' +
															'</center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +   		                     
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/110.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/110-1.jpg">' +
															'<h5>Lorem Ipsum</h5>' +	
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +                                 				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/110-2.jpg">' +
															'<h5>Lorem Ipsum</h5>' +	
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +                               				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/111.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/111.jpg">' +                                  				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 style="font-size:30px;;margin-top:0">Lorem Ipsum</h1>' +
															'<p class="lead"><i>Beautiful Content</i></p>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																'<tr>' +
																	'<td>' +
																		'<table>' +
																			'<tbody>' +
																			'<tr>' +
																				'<td>' +
																					'<a href="#" align="center">Read More</a>' +
																				'</td>' +
																			'</tr>' +
																			'</tbody>' +
																		'</table>' +
																	'</td>' +
																	'<td class="expander"></td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +					
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/112.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h1 style="font-size:30px;;margin-top:0">Lorem Ipsum</h1>' +
															'<p class="lead"><i>Beautiful Content</i></p>' +
															'<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																'<tr>' +
																	'<td>' +
																		'<table>' +
																			'<tbody>' +
																			'<tr>' +
																				'<td>' +
																					'<a href="#" align="center">Read More</a>' +
																				'</td>' +
																			'</tr>' +
																			'</tbody>' +
																		'</table>' +
																	'</td>' +
																	'<td class="expander"></td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +            				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/112.jpg">' + 	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/113.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/113.jpg">' +                               				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h3 style="margin-top:0">Lorem Ipsum</h3>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/114.png',
		    'category': '14',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h3 style="margin-top:0">Lorem Ipsum</h3>' +
															'<p>Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +						
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/114.jpg">' +	
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/115.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/115-1.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature One</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
															'<center data-parsed="">' +
																'<table class="button small float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +    
															'</center>' +  
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/115-2.png" align="center" class="float-center"></center>' +
															'<h5 class="text-center">Feature Two</h5>' +
															'<p class="text-center">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
															'<center data-parsed="">' +
																'<table class="button small float-center">' +
																	'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																	'</tbody>' +
																'</table>' +    
															'</center>' +  
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/116.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/116-1.png">' +
															'<h5>Feature One</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +     
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/116-2.png">' +
															'<h5>Feature Two</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
															'<table class="button small">' +
																'<tbody>' +
																	'<tr>' +
																		'<td>' +
																			'<table>' +
																				'<tbody>' +
																				'<tr>' +
																					'<td>' +
																						'<a href="#" title="">Read More</a>' +
																					'</td>' +
																				'</tr>' +
																				'</tbody>' +
																			'</table>' +
																		'</td>' +
																	'</tr>' +
																'</tbody>' +
															'</table>' +     
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/117.png',
		    'category': '13',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/117-1.png">' +
															'<h5>Feature One</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/117-2.png">' +
															'<h5>Feature Two</h5>' +
															'<p>Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/118.png',
		    'category': '16',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-4 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/118.jpg">' +                                				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-8 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/icon-quote.png">' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>' +
															'<p>By Your Name</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/119.png',
		    'category': '16',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-8 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/icon-quote.png">' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="16px" style="font-size:16px;line-height:16px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +
															'<p class="lead">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>' +
															'<p>By Your Name</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +   
								
											'<th class="small-12 large-4 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/119.jpg">' +                                				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +                         		     
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/120.png',
		    'category': '18',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                   
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-6 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/120-1.png">' +
															'<h5>Contact Info</h5>' +
															'<p>123 Street Name, City.  State 1234. Phone: (123) 456 7890.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
											 '<th class="small-12 large-6 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/120-2.png">' +
															'<h5>Get in Touch</h5>' +
															'<p>If you have any questions, please write to us at <a href="#" title="">example@example.com</a></p>' +
														'</th>' +                                			
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +           
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/121.png',
		    'category': '17',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +                                 
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-3 columns first">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<img src="/contentbuilder/assets//email-blocks/images/121.jpg">' +                                				                              				
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
								
											'<th class="small-12 large-9 columns last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<h5 style="margin-top:0">By Your Name</h5>' +
															'<p> Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +        
										'</tr>' +
										'</tbody>' +
									'</table>' +
								'</td>' +
							 '</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/122.png',
		    'category': '1',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/image.png" align="center" class="float-center" style="width: 130px; height: 80px;"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/123.png',
		    'category': '1',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/image.png" align="center" class="float-center" style="width: 240px; height: 80px;"></center>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		{
		    'thumbnail': 'preview/124.png',
		    'category': '1',
		    'html':
				'<div>' +
					'<table align="center" class="container float-center">' +
						'<tbody>' +
							'<tr>' +
								'<td>' +
									'<table class="row">' +
										'<tbody>' +
										'<tr>' +
											'<th class="small-12 large-12 columns first last">' +
												'<table>' +
													'<tbody>' +
													'<tr>' +
														'<th>' +
															'<center data-parsed=""><img src="/contentbuilder/assets//email-blocks/images/image.png" align="center" class="float-center" style="width: 240px; height: 80px;"></center>' +
															'<table class="spacer">' +
																'<tbody>' +
																'<tr>' +
																	'<td height="18px" style="font-size:18px;line-height:18px;">&nbsp;</td>' +
																'</tr>' +
																'</tbody>' +
															'</table>' +			
															'<p class="text-center">MADE BY YOUR NAME</p>' +
														'</th>' +
													'</tr>' +
													'</tbody>' +
												'</table>' +
											'</th>' +
										'</tr>' +
										'</tbody>' +
									'</table>' +                            
								'</td>' +
							'</tr>' +
						'</tbody>' +
					'</table>' +
				'</div>'
		},
		
		
	]

};
