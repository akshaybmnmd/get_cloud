<?php
session_start();

?>

<div class="row">
	<style>
		.button {
			background-color: #4CAF50;
			/* Green */
			border: none;
			color: white;
			padding: 16px 32px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
			margin: 4px 2px;
			transition-duration: 0.4s;
			cursor: pointer;
		}

		.button1 {
			background-color: white;
			color: black;
			border: 2px solid #4CAF50;
		}

		.button1:hover {
			background-color: #4CAF50;
			color: white;
		}

		.button2 {
			background-color: white;
			color: black;
			border: 2px solid #008CBA;
		}

		.button2:hover {
			background-color: #008CBA;
			color: white;
		}

		.custom-file-input::-webkit-file-upload-button {
			visibility: hidden;
		}

		.custom-file-input::before {
			content: '+ Add Images';
			display: inline-block;
			background: linear-gradient(top, #f9f9f9, #e3e3e3);
			background-color: white;
			text-align: center;
			border: 2px solid #4CAF50;
			color: black;
			padding: 16px 32px;
			white-space: nowrap;
			margin: 4px 2px;
			transition-duration: 0.4s;
			cursor: pointer;
			font-size: 16px;
		}

		.custom-file-input:hover::before {
			background-color: #4CAF50;
			color: white;
		}

		.custom-file-input:active::before {
			background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
		}

		.custom-file-input2::-webkit-file-upload-button {
			visibility: hidden;
		}

		.custom-file-input2::before {
			content: '+ Add Videos';
			display: inline-block;
			background: linear-gradient(top, #f9f9f9, #e3e3e3);
			background-color: white;
			text-align: center;
			border: 2px solid #4CAF50;
			color: black;
			padding: 16px 32px;
			white-space: nowrap;
			margin: 4px 2px;
			transition-duration: 0.4s;
			cursor: pointer;
			font-size: 16px;
		}

		.custom-file-input2:hover::before {
			background-color: #4CAF50;
			color: white;
		}

		.custom-file-input2:active::before {
			background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
		}
	</style>
	<div>
		<form enctype="multipart/form-data" action="actions/upload_image.php" method="post">
			<input type="file" class="custom-file-input" name="upload" accept="image/*" />
			<input type="hidden" name="submitted" value="TRUE" />
			<button type="submit" class="button button2" value="Submit"><i class="fas fa-upload"></i>&nbsp;Upload image</button>
		</form>
	</div>

	<div>
		<form enctype="multipart/form-data" action="actions/upload_video.php" method="post">
			<input type="file" class="custom-file-input2" name="upload" accept="video/*" accept="video/x-matroska" />
			<input type="hidden" name="submitted" value="TRUE" />
			<button type="submit" class="button button2" value="Submit"><i class="fas fa-upload"></i>&nbsp;Upload video</button>
		</form>
	</div>
</div>
<script src="js/notyf.min.js"></script>
<script>
	var notyf = new Notyf();
	<?php if (isset($_SESSION['server'])) { ?>
		setTimeout(function() {
			notyf.confirm("<?php echo $_SESSION['message']; ?>");
		}, 500);
		console.log("<?php echo $_SESSION['message']; ?>");
	<?php unset($_SESSION['server']);
	} ?>
</script>