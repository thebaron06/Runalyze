	<h1>Eine Trainings-Datei hochladen</h1>

	<div id="upload-container">
		<div class="c button hide" id="file-upload">Datei hochladen</div>
	</div>

<script>
function createUploader() {
	$("#file-upload").removeClass("hide");
	new AjaxUpload('#file-upload', {
		allowedExtensions: [<?php echo $AllowedFormatsForJS; ?>],
		action: '<?php echo $_SERVER['SCRIPT_NAME']; ?>?json=true',
		onSubmit : function(file, extension){
			$("#upload-container").addClass('loading');
		},
		onComplete : function(file, response){
			if (response.substring(0,7) == 'success')
				$("#ajax").loadDiv('<?php echo $_SERVER['SCRIPT_NAME']; ?>?file='+encodeURIComponent(file));
			else {
				if (response == '')
					response = 'An unknown error occured.';
				$("#ajax").append('<p class="error">'+response+'</p>');
			}
		}		
	});
}
</script>

	<p class="text">
		&nbsp;
	</p>

	<p class="info">
		Unterst&uuml;tzte Formate: <?php echo $AllowedFormats; ?>
	</p>

<?php foreach (self::$additionalImporterInfo as $info): ?>
	<p class="info">
		<?php echo $info; ?>
	</p>
<?php endforeach; ?>

	<p class="warning">
		Achtung: Bei *.tcx- und *.logbook-Dateien mit mehreren Trainings werden diese sofort ohne weitere &Uuml;berpr&uuml;fung in die Datenbank eingetragen.
	</p>