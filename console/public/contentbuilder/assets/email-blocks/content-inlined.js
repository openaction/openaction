
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td class="wrapper-inner" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table align="center" class="container" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; margin: 0 auto; padding: 0; text-align: inherit; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-6 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 303px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;"><img src="/contentbuilder/assets//email-blocks/images/image.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; height: 60px; max-width: 100%; outline: none; text-decoration: none; width: 200px;"></th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-6 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 303px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p class="text-right" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: right;"></p>' +
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
			'thumbnail': 'preview/002.png',
			'category': '2',
			'html':

				'<div>' +
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum is dummy text</h1></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p> ' +
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
			'thumbnail': 'preview/006.png',
			'category': '6',
			'html':

				'<div>' +
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum is dummy text</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p > ' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">BEAUTIFUL CONTENT</h1>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Lorem Ipsum is simply dummy text of the printing industry.</i></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Hi, John Roberts</h1>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Say Hello to Our New Look</h1>' +
				'<p class="lead text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h2 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum is dummy text</h2>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h2 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum <small style="color: #cacaca; font-size: 80%;">This is a note.</small></h2>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/014.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. <a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Click it!</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/015.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner primary" style="Margin: 0; background: #def0fc; border: 1px solid #444444; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. <a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Click it!</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;"><img src="/contentbuilder/assets//email-blocks/images/016.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/017.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/018.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/019.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/020.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/021.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Item</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Item</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 115.33333px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Item</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 115.33333px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Item</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 115.33333px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-check.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Item</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/024-1.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature One</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/024-2.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Two</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 115.33333px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/025-1.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature One</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 115.33333px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/025-2.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Two</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 115.33333px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/025-3.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Three</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature Item</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature Item</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature Item</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature Item</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/icon-check.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature Item</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h3 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 26px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Create Something Awesome</h3>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Get Started</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/029-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">View More</a>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/029-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">View More</a>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/030-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">View More</a>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/030-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">View More</a>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 115.33333px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/031.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-8 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Insert Text Here.</h1>' +
				'<table class="button large" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px 20px 10px 20px; text-align: left; text-decoration: none;">Sign Up</a>' +
				'<table style = "border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;" > ' +
				'<tbody>' +
				'<tr <td="" style="padding: 0; text-align: left; vertical-align: top;"></tr>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-8 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 308.66667px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/032.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum dolor sit amet.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum dolor sit amet.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-8 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 308.66667px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/033.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-8 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 308.66667px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/034.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-8 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 308.66667px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/035.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/036.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/037.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/038.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/039.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/040-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply text of the printing industry.&amp;nbsp;<a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Click it!</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/040-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply text of the printing industry.&amp;nbsp;<a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Click it!</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/041-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/041-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/042-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/042-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/042-3.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">By Your Name</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 32px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT OF THE PRINTING INDUSTRY</h1>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>This is a special report</i></p>' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 48px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/046.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Lorem Ipsum is Simply Text</h4>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">15 sections | 567 Min</p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 48px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">BEAUTIFUL CONTENT</h1>' +
				'<p class="lead text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><i>Lorem Ipsum is simply dummy text of the printing industry.</i></p>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/048.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 48px; font-weight: normal; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Beautiful Content</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/049.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">BEAUTIFUL CONTENT</p>' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 48px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM IS SIMPLY TEXT</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/050.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">BEAUTIFUL CONTENT</p>' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 48px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Download</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="container secondary" align="center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; margin: 0 auto; padding: 0; text-align: inherit; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td class="wrapper-inner" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Connect With Us:</h5>' +
				'<table align="left" class="menu vertical" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0 auto; color: #0a0a0a; display: block; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 0; text-align: left;" class="menu-item float-center"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none; width: 100%;">Twitter</a></th>' +
				'<th style="Margin: 0 auto; color: #0a0a0a; display: block; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 0; text-align: left;" class="menu-item float-center"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none; width: 100%;">Facebook</a></th>' +
				'<th style="Margin: 0 auto; color: #0a0a0a; display: block; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 0; text-align: left;" class="menu-item float-center"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none; width: 100%;">Google +</a></th>' +
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
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Contact Info:</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Phone: 123-456-7890</p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Email: <a href="mailto:#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">example@example.com</a></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row footer text-center" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: center; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/image.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; height: 50px; max-width: 100%; outline: none; text-decoration: none; width: 170px;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Call us at 123.456.7890<br> Email us at example@example.com</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">123 Street Name<br> State 12345</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">More Reading:</h4>' +
				'<ul>' +
				'<li><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Lorem Ipsum Dolor Sit Amet</a></li>' +
				'<li><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Lorem Ipsum Dolor Sit Amet</a></li>' +
				'<li><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Lorem Ipsum Dolor Sit Amet</a></li>' +
				'</ul>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Get Involved:</h4>' +
				'<ul>' +
				'<li><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Facebook</a></li>' +
				'<li><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Twitter</a></li>' +
				'<li><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Instagram</a></li>' +
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
				'<table align="center" class="container body-border float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table align="center" class="menu float-center" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: auto !important;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">example@example.com</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Facebook</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Twitter</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">(123)-456-7890</a></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table align="center" class="menu text-center float-center" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: auto !important;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: center; text-decoration: none;">Home</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: center; text-decoration: none;">About</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: center; text-decoration: none;">Portfolio</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: center; text-decoration: none;">Contact</a></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table align="center" class="menu float-center" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: auto !important;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Terms</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Privacy</a></th>' +
				'<th class="menu-item float-center" style="Margin: 0 auto; color: #0a0a0a; float: none; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Unsubscribe</a></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row collapsed footer" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">@copywrite nobody<br> <a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">example@example.com</a>                              | <a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Manage Email Notifications</a>                              | <a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Unsubscribe</a></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">You received this email because you\'re signed up to receive updates from us. <a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Click here to unsubscribe.</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><small style="color: #cacaca; font-size: 80%;">You\'re getting this email because you\'ve signed up for email updates. If you want to opt-out of future emails, <a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">unsubscribe here</a>.</small></p>' +
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
			'category': '',
			'html': '<div>' +
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><small style="color: #cacaca; font-size: 80%;">You received this email because you\'re signed up to get updates from us. <a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Click here to unsubscribe.</a></small></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="button large expand" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<center data-parsed="" style="min-width: 0; width: 100%;"><a href="#" align="center" class="float-center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold; line-height: 1.7; margin: 0; padding: 10px 20px 10px 20px; padding-left: 0; padding-right: 0; text-align: center; text-decoration: none; width: 100%;">Button</a></center>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="button large" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold; line-height: 1.7; margin: 0; padding: 10px 20px 10px 20px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="button" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 1.7; margin: 0; padding: 8px 16px 8px 16px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="button tiny" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 4px 8px 4px 8px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 10px; font-weight: normal; line-height: 1.7; margin: 0; padding: 4px 8px 4px 8px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button large float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold; line-height: 1.7; margin: 0; padding: 10px 20px 10px 20px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 1.7; margin: 0; padding: 8px 16px 8px 16px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button tiny float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 4px 8px 4px 8px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 10px; font-weight: normal; line-height: 1.7; margin: 0; padding: 4px 8px 4px 8px; text-align: left; text-decoration: none;">Button</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner primary" style="Margin: 0; background: #def0fc; border: 1px solid #444444; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<hr style="border: none; border-top: rgba(0,0,0,0.18) 1px solid; margin: 30px 0 25px; padding: 5px;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">By Your Name</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">By Your Name</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 593px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;"><img src="/contentbuilder/assets//email-blocks/images/075.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<table class="button small rounded" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/076.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<table class="button small rounded" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/077.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/078.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-8 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-8 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h4 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 24px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h4>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/079.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/081.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-8 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-8 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/082.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">$19</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Buy Now</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">$39</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Buy Now</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">PRODUCT NAME</h5>' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">$19</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">BUY NOW</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">PRODUCT NAME</h5>' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">$29</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">BUY NOW</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">$19</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Buy Now</a></p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">$29</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Buy Now</a></p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 40px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">$39</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Buy Now</a></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h3 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 26px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM IS SIMPLY TEXT</h3>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Read More</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner primary" style="Margin: 0; background: #def0fc; border: 1px solid #444444; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h3 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 26px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM IS SIMPLY TEXT</h3>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><a href="#" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Read More</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Read More</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Read More</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner primary" style="Margin: 0; background: #def0fc; border: 1px solid #444444; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Read More</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner primary" style="Margin: 0; background: #def0fc; border: 1px solid #444444; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">Read More</a></p>' +
				'</th>' +
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small rounded float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Discover More</a>' +
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
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner" style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small rounded float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Discover More</a>' +
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
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner primary" style="Margin: 0; background: #def0fc; border: 1px solid #444444; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small rounded float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Discover More</a>' +
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
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="callout-inner primary" style="Margin: 0; background: #def0fc; border: 1px solid #444444; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 10px; text-align: left; width: 100%;">' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing and industry. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small rounded float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Discover More</a>' +
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
				'<th class="expander" style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/091.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/092.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-8 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY TEXT</h1>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-8 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY TEXT</h1>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/093.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/094.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 36px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY TEXT</h1>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 36px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY TEXT</h1>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/095.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/096.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">BEAUTIFUL CONTENT</h1>' +
				'<p class="text-center lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/097.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
				'<p class="text-center lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/098.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/099.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h2 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum is Simply Dummy Text of the Printing Industry.</h2>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</i></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/100.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Lorem Ipsum Dolor sit Amet</i></p>' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/101.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Beautiful Content</i></p>' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 30px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Beautiful Content</i></p>' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 30px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/102.jpg" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 303px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/103-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 303px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/103-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/104-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/104-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/105-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="18px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 18px; font-weight: normal; hyphens: auto; line-height: 18px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/105-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="18px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 18px; font-weight: normal; hyphens: auto; line-height: 18px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/106.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s. Lorem Ipsum is simply dummy text.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/107.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 40px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">LOREM IPSUM IS SIMPLY DUMMY TEXT</h1>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button small rounded float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/109.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
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

				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;">' +
				'<table class="button small rounded float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: none; border-collapse: collapse !important; border-radius: 500px; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/110-1.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/110-2.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/111.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 30px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h1>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Beautiful Content</i></p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h1 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Open Sans; font-size: 30px; font-weight: bold; line-height: 1.3; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h1>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"><i>Beautiful Content</i></p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" align="center" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
				'</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</td>' +
				'<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/112.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/113.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h3 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 26px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h3>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h3 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 26px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">Lorem Ipsum</h3>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/114.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/115-1.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature One</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/115-2.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></center>' +
				'<h5 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: center; word-wrap: normal;">Feature Two</h5>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'<center data-parsed="" style="min-width: 212px; width: 100%;">' +
				'<table class="button small float-center" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 0 16px 0; padding: 0; text-align: center; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/116-1.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature One</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/116-2.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature Two</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'<table class="button small" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #2199e8; border: 2px solid #2199e8; border-collapse: collapse !important; color: #fefefe; font-family: Lato, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<a href="#" title="" style="Margin: 0; border: 0 solid #2199e8; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 5px 10px 5px 10px; text-align: left; text-decoration: none;">Read More</a>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/117-1.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature One</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/117-2.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Feature Two</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing indutry.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-4 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/118.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-8 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">By Your Name</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-8 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 360.66667px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/icon-quote.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="lead" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.6; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">By Your Name</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-4 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 167.33333px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/119.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/120-1.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Contact Info</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">123 Street Name, City. State 1234. Phone: (123) 456 7890.</p>' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +
				'<th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 264px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/120-2.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; padding: 0; text-align: left; word-wrap: normal;">Get in Touch</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">If you have any questions, please write to us at <a href="#" title="" style="Margin: 0; color: #2199e8; font-family: Lato, sans-serif; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left; text-decoration: none;">example@example.com</a></p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-3 columns first" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 13px; text-align: left; width: 119px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<img src="/contentbuilder/assets//email-blocks/images/121.jpg" style="-ms-interpolation-mode: bicubic; clear: both; display: block; max-width: 100%; outline: none; text-decoration: none; width: auto;">' +
				'</th>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'</th>' +

				'<th class="small-12 large-9 columns last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 13px; padding-right: 26px; text-align: left; width: 409px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<h5 style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Roboto, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.7; margin: 12px 0; margin-bottom: 10px; margin-top: 0; padding: 0; text-align: left; word-wrap: normal;">By Your Name</h5>' +
				'<p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;"> Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.</p>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/image.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; height: 80px; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: 130px;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/image.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; height: 80px; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: 240px;"></center>' +
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
				'<table align="center" class="container float-center" style="Margin: 0 auto; background: #fefefe; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.7; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">' +
				'<table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 26px; padding-right: 26px; text-align: left; width: 554px;">' +
				'<table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<th style="Margin: 0; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0; text-align: left;">' +
				'<center data-parsed="" style="min-width: 502px; width: 100%;"><img src="/contentbuilder/assets//email-blocks/images/image.png" align="center" class="float-center" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; height: 80px; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: 240px;"></center>' +
				'<table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">' +
				'<tbody>' +
				'<tr style="padding: 0; text-align: left; vertical-align: top;">' +
				'<td height="18px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 18px; font-weight: normal; hyphens: auto; line-height: 18px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&amp;nbsp;</td>' +
				'</tr>' +
				'</tbody>' +
				'</table>' +
				'<p class="text-center" style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Lato, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.7; margin: 0; margin-bottom: 10px; padding: 0; text-align: center;">MADE BY YOUR NAME</p>' +
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

		}



	]

};
