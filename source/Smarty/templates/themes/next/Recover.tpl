{* crmv@94525 *}
{assign var="THEME" value="next"}
{assign var="RELPATH" value=$PATH}
{assign var="BROWSER_TITLE" value='LBL_BROWSER_TITLE'|@getTranslatedString:'APP_STRINGS'}
{include file="HTMLHeader.tpl" head_include="icons,jquery,jquery_plugins,prototype"}

<link rel="stylesheet" type="text/css" href="{$RELPATH}themes/{$THEME}/recover.css" />

<body>

<div id="main-container" class="container">
	<div class="row">
		<div class="col-xs-offset-1 col-xs-10">
				
			<div id="content" class="col-xs-12">
				<div id="content-cont" class="col-xs-12">
					<div id="content-inner-cont" class="col-xs-12">
							
						<div class="col-xs-12 content-padding">	
							<div class="col-xs-6 nopadding vcenter text-left">
								<h2>{$TITLE}</h2>
							</div><!--
							--><div class="col-xs-6 nopadding vcenter text-right">
								<a href="http://www.vtecrm.com" target="_blank">
									<img width="200" src="{$RELPATH}{'login'|get_logo}" />
								</a>
							</div>
						</div>

						<div class="col-xs-12 content-padding">	
							{$BODY}
						</div>

					</div>
				</div>
			</div>
	
			<div id="footer" class="col-xs-12 content-padding">
				<div id="footer-inner" class="col-xs-12 content-padding text-center">
					<div class="spacer-50"></div>
				</div>
			</div>
				
		</div>
	</div>
</div>

</body>
</html>