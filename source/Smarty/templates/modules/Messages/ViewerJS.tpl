<!DOCTYPE html>
<!--
  Copyright (C) 2012-2014 KO GmbH <copyright@kogmbh.com>

  @licstart
  This file is part of WebODF.

  WebODF is free software: you can redistribute it and/or modify it
  under the terms of the GNU Affero General Public License (GNU AGPL)
  as published by the Free Software Foundation, either version 3 of
  the License, or (at your option) any later version.

  WebODF is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with WebODF.  If not, see <http://www.gnu.org/licenses/>.
  @licend

  @source: http://www.webodf.org/
  @source: https://github.com/kogmbh/WebODF/
-->

<!--
  This file is a derivative from a part of Mozilla's PDF.js project. The
  original license header follows.
-->

<!--
Copyright 2012 Mozilla Foundation

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
-->
<!-- crmv@62414 -->
<html dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!-- If you want to use custom CSS (@font-face rules, for example) you should uncomment
             the following reference and use a local.css file for that. See the example.local.css
             file for a sample.
        <link rel="stylesheet" type="text/css" href="local.css" media="screen"/>
        -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
        <link rel="stylesheet" type="text/css" href="modules/Messages/src/ViewerJS/viewer.css" media="screen"/>
        <script src="modules/Messages/src/ViewerJS/viewer.js" type="text/javascript" charset="utf-8"></script>
        <script src="modules/Messages/src/ViewerJS/PluginLoader.js" type="text/javascript" charset="utf-8"></script>
        <script>
			window.location.hash = '{$REQUESTED_FILE}';
            loadDocument(window.location.hash);
        </script>
    </head>

    <body>
        <div id = "viewer">
            <div id = "titlebar">
                <div id = "documentName" style="display:none;"></div>
				<div id = "toolbarLeft">
                        <div id = "navButtons" class = "splitToolbarButton">
                            <button id = "previous" class = "toolbarButton pageUp" title = "{$MOD.LBL_VIEWERJS_PREV_PAGE}"></button>
                            <div class="splitToolbarButtonSeparator"></div>
                            <button id = "next" class = "toolbarButton pageDown" title = "{$MOD.LBL_VIEWERJS_NEXT_PAGE}"></button>
                        </div>
                        <label id = "pageNumberLabel" class = "toolbarLabel" for = "pageNumber">{$MOD.LBL_VIEWERJS_PAGE}:</label>
                        <input type = "number" id = "pageNumber" class = "toolbarField pageNumber"/>
                        <span id = "numPages" class = "toolbarLabel"></span>
                    </div>
                    <div id = "toolbarMiddleContainer" class = "outerCenter">
                        <div id = "toolbarMiddle" class = "innerCenter">
                            <div id = 'zoomButtons' class = "splitToolbarButton">
                                <button id = "zoomOut" class = "toolbarButton zoomOut" title = "{$MOD.LBL_VIEWERJS_ZOOM_OUT}"></button>
                                <div class="splitToolbarButtonSeparator"></div>
                                <button id = "zoomIn" class = "toolbarButton zoomIn" title = "{$MOD.LBL_VIEWERJS_ZOOM_IN}"></button>
                            </div>
                            <span id="scaleSelectContainer" class="dropdownToolbarButton">
                                <select id="scaleSelect" title="{$MOD.LBL_VIEWERJS_ZOOM}" oncontextmenu="return false;">
                                    <option id="pageAutoOption" value="auto" selected>{$MOD.LBL_VIEWERJS_ZOOM_AUTO}</option>
                                    <option id="pageActualOption" value="page-actual">{$MOD.LBL_VIEWERJS_ZOOM_AS}</option>
                                    <option id="pageWidthOption" value="page-width">{$MOD.LBL_VIEWERJS_ZOOM_AL}</option>
                                    <option id="customScaleOption" value="custom"> </option>
                                    <option value="0.5">50%</option>
                                    <option value="0.75">75%</option>
                                    <option value="1">100%</option>
                                    <option value="1.25">125%</option>
                                    <option value="1.5">150%</option>
                                    <option value="2">200%</option>
                                </select>
                            </span>
                            <div id = "sliderContainer">
                                <div id = "slider"></div>
                            </div>
                        </div>
                    </div>
                <div id = "toolbarRight">
                    <button id = "presentation" class = "toolbarButton presentation" title = "{$MOD.LBL_VIEWERJS_PRESENTATION}"></button>
                    <button id = "fullscreen" class = "toolbarButton fullscreen" title = "{$MOD.LBL_VIEWERJS_FULLSCREEN}"></button>
                    <button id = "download" class = "toolbarButton download" title = "{$MOD.LBL_VIEWERJS_DOWNLOAD}"></button>
                </div>
           </div>
            <div id = "canvasContainer">
                <div id = "canvas"></div>
            </div>
            <div id = "overlayNavigator">
                <div id = "previousPage"></div>
                <div id = "nextPage"></div>
            </div>
            <div id = "overlayCloseButton">
            &#10006;
            </div>
            <div id = "dialogOverlay"></div>
            <div id = "blanked"></div>
        </div>
    </body>
</html>