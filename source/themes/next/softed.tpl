{* crmv@82419 crmv@99315 *}

{* modal dialog that replaces alerts *}
<div id="alert-dialog" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		<div class="modal-body">
			<p id="alert-dialog-content"></p>
		</div>
		<div class="hidden modal-footer">
			<button class="btn btn-primary btn-ok" data-dismiss="modal">OK</button>
		</div>
		</div>
	</div>
</div>

{* modal dialog that replaces confirms *}
<div id="confirm-dialog" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		<div class="modal-body">
			<p id="confirm-dialog-content"></p>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary btn-cancel" data-dismiss="modal">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
			<button class="btn btn-primary btn-ok" data-dismiss="modal">OK</button>
		</div>
		</div>
	</div>
</div>