<script>
	app.viewModels.titles.create.map();
	vars.trans.userLinkAdded = '<?php echo trans("stream::main.userLinkAdded") ?>';
	ko.applyBindings(app.viewModels.titles.create, $('#add-link-modal')[0]);
</script>